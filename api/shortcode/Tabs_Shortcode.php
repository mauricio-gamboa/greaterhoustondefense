<?php

class Tabs_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    private static $TAB_ATTR_TITLE = "title";
    var $tabs = array();

    function render($attr, $inner_content = null, $code = '')
    {
        $content = '';
        switch ($code) {
            case "tabs":
                unset($this->tabs);
                $this->tabs = array();
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_tabs($attr, $inner_content);
                break;
            case "tab":
                $inner_content = do_shortcode($this->prepare_content($inner_content));
                $content .= $this->render_tab($attr, $inner_content);
                break;
        }
        return $content;
    }

    private function render_tabs($attr, $inner_content = null)
    {
        $content = '<div class="tabs">';
        $content .= '<ul class="nav clearfix">';
        foreach ($this->tabs as $k => $v) {
            $content .= $k;
        }
        $content .= '</ul>';
        foreach ($this->tabs as $k => $v) {
            $content .= $v;
        }
        $content .= '</div>';
        return $content;
    }

    private function render_tab($attr, $inner_content = null)
    {
        extract(shortcode_atts(array(Tabs_Shortcode::$TAB_ATTR_TITLE => ''), $attr));
        $title = ___($title);
        $i = count($this->tabs) + 1;
        $key = "<li><a href=\"#tab-$i\">$title</a></li>";
        $value = "<div id=\"tab-$i\" class=\"tab\">$inner_content</div>";
        $this->tabs[$key] = $value;
        return '';
    }

    function get_names()
    {
        return array('tabs', 'tab');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-tabs-form" class="generic-form" method="post" action="#" data-sc="tabs">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-tabs-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-tabs-content" name="sc-tabs-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-tabs-form-submit" type="submit" name="submit" value="' . __('Insert Tabs', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-tabs-form-add" type="submit" name="submit" value="' . __('Add Tab', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-add-tab-dialog" title="' . __('New Tab', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-add-tab-form" class="generic-form" method="post" action="#" data-sc="tab">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-tab-title">' . __('Tab Title', 'finesse') . ':</label>';
        $content .= '<input id="sc-tab-title" name="sc-tab-title" type="text" class="required" data-attr-name="'.Tabs_Shortcode::$TAB_ATTR_TITLE.'" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-tab-content">' . __('Tab Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-tab-content" name="sc-tab-content" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-add-tab-form-submit" type="submit" value="' . __('Add Tab', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-add-tab-form-cancel" type="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
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
        return __('Tabs', 'finesse');
    }
}

?>