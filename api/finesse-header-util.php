<?php

function finesse_html2rgb($color)
{
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }
    if (strlen($color) == 6) {
        list($r, $g, $b) = array($color[0] . $color[1],
            $color[2] . $color[3],
            $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        list($r, $g, $b) = array($color[0] . $color[0],
            $color[1] . $color[1],
            $color[2] . $color[2]);
    } else {
        return false;
    }

    $r = hexdec($r);
    $g = hexdec($g);
    $b = hexdec($b);

    return array($r, $g, $b);
}

function get_admin_custom_css()
{
    global $font_manager;
    $output = '';
    $fonts = $font_manager->get_all_fonts();
    foreach ($fonts as $font) {
        $font_url = $font->font_url;
        $output .= "@import url(" . $font_url . ");\n";
    }
    foreach ($fonts as $font) {
        $font_name = $font->font_name;
        $output .= "." . str_replace(' ', '-', $font->font_name) . "-normal-400{
            font-family: '$font_name' !important;
            font-style: normal;
            font-weight: 400;
            font-size: 24px;
        }\n";
    }
    if (strlen($output) > 0) {
        return "\n<style type=\"text/css\">\n" . $output . "</style>\n";
    } else {
        return '';
    }
}

function get_custom_css()
{
    $output = get_layout_specific_css_styles();
    $output .= get_bg_color_css_styles();
    $output .= get_text_color_css_styles();
    $output .= get_sprites_css_styles();
    $output .= get_typography_css_styles();
    $output .= get_other_css_styles();

    if (strlen($output) > 0) {
        global $font_manager;
        $font_import = '';
        $fonts = $font_manager->get_all_fonts();
        foreach ($fonts as $font) {
            $font_url = $font->font_url;
            $font_import .= "@import url(" . $font_url . ");\n";
        }
        return "\n<style type=\"text/css\">\n" . $font_import . $output . "</style>\n";
    } else {
        return '';
    }
}

function get_layout_specific_css_styles()
{
    global $theme_options;
    $output = '';
    if (get_current_layout() == 'boxed') {
        $template_uri = get_template_directory_uri();
        $finesse_boxed_bg_color = $theme_options->get_option_value('finesse_boxed_bg_color');
        $finesse_boxed_bg_pattern = $theme_options->get_option_value('finesse_boxed_bg_pattern');
        if ($finesse_boxed_bg_pattern != 'none') {
            $finesse_boxed_bg_pattern = str_replace('..', $template_uri, $finesse_boxed_bg_pattern);
            $finesse_boxed_bg_pattern = 'url(' . $finesse_boxed_bg_pattern . ')';
        }
        $output .= "#wrap {
	width: 1020px;
	max-width: 100%;
	margin: 0 auto;
	background-color: #fff;
	box-shadow: 0 115px 8px rgba(0, 0, 0, 0.24);
}\n";

    } else {
        if ($theme_options->is_option_changed('finesse_wide_bg_pattern')) {
            $template_uri = get_template_directory_uri();
            $finesse_wide_bg_pattern = $theme_options->get_option_value('finesse_wide_bg_pattern');
            if ($finesse_wide_bg_pattern != 'none') {
                $finesse_wide_bg_pattern = str_replace('..', $template_uri, $finesse_wide_bg_pattern);
                $finesse_wide_bg_pattern = 'url(' . $finesse_wide_bg_pattern . ')';
            }
            $output .= "body {
	background-color: #fff;
	background-image: $finesse_wide_bg_pattern;
}\n";
        }
    }
    return $output;
}

function get_bg_color_css_styles()
{
    global $theme_options;
    $output = '';

    if ($theme_options->is_option_changed('finesse_slider_nav_arrows_bg_color')) {
        $finesse_slider_nav_arrows_bg_color = finesse_html2rgb($theme_options->get_option_value('finesse_slider_nav_arrows_bg_color'));
        $output .= ".flex-direction-nav a:link, .flex-direction-nav a:visited,
.entry-slider-nav a { /* position here is important */
	background-color: rgba($finesse_slider_nav_arrows_bg_color[0],$finesse_slider_nav_arrows_bg_color[1],$finesse_slider_nav_arrows_bg_color[2],0.8);
}\n";
    }
    if ($theme_options->is_option_changed('finesse_button_bg_color')) {
        $finesse_button_bg_color = $theme_options->get_option_value('finesse_button_bg_color');
        $output .= ".button, .content-form input.button, #comment-form input.button,
.flex-direction-nav a:hover, .flex-direction-nav a:active,
.ie8 .flex-direction-nav a:link, .ie8 .flex-direction-nav a:visited,
.ie8 .flex-direction-nav a:hover, .ie8 .flex-direction-nav a:active,
.jcarousel-prev:hover, .jcarousel-prev:focus,
.jcarousel-next:hover, .jcarousel-next:focus,
.entry-slider-nav a:hover, .entry-slider-nav a:active,
a.post-format-wrap:hover .post-format, #toTop:hover,
.page-nav a:hover,
ul.tags a:hover,
.page-nav li.current,
.button.black:hover,
#filter a:hover, #filter li.active a,
.pricing-box.featured .header,
.highlight.colored,
.flex-control-paging a.flex-active,
ul#navlist li.current a,
.ddsubmenustyle li a:hover,
.ie8 .entry-slider-nav a {
	background-color: $finesse_button_bg_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_pt_highlight_bg_color')) {
        $finesse_pt_highlight_bg_color = $theme_options->get_option_value('finesse_pt_highlight_bg_color');
        $output .= ".pricing-box.featured .title {
    background-color: $finesse_pt_highlight_bg_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_table_row_hover_bg_color')) {
        $finesse_table_row_hover_bg_color = $theme_options->get_option_value('finesse_table_row_hover_bg_color');
        $output .= ".gen-table tbody tr:hover th, .gen-table tbody tr:hover td {
	background-color: $finesse_table_row_hover_bg_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_text_selection_bg_color')) {
        $finesse_text_selection_bg_color = $theme_options->get_option_value('finesse_text_selection_bg_color');
        $output .= "::-moz-selection {background: $finesse_text_selection_bg_color;}
::selection {background: $finesse_text_selection_bg_color;}
ins, mark {background-color: $finesse_text_selection_bg_color;}\n";
    }
    return $output;
}

function get_text_color_css_styles()
{
    global $theme_options;
    $output = '';
    if ($theme_options->is_option_changed('finesse_body_text_color')) {
        $finesse_body_text_color = $theme_options->get_option_value('finesse_body_text_color');
        $output .= "body, .widget ul.menu li > a, #polyglotLanguageSwitcher a,
a:hover, a > *, #logo a, ul#navlist li a, .entry-title a, #filter a, .entry-meta .title, ol.comment-list .comment-author a, ul.tags a, ul#search-results h2 a, ul#search-results h2 a strong, .page-nav a, .tabs ul.nav li a,
.caption-quote-form input[type=\"text\"],
.caption-quote-form input[type=\"tel\"],
.caption-quote-form input[type=\"email\"],
.caption-quote-form select,
.caption-quote-form .text-field,
.caption-quote-form .select-element {
	color: $finesse_body_text_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_link_color')) {
        $finesse_link_color = $theme_options->get_option_value('finesse_link_color');
        $output .= "a, a > *,
#polyglotLanguageSwitcher a:hover, #footer-top a,
#footer-bottom a:hover,
#footer-top .tweet_time a:hover,
ul#search-results h2 a:hover, ul#search-results h2 a:hover strong,
.widget ul.menu li > a:hover, .widget ul.menu li.current-menu-item > a,
.tabs ul.nav li a:hover, .tabs ul.nav li.ui-state-active a,
span.toggle-title:hover, span.accordion-title:hover, span.toggle-title.ui-state-active, span.accordion-title.ui-state-active,
.pricing-table .featured span.price span,
ol.comment-list .comment-author a:hover,
.flex-caption a:hover,
.entry-title a:hover,
.iconbox h4 a:hover {
	color: $finesse_link_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_navigation_text_color')) {
        $finesse_navigation_text_color = $theme_options->get_option_value('finesse_navigation_text_color');
        $output .= "ul#navlist li.current a, ul#navlist li a:hover, ul#navlist li a.selected, .ddsubmenustyle li a {
	color: $finesse_navigation_text_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_grey_text_color')) {
        $finesse_grey_text_color = $theme_options->get_option_value('finesse_grey_text_color');
        $output .= ".quote-content, #tagline, .entry-meta, pre, code, ol.comment-list .comment-meta {
	color: $finesse_grey_text_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_secondary_text_color')) {
        $finesse_secondary_text_color = $theme_options->get_option_value('finesse_secondary_text_color');
        $output .= ".tip, caption, .caption, ::-webkit-input-placeholder, :-moz-placeholder, .team-member span.job-title {
	color: $finesse_secondary_text_color;
}\n";
    }
    return $output;
}

function get_sprites_css_styles()
{
    global $theme_options;
    $output = '';
    if ($theme_options->is_option_changed('finesse_social_links_bg_position')) {
        $finesse_social_links_bg_position = $theme_options->get_option_value('finesse_social_links_bg_position');
        $output .= ".social-links a:hover {
	background-position: $finesse_social_links_bg_position;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_team_member_social_links_bg_position')) {
        $finesse_team_member_social_links_bg_position = $theme_options->get_option_value('finesse_team_member_social_links_bg_position');
        $output .= ".team-member .social-links a:hover {
	background-position: $finesse_team_member_social_links_bg_position;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_icon_box_bg_position')) {
        $finesse_icon_box_bg_position = $theme_options->get_option_value('finesse_icon_box_bg_position');
        $output .= ".iconbox-icon {
	background-position: $finesse_icon_box_bg_position;
}\n";
    }
    return $output;
}

function get_typography_css_styles()
{
    global $theme_options;
    $output = '';
    if ($theme_options->is_option_changed('finesse_body_font_family') ||
        $theme_options->is_option_changed('finesse_body_font_size') ||
        $theme_options->is_option_changed('finesse_body_line_height')
    ) {
        $finesse_body_font_family = $theme_options->get_option_value('finesse_body_font_family');
        $finesse_body_font_family1 = trim($finesse_body_font_family[0]);
        $finesse_body_font_family2 = trim($finesse_body_font_family[1]) == '' ? '' : ', ' . trim($finesse_body_font_family[1]);
        $finesse_body_font_size = $theme_options->get_option_value('finesse_body_font_size');
        $finesse_body_line_height = $theme_options->get_option_value('finesse_body_line_height');
        $output .= "body, .quote-content a {
	font-family: $finesse_body_font_family1$finesse_body_font_family2;
}\n";
        $output .= "body {
	font-size: $finesse_body_font_size;
	line-height: $finesse_body_line_height;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_h1_font_family') ||
        $theme_options->is_option_changed('finesse_h1_font_weight') ||
        $theme_options->is_option_changed('finesse_h1_font_size') ||
        $theme_options->is_option_changed('finesse_h1_line_height')
    ) {
        $finesse_h1_font_family = $theme_options->get_option_value('finesse_h1_font_family');
        $finesse_h1_font_family1 = trim($finesse_h1_font_family[0]);
        $finesse_h1_font_family2 = trim($finesse_h1_font_family[1]) == '' ? '' : ', ' . trim($finesse_h1_font_family[1]);
        $finesse_h1_font_weight = $theme_options->get_option_value('finesse_h1_font_weight');
        $finesse_h1_font_size = $theme_options->get_option_value('finesse_h1_font_size');
        $finesse_h1_line_height = $theme_options->get_option_value('finesse_h1_line_height');
        $output .= "h1 {
	font-family: $finesse_h1_font_family1$finesse_h1_font_family2;
	font-weight: $finesse_h1_font_weight;
	font-size: $finesse_h1_font_size;
	line-height: $finesse_h1_line_height;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_h2_font_family') ||
        $theme_options->is_option_changed('finesse_h2_font_weight') ||
        $theme_options->is_option_changed('finesse_h2_font_size') ||
        $theme_options->is_option_changed('finesse_h2_line_height')
    ) {
        $finesse_h2_font_family = $theme_options->get_option_value('finesse_h2_font_family');
        $finesse_h2_font_family1 = trim($finesse_h2_font_family[0]);
        $finesse_h2_font_family2 = trim($finesse_h2_font_family[1]) == '' ? '' : ', ' . trim($finesse_h2_font_family[1]);
        $finesse_h2_font_weight = $theme_options->get_option_value('finesse_h2_font_weight');
        $finesse_h2_font_size = $theme_options->get_option_value('finesse_h2_font_size');
        $finesse_h2_line_height = $theme_options->get_option_value('finesse_h2_line_height');
        $output .= "h2 {
	font-family: $finesse_h2_font_family1$finesse_h2_font_family2;
	font-weight: $finesse_h2_font_weight;
	font-size: $finesse_h2_font_size;
	line-height: $finesse_h2_line_height;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_h3_font_family') ||
        $theme_options->is_option_changed('finesse_h3_font_weight') ||
        $theme_options->is_option_changed('finesse_h3_font_size') ||
        $theme_options->is_option_changed('finesse_h3_line_height')
    ) {
        $finesse_h3_font_family = $theme_options->get_option_value('finesse_h3_font_family');
        $finesse_h3_font_family1 = trim($finesse_h3_font_family[0]);
        $finesse_h3_font_family2 = trim($finesse_h3_font_family[1]) == '' ? '' : ', ' . trim($finesse_h3_font_family[1]);
        $finesse_h3_font_weight = $theme_options->get_option_value('finesse_h3_font_weight');
        $finesse_h3_font_size = $theme_options->get_option_value('finesse_h3_font_size');
        $finesse_h3_line_height = $theme_options->get_option_value('finesse_h3_line_height');
        $output .= "h3 {
	font-family: $finesse_h3_font_family1$finesse_h3_font_family2;
	font-weight: $finesse_h3_font_weight;
	font-size: $finesse_h3_font_size;
	line-height: $finesse_h3_line_height;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_h4_font_family') ||
        $theme_options->is_option_changed('finesse_h4_font_weight') ||
        $theme_options->is_option_changed('finesse_h4_font_size') ||
        $theme_options->is_option_changed('finesse_h4_line_height')
    ) {
        $finesse_h4_font_family = $theme_options->get_option_value('finesse_h4_font_family');
        $finesse_h4_font_family1 = trim($finesse_h4_font_family[0]);
        $finesse_h4_font_family2 = trim($finesse_h4_font_family[1]) == '' ? '' : ', ' . trim($finesse_h4_font_family[1]);
        $finesse_h4_font_weight = $theme_options->get_option_value('finesse_h4_font_weight');
        $finesse_h4_font_size = $theme_options->get_option_value('finesse_h4_font_size');
        $finesse_h4_line_height = $theme_options->get_option_value('finesse_h4_line_height');
        $output .= "h4 {
	font-family: $finesse_h4_font_family1$finesse_h4_font_family2;
	font-weight: $finesse_h4_font_weight;
	font-size: $finesse_h4_font_size;
	line-height: $finesse_h4_line_height;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_h5_font_family') ||
        $theme_options->is_option_changed('finesse_h5_font_weight') ||
        $theme_options->is_option_changed('finesse_h5_font_size') ||
        $theme_options->is_option_changed('finesse_h5_line_height')
    ) {
        $finesse_h5_font_family = $theme_options->get_option_value('finesse_h5_font_family');
        $finesse_h5_font_family1 = trim($finesse_h5_font_family[0]);
        $finesse_h5_font_family2 = trim($finesse_h5_font_family[1]) == '' ? '' : ', ' . trim($finesse_h5_font_family[1]);
        $finesse_h5_font_weight = $theme_options->get_option_value('finesse_h5_font_weight');
        $finesse_h5_font_size = $theme_options->get_option_value('finesse_h5_font_size');
        $finesse_h5_line_height = $theme_options->get_option_value('finesse_h5_line_height');
        $output .= "h5 {
	font-family: $finesse_h5_font_family1$finesse_h5_font_family2;
	font-weight: $finesse_h5_font_weight;
	font-size: $finesse_h5_font_size;
	line-height: $finesse_h5_line_height;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_h6_font_family') ||
        $theme_options->is_option_changed('finesse_h6_font_weight') ||
        $theme_options->is_option_changed('finesse_h6_font_size') ||
        $theme_options->is_option_changed('finesse_h6_line_height')
    ) {
        $finesse_h6_font_family = $theme_options->get_option_value('finesse_h6_font_family');
        $finesse_h6_font_family1 = trim($finesse_h6_font_family[0]);
        $finesse_h6_font_family2 = trim($finesse_h6_font_family[1]) == '' ? '' : ', ' . trim($finesse_h6_font_family[1]);
        $finesse_h6_font_weight = $theme_options->get_option_value('finesse_h6_font_weight');
        $finesse_h6_font_size = $theme_options->get_option_value('finesse_h6_font_size');
        $finesse_h6_line_height = $theme_options->get_option_value('finesse_h6_line_height');
        $output .= "h6 {
	font-family: $finesse_h6_font_family1$finesse_h6_font_family2;
	font-weight: $finesse_h6_font_weight;
	font-size: $finesse_h6_font_size;
	line-height: $finesse_h6_line_height;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_caption_heading_font_size') ||
        $theme_options->is_option_changed('finesse_caption_heading_line_height')
    ) {
        $finesse_caption_heading_font_size = $theme_options->get_option_value('finesse_caption_heading_font_size');
        $finesse_caption_heading_line_height = $theme_options->get_option_value('finesse_caption_heading_line_height');
        $output .= ".flex-caption h2 {
	font-size: $finesse_caption_heading_font_size;
	line-height: $finesse_caption_heading_line_height;
}\n";
    }
    return $output;
}

function get_other_css_styles()
{
    global $theme_options;
    $output = '';
    if ($theme_options->is_option_changed('finesse_pb_header_border_color')) {
        $finesse_pb_header_border_color = $theme_options->get_option_value('finesse_pb_header_border_color');
        $output .= ".pricing-box.featured .header {
	 border-color: $finesse_pb_header_border_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_general_border_color')) {
        $finesse_general_border_color = $theme_options->get_option_value('finesse_general_border_color');
        $output .= "ul#navlist li.current a,
.ddsubmenustyle li a:hover,
.infobox {
	border-color: $finesse_general_border_color;
}\n";
    }
    if ($theme_options->is_option_changed('finesse_player_controls_bg_color')) {
        $finesse_player_controls_bg_color = $theme_options->get_option_value('finesse_player_controls_bg_color');
        $finesse_player_controls_bg_color1 = $finesse_player_controls_bg_color[0];
        $finesse_player_controls_bg_color2 = $finesse_player_controls_bg_color[1];
        $finesse_player_controls_bg_color1_rgb = finesse_html2rgb($finesse_player_controls_bg_color1);
        $finesse_player_controls_bg_color2_rgb = finesse_html2rgb($finesse_player_controls_bg_color2);
        $output .= ".mejs-controls .mejs-time-rail .mejs-time-loaded {
	background: $finesse_player_controls_bg_color2;
	background: rgba($finesse_player_controls_bg_color2_rgb[0],$finesse_player_controls_bg_color2_rgb[1],$finesse_player_controls_bg_color2_rgb[2],0.8);
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(rgba($finesse_player_controls_bg_color1_rgb[0],$finesse_player_controls_bg_color1_rgb[1],$finesse_player_controls_bg_color1_rgb[2],0.8)), to(rgba($finesse_player_controls_bg_color2_rgb[0],$finesse_player_controls_bg_color2_rgb[1],$finesse_player_controls_bg_color2_rgb[2],0.8)));
	background: -webkit-linear-gradient(top, rgba($finesse_player_controls_bg_color1_rgb[0],$finesse_player_controls_bg_color1_rgb[1],$finesse_player_controls_bg_color1_rgb[2],0.8), rgba($finesse_player_controls_bg_color2_rgb[0],$finesse_player_controls_bg_color2_rgb[1],$finesse_player_controls_bg_color2_rgb[2],0.8));
	background: -moz-linear-gradient(top, rgba($finesse_player_controls_bg_color1_rgb[0],$finesse_player_controls_bg_color1_rgb[1],$finesse_player_controls_bg_color1_rgb[2],0.8), rgba($finesse_player_controls_bg_color2_rgb[0],$finesse_player_controls_bg_color2_rgb[1],$finesse_player_controls_bg_color2_rgb[2],0.8));
	background: -o-linear-gradient(top, rgba($finesse_player_controls_bg_color1_rgb[0],$finesse_player_controls_bg_color1_rgb[1],$finesse_player_controls_bg_color1_rgb[2],0.8), rgba($finesse_player_controls_bg_color2_rgb[0],$finesse_player_controls_bg_color2_rgb[1],$finesse_player_controls_bg_color2_rgb[2],0.8));
	background: -ms-linear-gradient(top, rgba($finesse_player_controls_bg_color1_rgb[0],$finesse_player_controls_bg_color1_rgb[1],$finesse_player_controls_bg_color1_rgb[2],0.8), rgba($finesse_player_controls_bg_color2_rgb[0],$finesse_player_controls_bg_color2_rgb[1],$finesse_player_controls_bg_color2_rgb[2],0.8));
	background: linear-gradient(rgba($finesse_player_controls_bg_color1_rgb[0],$finesse_player_controls_bg_color1_rgb[1],$finesse_player_controls_bg_color1_rgb[2],0.8), rgba($finesse_player_controls_bg_color2_rgb[0],$finesse_player_controls_bg_color2_rgb[1],$finesse_player_controls_bg_color2_rgb[2],0.8));
}\n";
    }
    if ($theme_options->is_option_changed('finesse_player_controls_focus_color')) {
        $finesse_player_controls_focus_color = $theme_options->get_option_value('finesse_player_controls_focus_color');
        $output .= ".mejs-controls .mejs-button button:focus {
	outline-color: $finesse_player_controls_focus_color;
}\n";
    }
    return $output;
}

