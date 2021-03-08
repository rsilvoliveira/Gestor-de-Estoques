<?php

namespace FluentFormPro\Components\Post\Components;

use FluentForm\App\Services\FormBuilder\Components\Text;

class NonHierarchicalTaxonomy extends Text
{
    public function compile($data, $form)
    {
        $data['attributes']['type'] = 'text';
        
        return parent::compile($data, $form);
    }
}