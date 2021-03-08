<?php

namespace FluentFormPro\Payments;

use FluentForm\App\Databases\Migrations\FormSubmissions;
use FluentForm\App\Helpers\Helper;
use FluentFormPro\Payments\Migrations\Migration;

class AjaxEndpoints
{
    public function handleEndpoint($route)
    {
        $validRoutes = [
            'enable_payment' => 'enablePaymentModule',
            'update_global_settings' => 'updateGlobalSettings',
            'get_payment_method_settings' => 'getPaymentMethodSettings',
            'save_payment_method_settings' => 'savePaymentMethodSettings',
            'get_form_settings' => 'getFormSettings',
            'save_form_settings' => 'saveFormSettings'
        ];

        if(isset($validRoutes[$route])) {
            $this->{$validRoutes[$route]}();
        }

        die();
    }

    public function enablePaymentModule()
    {
        $this->upgradeDb();
        // Update settings
        $settings = PaymentHelper::updatePaymentSettings([
            'status' => 'yes'
        ]);
        // send response to reload the page

        wp_send_json_success([
            'message' => __('Payment Module successfully enabled!', 'fluentformpro'),
            'settings' => $settings,
            'reload' => 'yes'
        ]);
    }

    private function upgradeDB()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'fluentform_transactions';
        $cols = $wpdb->get_col("DESC {$table}", 0);

        if($cols && in_array('subscription_id', $cols) && in_array('transaction_hash', $cols)) {
            // We are good
        } else {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
            Migration::migrate();
            // Migrate the database
            FormSubmissions::migrate(true); // Add payment_total
        }
    }

    public function updateGlobalSettings()
    {
        $settings = wp_unslash($_REQUEST['settings']);

        // Update settings
        $settings = PaymentHelper::updatePaymentSettings($settings);

        // send response to reload the page
        wp_send_json_success([
            'message' => __('Settings successfully updated!', 'fluentformpro'),
            'settings' => $settings,
            'reload' => 'yes'
        ]);

    }

    public function getPaymentMethodSettings()
    {
        $method = sanitize_text_field($_REQUEST['method']);
        $settings = apply_filters('fluentform_payment_settings_'.$method, []);

        wp_send_json_success([
            'settings' => ($settings) ? $settings : false
        ]);
    }

    public function savePaymentMethodSettings()
    {
        $method = sanitize_text_field($_REQUEST['method']);
        $settings = wp_unslash($_REQUEST['settings']);

        $validationErrors = apply_filters('payment_method_settings_validation_'.$method, [], $settings);

        if($validationErrors) {
            wp_send_json_error([
                'message' => __('Failed to save settings', 'fluentformpro'),
                'errors' => $validationErrors
            ], 423);
        }

        update_option('fluentform_payment_settings_'.$method, $settings, 'yes');

        wp_send_json_success([
            'message' => __('Settings successfully updated', 'fluentformpro')
        ]);
    }

    public function getFormSettings()
    {
        $formId = intval($_REQUEST['form_id']);
        $settings = PaymentHelper::getFormSettings($formId, 'admin');
        wp_send_json_success([
            'settings' => $settings,
            'currencies' => PaymentHelper::getCurrencies(),
            'payment_methods' => PaymentHelper::getFormPaymentMethods($formId)
        ], 200);
    }

    public function saveFormSettings()
    {
        $formId = intval($_REQUEST['form_id']);
        $settings = wp_unslash($_REQUEST['settings']);
        Helper::setFormMeta($formId, '_payment_settings', $settings);

        wp_send_json_success([
            'message' => __('Settings successfully saved', 'fluentformpro')
        ], 200);
    }
}