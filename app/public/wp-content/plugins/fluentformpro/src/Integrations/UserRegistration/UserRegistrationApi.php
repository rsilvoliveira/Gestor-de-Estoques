<?php

namespace FluentFormPro\Integrations\UserRegistration;

use FluentForm\App\Helpers\Helper;
use FluentForm\Framework\Helpers\ArrayHelper;

class UserRegistrationApi
{
    public function getUserRoles()
    {
        $roles = get_editable_roles();

        $validRoles = [];
        foreach ($roles as $roleKey => $role) {
            if (!ArrayHelper::get($role, 'capabilities.manage_options')) {
                $validRoles[$roleKey] = $role['name'];
            }
        }

        return apply_filters('fluentorm_UserRegistration_creatable_roles', $validRoles);
    }

    public function validate($settings, $settingsFields)
    {
        foreach ($settingsFields['fields'] as $field) {

            if ($field['key'] != 'CustomFields') continue;

            $errors = [];

            foreach ($field['primary_fileds'] as $primaryField) {
                if (!empty($primaryField['required'])) {
                    if (empty($settings[$primaryField['key']])) {
                        $errors[$primaryField['key']] = $primaryField['label'] . ' is required.';
                    }
                }
            }

            if ($errors) {
                wp_send_json_error([
                    'message' => array_shift($errors),
                    'errors' => $errors
                ], 422);
            }
        }

        return $settings;
    }

    public function registerUser($feed, $formData, $entry, $form, $integrationKey)
    {
        if (get_current_user_id()) return;

        if (!is_email($feed['processedValues']['Email'])) {
            $feed['processedValues']['Email'] = ArrayHelper::get(
                $formData, $feed['processedValues']['Email']
            );
        }

        if (!is_email($feed['processedValues']['Email'])) return;

        if (email_exists($feed['processedValues']['Email'])) return;

        $this->createUser($feed, $formData, $entry, $form, $integrationKey);
    }

    protected function createUser($feed, $formData, $entry, $form, $integrationKey)
    {
        $parsedData = $feed['processedValues'];

        $email = $parsedData['Email'];

        if (empty($parsedData['password'])) {
            $password = wp_generate_password(8);
        } else {
            $password = $parsedData['password'];
        }

        $userId = wp_create_user($email, $password, $email);

        if (is_wp_error($userId)) {
            return $this->addLog(
                $feed['settings']['name'],
                'failed',
                $userId->get_error_message(),
                $form->id,
                $entry->id,
                $integrationKey
            );
        }

        $this->updateUser($parsedData, $userId);

        $this->addUserRole($parsedData, $userId);

        $this->addUserMeta($parsedData, $userId, $form->id);

        $this->maybeLogin($parsedData, $userId, $entry);

        $this->maybeSendEmail($parsedData, $userId);

        $this->addLog(
            $feed['settings']['name'],
            'success',
            'user has been successfully created. Created User ID: ' . $userId,
            $form->id,
            $entry->id,
            $integrationKey
        );

        wpFluent()->table('fluentform_submissions')
            ->where('id', $entry->id)
            ->update([
                'user_id' => $userId
            ]);
    }

    protected function updateUser($parsedData, $userId)
    {
        $name = trim($parsedData['first_name'] . ' ' . $parsedData['last_name']);

        if ($name) {
            wp_update_user([
                'ID' => $userId,
                'user_nicename' => $name,
                'display_name' => $name
            ]);
        }
    }

    protected function addUserRole($parsedData, $userId)
    {
        $userRoles = $this->getUserRoles();
        $assignedRole = $parsedData['userRole'];

        if (!isset($userRoles[$assignedRole])) {
            $assignedRole = 'subscriber';
        }

        $user = new \WP_User($userId);
        $user->set_role($assignedRole);

    }

    protected function addUserMeta($parsedData, $userId, $formId)
    {
        foreach ($parsedData['userMeta'] as $userMeta) {
            $userMetas[$userMeta['label']] = $userMeta['item_value'];
        }

        $userMetas = array_merge($userMetas, [
            'first_name' => $parsedData['first_name'],
            'last_name' => $parsedData['last_name']
        ]);

        if (!isset($userMetas['nickname'])) {
            $userMetas['nickname'] = $parsedData['first_name'] . ' ' . $parsedData['last_name'];
        }

        foreach ($userMetas as $metaKey => $metaValue) {
            if (trim($metaValue)) {
                update_user_meta($userId, $metaKey, $metaValue);
            }
        }

        update_user_meta($userId, 'fluentform_user_id', $formId);
    }

    protected function maybeLogin($parsedData, $userId, $entry = false)
    {
        if (ArrayHelper::isTrue($parsedData, 'enableAutoLogin')) {
            // check if it's payment success page
            // or direct url
            if(isset($_REQUEST['fluentform_payment_api_notify']) && $entry) {
                // This payment IPN request so let's keep a reference for real request
                Helper::setSubmissionMeta($entry->id, '_make_auto_login', $userId, $entry->form_id);
                return;
            }


            wp_clear_auth_cookie();
            wp_set_current_user($userId);
            wp_set_auth_cookie($userId);
        }
    }

    protected function maybeSendEmail($parsedData, $userId)
    {
        if (ArrayHelper::isTrue($parsedData, 'sendEmailToNewUser')) {
            // This will send an email with password setup link
            wp_new_user_notification($userId, null, 'user');
        }
    }

    protected function addLog($title, $status, $description, $formId, $entryId, $integrationKey)
    {
        do_action('ff_log_data', [
            'title' => $title,
            'status' => $status,
            'description' => $description,
            'parent_source_id' => $formId,
            'source_id' => $entryId,
            'component' => $integrationKey,
            'source_type' => 'submission_item'
        ]);
    }
}
