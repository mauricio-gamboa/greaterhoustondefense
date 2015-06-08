<?php

class Grid_Columns_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    private static $GC_ATTR_SEPARATOR = "separator";
    private static $GC_ATTR_LAYOUT = "layout";
    static $layouts = array("2", "3", "4", "1/3", "2/3", "1/4", "3/4");

    function render($attr, $inner_content = null, $code = '')
    {
        $inner_content = do_shortcode($this->prepare_content($inner_content));
        extract(shortcode_atts(array(
            Grid_Columns_Shortcode::$GC_ATTR_SEPARATOR => '|',
            Grid_Columns_Shortcode::$GC_ATTR_LAYOUT => '2'), $attr));
        $columns = $this->get_columns_no($layout);
        $lines = explode($separator, $inner_content, $columns);
        $content = '';
        for ($i = 0; $i < count($lines); $i++) {
            $content .= '<div class="' . $this->get_class($layout, $i) . '">';
            $content .= $lines[$i];
            $content .= '</div>';
        }
        return $content;
    }

    private function get_class($layout, $index)
    {
        switch ($layout) {
            case "2":
                $class = ($index == 1) ? 'one-half column-last' : 'one-half';
                break;
            case "3":
                $class = ($index == 2) ? 'one-third column-last' : 'one-third';
                break;
            case "4":
                $class = ($index == 3) ? 'one-fourth column-last' : 'one-fourth';
                break;
            case "1/3":
                $class = ($index == 1) ? 'two-thirds column-last' : 'one-third';
                break;
            case "2/3":
                $class = ($index == 1) ? 'one-third column-last' : 'two-thirds';
                break;
            case "1/4":
                $class = ($index == 1) ? 'three-fourths column-last' : 'one-fourth';
                break;
            case "3/4":
                $class = ($index == 1) ? 'one-fourth column-last' : 'three-fourths';
                break;
            default:
                $class = '';
        }
        return $class;
    }

    private function get_columns_no($layout)
    {
        switch ($layout) {
            case "3":
                return 3;
            case "4":
                return 4;
            default:
                return 2;
        }
    }

    function get_names()
    {
        return array('gridcolumn', 'gc');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-gc-form" class="generic-form" method="post" action="#" data-sc="gc">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-gc-layout">' . __('Layout', 'finesse') . ':</label>';
        $content .= '<select id="sc-gc-layout" name="sc-gc-layout" data-attr-name="' . Grid_Columns_Shortcode::$GC_ATTR_LAYOUT . '" data-attr-type="attr">';
        $content .= '<option value="2">' . __('One Half', 'finesse') . '</option>';
        $content .= '<option value="3">' . __('One Third', 'finesse') . '</option>';
        $content .= '<option value="4">' . __('One Fourth', 'finesse') . '</option>';
        $content .= '<option value="1/3">' . __('One Third - Two Thirds', 'finesse') . '</option>';
        $content .= '<option value="2/3">' . __('Two Thirds - One Third', 'finesse') . '</option>';
        $content .= '<option value="1/4">' . __('One Fourth - Three Fourths', 'finesse') . '</option>';
        $content .= '<option value="3/4">' . __('Three Fourths - One Fourth', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-gc-separator">' . __('Column Separator', 'finesse') . ':</label>';
        $content .= '<input id="sc-gc-separator" name="sc-gc-separator" type="text" class="required" value="|" data-attr-name="' . Grid_Columns_Shortcode::$GC_ATTR_SEPARATOR . '" data-attr-type="attr">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="sc-gc-content">' . __('Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-gc-content" name="sc-gc-content" class="required" data-attr-type="content"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-gc-form-submit" type="submit" name="submit" value="' . __('Insert Grid Columns', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-gc-form-add" type="submit" name="submit" value="' . __('Add Column', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        $content .= '<div id="sc-add-gc-dialog" title="' . __('New Grid Column', 'finesse') . '" style="display: none">';
        $content .= '<form id="sc-add-gc-form" class="generic-form" method="post" action="#">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-gc-col">' . __('Column Content', 'finesse') . ':</label>';
        $content .= '<textarea id="sc-gc-col" name="sc-gc-col" class="required"></textarea>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-add-gc-form-submit" type="submit" value="' . __('Add Column', 'finesse') . '" class="button-primary">';
        $content .= '<input id="sc-add-gc-form-cancel" type="submit" value="' . __('Cancel', 'finesse') . '" class="button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        $content .= '</div>';
        return $content;
    }

    function get_group_title()
    {
        return __('Grid Columns', 'finesse');
    }

    function get_title()
    {
        return __('Grid Columns', 'finesse');
    }
}
