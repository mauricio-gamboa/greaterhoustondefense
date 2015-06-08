<?php

class Site_Map_Shortcode extends Abstract_Finesse_Shortcode implements Shortcode_Designer
{
    private static $SM_ATTR_TYPE = "type";
    var $slide_index = 1;
    var $display_last_posts_count = 20;
    var $slides = array();

    function render($attr, $inner_content = null, $code = '')
    {
        extract(shortcode_atts(array(Site_Map_Shortcode::$SM_ATTR_TYPE => 'complex'), $attr));
        $content = '';
        $sitemap = wp_nav_menu(array(
            'theme_location' => 'primary',
            'echo' => false,
            'container' => '',
            'items_wrap' => '<ul class="arrow">%3$s</ul>',
            'walker' => new Sitemap_Menu_Walker()
        ));

        if ($type == 'simple') {
            $content .= $sitemap;
        } else {
            $content .= '[gridcolumn layout="3"]';
            $content .= '<h2>' . __('Pages', 'finesse') . '</h2>';
            $content .= $sitemap;

            $content .= '|';

            $content .= '<h2>' . __('Blog Archives', 'finesse') . '</h2>';
            $content .= '<section>';
            $content .= '<h3>' . __('Archives by Month', 'finesse') . '</h3>';
            $content .= '<ul class="arrow">';
            $content .= wp_get_archives(array(
                'type' => 'monthly',
                'show_post_count' => false,
                'echo' => 0));
            $content .= '</ul>';
            $content .= '</section>';

            $content .= $this->get_group_by_category_section();
            $content .= $this->get_group_by_tag_section();
            $content .= $this->get_group_by_author_section();

            $content .= '|';

            $content .= '<h2>' . sprintf(__('Latest %d Posts', 'finesse'), $this->display_last_posts_count) . '</h2>';
            $content .= $this->get_latest_posts();
            $content .= '[/gridcolumn]';

            $content = do_shortcode($content);
        }
        return $content;
    }

    private function get_group_by_category_section()
    {
        $content = '<section>';
        $content .= '<h3>' . __('Archives by Category', 'finesse') . '</h3>';
        $content .= '<ul class="arrow">';
        $query_result = wp_list_categories(array(
            'style' => 'none',
            'show_count' => false,
            'echo' => 0));
        $categories = explode("\n", strip_tags($query_result, '<a>'));
        foreach ($categories as $category) {
            if (strlen(trim($category)) > 0) {
                $content .= '<li>' . $category . '</li>';
            }
        }
        $content .= '</ul>';
        $content .= '</section>';
        return $content;
    }

    private function get_group_by_tag_section()
    {
        $content = '<section>';
        $content .= '<h3>' . __('Archives by Tag', 'finesse') . '</h3>';
        $content .= '<ul class="arrow">';
        $query_result = wp_list_categories(array(
            'style' => 'none',
            'show_count' => false,
            'taxonomy' => 'post_tag',
            'echo' => 0));
        $categories = explode("\n", strip_tags($query_result, '<a>'));
        foreach ($categories as $category) {
            if (strlen(trim($category)) > 0) {
                $content .= '<li>' . $category . '</li>';
            }
        }
        $content .= '</ul>';
        $content .= '</section>';
        return $content;
    }

    private function get_group_by_author_section()
    {
        $content = '<section>';
        $content .= '<h3>' . __('Archives by Author', 'finesse') . '</h3>';
        $content .= '<ul class="arrow">';
        global $wpdb;
        $query = "SELECT user_nicename, post_author, count(post_author) as posts FROM $wpdb->posts, $wpdb->users WHERE post_type = 'post' AND post_status = 'publish' AND post_author = $wpdb->users.ID GROUP BY post_author ORDER BY user_nicename ASC;";
        $results = $wpdb->get_results($query);
        foreach ((array)$results as $result) {
            $url = get_author_posts_url($result->post_author);
            $author_name = $result->user_nicename;
//            $posts = $result->posts;
            $content .= "<li><a href=\"$url\">$author_name</a></li>";
        }
        $content .= '</ul>';
        $content .= '</section>';
        return $content;
    }

    private function get_latest_posts()
    {
        $content = '<ul class="arrow">';
        $query = new WP_Query();
        $query->query('post_type=post&showposts=' . $this->display_last_posts_count);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $title = get_the_title();
                $url = get_permalink();
                $content .= '<li><a href="' . $url . '">' . $title . '</a></li>';
            }
        }
        $content .= '</ul>';
        return $content;
    }

    function get_names()
    {
        return array('sitemap', 'sm');
    }

    function get_visual_editor_form()
    {
        $content = '<form id="sc-sm-form" class="generic-form" method="post" action="#" data-sc="sm">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="sc-sm-type">' . __('Type', 'finesse') . ':</label>';
        $content .= '<select id="sc-sm-type" name="sc-sm-type" data-attr-name="'.Site_Map_Shortcode::$SM_ATTR_TYPE.'" data-attr-type="attr">';
        $content .= '<option value="complex">' . __('Complex (Pages, Blog Archives, Latest Posts)', 'finesse') . '</option>';
        $content .= '<option value="simple">' . __('Simple (Pages)', 'finesse') . '</option>';
        $content .= '</select>';
        $content .= '</div>';
        $content .= '<div >';
        $content .= '<input id="sc-sm-form-submit" type="submit" name="submit" value="' . __('Insert Site-Map', 'finesse') . '" class="button-primary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';

        return $content;
    }

    function get_group_title()
    {
        return __('Others', 'finesse');
    }

    function get_title()
    {
        return __('Site Map', 'finesse');
    }
}

class Sitemap_Menu_Walker extends Walker_Nav_Menu
{
    function start_lvl(&$output, $depth)
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul>\n";
    }

    function start_el(&$output, $item, $depth, $args)
    {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $output .= $indent . '<li>';

        $attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

}