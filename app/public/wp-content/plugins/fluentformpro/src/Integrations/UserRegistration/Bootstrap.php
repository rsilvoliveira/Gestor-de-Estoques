<?php

namespace FluentFormPro\Integrations\UserRegistration;

use FluentForm\App\Services\Integrations\IntegrationManager;
use FluentForm\Framework\Foundation\Application;

class Bootstrap extends IntegrationManager
{
    public $category = 'wp_core';
    public $disableGlobalSettings = 'yes';

    public function __construct(Application $app)
    {
        parent::__construct(
            $app,
            'User Registration',
            'UserRegistration',
            '_fluentform_user_registration_settings',
            'user_registration_feeds',
            1
        );

        $this->userApi = new UserRegistrationApi;

        $this->logo = $this->app->url('public/img/integrations/user_registration.png');

        $this->description = 'Create WordPress user when when a form is submitted.';

        add_filter('fluentform_notifying_async_UserRegistration', '__return_false');

        add_filter('fluentform_save_integration_value_' . $this->integrationKey, [$this, 'validate']);

        $this->registerAdminHooks();
    }

    public function pushIntegration($integrations, $formId)
    {
        $integrations[$this->integrationKey] = [
            'category'                => 'wp_core',
            'disable_global_settings' => 'yes',
            'logo'                    => $this->logo,
            'title'                   => $this->title . ' Integration',
            'is_active'               => $this->isConfigured()
        ];

        return $integrations;
    }

    public function getIntegrationDefaults($settings, $formId = null)
    {
        return [
            'name'               => '',
            'Email'              => '',
            'CustomFields'       => (object)[],
            'userRole'           => 'subscriber',
            'userMeta'           => [['label' => '', 'item_value' => '']],
            'enableAutoLogin'    => false,
            'sendEmailToNewUser' => false,
            'conditionals'       => [
                'conditions' => [],
                'status'     => false,
                'type'       => 'all'
            ],
            'enabled'            => true
        ];
    }

    public function getSettingsFields($settings, $formId = null)
    {
        return [
            'fields'              => [
                [
                    'key'         => 'name',
                    'label'       => 'Name',
                    'required'    => true,
                    'placeholder' => 'Your Feed Name',
                    'component'   => 'text'
                ],
                [
                    'key'                => 'CustomFields',
                    'require_list'       => false,
                    'label'              => 'Map Fields',
                    'tips'               => 'Associate your user registration fields to the appropriate Fluent Form fields by selecting the appropriate form field from the list.',
                    'component'          => 'map_fields',
                    'field_label_remote' => 'User Registration Field',
                    'field_label_local'  => 'Form Field',
                    'primary_fileds'     => [
                        [
                            'key'           => 'Email',
                            'label'         => 'Email Address',
                            'required'      => true,
                            'input_options' => 'emails'
                        ],
                        [
                            'key'   => 'first_name',
                            'label' => 'First Name'
                        ],
                        [
                            'key'   => 'last_name',
                            'label' => 'Last Name'
                        ],
                        [
                            'key'       => 'password',
                            'label'     => 'Password',
                            'help_text' => 'Keep empty to be auto generated',
                        ],
                    ]
                ],
                [
                    'require_list' => false,
                    'required'     => true,
                    'key'          => 'userRole',
                    'label'        => 'Default User Role',
                    'tips'         => 'Set default user role when registering a new user.',
                    'component'    => 'radio_choice',
                    'options'      => $this->userApi->getUserRoles()
                ],
                [
                    'require_list' => false,
                    'key'          => 'userMeta',
                    'label'        => 'User Meta',
                    'tips'         => 'Add user meta.',
                    'component'    => 'dropdown_label_repeater',
                ],
                [
                    'require_list'    => false,
                    'key'             => 'enableAutoLogin',
                    'label'           => 'Auto Login',
                    'checkobox_label' => 'Allow the user login automatically after registration',
                    'component'       => 'checkbox-single',
                ],
                [
                    'require_list'    => false,
                    'key'             => 'sendEmailToNewUser',
                    'label'           => 'Email Notification',
                    'checkobox_label' => 'Send default WordPress welcome email to user after registration',
                    'component'       => 'checkbox-single',
                ],
                [
                    'require_list' => false,
                    'key'          => 'conditionals',
                    'label'        => 'Conditional Logics',
                    'tips'         => 'Allow User Registration integration conditionally based on your submission values',
                    'component'    => 'conditional_block'
                ],
                [
                    'require_list'    => false,
                    'key'             => 'enabled',
                    'label'           => 'Status',
                    'component'       => 'checkbox-single',
                    'checkobox_label' => 'Enable This feed',
                    'inline_tip' => 'Please not that, This feel will only run if the visitor is logged out state and the email is not registered yet'
                ]
            ],
            'button_require_list' => false,
            'integration_title'   => $this->title
        ];
    }

    public function validate($settings)
    {
        return $this->userApi->validate(
            $settings, $this->getSettingsFields($settings)
        );
    }

    /*
     * Form Submission Hooks Here
     */
    public function notify($feed, $formData, $entry, $form)
    {
        $this->userApi->registerUser(
            $feed, $formData, $entry, $form, $this->integrationKey
        );
    }

    // There is no global settings, so we need
    // to return true to make this module work.
    public function isConfigured()
    {
        return true;
    }

    // This is an absttract method, so it's required.
    public function getMergeFields($list, $listId, $formId)
    {
        // ...
    }

    // This method should return global settings. It's not required for
    // this class. So we should return the default settings otherwise
    // there will be an empty global settings page for this module.
    public function addGlobalMenu($setting)
    {
        return $setting;
    }
}
