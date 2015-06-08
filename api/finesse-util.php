<?php

function get_theme_option($option_name)
{
    global $theme_options;
    $all_options = wp_load_alloptions();
    if (!array_key_exists($option_name, $all_options)) {
        $return_value = $theme_options->get_option_value($option_name);
    } else {
        $return_value = $all_options[$option_name];
    }
    return maybe_unserialize($return_value);
}

function get_site_color_schema()
{
    global $theme_options;
    $site_color = $theme_options->get_option_value('finesse_color_schema');
    if (!isset($site_color) || strlen($site_color) == 0) {
        $site_color = 'red';
    }
    return $site_color;
}

function is_internationalization_on()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_internationalization_enabled');
    return strtolower($on) == 'on';
}

function get_header_logo()
{
    global $theme_options;
    $logo = $theme_options->get_option_value('finesse_header_logo');
    if (isset($logo) && strlen($logo) > 0) {
        return $logo;
    } else {
        return '#';
    }
}

function get_tagline()
{
    global $theme_options;
    $tagline = $theme_options->get_option_value('finesse_tagline');
    if (isset($tagline) && strlen($tagline) > 0) {
        return ___($tagline);
    } else {
        return '';
    }
}

function get_page_description()
{
    global $theme_options;
    global $post;
    if (isset($post) && isset($theme_options)) {
        $description = get_post_meta(get_the_ID(), 'finesse_page_description', true);
        if (!isset($description) || trim($description) == '') {
            $description = $theme_options->get_option_value('finesse_page_description');
        }
    } else {
        $description = get_bloginfo('description');
    }
    return $description;
}

function get_page_keywords()
{
    global $theme_options;
    global $post;
    if (isset($post) && isset($theme_options)) {
        $keywords = get_post_meta(get_the_ID(), 'finesse_page_keywords', true);
        if (!isset($keywords) || trim($keywords) == '') {
            $keywords = $theme_options->get_option_value('finesse_page_keywords');
        }
    } else {
        $keywords = '';
    }
    return $keywords;
}

function get_fav_icon()
{
    global $theme_options;
    $favicon = $theme_options->get_option_value('finesse_favicon');
    if (!isset($favicon) || trim($favicon) == '') {
        $favicon = get_template_directory_uri() . '/images/favicon.ico';
    }
    return $favicon;
}

function is_search_header_displayed()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_display_search_header');
    return strtolower($on) == 'on';
}

function get_contact_address()
{
    global $theme_options;
    $contact_email = $theme_options->get_option_value('finesse_contact_address');
    if (!isset($contact_email)) {
        $contact_email = '';
    }
    return $contact_email;
}

function get_contact_email()
{
    global $theme_options;
    $contact_email = $theme_options->get_option_value('finesse_contact_email');
    if (!isset($contact_email)) {
        $contact_email = '';
    }
    return $contact_email;
}

function get_contact_phone()
{
    global $theme_options;
    $contact_phone = $theme_options->get_option_value('finesse_contact_phone');
    if (!isset($contact_phone)) {
        $contact_phone = '';
    }
    return $contact_phone;
}

function is_contact_details_displayed_in_header_enabled()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_display_contact_details_in_header');
    return strtolower($on) == 'on';
}

function display_related_posts()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_blog_display_related_posts');
    return strtolower($on) == 'on';
}

function is_display_post_meta_enabled()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_blog_display_post_meta');
    return strtolower($on) == 'on';
}

function is_display_post_comments_enabled()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_blog_display_post_comments');
    return strtolower($on) == 'on';
}

function get_portfolio_rows_count()
{
    global $theme_options;
    $finesse_portfolio_rows = $theme_options->get_option_value('finesse_portfolio_overview_rows_count');
    if (!isset($finesse_portfolio_rows) || !is_int($finesse_portfolio_rows) == 0) {
        $finesse_portfolio_rows = '3';
    }
    return intval($finesse_portfolio_rows);
}

function display_portfolio_related_posts()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_portfolio_display_related_posts');
    return strtolower($on) == 'on';
}

function is_portfolio_body_content_enabled()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_portfolio_enable_content');
    return strtolower($on) == 'on';
}

function get_twitter_username()
{
    global $theme_options;
    $twitter_username = $theme_options->get_option_value('finesse_social_twitter_username');
    if (!isset($twitter_username) || trim($twitter_username) == '') {
        $twitter_username = 'ixtendo';
    }
    return $twitter_username;
}

function get_facebook_url()
{
    global $theme_options;
    $facebook_url = $theme_options->get_option_value('finesse_social_facebook_url');
    if (!isset($facebook_url) || trim($facebook_url) == '') {
        $facebook_url = '';
    }
    return $facebook_url;
}

function get_gplus_url()
{
    global $theme_options;
    $gplus_url = $theme_options->get_option_value('finesse_social_gplus_url');
    if (!isset($gplus_url) || trim($gplus_url) == '') {
        $gplus_url = '';
    }
    return $gplus_url;
}

function get_youtube_url()
{
    global $theme_options;
    $youtube_url = $theme_options->get_option_value('finesse_social_youtube_url');
    if (!isset($youtube_url) || trim($youtube_url) == '') {
        $youtube_url = '';
    }
    return $youtube_url;
}

function get_skype_url()
{
    global $theme_options;
    $skype_url = $theme_options->get_option_value('finesse_social_skype_url');
    if (!isset($skype_url) || trim($skype_url) == '') {
        $skype_url = '';
    }
    return $skype_url;
}

function get_flicker_id()
{
    global $theme_options;
    $flickr_id = $theme_options->get_option_value('finesse_social_flickr_id');
    if (!isset($flickr_id) || trim($flickr_id) == '') {
        $flickr_id = '';
    }
    return $flickr_id;
}

function is_rss_enabled()
{
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_rss_enabled');
    return isset($on) && (strtolower($on) == 'on');
}

function get_rss_url()
{
    global $theme_options;
    $rss_type = $theme_options->get_option_value('finesse_rss_type');
    return get_bloginfo($rss_type, 'display');
}

function is_responsive_enabled(){
    global $theme_options;
    $on = $theme_options->get_option_value('finesse_responsive_enabled');
    return isset($on) && (strtolower($on) == 'on');
}

function get_supported_languages()
{
    $languages = get_option('finesse_languages');
    if (isset($languages) && is_array($languages) && count($languages) > 0) {
        return $languages;
    } else {
        return array();
    }
}

function save_supported_languages($languages)
{
    update_option('finesse_languages', $languages);
}

function get_footer_text()
{
    global $theme_options;
    $value = $theme_options->get_option_value('finesse_footer_text');
    if (isset($value) && strlen($value) > 0) {
        return html_entity_decode($value);
    } else {
        return '';
    }
}

function get_tracking_code()
{
    global $theme_options;
    $tracking_code = $theme_options->get_option_value('finesse_tracking_code');
    if (isset($tracking_code) && trim($tracking_code) != '') {
        return $tracking_code;
    } else {
        return false;
    }
}

function get_dynamic_sidebars()
{
    $dynamic_sidebars = get_option('finesse_dynamic_sidebars');
    if (!$dynamic_sidebars) {
        return array();
    } else {
        return $dynamic_sidebars;
    }
}

function save_dynamic_sidebars($dynamic_sidebars)
{
    update_option('finesse_dynamic_sidebars', $dynamic_sidebars);
}

function get_current_page_sidebar()
{
    global $wp_registered_sidebars;
    global $post;
    if (isset($post) && isset($wp_registered_sidebars)) {
        $sidebar_name = get_post_meta(get_the_ID(), 'finesse_sidebar_name', true);
        if (is_string($sidebar_name) && array_key_exists(sanitize_title($sidebar_name), $wp_registered_sidebars)) {
            return $sidebar_name;
        }
    }
    return 'default-sidebar';
}

function get_current_layout()
{
    global $theme_options;
    return $theme_options->get_option_value('finesse_layout_type');
}

//-------------------------------------------- UTIL FUNCTIONS ----------------------------------------------------------

if (!function_exists('print_language_switcher')) {
    function print_language_switcher()
    {
        $supported_languages = get_supported_languages();
        if (is_internationalization_on()) {
            echo '<div id="polyglotLanguageSwitcher">';
            echo '<form action="#">';
            echo '<select id="polyglot-language-options">';
            foreach ($supported_languages as $key => $value) {
                $id = substr(strtolower($value), 3);
                $current_locale = get_locale();
                $selected_attr = ($value == $current_locale) ? 'selected' : '';
                if ($value == 'en_US') {
                    $id = 'us';
                }
                echo '<option id="' . $id . '" value="' . $value . '" ' . $selected_attr . '>' . $key . '</option>';
            }
            echo '</select >';
            echo '</form >';
            echo '</div>';
        }
    }
}

if (!function_exists('contains_string')) {
    function contains_string($haystack, $needle)
    {
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            return false;
        } else {
            return true;
        }
    }
}

if (!function_exists('start_with')) {
    function start_with($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if (!function_exists('end_with')) {
    function end_with($haystack, $needle)
    {
        $length = strlen($needle);
        $start = $length * -1; //negative
        return (substr($haystack, $start) === $needle);
    }
}

if (!function_exists('emphasize')) {
    function emphasize($haystack, $needle)
    {
        return preg_replace("/$needle/i", '<strong>$0</strong>', $haystack);
    }
}

if (!function_exists('shrink_without_strip_tags')) {
    function shrink_without_strip_tags($content, $limit = 350, $read_more_symbol = '&hellip;')
    {
        if (strlen($content) > $limit) {
            if ($read_more_symbol == '') {
                $read_more_symbol = '.';
            } else {
                $read_more_symbol = ' ' . $read_more_symbol;
            }
            $content = substr($content, 0, strpos($content, " ", $limit)) . $read_more_symbol;
        }
        return $content;
    }
}

if (!function_exists('shrink')) {
    function shrink($content, $limit = 350, $read_more_symbol = '&hellip;')
    {
        $content = strip_tags($content);
        if (strlen($content) > $limit) {
            if ($read_more_symbol == '') {
                $read_more_symbol = '.';
            } else {
                $read_more_symbol = ' ' . $read_more_symbol;
            }
            $content = substr($content, 0, strpos($content, " ", $limit)) . $read_more_symbol;
        }
        return $content;
    }
}

if (!function_exists('shrink_starting_from')) {
    function shrink_starting_from($haystack, $needle, $limit = 350, $read_more_symbol = '&hellip;')
    {
        $haystack = strip_tags($haystack);
        $tmp_haystack = stristr($haystack, $needle);
        if ($tmp_haystack) {
            $haystack = $read_more_symbol . ' ' . $tmp_haystack;
        }
        if (strlen($haystack) > $limit) {
            if ($read_more_symbol == '') {
                $read_more_symbol = '.';
            } else {
                $read_more_symbol = ' ' . $read_more_symbol;
            }
            $haystack = substr($haystack, 0, strpos($haystack, " ", $limit)) . $read_more_symbol;
        }
        return $haystack;
    }
}

function get_url_content($url, $method = 'GET')
{
    $headers = get_headers($url);
    if (strrpos($headers[0], '200')) {
        $opts = array(
            'http' => array(
                'method' => $method,
                'header' => "Accept: text/html\r\n"
            )
        );
        $context = stream_context_create($opts);
        return file_get_contents($url, false, $context);
    } else {
        return null;
    }
}

function escape_json_value($value)
{
    $value = preg_replace("/\r\n|\r|\n/", ' ', stripslashes($value));
    return addslashes($value);
}

if (!function_exists('is_ie_version')) {
    function is_ie_version($ie_version)
    {
        preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if (count($matches) > 1) {
            //Then we're using IE
            $version = $matches[1];
            return intval($ie_version) == intval($version);
        }
        return false;
    }
}

if (!function_exists('finesse_parse_ini_string')) {
    function finesse_parse_ini_string($str, $process_sections = false)
    {
        $lines = explode("\n", $str);
        $return = Array();
        $inSect = false;
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line || $line[0] == "#" || $line[0] == ";") {
                continue;
            }
            if ($line[0] == "[" && $endIdx = strpos($line, "]")) {
                $inSect = substr($line, 1, $endIdx - 1);
                continue;
            }
            if (!strpos($line, '=')) { // (We don't use "=== false" because value 0 is not valid as well)
                continue;
            }

            $tmp = explode("=", $line, 2);
            if ($process_sections && $inSect)
                $return[$inSect][trim($tmp[0])] = ltrim($tmp[1]);
            else
                $return[trim($tmp[0])] = ltrim($tmp[1]);
        }
        return $return;
    }
}