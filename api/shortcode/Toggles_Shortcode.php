<?php

class Toggles_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    private static $TOGGLES_ATTR_TYPE = "type";
    private static $TOGGLE_ATTR_TITLE = "title";
    private static $TOGGLE_ATTR_STATE = "state";
    var $toggles = array();

    private function init()
    {
        unset($this->toggles);
        $this->toggles = array();
    }

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        switch ($code) {
            case "toggles":
                $this->init();
                do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_toggles($attr);
                break;
            case "toggle":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $this->render_toggle($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_toggles($attr)
    {
        extract(shortcode_atts(array(Toggles_Shortcode::$TOGGLES_ATTR_TYPE => 'default'), $attr));
        if ($type == 'accordion') {
            $content = '<div class="accordion">';
            foreach ($this->toggles as $v) {
                $content .= '<div>';
                $content .= "<span class=\"accordion-title\">" . $v['title'] . "</span>";
                $content .= '<div class="accordion-inner">';
                $content .= $v['value'];
                $content .= '</div></div>';
            }
            $content .= '</div>';
        } else {
            $content = '';
            foreach ($this->toggles as $v) {
                $content .= '<div data-id="' . $v['state'] . '" class="toggle">';
                $content .= "<span class=\"toggle-title\">" . $v['title'] . "</span>";
                $content .= '<div class="toggle-inner">';
                $content .= $v['value'];
                $content .= '</div></div>';
            }
        }
        return $content;
    }

    private function render_toggle($attr, $inner_content = null)
    {
        extract(shortcode_atts(array(
            Toggles_Shortcode::$TOGGLE_ATTR_TITLE => '',
            Toggles_Shortcode::$TOGGLE_ATTR_STATE => 'closed'), $attr));
        $title = ___($title);
        array_push($this->toggles, array('title' => $title, 'value' => $inner_content, 'state' => $state));
    }

    function get_names()
    {
        return array('toggles', 'toggle');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-toggle-form" class="generic-form" method="post" action="#" data-sc="toggles">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-toggles-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-toggles-content" name="sc-toggles-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-toggles-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-toggles-type" name="sc-toggles-type" data-attr-name="' . Toggles_Shortcode::$TOGGLES_ATTR_TYPE . '" data-attr-type="attr">';
        $content .= '<option value="default" selected>' . __('Default', 'finesse') . '</option>';
        $content .= '<option value="accordion">' . __('Accordion', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-toggle-form-submit" type="submit" name="submit" value="' . __('Insert Toggles', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-toggle-form-add" type="submit" name="submit" value="' . __('Add Toggle', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-add-toggle-tab-dialog" title="' . __('New Toggle', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-add-toggle-tab-form" class="generic-form" method="post" action="#" data-sc="toggle">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-toggle-title">' . __('Title', 'finesse') . ':</label>';
        $content .= '<input id="sc-toggle-title" name="sc-toggle-title" type="text" class="required" data-attr-name="' . Toggles_Shortcode::$TOGGLE_ATTR_TITLE . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-toggle-state">' . __('State', 'finesse') . ':</label>';
        $content .= '<select id="sc-toggle-state" name="sc-toggle-state" data-attr-name="' . Toggles_Shortcode::$TOGGLE_ATTR_STATE . '" data-attr-type="attr">';
        $content .= '<option value="closed" selected>' . __('Collapsed (closed)', 'finesse') . '</option>';
        $content .= '<option value="open">' . __('Expanded (open)', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-toggle-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-toggle-content" name="sc-toggle-content" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-add-toggle-tab-form-submit" type="submit" value="' . __('Add Toggle', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-add-toggle-tab-form-cancel" type="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';
        return $content;
    }

    function get_group_title()
    {
        return __('Dynamic Elements', 'finesse');
    }

    function get_title()
    {
        return __('Toggles', 'finesse');
    }
}