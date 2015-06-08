<?php

class Finesse_Archives_Widget extends WP_Widget_Archives
{
    function __construct() {
        parent::__construct();
    }

    function widget($args, $instance)
    {
        extract($args);
        $c = !empty($instance['count']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Archives', 'finesse') : $instance['title'], $instance, $this->id_base);

        echo $before_widget;
        if ($title) {
            echo $before_title . $title . $after_title;
        }

        if ($d) {
            $options = wp_get_archives(apply_filters('widget_archives_dropdown_args', array('echo' => 'false', 'type' => 'monthly', 'format' => 'option', 'show_post_count' => $c)));
            echo '<select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;"> <option value="">' . __('Select Month', 'finesse') . '</option> ' . $options . ' </select>';
        } else {
            echo '<ul class="menu">' . "\n";
            wp_get_archives(apply_filters('widget_archives_args', array('type' => 'monthly', 'show_post_count' => $c)));
            echo '</ul>' . "\n";
        }
        echo $after_widget;
    }

}
