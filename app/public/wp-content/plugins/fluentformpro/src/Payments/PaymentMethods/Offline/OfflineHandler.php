<?php

namespace FluentFormPro\Payments\PaymentMethods\Offline;

class OfflineHandler
{
    protected $key = 'test';

    public function init()
    {
        add_filter('fluentform_payment_settings_' . $this->key, array($this, 'getSettings'));

        if(!$this->isEnabled()) {
            return;
        }

        add_filter(
            'fluentformpro_available_payment_methods',
            [$this, 'pushPaymentMethodToForm']
        );

        (new OfflineProcessor())->init();
    }

    public function pushPaymentMethodToForm($methods)
    {
        $methods[$this->key] = [
            'title' => __('Test Payment', 'fluentformpro'),
            'enabled' => 'yes',
            'method_value' => $this->key,
            'settings' => [
                'option_label' => [
                    'type' => 'text',
                    'template' => 'inputText',
                    'value' => 'Test Payment',
                    'label' => 'Method Label'
                ]
            ]
        ];

        return $methods;
    }

    public function getSettings()
    {
        $defaults = [
            'is_active' => 'no',
            'payment_mode' => 'test',
            'payment_instruction' => ''
        ];

        $settings = get_option('fluentform_payment_settings_test', []);

        $settings = wp_parse_args($settings, $defaults);

        return $settings;
    }

    public function isEnabled()
    {
        $settings = $this->getSettings();
        return $settings['is_active'] == 'yes';
    }
}
