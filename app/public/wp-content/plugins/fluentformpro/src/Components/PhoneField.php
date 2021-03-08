<?php

namespace FluentFormPro\Components;

use FluentForm\App\Services\FormBuilder\BaseFieldManager;
use FluentForm\Framework\Helpers\ArrayHelper;

class PhoneField extends BaseFieldManager
{
    public function __construct()
    {
        parent::__construct(
            'phone',
            'Phone/Mobile',
            ['phone', 'telephone', 'mobile'],
            'general'
        );
    }

    function getComponent()
    {
        return [
            'index'          => 15,
            'element'        => $this->key,
            'attributes'     => [
                'name'        => $this->key,
                'class'       => '',
                'value'       => '',
                'type'        => 'tel',
                'placeholder' => __('Mobile Number', 'fluentformpro')
            ],
            'settings'       => [
                'container_class'    => '',
                'placeholder'        => '',
                'int_tel_number'     => 'only_country_flag',
                'auto_select_country' => 'no',
                'label'              => $this->title,
                'label_placement'    => '',
                'help_message'       => '',
                'admin_field_label'  => '',
                'validation_rules'   => [
                    'required'           => [
                        'value'   => false,
                        'message' => __('This field is required', 'fluentformpro'),
                    ],
                    'valid_phone_number' => [
                        'value'   => false,
                        'message' => __('Phone number is not valid', 'fluentformpro')
                    ]
                ],
                'conditional_logics' => []
            ],
            'editor_options' => [
                'title'      => $this->title . ' Field',
                'icon_class' => 'el-icon-phone-outline',
                'template'   => 'inputText'
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
            'int_tel_number',
            'auto_select_country',
            'validation_rules',
        ];
    }

    public function generalEditorElement()
    {
        return [
            'int_tel_number' => [
                'template'  => 'radio',
                'label'     => 'Enable Smart Phone Field',
                'help_text' => 'Enable this if you want to display smart phone input which will show flags and validate the number',
                'options'   => [
                    [
                        'label' => 'Disable',
                        'value' => 'no'
                    ],
                    [
                        'label' => 'Only Flag',
                        'value' => 'only_country_flag'
                    ],
                    [
                        'label' => 'With Extended Number Format',
                        'value' => 'with_extended_validation'
                    ]
                ]
            ],
            'auto_select_country' => [
                'template'  => 'radio',
                'label'     => 'Enable Auto Country Select',
                'help_text' => 'If you enable this, The country will be selected based on user\'s ip address. ipinfo.io service will be used here',
                'options'   => [
                    [
                        'label' => 'No',
                        'value' => 'no'
                    ],
                    [
                        'label' => 'Yes',
                        'value' => 'yes'
                    ]
                ],
                'dependency' => array(
                    'depends_on' => 'settings/int_tel_number',
                    'value'      => 'no',
                    'operator'   => '!='
                )
            ]
        ];
    }

    public function render($data, $form)
    {

        $elementName = $data['element'];
        $data = apply_filters('fluenform_rendering_field_data_'.$elementName, $data, $form);

        $data['attributes']['class'] = @trim('ff-el-form-control ff-el-phone ' . $data['attributes']['class']);
        $data['attributes']['id'] = $this->makeElementId($data, $form);

        if($tabIndex = \FluentForm\App\Helpers\Helper::getNextTabIndex()) {
            $data['attributes']['tabindex'] = $tabIndex;
        }

        $flagType = ArrayHelper::get($data, 'settings.int_tel_number');

        if ($flagType == 'only_country_flag' || $flagType == 'with_extended_validation') {
           // $data['attributes']['placeholder'] = '';
            $data['attributes']['class'] .= ' ff_el_' . $flagType;
            $this->pushScripts($data, $form);
        }
        $elMarkup = "<input ".$this->buildAttributes($data['attributes'], $form).">";


        $html = $this->buildElementMarkup($elMarkup, $data, $form);
        echo apply_filters('fluenform_rendering_field_html_'.$elementName, $html, $data, $form);
    }

    private function pushScripts($data, $form)
    {
        // We can add assets for this field
        wp_enqueue_style('intlTelInput');
        wp_enqueue_script('intlTelInput');

        if (ArrayHelper::get($data, 'settings.int_tel_number') == 'with_extended_validation') {
            wp_enqueue_script('intlTelInputUtils');
        }



        add_action('wp_footer', function () use ($data, $form) {
            $geoLocate = ArrayHelper::get($data, 'settings.auto_select_country') == 'yes';

            $itlOptions = [
                'separateDialCode' => false,
                'nationalMode' => true,
                'autoPlaceholder' => 'aggressive',
                'formatOnDisplay' => true
            ];

            if($geoLocate) {
                $itlOptions['initialCountry'] = 'auto';
            } else {
                $itlOptions['initialCountry'] = '';
            }
            $itlOptions = apply_filters('fluentform_itl_options', $itlOptions, $data, $form);
            $itlOptions = json_encode($itlOptions);
            $ipProvider = apply_filters('fluentform_ip_provider', 'https://ipinfo.io');
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    function initTelInput() {
                        var telInput = jQuery('.<?php echo $form->instance_css_class; ?>').find("#<?php echo $data['attributes']['id']; ?>");
                        if(!telInput.length) {
                            return;
                        }
                        var itlOptions = JSON.parse('<?php echo $itlOptions; ?>');
                        <?php if($geoLocate): ?>
                        itlOptions.geoIpLookup = function(success, failure) {
                            jQuery.get("<?php echo $ipProvider; ?>", function(res) {
                                return true;
                            }, "json").always(function(resp) {
                                var countryCode = (resp && resp.country) ? resp.country : "";
                                success(countryCode);
                            });
                        };
                        <?php endif; ?>
                        var iti = intlTelInput(telInput[0], itlOptions);
                        telInput.on("keyup change", function () {
                            if (typeof intlTelInputUtils !== 'undefined') { // utils are lazy loaded, so must check
                                var currentText = iti.getNumber(intlTelInputUtils.numberFormat.E164);
                                if (typeof currentText === 'string') { // sometimes the currentText is an object :)
                                    iti.setNumber(currentText); // will autoformat because of formatOnDisplay=true
                                }
                            }
                        });
                    }

                    initTelInput();
                    $(document).on('reInitExtras', '.<?php echo $form->instance_css_class; ?>', function () {
                        initTelInput();
                    });
                });
            </script>
            <?php
        }, 9999);
    }
}