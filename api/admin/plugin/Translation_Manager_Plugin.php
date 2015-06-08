<?php

class Translation_Manager_Plugin extends Finesse_Plugin
{

    private $success_messages = array(
        's1' => 'The new message was successfully added.',
        's2' => 'All messages was successfully saved.',
        's3' => 'The new language was successfully added.',
        's4' => 'All messages was successfully deleted.',
        's5' => 'All messages was successfully imported.',
        's6' => 'The message was successfully deleted.',
    );
    private $error_messages = array(
        'e1' => 'The language name already exists.',
        'e2' => 'The locale value already exists.',
    );
    private $messages;

    public function __construct()
    {
        parent::__construct();
    }

    function register_actions()
    {
        add_action('wp_ajax_tm-add-msg-form', array($this, 'render_add_msg_form'));
        add_action('wp_ajax_tm-add-msg', array($this, 'process_add_msg'));
        add_action('wp_ajax_tm-update-msg', array($this, 'process_update_all_msg'));
        add_action('wp_ajax_tm-remove-all-msg', array($this, 'process_remove_all_msg'));
        add_action('wp_ajax_tm-import-db-msg', array($this, 'process_import_db_msg'));
        add_action('wp_ajax_tm-remove-msg', array($this, 'process_remove_msg'));

        add_action('wp_ajax_tm-add-locale-form', array($this, 'render_add_locale_form'));
        add_action('wp_ajax_tm-update-locale-form', array($this, 'render_update_locale_form'));
        add_action('wp_ajax_tm-add-locale', array($this, 'process_add_locale'));
        add_action('wp_ajax_tm-update-locale', array($this, 'process_update_locale'));
        add_action('wp_ajax_tm-remove-locale', array($this, 'process_remove_locale'));

    }

    private function get_table_name()
    {
        global $wpdb;
        return $table_name = $wpdb->base_prefix . "finesse_translation";
    }

    protected function activate()
    {
    }

    private function is_table_created()
    {
        global $wpdb;
        $table_name = $this->get_table_name();
        return $wpdb->get_row($wpdb->prepare("show tables like %s", $table_name));
    }

    function render()
    {
        echo '<div class="wrap">';
        echo '<div class="icon32" id="icon-themes"></div>';
        echo '<h2>Finesse - Translation Manager</h2>';

        if (isset($_REQUEST['msg'])) {
            $msg = $_REQUEST['msg'];
            if (array_key_exists($msg, $this->success_messages)) {
                echo '<div class="updated below-h2"><p>' . $this->success_messages[$msg] . '</p></div>';
            } elseif (array_key_exists($msg, $this->error_messages)) {
                echo '<div class="error below-h2"><p>' . $this->error_messages[$msg] . '</p></div>';
            }
        }

        echo '<div class="plugin-content">';
        if (!is_internationalization_on()) {
            $url = get_home_url() . '/wp-admin/themes.php?page=finesse-theme-options&tab=general-settings&expand=internationalization';
            echo '<div class="error" id="message"><p>The internationalization is not enable. Go <a href="' . $url . '">here</a> to enable it.</p></div>';
        }
        echo '<p><h2>Supported Languages <a class="thickbox add-new-h2" href="admin-ajax.php?action=tm-add-locale-form&_wp_http_referer=' . esc_attr($_SERVER['REQUEST_URI']) . '" title="Add New Language">Add New Language</a></h2></p>';
        echo '<div id="locale-list">';

        echo $this->get_list_of_locales();

        echo '</div>';
        echo '</div>';
        echo '<p><h2>Internationalization Messages <a class="thickbox add-new-h2" href="admin-ajax.php?action=tm-add-msg-form&_wp_http_referer=' . esc_attr($_SERVER['REQUEST_URI']) . '" title="Add New Message">Add New Message</a></h2></p>';
        echo '<div class="plugin-content">';
        echo '<div id="msg-list">';
        echo '<form id="tm-keys-msgs-form" method="post" action="admin-ajax.php">';
        echo '<p>';
        echo '<input id="tm-update-all-messages" class="button-primary" type="submit" value="Update All Messages">';
        echo '<input id="tm-delete-all-messages" class="button-secondary" type="submit" value="Delete All Messages">';
        $table_exists = $this->is_table_created();
        if (isset($table_exists)) {
            echo '<input id="tm-import-db-messages" class="button-secondary" type="submit" value="Import From Database">';
        }
        echo '</p>';

        echo $this->get_list_of_messages();
        echo '<input type="hidden" id="tm-action" name="action" value="tm-update-msg">';
        echo '<input type="hidden" name="nonce" value="' . wp_create_nonce('tm-update-msg') . '" />';
        wp_referer_field(true);
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    function get_title()
    {
        return __('Translation Manager', 'finesse');
    }

    function get_slug()
    {
        return 'finesse-translation';
    }

    function find_message_by_key_and_locale($key, $locale)
    {
        if (!isset($this->messages)) {
            $this->messages = $this->get_all_messages_grouped_by_locale();
        }

        if (array_key_exists($locale, $this->messages)) {
            $array = $this->messages[$locale];
            if (array_key_exists($key, $array)) {
                return $array[$key];
            }
        }
        return $key;
    }

    private function get_all_messages_grouped_by_keys()
    {
        global $wp_filesystem;
        $message_keys = array();
        $message_values = array();
        $languages = get_supported_languages();
        foreach ($languages as $lang_name => $locale) {
            $ini_file_name = FINESSE_THEME_PATH . '/lang/' . $locale . '.ini';
            if ($wp_filesystem->exists($ini_file_name) && $wp_filesystem->is_file($ini_file_name)) {
                $ini_content = $wp_filesystem->get_contents($ini_file_name);
                $key_val_array = $this->parse_ini_content($ini_content);
                foreach ($key_val_array as $key => $value) {
                    if (!array_key_exists($key, $message_keys)) {
                        $message_keys[$key] = '';
                    }
                    $message_values[$locale . '_' . $key] = $value;
                }
            }
        }
        return array('keys' => $message_keys, 'values' => $message_values);
    }

    private function get_all_messages_grouped_by_locale()
    {
        global $wp_filesystem;
        $result = array();
        $languages = get_supported_languages();
        foreach ($languages as $lang_name => $locale) {
            $ini_file_name = FINESSE_THEME_PATH . '/lang/' . $locale . '.ini';
            if ($wp_filesystem->exists($ini_file_name) && $wp_filesystem->is_file($ini_file_name)) {
                $ini_content = $wp_filesystem->get_contents($ini_file_name);
                $key_val_array = $this->parse_ini_content($ini_content);
                $result[$locale] = $key_val_array;
            }
        }
        return $result;
    }

    private function get_list_of_locales()
    {
        $content = '<table cellspacing="0" class="wp-list-table widefat fixed media">';
        $content .= '<thead>';
        $content .= '<tr>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>Language</span></a></th>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>Locale</span></a></th>';
        $content .= '</tr>';
        $content .= '</thead>';

        $content .= '<tfoot>';
        $content .= '<tr>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>Language</span></a></th>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>Locale</span></a></th>';
        $content .= '</tr>';
        $content .= '</tfoot>';
        $content .= '<tbody id="the-list">';
        $languages = get_supported_languages();
        $i = 0;
        foreach ($languages as $key => $value) {
            $edit_url = 'admin-ajax.php?action=tm-update-locale-form&amp;locale=' . $value;
            $delete_url = 'action=tm-remove-locale&amp;locale=' . $value;
            $alternate_class = ($i % 2 == 0) ? 'alternate' : '';
            $content .= "<tr valign=\"top\" class=\"$alternate_class author-self status-inherit\">";
            $content .= '<td class="title column-title"><strong>' . $key . '</strong>';
            $content .= '<div class="row-actions"><span class="edit"><a class="thickbox" href="' . $edit_url . '">Edit</a> | </span><span class="delete"><a href="' . $delete_url . '" class="tm-delete-locale">Delete</a></span></div>';
            $content .= '</td>';
            $content .= '<td class="title column-title"><strong>' . $value . '</strong></td>';
            $i++;
        }
        $content .= '</tbody>';
        $content .= '</table>';
        return $content;
    }

    private function get_list_of_messages()
    {
        $content = '<table cellspacing="0" class="wp-list-table widefat fixed media">';
        $content .= '<thead>';
        $content .= '<tr>';
        $content .= '<th style="" class="manage-column column-response" scope="col"><span>Key</span></a></th>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>Message</span></a></th>';
        $content .= '<th style="" class="manage-column column-response" scope="col"><span>Actions</span></a></th>';
        $content .= '</tr>';
        $content .= '</thead>';

        $content .= '<tfoot>';
        $content .= '<tr>';
        $content .= '<th style="" class="manage-column column-response" scope="col"><span>Key</span></a></th>';
        $content .= '<th style="" class="manage-column column-title" scope="col"><span>Message</span></a></th>';
        $content .= '<th style="" class="manage-column column-response" scope="col"><span>Actions</span></a></th>';
        $content .= '</tr>';
        $content .= '</tfoot>';
        $content .= '<tbody id="the-list">';
        $languages = get_supported_languages();
        $all_messages = $this->get_all_messages_grouped_by_keys();
        $messages_keys = $all_messages['keys'];
        ksort($messages_keys);
        $messages_values = $all_messages['values'];
        $i = 0;

        foreach ($messages_keys as $key => $a) {
            $alternate_class = ($i % 2 == 0) ? 'alternate' : '';
            $key_id = uniqid();
            $tr_id = uniqid();

            $content .= "<tr id=\"$tr_id\" valign=\"top\" class=\"$alternate_class author-self status-inherit\">";
            $content .= '<td class="title column-title"><div><input type="text" id="' . $key_id . '" name="keys[]" value="' . $key . '" class="required tm-key"></div></td>';
            $content .= '<td class="title column-title">';
            foreach ($languages as $lang_name => $locale) {
                $message_value_key = $locale . '_' . $key;
                $message_value = array_key_exists($message_value_key, $messages_values) ? $messages_values[$message_value_key] : '';
                $message_value = htmlentities($message_value);
                $content .= '<div class="tm-msg-wrap"><input type="text" name="" value="' . $message_value . '" data-key-id="' . $key_id . '" data-locale="' . $locale . '" class="required tm-msg"> <strong>' . $lang_name . '</strong></div>';
            }
            $content .= '</td>';
            $content .= '<td class="title column-title">';
            $content .= '<div class="row-actions"><span class="delete"><a href="#" data-tr-id="' . $tr_id . '" data-key="' . $key . '" class="tm-delete-msg">Delete</a></span></div>';
            $content .= '</td>';
            $content .= '</tr>';
            $i++;
        }

        $content .= '</tbody>';
        $content .= '</table>';
        return $content;
    }

    function render_add_msg_form()
    {
        $supported_languages = get_supported_languages();
        $content = '<form id="tm-add-msg-form" class="generic-form" method="post" action="admin-ajax.php">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="key-id">' . __('Key', 'finesse') . ':</label>';
        $content .= '<input type="text" name="key" class="required">';
        $content .= '</div>';
        foreach ($supported_languages as $lang => $locale) {
            $field_id = $locale . '-msg';
            $content .= '<div>';
            $content .= '<label for="' . $field_id . '">' . $lang . ' ' . __('Message', 'finesse') . ':</label>';
            $content .= '<input type="text" id="' . $field_id . '" name="' . $field_id . '" class="required">';
            $content .= '</div>';
        }
        $content .= '<input type="hidden" name="action" value="tm-add-msg" >';
        $content .= '<input type="hidden" name="nonce" value="' . wp_create_nonce('tm-add-msg') . '" />';
        $content .= '<input type="hidden" name="_wp_http_referer" value="' . $_REQUEST['_wp_http_referer'] . '" />';
        $content .= '<input type="submit" id="tm-add-msg-form-submit" value="' . __('Add Message', 'finesse') . '" class="button-primary button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        echo $content;
        die();
    }

    function process_add_msg()
    {
        global $wp_filesystem;
        $key = $_POST['key'];
        $messages_grouped_by_locale = $this->get_all_messages_grouped_by_locale();
        $supported_languages = get_supported_languages();
        foreach ($supported_languages as $lang => $locale) {
            if (array_key_exists($locale, $messages_grouped_by_locale)) {
                $messages = $messages_grouped_by_locale[$locale];
            } else {
                $messages = array();
            }

            $msg_name = $locale . '-msg';
            if (isset($_POST[$msg_name])) {
                $messages[$key] = stripslashes($_POST[$msg_name]);
            }

            $ini_file_content = $this->generate_ini_content($messages);
            $ini_file_name = FINESSE_THEME_PATH . '/lang/' . $locale . '.ini';
            $wp_filesystem->put_contents($ini_file_name, $ini_file_content);
        }

        $referer = $_POST['_wp_http_referer'];
        $redirect_location = 'http://' . $_SERVER['HTTP_HOST'] . $referer . '&msg=s1';
        header('Location: ' . $redirect_location);
    }

    function process_update_all_msg()
    {
        global $wp_filesystem;
        if (isset($_POST['keys'])) {
            $keys = $_POST['keys'];
            $supported_languages = get_supported_languages();
            foreach ($supported_languages as $lang_name => $locale) {
                $kv_array = array();
                foreach ($keys as $key) {
                    $msg = '';
                    $msg_name = $locale . '-' . str_replace('.', '_', $key);
                    if (isset($_POST[$msg_name])) {
                        $msg = $_POST[$msg_name];
                    }
                    $kv_array[$key] = stripslashes($msg);
                }

                $ini_file_name = FINESSE_THEME_PATH . '/lang/' . $locale . '.ini';
                $ini_file_content = $this->generate_ini_content($kv_array);
                $wp_filesystem->put_contents($ini_file_name, $ini_file_content);
            }
        }

        $referer = $_POST['_wp_http_referer'];
        $redirect_location = 'http://' . $_SERVER['HTTP_HOST'] . $referer . '&msg=s2';
        header('Location: ' . $redirect_location);
    }

    function process_remove_msg()
    {
        global $wp_filesystem;
        $key = $_POST['key'];
        $messages_grouped_by_locale = $this->get_all_messages_grouped_by_locale();
        $supported_languages = get_supported_languages();
        foreach ($supported_languages as $lang => $locale) {
            if (array_key_exists($locale, $messages_grouped_by_locale)) {
                $messages = $messages_grouped_by_locale[$locale];
                if (array_key_exists($key, $messages)) {
                    unset($messages[$key]);
                }
                $ini_file_content = $this->generate_ini_content($messages);
                $ini_file_name = FINESSE_THEME_PATH . '/lang/' . $locale . '.ini';
                $wp_filesystem->put_contents($ini_file_name, $ini_file_content);
            }
        }
        die();
    }

    function process_remove_all_msg()
    {
        global $wp_filesystem;
        $supported_languages = get_supported_languages();
        foreach ($supported_languages as $lang_name => $locale) {
            $ini_file_name = FINESSE_THEME_PATH . '/lang/' . $locale . '.ini';
            $ini_file_content = $this->generate_ini_content(array());
            $wp_filesystem->put_contents($ini_file_name, $ini_file_content);
        }

        $referer = $_POST['_wp_http_referer'];
        $redirect_location = 'http://' . $_SERVER['HTTP_HOST'] . $referer . '&msg=s4';
        header('Location: ' . $redirect_location);
    }

    function process_import_db_msg()
    {
        global $wpdb;
        global $wp_filesystem;
        $table_name = $this->get_table_name();
        $rows = $wpdb->get_results("SELECT msg_key, msg, locale FROM $table_name order by locale, msg_key asc");

        $messages_grouped_by_locale = array();
        foreach ($rows as $i => $row) {
            $key = $row->msg_key;
            $msg = $row->msg;
            $locale = $row->locale;

            if (!array_key_exists($locale, $messages_grouped_by_locale)) {
                $messages_grouped_by_locale[$locale] = array();
            }

            $messages_grouped_by_locale[$locale][$key] = $msg;
        }

        foreach ($messages_grouped_by_locale as $locale => $messages) {
            $ini_file_name = FINESSE_THEME_PATH . '/lang/' . $locale . '.ini';
            $ini_file_content = $this->generate_ini_content($messages);
            $wp_filesystem->put_contents($ini_file_name, $ini_file_content);
        }

        $referer = $_POST['_wp_http_referer'];
        $redirect_location = 'http://' . $_SERVER['HTTP_HOST'] . $referer . '&msg=s5';
        header('Location: ' . $redirect_location);
    }

    function render_add_locale_form()
    {
        $content = '<form id="tm-add-locale-form" class="generic-form" method="post" action="admin-ajax.php">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="lang-id">' . __('Language', 'finesse') . ':</label>';
        $content .= '<input type="text" id="lang-id" name="lang" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="locale-id">' . __('Locale', 'finesse') . ':</label>';
        $content .= '<input type="text" id="locale-id" name="locale" class="small-text required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input type="hidden" name="action" value="tm-add-locale" >';
        $content .= '<input type="hidden" name="nonce" value="' . wp_create_nonce('tm-add-locale') . '" />';
        $content .= '<input type="hidden" name="_wp_http_referer" value="' . $_REQUEST['_wp_http_referer'] . '" />';
        $content .= '<input type="submit" id="tm-add-locale-form-submit" value="' . __('Add Language', 'finesse') . '" class="button-primary button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        echo $content;
        die();
    }

    function render_update_locale_form()
    {
        $locale = $_REQUEST['locale'];
        $lang = '';
        $languages = get_supported_languages();
        foreach ($languages as $key => $value) {
            if (strtolower($value) == strtolower($locale)) {
                $lang = $key;
            }
        }
        $content = '<form id="tm-update-locale-form" class="generic-form" method="post" action="admin-ajax.php">';
        $content .= '<fieldset>';
        $content .= '<div>';
        $content .= '<label for="lang-id">' . __('Language', 'finesse') . ':</label>';
        $content .= '<input type="text" id="lang-id" name="lang" value="' . $lang . '" class="required">';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<label for="locale-id">' . __('Locale', 'finesse') . ':</label>';
        $content .= '<input type="text" id="locale-id" name="l" class="small-text required" value="' . $locale . '" disabled>';
        $content .= '</div>';
        $content .= '<div>';
        $content .= '<input type="hidden" name="locale" value="' . $locale . '" >';
        $content .= '<input type="hidden" name="action" value="tm-update-locale" >';
        $content .= '<input type="hidden" name="nonce" value="' . wp_create_nonce('tm-update-locale') . '" />';
        $content .= '<input type="submit" id="tm-update-locale-form-submit" value="' . __('Update Language', 'finesse') . '" class="button-primary button-secondary">';
        $content .= '</div>';
        $content .= '</fieldset>';
        $content .= '</form>';
        echo $content;
        die();
    }

    function process_add_locale()
    {
        $referer = $_POST['_wp_http_referer'];
        $redirect_location = 'http://' . $_SERVER['HTTP_HOST'] . $referer;
        try {
            $lang = $_POST['lang'];
            $locale = $_POST['locale'];
            $languages = get_supported_languages();
            foreach ($languages as $key => $value) {
                if (strtolower($key) == strtolower($lang)) {
                    throw new Exception('e1');
                }
                if (strtolower($value) == strtolower($locale)) {
                    throw new Exception('e2');
                }
            }
            $languages[$lang] = $locale;
            save_supported_languages($languages);
            $redirect_location .= '&msg=s3';
        } catch (Exception $e) {
            $redirect_location .= '&msg=' . $e->getMessage();
        }
        header('Location: ' . $redirect_location);
    }

    function process_update_locale()
    {
        try {
            if (!isset($_POST['lang']) || empty($_POST['lang'])) {
                throw new Exception("The language name must not be empty!");
            }
            if (!isset($_POST['locale']) || empty($_POST['locale'])) {
                throw new Exception("The locale must not be empty!");
            }
            $lang = $_POST['lang'];
            $locale = $_POST['locale'];
            $languages = get_supported_languages();
            foreach ($languages as $key => $value) {
                if (strtolower($value) == strtolower($locale)) {
                    $old_key = $key;
                }
            }
            if (isset($old_key)) {
                unset($languages[$old_key]);
            }
            $languages[$lang] = $locale;
            save_supported_languages($languages);
            echo $this->get_list_of_locales();
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
        }
        die();
    }

    function process_remove_locale()
    {
        try {
            if (!isset($_POST['locale']) || empty($_POST['locale'])) {
                throw new Exception("The locale must not be empty!");
            }
            $locale = $_POST['locale'];
            $old_languages = get_supported_languages();
            $languages = array();
            foreach ($old_languages as $key => $value) {
                if (strtolower($value) != strtolower($locale)) {
                    $languages[$key] = $value;
                }
            }
            save_supported_languages($languages);
            echo $this->get_list_of_locales();
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
        }
        die();
    }

    private function parse_ini_content($ini_content)
    {
        $kv_array = array();
        $lines = explode("\n", $ini_content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line || empty($line) || start_with($line, '#')) {
                continue;
            }
            $key_val = explode('=', $line, 2);
            $kv_array[trim($key_val[0])] = trim($key_val[1]);
        }
        return $kv_array;
    }

    private function generate_ini_content($array)
    {
        $content = "";
        foreach ($array as $key => $elem) {
            if (empty($elem)) {
                $content .= "$key=\n";
            } else {
                $content .= "$key=$elem\n";
            }
        }
        return $content;
    }
}
