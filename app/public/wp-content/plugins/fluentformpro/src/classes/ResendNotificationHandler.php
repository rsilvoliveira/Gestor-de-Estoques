<?php

namespace FluentFormPro\classes;

use FluentForm\App\Modules\Form\Form;
use FluentForm\App\Services\FormBuilder\ShortCodeParser;
use FluentForm\Framework\Helpers\ArrayHelper;

class ResendNotificationHandler
{
    public function resend()
    {
        $notificationId = intval(ArrayHelper::get($_REQUEST, 'notification_id'));
        $formId = intval(ArrayHelper::get($_REQUEST, 'form_id'));
        $entryId = intval(ArrayHelper::get($_REQUEST, 'entry_id'));
        $sendToType = sanitize_text_field(ArrayHelper::get($_REQUEST, 'send_to_type'));
        $customRecipient = sanitize_text_field(ArrayHelper::get($_REQUEST, 'send_to_custom_email'));

        $feed = wpFluent()->table('fluentform_form_meta')
                            ->where('id', $notificationId)
                            ->where('meta_key', 'notifications')
                            ->where('form_id', $formId)
                            ->first();

        if(!$feed) {
            wp_send_json_error([
                'message' => __('Sorry, No notification found!')
            ], 423);
        }

        $parsedValue = \json_decode($feed->value, true);

        $entry = wpFluent()->table('fluentform_submissions')
                        ->where('id', $entryId)
                        ->first();

        $formData = \json_decode($entry->response, true);

        $processedValues = ShortCodeParser::parse($parsedValue, $entryId, $formData);


        if($sendToType == 'custom') {
            $processedValues['bcc'] = '';
            $processedValues['sendTo']['email'] = $customRecipient;
        }


        $form = wpFluent()->table('fluentform_forms')
            ->where('id', $formId)
            ->first();

        $enabledFeed = [
            'id'       => $feed->id,
            'meta_key' => $feed->meta_key,
            'settings' => $parsedValue,
            'processedValues' => $processedValues
        ];

        add_action('wp_mail_failed', function ($error) {
            $reason = $error->get_error_message();
            wp_send_json_error([
                'message' => "Email Notification failed to sent. Reason: " . $reason
            ], 423);
        }, 10, 1);


        $notifier = wpFluentForm()->make(
            'FluentForm\App\Services\FormBuilder\Notifications\EmailNotification'
        );
        $notifier->notify($enabledFeed['processedValues'], $formData, $form, $entry->id);
        
        wp_send_json_success([
            'message' => 'Notification successfully resent'
        ], 200);

    }
}