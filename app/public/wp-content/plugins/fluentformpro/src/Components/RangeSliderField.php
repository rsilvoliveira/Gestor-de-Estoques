<?php

namespace FluentFormPro\Components;

use FluentForm\App\Services\FormBuilder\BaseFieldManager;

class RangeSliderField extends BaseFieldManager
{
    public function __construct()
    {
        parent::__construct(
            'rangeslider',
            'Range Slider',
            ['range', 'number', 'slider'],
            'advanced'
        );
    }

    function getComponent()
    {
        return [
            'index'          => 15,
            'element'        => $this->key,
            'attributes'     => [
                'name'  => $this->key,
                'class' => '',
                'value' => '',
                'min'   => 0,
                'max'   => 10,
                'type'  => 'range'
            ],
            'settings'       => [
                'container_class'    => '',
                'placeholder'        => '',
                'label'              => $this->title,
                'label_placement'    => '',
                'help_message'       => '',
                'admin_field_label'  => '',
                'validation_rules'   => [
                    'required' => [
                        'value'   => false,
                        'message' => __('This field is required', 'fluentformpro'),
                    ]
                ],
                'conditional_logics' => []
            ],
            'editor_options' => [
                'title'      => $this->title . ' Field',
                'icon_class' => 'dashicons dashicons-leftright',
                'template'   => 'inputSlider'
            ],
        ];
    }

    public function getGeneralEditorElements()
    {
        return [
            'label',
            'admin_field_label',
            'value',
            'min',
            'max',
            'label_placement',
            'validation_rules',
        ];
    }

    public function render($data, $form)
    {
        $elementName = $data['element'];
        $data = apply_filters('fluenform_rendering_field_data_'.$elementName, $data, $form);

        if($tabIndex = \FluentForm\App\Helpers\Helper::getNextTabIndex()) {
            $data['attributes']['tabindex'] = $tabIndex;
        }

        $data['attributes']['class'] = @trim('ff-el-form-control '. $data['attributes']['class']);
        $data['attributes']['id'] = $this->makeElementId($data, $form);

        $this->registerScripts($form, $data['attributes']['id']);


        if($data['attributes']['value'] == '') {
            $data['attributes']['value'] = 0;
        }

        $data['attributes']['type'] = 'range';
        $data['attributes']['data-calc_value'] = $data['attributes']['value'];

        $elMarkup = "<div class='ff_slider_wrapper'><input ".$this->buildAttributes($data['attributes'], $form)."></div>";

        $html =  $this->buildElementMarkup($elMarkup, $data, $form);
        echo apply_filters('fluenform_rendering_field_html_'.$elementName, $html, $data, $form);

    }

    private function registerScripts($form, $elementId)
    {
        wp_enqueue_script(
            'rangeslider',
            FLUENTFORMPRO_DIR_URL . 'public/libs/rangeslider/rangeslider.min.js',
            array('jquery'),
            '2.3.0',
            true
        );

        wp_enqueue_style(
            'rangeslider',
            FLUENTFORMPRO_DIR_URL . 'public/libs/rangeslider/rangeslider.css',
            array(),
            '2.3.0',
            'all'
        );

        add_action('wp_footer', function () use($form, $elementId) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    function initRangeSlider() {
                        var $element = $('#<?php echo $elementId; ?>');
                        if(!$element.length) {
                            return;
                        }
                        $element.rangeslider({
                            polyfill: false,
                            onSlide: function(position, value) {
                                $('#<?php echo $elementId; ?>').closest('.ff_slider_wrapper').find('.rangeslider__handle').attr('data-before', value);
                            },
                            onInit: function() {
                                $element.closest('.ff_slider_wrapper').find('.rangeslider__handle').attr('data-before', $element.val());
                            }
                        });
                        $element.on('change', function () {
                            $(this).closest('.ff_slider_wrapper').find('.rangeslider__handle').attr('data-before', $(this).val());
                        });
                    }
                    initRangeSlider();
                    
                    $(document).on('reInitExtras', '.<?php echo $form->instance_css_class; ?>', function () {
                        initRangeSlider();
                    });
                });
            </script>
            <?php
        }, 99);

    }
}