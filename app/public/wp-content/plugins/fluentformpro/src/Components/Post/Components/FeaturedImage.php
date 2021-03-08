<?php

namespace FluentFormPro\Components\Post\Components;

use FluentForm\App\Services\FormBuilder\BaseFieldManager;
use FluentForm\Framework\Helpers\ArrayHelper;

class FeaturedImage extends BaseFieldManager
{
    public function __construct()
    {
        parent::__construct(
            'featured_image',
            'Featured Image',
            ['image', 'featured_image'],
            'post'
        );
    }

    function getComponent()
    {
        return [
            'index'          => 3,
            'element'        => $this->key,
            'attributes'     => [
                'name'        => $this->key,
                'class'       => '',
                'value'       => '',
                'type'        => 'file',
                'placeholder' => '',
                'accept' => 'image/*'
            ],
            'settings'       => [
                'container_class'    => '',
                'placeholder'        => '',
                'label'              => $this->title,
                'label_placement'    => '',
                'help_message'       => '',
                'admin_field_label'  => '',
                'btn_text' => 'Choose File',
                'validation_rules'   => [
                    'required'           => [
                        'value'   => false,
                        'message' => __('This field is required', 'fluentformpro'),
                    ],
                    'max_file_size' => [
                        'value' => 1048576,
                        '_valueFrom' => 'MB',
                        'message' => __('Maximum file size limit is 1MB', 'fluentformpro')
                    ],
                    'max_file_count' => [
                        'value' => 1,
                        'message' => __('You can upload maximum 1 image', 'fluentformpro')
                    ],
                    'allowed_image_types' => [
                        'value' => array(),
                        'message' => __('Allowed image types does not match', 'fluentformpro')
                    ]
                ],
                'conditional_logics' => []
            ],
            'editor_options' => [
                'title' => $this->title,
                'icon_class' => 'ff-edit-images',
                'template' => 'inputFile'
            ],
        ];
    }

    public function getGeneralEditorElements()
    {
        return [
            'label',
            'admin_field_label',
            'placeholder',
            'value',
            'label_placement',
            'validation_rules',
        ];
    }

    public function render($data, $form)
    {
        $elementName = $data['element'];

        $data = apply_filters('fluenform_rendering_field_data_'.$elementName, $data, $form);

        $data['attributes']['class'] = @trim(
            'ff-el-form-control '. $data['attributes']['class'].' ff-screen-reader-element'
        );

        $data['attributes']['id'] = $this->makeElementId($data, $form);

        $data['attributes']['multiple'] = false;

        if($tabIndex = \FluentForm\App\Helpers\Helper::getNextTabIndex()) {
            $data['attributes']['tabindex'] = $tabIndex;
        }
        
        $btnText = ArrayHelper::get($data, 'settings.btn_text');

        if(!$btnText) {
            $btnText = __('Choose File', 'fluentformpro');
        }

        $elMarkup = "<label for='".$data['attributes']['id']."' class='ff_file_upload_holder'><span class='ff_upload_btn ff-btn'>".$btnText."</span> <input %s></label>";

        $elMarkup = sprintf($elMarkup, $this->buildAttributes($data['attributes'], $form));

        $html = $this->buildElementMarkup($elMarkup, $data, $form);

        echo apply_filters('fluenform_rendering_field_html_'.$elementName, $html, $data, $form);
        
        $this->enqueueProScripts();
    }

    public function enqueueProScripts()
    {
        wp_enqueue_script('fluentform-uploader-jquery-ui-widget');
        wp_enqueue_script('fluentform-uploader-iframe-transport');
        wp_enqueue_script('fluentform-uploader');
    }
}
