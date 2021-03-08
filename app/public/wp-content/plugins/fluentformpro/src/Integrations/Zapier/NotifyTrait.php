<?php

namespace FluentFormPro\Integrations\Zapier;

use FluentForm\App\Modules\Form\FormFieldsParser;
use FluentForm\App\Services\Integrations\LogResponseTrait;

trait NotifyTrait
{
    use LogResponseTrait;

    public function notify($feed, $formData, $entry, $form)
    {
        try {
            $values = $feed['processedValues'];
            $payload = ['body' => $formData];
            $result = wp_remote_post($values['url'], $payload);

            if (is_wp_error($result)) {
                throw new \Exception($result->get_error_message(), 0);
            }

            $message = $result['response']['message'];

            if (substr($result['response']['code'], 0, 1) != 2) {
                throw new \Exception($message, 0);
            }

            do_action('ff_log_data', [
                'parent_source_id' => $form->id,
                'source_type' => 'submission_item',
                'source_id' => $entry->id,
                'component' => 'zapier',
                'status' => 'success',
                'title' => $feed['settings']['name'],
                'description' => 'Zapier feed has been successfully initialed and pushed data'
            ]);
            $this->logResponse(['message' => $message], $feed, $formData, $form, $entry->id, 'success');

        } catch (\Exception $e) {
            do_action('ff_log_data', [
                'parent_source_id' => $form->id,
                'source_type' => 'submission_item',
                'source_id' => $entry->id,
                'component' => 'zapier',
                'status' => 'failed',
                'title' => $feed['settings']['name'],
                'description' => $e->getMessage()
            ]);
            $this->logResponse([
                'message' => $e->getMessage()
            ], $feed, $formData, $form, $entry->id, 'failed');
        }
    }


    public function verifyEndpoint()
    {
        $formId = intval($this->app->request->get('form_id'));

        $form = wpFluent()->table('fluentform_forms')->find($formId);

        $fields = array_map(function ($f) {
            return str_replace('.*', '', $f);
        }, array_keys(FormFieldsParser::getInputs($form)));

        $webHook = wpFluent()
            ->table($this->table)
            ->where('form_id', $formId)
            ->where('meta_key', $this->metaKey)
            ->first();

        $webHook = json_decode($webHook->value);

        $requestData = json_encode(
            array_combine($fields, array_fill(0, count($fields), ''))
        );

        $requestHeaders['Content-Type'] = 'application/json';

        $payload = [
            'body'    => $requestData,
            'method'  => 'POST',
            'headers' => $requestHeaders
        ];

        $response = wp_remote_request($webHook->url, $payload);

        if (is_wp_error($response)) {
            return wp_send_json_error(array(
                'message' => $response->get_error_message()
            ), 400);
        }

        wp_send_json_success(array(
            'message' => __('Sample sent successfully.'),
        ));
    }
}
