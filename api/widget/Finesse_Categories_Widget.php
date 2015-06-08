<?php

class Finesse_Categories_Widget extends WP_Widget_Categories
{
    function __construct() {
        parent::__construct();
    }

    function widget($args, $instance)
    {
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Categories', 'finesse') : $instance['title'], $instance, $this->id_base);
        $c = !empty($instance['count']) ? '1' : '0';
        $h = !empty($instance['hierarchical']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';

        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }

        $cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h);
        if ($d) {
            $home_url = home_url();
            $cat_args['show_option_none'] = __('Select Category', 'finesse');
            wp_dropdown_categories(apply_filters('widget_categories_dropdown_args', $cat_args));
            echo "<script type=\"text/javascript\">
            var dropdown = document.getElementById(\"cat\");
            function onCatChange() {
                if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
                    location.href = \"$home_url/?cat=\"+dropdown.options[dropdown.selectedIndex].value;
                }
            }
            dropdown.onchange = onCatChange;
        </script>";

        } else {
            echo '<ul class="menu">' . "\n";
            $cat_args['title_li'] = '';
            wp_list_categories(apply_filters('widget_categories_args', $cat_args));
            echo '</ul>' . "\n";
        }
        echo $after_widget;
    }

}
