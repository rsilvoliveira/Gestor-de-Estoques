<?php

namespace FluentFormPro\Components;

use FluentForm\App\Services\FormBuilder\Components\BaseComponent;
use FluentForm\Framework\Helpers\ArrayHelper;

class FormStep extends BaseComponent
{
    /**
     * Compile and echo step header
     * @param array $data [element data]
     * @param object $form [Form Object]
     * @return viod
     */
    public function stepStart($data, $form)
    {
        if (!$data) return;


        if ($data['settings']['progress_indicator'] == 'steps') {
            $nav = "<ul class='ff-step-titles'><li class='ff_active'><span>" . implode('</span></li><li><span>', $data['settings']['step_titles']) . "</span></li></ul>";
        } elseif ($data['settings']['progress_indicator'] == 'progress-bar') {
            $nav = "<div class='ff-el-progress-status'></div>
            <div class='ff-el-progress'>
                <div class='ff-el-progress-bar'><span></span></div>
            </div>
            <ul style='display: none' class='ff-el-progress-title'>
                <li>" . implode('</li><li>', $data['settings']['step_titles']) . "</li>
            </ul>";
        } else {
            $nav = '';
        }

        $data['attributes']['data-disable_auto_focus'] = ArrayHelper::get($data, 'settings.disable_auto_focus', 'no');
        $atts = $this->buildAttributes(
            \FluentForm\Framework\Helpers\ArrayHelper::except($data['attributes'], 'name')
        );

        echo "<div class='ff-step-container' {$atts}>";
        if ($nav) {
            echo "<div class='ff-step-header'>{$nav}</div>";
        }

        echo "<div class='ff-step-body'>";
        $data['attributes']['class'] .= ' fluentform-step';
        $data['attributes']['class'] = trim($data['attributes']['class']) . ' active';
        $atts = $this->buildAttributes(
            \FluentForm\Framework\Helpers\ArrayHelper::except($data['attributes'], 'name')
        );
        echo "<div {$atts}>";
    }

    /**
     * Compile and echo the html element
     * @param array $data [element data]
     * @param stdClass $form [Form Object]
     * @return viod
     */
    public function compile($data, $form)
    {
        echo $this->compileButtons($data['settings']);
        $data['attributes']['class'] .= ' fluentform-step';
        $atts = $this->buildAttributes(
            \FluentForm\Framework\Helpers\ArrayHelper::except($data['attributes'], 'name')
        );
        echo "</div><div {$atts}>";
    }

    /**
     * Compile and echo step footer
     * @param array $data [element data]
     * @param stdClass $form [Form Object]
     * @return viod
     */
    public function stepEnd($data, $form)
    {
        $btnPrev = $this->compileButtons($data['settings']);
        ?>
        <div class="ff-t-container ff-inner_submit_container ff-column-container ff_columns_total_2">
            <div class="ff-t-cell ff-t-column-1"><?php  echo "{$btnPrev}"; ?></div>
            <div class="ff-t-cell ff-t-column-2"><?php $this->app->doAction('fluentform_render_item_submit_button', $form->fields['submitButton'], $form);  ?></div>
        </div>
        </div></div></div>
        <?php
        add_filter('fluentform_is_hide_submit_btn_' . $form->id, '__return_true');
    }

    /**
     * Compile next and prev buttons
     * @param array $data [element data]
     * @return viod
     */
    protected function compileButtons($data)
    {
        $btnPrev = $btnNext = '';
        $prev = isset($data['prev_btn']) ? $data['prev_btn'] : null;
        $next = isset($data['next_btn']) ? $data['next_btn'] : null;

        if ($prev) {
            if ($prev['type'] == 'default') {

                $tabIndex = \FluentForm\App\Helpers\Helper::getNextTabIndex();
                $tabIndexHtml = '';
                if($tabIndex) {
                    $tabIndexHtml = "tabindex='".$tabIndex."' ";
                }
                $btnPrev = "<button ".$tabIndexHtml." type='button' data-action='prev' class='ff-btn ff-btn-prev ff-btn-secondary'>" . $prev['text'] . "</button>";
            } else {
                $btnPrev = "<img class='prev' src={$prev['url']}>";
            }
        }

        if ($next) {
            if ($next['type'] == 'default') {

                $tabIndex = \FluentForm\App\Helpers\Helper::getNextTabIndex();
                $tabIndexHtml = '';
                if($tabIndex) {
                    $tabIndexHtml = "tabindex='".$tabIndex."' ";
                }
                $btnNext = "<button ".$tabIndexHtml." type='button' data-action='next' class='ff-float-right ff-btn ff-btn-next ff-btn-secondary'>" . $next['text'] . "</button>";
            } else {
                $btnNext = "<img class='next' src={$next['url']}>";
            }
        }

        return "<div class='step-nav ff_step_nav_last'>{$btnPrev}{$btnNext}</div>";
    }
}
