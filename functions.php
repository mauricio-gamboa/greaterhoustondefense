<?php

define('FINESSE_THEME_VERSION', '1.3');
define('FINESSE_DEFAULT_LOCALE', 'en_UK');
define('FINESSE_FUNCTIONS_FILE_PATH', dirname(__FILE__));
define('FINESSE_SIDEBAR_FOOTER', 'Footer Sidebar');
define('FINESSE_SIDEBAR_DEFAULT', 'Default Sidebar');
define('IS_STYLE_SWITCHER_ENABLED', false);
define('IS_BROWSER_COMPATIBILITY_CHECK_ENABLED', true);
define('HTTP_PROTOCOL', 'http');

include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/finesse-file-access.php');


if (!isset($content_width)) {
    $content_width = 900;
}

//---------------------------------------------- BROWSER DETECTION -----------------------------------------------------
if (IS_BROWSER_COMPATIBILITY_CHECK_ENABLED) {
    add_filter('init', 'browser_compatibility_check_filter');
    function browser_compatibility_check_filter()
    {
        if (isset($_REQUEST['unsupported']) && $_REQUEST['unsupported'] == 'true') {
            include FINESSE_FUNCTIONS_FILE_PATH . '/update-browser.php';
            exit;
        }
    }
}

//------------------------------------- INTERNATIONALIZATION CONFIGURATION ---------------------------------------------
add_filter('locale', 'get_finesse_locale');
if (!function_exists('get_finesse_locale')) {
    function get_finesse_locale($locale)
    {
        $SESSION_KEY = 'finesse_language';
        if (!session_id()) {
            session_start();
        }
        $languages = get_option('finesse_languages');
        $int_on = get_option('finesse_internationalization_enabled');
        if (isset($int_on) && strtolower($int_on) == 'on') {
            if (isset($_REQUEST['lang'])) {
                $locale = $_REQUEST['lang'];
                $_SESSION[$SESSION_KEY] = $locale;
            } elseif (isset($_SESSION[$SESSION_KEY])) {
                $locale = $_SESSION[$SESSION_KEY];
            }

            $found = false;
            if (isset($languages) && is_array($languages) && count($languages) > 0) {
                foreach ($languages as $key => $value) {
                    if ($value == $locale) {
                        $found = true;
                    }
                }
            }
            if (!$found) {
                $locale = FINESSE_DEFAULT_LOCALE;
                $_SESSION[$SESSION_KEY] = $locale;
            }
        } else {
            $locale = 'en_US';
        }

        return $locale;
    }
}

load_theme_textdomain('finesse', FINESSE_FUNCTIONS_FILE_PATH . '/lang');

if (!function_exists('___')) {
    function ___($key)
    {
        if (isset($key) && strlen($key) > 0 && strlen($key) <= 256) {
            global $translation_manager;
            $locale = get_locale();
            $message = $translation_manager->find_message_by_key_and_locale(trim($key), $locale);
            if (isset($message) && strlen($message) > 0) {
                $message = str_replace("\\'", "", $message);
                $message = str_replace("\\", "", $message);
                return $message;
            } else {
                return $key;
            }
        } else {
            return $key;
        }
    }
}

add_filter('widget_title', 'finesse_translation_filter');
add_filter('widget_text', 'finesse_translation_filter');
add_filter('the_title', 'finesse_translation_filter');

if (!function_exists('finesse_translation_filter')) {
    function finesse_translation_filter($value)
    {
        return ___($value);
    }
}

//------------------------------------------ LOADING SCRIPTS & STYLES --------------------------------------------------
add_action('wp_enqueue_scripts', 'enqueue_finesse_scripts');
if (!function_exists('enqueue_finesse_scripts')) {
    function enqueue_finesse_scripts()
    {
        $template_uri = get_template_directory_uri();
        //header CSS
        wp_enqueue_style('color-style', $template_uri . '/css/colors/' . get_site_color_schema() . '.css');
        if (IS_STYLE_SWITCHER_ENABLED) {
            wp_enqueue_style('style-switcher-style', $template_uri . '/style-switcher/style-switcher.css');
        }

        wp_deregister_script('jquery');
        wp_register_script('jquery', $template_uri . '/js/jquery-1.7.2.min.js');
        //header JS
        wp_enqueue_script('jquery');
        if (IS_BROWSER_COMPATIBILITY_CHECK_ENABLED) {
            wp_enqueue_script('browser-check', $template_uri . '/js/ie.js', array('jquery'));
        }
        wp_enqueue_script('form-processor', $template_uri . '/js/form-processor.js', array('jquery'));
        wp_enqueue_script('jquery-easing', $template_uri . '/js/jquery.easing.1.3.js', array('jquery'));
        wp_enqueue_script('modernizr', $template_uri . '/js/modernizr.custom.js', array('jquery'));
        if (is_ie_version(8)) {
            wp_enqueue_script('respond', $template_uri . '/js/respond.min.js', array('jquery', 'jquery-easing', 'modernizr'));
        }
        if (IS_STYLE_SWITCHER_ENABLED) {
            wp_enqueue_script('style-switcher', $template_uri . '/style-switcher/style-switcher.js', array('jquery'), FINESSE_THEME_VERSION);
        }
        wp_enqueue_script('polyglot-language-switcher', $template_uri . '/js/jquery.polyglot.language.switcher.js', array('jquery'), FINESSE_THEME_VERSION);
        wp_enqueue_script('ddlevelsmenu', $template_uri . '/js/ddlevelsmenu.js', array('jquery', 'modernizr'));
        wp_enqueue_script('ddlevelsmenu-invoke', $template_uri . '/js/ddlevelsmenu-invoke.js', array('jquery', 'modernizr', 'ddlevelsmenu'));
        wp_enqueue_script('tinynav', $template_uri . '/js/tinynav.min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-validate', $template_uri . '/js/jquery.validate.min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-flexslider', $template_uri . '/js/jquery.flexslider-min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-jcarousel', $template_uri . '/js/jquery.jcarousel.min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-totop', $template_uri . '/js/jquery.ui.totop.min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-ui', $template_uri . '/js/jquery-ui-1.10.0.custom.min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-fancybox', $template_uri . '/js/jquery.fancybox.pack.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-cycle', $template_uri . '/js/jquery.cycle.all.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('mediaelements-js', $template_uri . '/js/mediaelement-and-player.min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-fitvids', $template_uri . '/js/jquery.fitvids.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-tweet', $template_uri . '/js/jquery.tweet.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-jflickrfeed', $template_uri . '/js/jflickrfeed.min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-quicksand', $template_uri . '/js/jquery.quicksand.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('jquery-touchSwipe', $template_uri . '/js/jquery.touchSwipe.min.js', array('jquery', 'ddlevelsmenu', 'ddlevelsmenu-invoke'));
        wp_enqueue_script('googleapis', HTTP_PROTOCOL . '://maps.googleapis.com/maps/api/js?sensor=false');
        wp_enqueue_script('jquery-gmap', $template_uri . '/js/jquery.gmap.min.js', array('jquery', 'googleapis'));
        if (is_singular() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
        wp_enqueue_script('custom', $template_uri . '/js/custom.js', array('jquery', 'form-processor'), FINESSE_THEME_VERSION);
    }
}

add_action('admin_print_scripts', 'enqueue_finesse_admin_scripts');
function enqueue_finesse_admin_scripts()
{
    $template_url = get_template_directory_uri();
    wp_enqueue_script('thickbox');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core', 'jquery');
    wp_enqueue_script('jquery-ui-tabs', 'jquery');
    wp_enqueue_script('jquery-ui-accordion', 'jquery');
    wp_enqueue_script('jquery-ui-draggable', 'jquery');
    wp_enqueue_script('jquery-ui-droppable', 'jquery');
    wp_enqueue_script('jquery-ui-sortable', 'jquery');
    wp_enqueue_script('jquery-ui-position', 'jquery');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('jquery-validate', $template_url . '/admin/js/jquery.validate.min.js', array('jquery'));
    wp_enqueue_script('color-picker', $template_url . '/admin/js/colorpicker.js', array('jquery'));
    wp_enqueue_script('js-base64', $template_url . '/admin/js/base64.js', array('jquery'));
    wp_enqueue_script('admin-script', $template_url . '/admin/js/admin.js', array('jquery', 'jquery-validate',
            'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-draggable', 'jquery-ui-droppable',
            'jquery-ui-sortable', 'jquery-ui-position', 'media-upload', 'thickbox', 'color-picker', 'js-base64'),
        FINESSE_THEME_VERSION);
    wp_enqueue_script('shortcode-editor', $template_url . '/admin/js/shortcode-editor.js', array('jquery',
            'jquery-validate', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-draggable',
            'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-position', 'media-upload', 'thickbox', 'color-picker',
            'admin-script', 'js-base64'),
        FINESSE_THEME_VERSION);
    wp_enqueue_script('slider-editor', $template_url . '/admin/js/slider-editor.js', array('jquery',
            'jquery-validate', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-draggable',
            'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-position', 'media-upload', 'thickbox', 'color-picker',
            'admin-script', 'js-base64'),
        FINESSE_THEME_VERSION);
    wp_enqueue_script('contact-editor', $template_url . '/admin/js/contact-editor.js', array('jquery',
            'jquery-validate', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-draggable',
            'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-position', 'media-upload', 'thickbox', 'color-picker',
            'admin-script', 'js-base64'),
        FINESSE_THEME_VERSION);
}

add_action('admin_print_styles', 'enqueue_finesse_admin_styles');
function enqueue_finesse_admin_styles()
{
    $template_uri = get_template_directory_uri();
    wp_enqueue_style('thickbox');
    wp_enqueue_style('dialog-style', $template_uri . '/admin/css/jquery.ui.dialog.css');
    wp_enqueue_style('colorpicker-style', $template_uri . '/admin/css/colorpicker.css');
    wp_enqueue_style('custom-admin-style', $template_uri . '/admin/css/admin-style.css');
}

//--------------------------------------------- CLASS LOADING ----------------------------------------------------------
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/menu/Finesse_Main_Menu_Walker.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/X_Meta_Box.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/page/Page_Customizer.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/page/Simple_Page_Customizer.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/page/Post_Page_Customizer.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/page/Portfolio_Page_Customizer.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/plugin/Finesse_Plugin.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/plugin/Font_Manager_Plugin.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/plugin/Import_Manager_Plugin.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/plugin/Newsletter_Manager_Plugin.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/plugin/Sidebar_Manager_Plugin.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/plugin/Translation_Manager_Plugin.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/plugin/Plugin_Manager.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Archives_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Categories_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Contact_Details_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Flickr_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Latest_Tweets_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Most_Used_Tags_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Recent_Posts_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/widget/Finesse_Text_Widget.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Abstract_Finesse_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Shortcode_Designer.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Audio_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Blockquote_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Button_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Clients_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Dropcap_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Embedded_Video_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Form_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Grid_Columns_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Highlight_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Icon_Box_Carousel_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Icon_Box_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Image_Gallery_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Image_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Info_Box_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/List_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Newsletter_Subscription_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Notification_Box_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Posts_Carousel_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Preformatted_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Pricing_Boxes_Shortcodes.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Pricing_Table_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Self_Hosted_Video_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Site_Map_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Table_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Tabs_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Team_Members_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Testimonials_Carousel_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Toggles_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Translation_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Wrap_Shortcode.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/shortcode/Shortcode_Manager.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/contact/Contact_Map_Manager.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/media/Page_Media_Manager.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/util/Email_Util.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/util/Embedded_Video.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/util/Multimedia_Util.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/util/Post_Util.php');

//------------------------------------------- PLUGINS INITIALIZATION ---------------------------------------------------
$import_manager = new Import_Manager_Plugin();
$sidebar_manager = new Sidebar_Manager_Plugin();
$font_manager = new Font_Manager_Plugin();
$newsletter_manager = new Newsletter_Manager_Plugin();
$translation_manager = new Translation_Manager_Plugin();

$plugin_manager = new Plugin_Manager();
$plugin_manager->register_plugin($import_manager);
$plugin_manager->register_plugin($sidebar_manager);
$plugin_manager->register_plugin($font_manager);
$plugin_manager->register_plugin($newsletter_manager);
$plugin_manager->register_plugin($translation_manager);
$plugin_manager->load_plugins();

//--------------------------------------------- THEME ACTIVATION ----------------------------------------------------

include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/finesse-config.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/finesse-pagination.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/finesse-util.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/finesse-header-util.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/finesse-forms.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/finesse-blog-util.php');
include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/newsletter/finesse_newsletter.php');

if (is_user_logged_in()) {
    include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/finesse-admin-page-config.php');
}

//--------------------------------------------- THEME CONFIGURATION ----------------------------------------------------

if (function_exists('add_theme_support')) {
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-formats', array('image', 'gallery', 'audio', 'video', 'aside', 'quote', 'link'));
}
if (function_exists('add_image_size')) {
    add_image_size('custom-slider-thumb', 940, 350, true);
    add_image_size('custom-blog-post-thumb', 700, 240, true);
    add_image_size('custom-portfolio-post-thumb', 700, 500, true);
    add_image_size('custom-icon-post-thumb', 41, 41, true);
    add_image_size('custom-medium-post-thumb', 220, 130, true);
    add_image_size('custom-team-member-thumb', 220, 220, true);
}

add_filter('upload_mimes', 'finesse_add_extra_mime_types_support');
function finesse_add_extra_mime_types_support($mimes)
{
    $mimes = array_merge($mimes, array(
        'webm' => 'video/webm',
        'srt|sub' => 'text/plain',
        'xml' => 'text/xml'
    ));

    return $mimes;
}

add_filter('excerpt_length', 'finesse_excerpt_length');
if (!function_exists('finesse_excerpt_length')) {
    function finesse_excerpt_length($length)
    {
        return 999999;
    }
}

add_action('admin_head', 'finesse_custom_admin_head_page');
function finesse_custom_admin_head_page()
{
    echo get_admin_custom_css();
}

add_action('wp_head', 'finesse_custom_head_page');
if (!function_exists('finesse_custom_head_page')) {
    function finesse_custom_head_page()
    {
        echo get_custom_css();
    }
}

//------------------------------------------------ MENUS SECTION -------------------------------------------------------
if (function_exists('register_nav_menus')) {
    register_nav_menus(array(
        'primary' => 'Main Menu',
    ));
    register_nav_menus(array(
        'footer' => 'Footer Menu',
    ));
}

function has_at_least_one_menu_item_defined()
{
    global $wpdb;
    $c = $wpdb->get_var("select count(*) as c from $wpdb->posts p where p.post_type='nav_menu_item'");
    return intval($c) > 0;
}

$has_at_least_one_menu_item_defined = has_at_least_one_menu_item_defined();

$simple_menu_walker = $has_at_least_one_menu_item_defined ? new Finesse_Main_Menu_Walker() : '';
$header_menu_walker = $has_at_least_one_menu_item_defined ? new Finesse_Main_Menu_Walker('header') : '';
$footer_menu_walker = $has_at_least_one_menu_item_defined ? new Finesse_Main_Menu_Walker('footer') : '';

//------------------------------------------------ SIDEBARS SECTION ----------------------------------------------------
if (function_exists('register_sidebar')) {

    register_sidebar(array(
        'name' => __(FINESSE_SIDEBAR_DEFAULT, 'finesse'),
        'id' => 'default-sidebar',
        'description' => __('The default sidebar', 'finesse'),
        'before_widget' => '<div class="widget">',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
        'after_widget' => '</div>'
    ));

    register_sidebar(array(
        'name' => __(FINESSE_SIDEBAR_FOOTER, 'finesse'),
        'id' => 'footer-sidebar',
        'description' => __('The sidebar for the footer', 'finesse'),
        'before_widget' => '<div class="widget">',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
        'after_widget' => '</div>'
    ));

    $sidebars = get_dynamic_sidebars();
    foreach ($sidebars as $sidebar) {
        register_sidebar(array(
            'id' => $sidebar['id'],
            'name' => $sidebar['name'],
            'description' => $sidebar['description'],
            'before_widget' => '<div class="widget">',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
            'after_widget' => '</div>'
        ));
    }

    add_filter('dynamic_sidebar_params', 'finesse_configure_footer_sidebar_params');
    if (!function_exists('finesse_configure_footer_sidebar_params')) {
        function finesse_configure_footer_sidebar_params($params)
        {
            if ($params[0]['name'] == FINESSE_SIDEBAR_FOOTER) {
                $sidebars = wp_get_sidebars_widgets();
                $widgets = $sidebars[$params[0]['id']];
                $widgets_count = count($widgets);
                $class_names = '';

                if ($widgets_count > 0) {
                    if ($widgets_count == 2) {
                        $class_names = 'one-half';
                    } else if ($widgets_count == 3) {
                        $class_names = 'one-third';
                    } else if ($widgets_count == 4) {
                        $class_names = 'one-fourth';
                    }
                }
                if (strlen($class_names) > 0) {
                    $widget_position = -1;
                    foreach ($widgets as $i => $widget) {
                        if ($widget == $params[0]['widget_id']) {
                            $widget_position = ($i + 1);
                        }
                    }
                    if ($widget_position == $widgets_count) {
                        $class_names .= ' column-last';
                    }
                    $class = ' class="' . $class_names . '"';
                } else {
                    $class = '';
                }
                $params[0]['before_widget'] = '<div' . $class . '>' . $params[0]['before_widget'];
                $params[0]['after_widget'] .= '</div>';
            }
            return $params;
        }
    }
}

//------------------------------------------------ WIDGETS SECTION ----------------------------------------------------

add_action('widgets_init', 'finesse_register_widgets');
if (!function_exists('finesse_register_widgets')) {
    function finesse_register_widgets()
    {
        unregister_widget('WP_Widget_Text');
        register_widget(Finesse_Widget::$TEXT_WIDGET);
        unregister_widget('WP_Widget_Recent_Posts');
        register_widget(Finesse_Widget::$RECENT_POSTS_WIDGET);
        unregister_widget('WP_Widget_Archives');
        register_widget(Finesse_Widget::$POST_ARCHIVES);
        unregister_widget('WP_Widget_Categories');
        register_widget(Finesse_Widget::$POST_CATEGORIES);

        register_widget(Finesse_Widget::$LATEST_TWEETS_WIDGET);
        register_widget(Finesse_Widget::$CONTACT_DETAILS_WIDGET);
        register_widget(Finesse_Widget::$FLICKR_WIDGET);
        register_widget(Finesse_Widget::$MOST_USED_TAGS);
    }
}

//------------------------------------------- SLIDER & CONTACT SECTION -------------------------------------------------
$post_media_manager = new Page_Media_Manager();
$contact_map_manager = new Contact_Map_Manager();

//---------------------------------------------- SHORTCODES SECTION ----------------------------------------------------
//remove_filter('the_content', 'wpautop');
//remove_filter('the_content', 'wptexturize');

$shortcode_manager = new Shortcode_Manager();
$shortcode_manager->add_shortcode(new Button_Shortcode());
$shortcode_manager->add_shortcode(new List_Shortcode());
$shortcode_manager->add_shortcode(new Info_Box_Shortcode());
$shortcode_manager->add_shortcode(new Icon_Box_Shortcode());
$shortcode_manager->add_shortcode(new Table_Shortcode());

$shortcode_manager->add_shortcode(new Tabs_Shortcode());
$shortcode_manager->add_shortcode(new Toggles_Shortcode());
$shortcode_manager->add_shortcode(new Icon_Box_Carousel_Shortcode());
$shortcode_manager->add_shortcode(new Posts_Carousel_Shortcode());
$shortcode_manager->add_shortcode(new Testimonials_Carousel_Shortcode());
$shortcode_manager->add_shortcode(new Notification_Box_Shortcode());

$shortcode_manager->add_shortcode(new Image_Shortcode());
$shortcode_manager->add_shortcode(new Image_Gallery_Shortcode());
$shortcode_manager->add_shortcode(new Embedded_Video_Shortcode());
$shortcode_manager->add_shortcode(new Self_Hosted_Video_Shortcode());
$shortcode_manager->add_shortcode(new Audio_Shortcode());

$shortcode_manager->add_shortcode(new Dropcap_Shortcode());
$shortcode_manager->add_shortcode(new Highlight_Shortcode());
$shortcode_manager->add_shortcode(new Preformatted_Shortcode());
$shortcode_manager->add_shortcode(new Blockquote_Shortcode());

$shortcode_manager->add_shortcode(new Grid_Columns_Shortcode());

$shortcode_manager->add_shortcode(new Pricing_Boxes_Shortcodes());
$shortcode_manager->add_shortcode(new Pricing_Table_Shortcode());

$shortcode_manager->add_shortcode(new Site_Map_Shortcode());
$shortcode_manager->add_shortcode(new Form_Shortcode());

$shortcode_manager->add_shortcode(new Newsletter_Subscription_Shortcode());
$shortcode_manager->add_shortcode(new Team_Members_Shortcode());
$shortcode_manager->add_shortcode(new Clients_Shortcode());
$shortcode_manager->add_shortcode(new Wrap_Shortcode());
$shortcode_manager->add_shortcode(new Translation_Shortcode());

$shortcode_manager->register_shortcodes();

//---------------------------------------------- PORTFOLIO SECTION ----------------------------------------------------
$portfolio_args = array(
    'label' => __('Portfolio', 'finesse'),
    'singular_label' => __('Portfolio', 'finesse'),
    'public' => true,
    'rewrite' => true,
    'show_ui' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'show_in_nav_menus' => true,
    'supports' => array('title', 'editor', 'post-formats')
);
register_post_type('portfolio', $portfolio_args);

add_action('init', 'register_portfolio_taxonomy');
function register_portfolio_taxonomy()
{
    register_taxonomy('filter', 'portfolio',
        array('hierarchical' => true,
            'label' => 'Filter',
            'query_var' => true,
            'show_in_nav_menus' => false,
            'rewrite' => array('slug' => 'filter'))
    );
}

add_action("template_redirect", 'portfolio_template_redirect');
function portfolio_template_redirect()
{
    global $wp_query;
    if ($wp_query->query_vars["post_type"] == "portfolio") {
        if (have_posts()) {
            include(FINESSE_FUNCTIONS_FILE_PATH . '/single-portfolio.php');
            die();
        } else {
            $wp_query->is_404 = true;
        }
    }
    wp_reset_postdata();
}
