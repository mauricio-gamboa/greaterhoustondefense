<?php

class Finesse_Recent_Posts_Widget extends WP_Widget_Recent_Posts
{
    function __construct() {
        parent::__construct();
    }

    function widget($args, $instance)
    {
        $cache = wp_cache_get('widget_recent_posts', 'widget');

        if (!is_array($cache)) {
            $cache = array();
        }

        if (!isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        if (isset($cache[$args['widget_id']])) {
            echo $cache[$args['widget_id']];
            return;
        }

        ob_start();
        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'finesse') : $instance['title'], $instance, $this->id_base);
        if (empty($instance['number']) || !$number = absint($instance['number'])) {
            $number = 10;
        }

        $r = new WP_Query(apply_filters('widget_posts_args', array('posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true)));
        if ($r->have_posts()) {
            echo str_replace("widget", "widget latest-posts", $before_widget);
            if ($title) {
                echo $before_title . $title . $after_title;
            }
            echo '<ul>' . "\n";
            while ($r->have_posts()) {
                $r->the_post();
                $post_title = esc_attr(get_the_title() ? get_the_title() : get_the_ID());
                $post_date = get_the_date();
                echo '<li>' . "\n";
                echo '<a href="' . get_permalink() . '" title="' . $post_title . '">' . $post_title . '</a>' . "\n";
                echo '<span>'.$post_date.'</span>' . "\n";
                echo '</li>' . "\n";
            }
            echo '</ul>' . "\n";
            echo $after_widget;
            wp_reset_postdata();
        }

        $cache[$args['widget_id']] = ob_get_flush();
        wp_cache_set('widget_recent_posts', $cache, 'widget');
    }
}
