<?php

namespace FluentFormPro\Components\Post;

use FluentForm\Framework\Helpers\ArrayHelper;
use FluentFormPro\Components\Post\EditorSettings;
use FluentFormPro\Components\Post\PostFormHandler;

class Bootstrap
{
    public static function boot()
    {
        return new static;
    }

    public function __construct()
    {
        $isEnabled =  $this->isEnabled();
        add_filter('fluentform_global_addons', function ($addons) use ($isEnabled) {
            $app = wpFluentForm();
            $addons['postFeeds'] = [
                'title' => 'Post/CPT Creation',
                'category' => 'wp_core',
                'description' => 'Create post/any cpt on form submission. It will enable many new features including dedicated post fields.',
                'logo' => $app->publicUrl('img/integrations/post-creation.png'),
                'enabled' => ($isEnabled) ? 'yes' : 'no'
            ];
            return $addons;
        });

        if(!$isEnabled) {
            return;
        }

        $this->registerHooks();

        $this->registerPostFields();
    }

    protected function registerHooks()
    {
        add_filter('fluent_all_forms_vars', function ($settings) {
            $settings['has_post_feature'] = true;
            return $settings;
        });

        add_filter('fluentform_response_render_taxonomy', array($this, 'formatResponse'), 10, 4);

        $editorSettings = new EditorSettings;

        add_action('fluentform_inserted_new_form', [
            $editorSettings, 'onNewFormCreated'
        ]);

        add_filter('fluent_editor_components', [
            $editorSettings, 'registerEditorTaxonomyFields'
        ]);

        add_filter('fluent_editor_element_settings_placement', [
            $editorSettings, 'elementPlacementSettings'
        ]);

        add_filter('fluentform_form_settings_menu', [
            $editorSettings, 'registerPostFormSettingsMenu'
        ], 10, 2);

        $postFormHandler = new PostFormHandler;

        add_action('fluentform_render_item_taxonomy', [
            $postFormHandler, 'renderTaxonomyFields'
        ], 10, 2);

        add_action('fluentform_submission_inserted_post_form', [
            $postFormHandler, 'onFormSubmissionInserted'
        ], 10, 3);
    }

    protected function registerPostFields()
    {
        new \FluentFormPro\Components\Post\Components\PostTitle;
        new \FluentFormPro\Components\Post\Components\PostContent;
        new \FluentFormPro\Components\Post\Components\PostExcerpt;
        new \FluentFormPro\Components\Post\Components\FeaturedImage;
    }

    private function isEnabled()
    {
        $globalModules = get_option('fluentform_global_modules_status');
        return $globalModules && isset($globalModules['postFeeds']) && $globalModules['postFeeds'] == 'yes';
    }

    public function formatResponse($response, $field, $form_id, $isHtml)
    {
        if(!$response) {
            return;
        }
        if(!is_array($response)) {
            return $response;
        }

        $options = ArrayHelper::get($field, 'raw.settings.options', []);

        if(!$options) {
            return fluentImplodeRecursive(', ', $response);
        }

        $formattedResponse = [];
        foreach ($response as $term_id) {
            if(isset($options[$term_id])) {
                $formattedResponse[] = $options[$term_id];
            } else {
                $formattedResponse[] = $term_id;
            }
        }

        if(!$isHtml) {
            return fluentImplodeRecursive(', ', $formattedResponse);
        }

        $html = $html = '<ul class="ff_entry_list">';
        foreach ($formattedResponse as $label => $response) {
            $html .= '<li>'.$response.'</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}
