<?php namespace FluentFormPro\classes;

class Calculation
{
    public function enqueueScripts()
    {
        wp_enqueue_script('math-expression-evaluator', FLUENTFORMPRO_DIR_URL.'public/libs/math-expression-evaluator.min.js', array(), '1.2.17');
    }
}