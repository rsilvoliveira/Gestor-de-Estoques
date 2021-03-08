<?php

namespace FluentFormPro\Components\ChainedSelect;

use Exception;
use FluentForm\App\Modules\Form\Form;
use FluentForm\App\Modules\Form\FormFieldsParser;

class ChainedSelectDataSourceManager
{
    protected  $app = null;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function saveDataSource($csvParser)
    {
        try {
            $request = $this->app->request;
            $type = $request->get('type');
            $formId = $request->get('form_id');
            $metaKey = $request->get('meta_key');

            // if ($url = $request->get('url')) {
            //     $this->validateUrl($url);
            //     $name = basename($url);
            //     $data = file_get_contents($url);
            // } else {
            //     if ($request->files('file')) {
            //         $file = $request->files('file')['file'];
            //         $name = $_FILES['file']['name'];
            //         $data = $file->getContents();
            //     }
            // }

            if ($request->files('file')) {
                $file = $request->files('file')['file'];
                $name = $_FILES['file']['name'];
                $data = $file->getContents();
            }

            if (!isset($data) || !$data) {
                throw new Exception('Oops! Something went wrong.', 400);
            }

            $csvParser->load_data($data);

            $result = $csvParser->parse($csvParser->find_delimiter());

            if (is_array($result) && count($result)) {
                // $this->saveFieldoptions($result, $formId, $metaKey);

                if ($this->saveFile($file, $formId, $metaKey)) {
                    return wp_send_json_success([
                        // 'url' => $url,
                        'url' => '',
                        'name' => $type === 'file' ? $name : '',
                        'headers' => $result[0],
                        'type' => $type,
                        'meta_key' => $metaKey
                    ]);
                }
            }

            throw new Exception('Oops! Something went wrong.', 400);

        } catch (Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()], $e->getCode());
        }
    }

    // protected function validateUrl($url)
    // {
    //     try {
    //         if (($status = $this->resourceExists($url)) != '200') {
    //             throw new Exception('Oops! URL doesn\'t exists or invalid!', $status);
    //         }
    //     } catch (Exception $e) {
    //         wp_send_json_error(['message' => $e->getMessage()], $e->getCode());
    //     }
    // }

    // protected function resourceExists($url)
    // {
    //     $headers = get_headers($url);
    //     return substr($headers[0], 9, 3);
    // }

    // protected function saveFieldoptions($result, $formId, $metaKey)
    // {
    //     $fieldMeta = wpFluent()
    //         ->table('fluentform_form_meta')
    //         ->where('meta_key', $metaKey)
    //         ->where('form_id', $formId)
    //         ->first();

    //     if (!$fieldMeta) {
    //         wpFluent()->table('fluentform_form_meta')
    //             ->insert([
    //                 'form_id'  => $formId,
    //                 'meta_key' => $metaKey,
    //                 'value'    => json_encode($this->formatOptions($result))
    //             ]);
    //     } else {
    //         wpFluent()->table('fluentform_form_meta')
    //             ->where('meta_key', $metaKey)
    //             ->where('form_id', $formId)
    //             ->update([
    //                 'form_id'  => $formId,
    //                 'meta_key' => $metaKey,
    //                 'value'    => json_encode($this->formatOptions($result))
    //             ]);
    //     }
    // }

    protected function formatOptions($options)
    {
        $headers = array_shift($options);

        $headers = array_map('trim', $headers);

        foreach ($options as $key => $option) {
            $options[$key] = @array_combine($headers, $option);
        }

        return array_filter($options);
    }

    protected function saveFile($file, $formId, $metaKey)
    {
        $fileArray = $file->toArray();

        $path = wp_upload_dir()['basedir'].FLUENTFORM_UPLOAD_DIR;

        if (!is_dir($path)) {
            mkdir($path, 0755);
            file_put_contents(
                wp_upload_dir()['basedir'].FLUENTFORM_UPLOAD_DIR.'/.htaccess',
                file_get_contents(FLUENTFORMPRO_DIR_PATH.'src/Stubs/htaccess.stub')
            );
        }

        $target = $path . '/' . $metaKey . '_' . $formId . '.csv';

        if (move_uploaded_file($fileArray['tmp_name'], $target)) {
            return $target;
        }
    }

    public function deleteDataSource()
    {
        $request = $this->app->request;

        $formId = $request->get('form_id');
        
        $name = $request->get('name');
        
        $metaKey = $request->get('meta_key');

        $path = wp_upload_dir()['basedir'].FLUENTFORM_UPLOAD_DIR;

        $target = $path . '/' . $metaKey . '_' . $formId . '.csv';
        
        $this->maybeDeleteDataSource($formId, $name, $metaKey, $target);

        wp_send_json_success([
            'message' => 'Data source deleted successfully.',
            'headers' => ['Parent', 'Child', 'Grand Child']
        ]);

        // wpFluent()->table('fluentform_form_meta')
        //     ->where('meta_key', $metaKey)
        //     ->where('form_id', $formId)
        //     ->delete();

        // wp_send_json_success([
        //     'message' => 'Data source deleted successfully.',
        //     'headers' => ['Parent', 'Child', 'Grand Child']
        // ]);
    }

    protected function maybeDeleteDataSource($formId, $name, $metaKey, $target)
    {
        $form = (new Form($this->app))->fetchForm($formId);
        
        $fields = FormFieldsParser::getFields($form);

        $chainedSelects = array_filter($fields, function($field) {
            return $field->element == 'chained_select';
        });

        $currentField = array_filter($chainedSelects, function($field) use ($name) {
            return $field->attributes->name == $name;
        });

        $currentField = reset($currentField);

        if (count($chainedSelects) == 1) {
            file_exists($target) && @unlink($target);
        } else {
            $metaKeys = array_map(function($field) {
                return $field->settings->data_source->meta_key;
            }, $chainedSelects);

            if (count(array_intersect($metaKeys, [$metaKey])) < 2) {
                file_exists($target) && @unlink($target);
            } 
        }

        if ($currentField) {
            return $this->updateCurrentField($currentField, $form, $formId);
        }
    }

    protected function updateCurrentField($currentField, $form, $formId)
    {
        $formFields = json_decode($form->form_fields, true);

        $currentField->settings->data_source = [
            'url' => '',
            'name' => '',
            'headers' => ['Parent', 'Child', 'Grand Child'],
            'type' => 'file',
            'meta_key' => null
        ];

        foreach ($formFields['fields'] as $key => $field) {
            if ($field['attributes']['name'] == $currentField->attributes->name) {
                $formFields['fields'][$key] = $currentField;
            }
        }

        wpFluent()->table('fluentform_forms')->where('id', $formId)->update([
            'form_fields' => json_encode($formFields)
        ]);
    }

    public function getOptionsForNextField($csvParser)
    {
        $result = [];

        $options = $this->formatOptions($this->getOptions($csvParser));

        foreach ($options as $option) {
            $statuses = [];

            foreach ($_REQUEST['params'] as $param) {
                $statuses[] = $option[$param['key']] == $param['value'];
            }

            if (count(array_filter($statuses)) == count($statuses)) {
                $result[] = $option;
            }
        }

        $response = array_unique(
            array_column($result, $_REQUEST['target_field'])
        );

        wp_send_json_success(array_combine($response, $response));
    }

    protected function getOptions($csvParser)
    {
        // $name = $_REQUEST['name'];
        
        $metaKey = $_REQUEST['meta_key'];

        $formId = intval($_REQUEST['form_id']);

        $path = wp_upload_dir()['basedir'].FLUENTFORM_UPLOAD_DIR . '/';

        // $file = $path . '/chained_select_' . $name . '_' . $formId . '.csv';

        $file = $path . $metaKey . '_' . $formId . '.csv';

        $csvParser->load_data($data = file_get_contents($file));

        return $csvParser->parse($csvParser->find_delimiter());
    }
}
