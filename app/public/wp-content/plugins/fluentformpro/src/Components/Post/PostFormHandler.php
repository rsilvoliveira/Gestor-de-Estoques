<?php

namespace FluentFormPro\Components\Post;

use FluentForm\App\Services\ConditionAssesor;
use FluentForm\App\Services\FormBuilder\ShortCodeParser;
use FluentForm\Framework\Helpers\ArrayHelper;
use FluentFormPro\Components\Post\Components\HierarchicalTaxonomy;
use FluentFormPro\Components\Post\Components\NonHierarchicalTaxonomy;

class PostFormHandler
{
    public function renderTaxonomyFields($data, $form)
    {
        if ($form->type != 'post') return;

        if (isset($data['taxonomy_settings'])) {
            if ($data['taxonomy_settings']['hierarchical']) {
                return (new HierarchicalTaxonomy)->compile($data, $form);
            } else {
                return (new NonHierarchicalTaxonomy)->compile($data, $form);
            }
        }
    }

    public function onFormSubmissionInserted($entryId, $formData, $form)
    {
        $feeds = $this->getFormFeeds($form);
        
        if (!$feeds) {
            return;
        }

        $postType = $this->getPostType($form);

        foreach ($feeds as $feed) {
            $feed->value = json_decode($feed->value, true);

            if (!ArrayHelper::get($feed->value, 'feed_status')) continue;

            $feed = ShortCodeParser::parse($feed->value, $entryId, $formData, $form);

            if (!$this->isConditionMet($feed, $formData)) {
                continue;
            }

            $postData = [
                'post_type'      => $postType,
                'post_status'    => ArrayHelper::get($feed, 'post_status'),
                'comment_status' => ArrayHelper::get($feed, 'comment_status')
            ];

            $postData = $this->mapPostFields($feed, $postData);

            $postData = $this->mapMetaFields($feed, $postData, $postType);

            $formFields = json_decode($form->form_fields, true);

            foreach ($formFields['fields'] as $field) {

                if ($field['element'] == 'featured_image') {
                    $fieldName = $field['attributes']['name'];

                    if (isset($formData[$fieldName])) {
                        $postData['featured_Image'] = $formData[$fieldName][0];
                    }
                }

                if (isset($field['taxonomy_settings'])) {
                    $postData = $this->mapTaxonomyFields(
                        $field['taxonomy_settings'], $postData, $formData
                    );
                }
            }

            $this->insertPost($feed, $postData, $form, $entryId);
        }
    }

    protected function isConditionMet($feed, $formData)
    {
        // We have to check if the feed meets the conditional Logic
        $conditionSettings = ArrayHelper::get($feed, 'conditionals');

        if (
            !$conditionSettings ||
            !ArrayHelper::isTrue($conditionSettings, 'status') ||
            !count(ArrayHelper::get($conditionSettings, 'conditions'))
        ) {
            return true;
        }

        return ConditionAssesor::evaluate($feed, $formData);
    }

    protected function getFormFeeds($form)
    {
        return wpFluent()->table('fluentform_form_meta')
            ->where('form_id', $form->id)
            ->where('meta_key', 'postFeeds')
            ->get();
    }

    protected function getPostType($form)
    {
        $postSettings = wpFluent()->table('fluentform_form_meta')
            ->where('form_id', $form->id)
            ->where('meta_key', 'post_settings')
            ->first()->value;

        $postSettings = json_decode($postSettings);

        return $postSettings->post_type;
    }

    protected function mapPostFields($feed, $postData)
    {
        foreach ($feed['post_fields_mapping'] as $postFieldMapping) {
            $postField = $postFieldMapping['post_field'];

            if ($postField != 'post_title') {
                $postData[$postField] = $postFieldMapping['form_field'];
            } else {
                $postData[$postField] = wp_strip_all_tags($postFieldMapping['form_field']);
            }
        }

        return $postData;
    }

    protected function mapMetaFields($feed, $postData, $postType)
    {
        $metaInputs = ArrayHelper::get($feed, 'meta_fields_mapping', []);

        foreach ($metaInputs as $metaFieldMapping) {
            $metaKey = $metaFieldMapping['meta_key'];
            $postData['meta_input'][$metaKey] = $metaFieldMapping['meta_value'];
        }

        $acfFields = ArrayHelper::get($feed, 'acf_mappings', []);

        if (!$acfFields) {
            // return $metaInputs;
            return $postData;
        }


        $acfOriginFields = $this->getAcfFields($postType);

        if (!$acfOriginFields) {
            // return $metaInputs;
            return $postData;
        }

        foreach ($acfFields as $acfField) {
            $fieldValue = ArrayHelper::get($acfField, 'field_value');
            $fieldKey = ArrayHelper::get($acfField, 'field_key');
            
            if (!$fieldKey || !$fieldValue || !isset($acfOriginFields[$fieldKey])) {
                continue;
            }

            $fieldConfig = $acfOriginFields[$fieldKey];
            $mataName = $fieldConfig['name'];
            $postData['meta_input'][$mataName] = $fieldValue;
            $postData['meta_input']['_' . $mataName] = $fieldKey;
        }

        return $postData;
    }

    protected function mapTaxonomyFields($taxonomySettings, $postData, $formData)
    {
        $taxonomyFieldName = $taxonomySettings['name'];

        if (!isset($formData[$taxonomyFieldName])) {
            return $postData;
        }

        $taxonomyData = $formData[$taxonomyFieldName];

        if ($taxonomyFieldName == 'category') {
            $postData['post_category'] = (array)$taxonomyData;
        } else if ($taxonomyFieldName == 'post_tag') {
            $tags = explode(',', $taxonomyData);
            $postData['tags_input'] = array_map('trim', $tags);
        } else {
            $postData['tax_input'][$taxonomyFieldName] = $taxonomyData;
        }

        return $postData;
    }

    protected function insertPost($feed, $postData, $form, $entryId)
    {
        $postId = wp_insert_post($postData);
        
        if (is_wp_error($postId)) {
            return;
        }

        $editLink = get_edit_post_link($postId);

        $info = 'WP Post/CPT created from submission. Post ID: '. $postId .'. <a href="'.$editLink.'" target="_blank">Edit Post/CPT</a>';

        do_action('ff_log_data', [
            'parent_source_id' => $form->id,
            'source_type' => 'submission_item',
            'source_id' => $entryId,
            'component' => 'postFeeds',
            'status' => 'success',
            'title' => 'Post created from form submission',
            'description' => $info
        ]);


        wpFluent()->table('fluentform_submission_meta')
                    ->insert([
                        'response_id' => $entryId,
                        'form_id' => $form->id,
                        'meta_key' => '__postFeeds_created_id',
                        'value' => $postId,
                        'name' => 'Post/CPT Created',
                        'created_at' => current_time('mysql'),
                        'updated_at' => current_time('mysql'),
                    ]);

        update_post_meta($postId, '_fluentform_id', $form->id);

        if (!function_exists('set_post_thumbnail')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }

        $format = 'post-format-' . $feed['post_format'];
        wp_set_post_terms($postId, $format, 'post_format');

        if (isset($postData['featured_Image'])) {
            $this->setFeaturedImage($postId, $postData['featured_Image']);
        }

        do_action('fluentform_post_integration_success', $postId, $postData, $entryId, $form, $feed);
    }

    protected function setFeaturedImage($postId, $featuredImage)
    {
        $wpFileType = wp_check_filetype($featuredImage, null);

        $attachmentData = [
            'post_mime_type' => $wpFileType['type'],
            'post_title'     => sanitize_file_name($featuredImage),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];

        $path = wp_upload_dir()['basedir'] . FLUENTFORM_UPLOAD_DIR;

        $file = $path . substr($featuredImage, strrpos($featuredImage, '/'));

        $attachmentId = wp_insert_attachment($attachmentData, $file, $postId);

        $attachmentMetaData = wp_generate_attachment_metadata($attachmentId, $file);

        wp_update_attachment_metadata($attachmentId, $attachmentMetaData);

        return set_post_thumbnail($postId, $attachmentId);
    }

    protected function getAcfFields($postType)
    {
        if (!class_exists('ACF')) {
            return [];
        }

        $field_groups = acf_get_field_groups([
            'post_type' => $postType
        ]);

        $formattedFields = [];
        
        $acceptedFields = [
            'text',
            'textarea',
            'number',
            'email',
            'url',
            'password',
            'wysiwyg',
            'date_picker',
            'date_time_picker',
            'time_picker'
        ];

        $acceptedFields = apply_filters('fluent_post_acf_accepted_fileds', $acceptedFields);

        foreach ($field_groups as $field_group) {
            
            $fields = acf_get_fields($field_group);
            
            foreach ($fields as $field) {

                if (in_array($field['type'], $acceptedFields)) {
                    $formattedFields[$field['key']] = [
                        'type'  => $field['type'],
                        'label' => $field['label'],
                        'name'  => $field['name'],
                        'key'   => $field['key']
                    ];
                }
            }
        }

        return $formattedFields;
    }
}
