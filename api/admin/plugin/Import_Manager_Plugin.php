<?php

class Import_Manager_Plugin extends Finesse_Plugin
{

    function register_actions()
    {
        add_action('wp_ajax_finesse-import-demo', array($this, 'import_demo'));
    }

    protected function activate()
    {
    }

    function render()
    {
        echo '<div class="wrap">';
        echo '<div class="icon32" id="icon-themes"></div>';
        echo '<h2>Finesse - ' . __('Import Demo', 'finesse') . '</h2>';

        echo '<div id="wait-message" class="updated below-h2" style="display: none"><p><strong>The demo import has started and will take a few moments. Please wait &hellip;</strong></p></div>';
        echo '<div id="success-message" class="updated below-h2" style="display: none"><p><strong>The data import has been successfully performed.</strong></p></div>';
        echo '<div id="error-message" class="error" style="display: none"><p><strong>Failed to import the demo. To see what were the problems activate the WordPress debug level.</strong></p></div>';
        echo '<div class="error"><p><strong>IMPORTANT</strong></p>';
        echo '<p>This plugin allows you to import the demo of the Finesse theme entirely (with all its settings).</p>';
        echo '<p>This import procedure will <strong>ERASE ALL DATA (pages, posts, comments, settings, users, etc.)</strong> from the database and insert new data.</p>';
        echo '<p>We strongly recommend you to <strong>RUN THIS IMPORT ON A NEW DATABASE</strong>.</p>';
        echo '<p>This plugin has been tested several times on our local environment (WordPress 3.5) and it works OK. We cannot guarantee that the import will work without problems in your case. This depends on your environment (database, WordPress version) settings.</p>';
        echo '<p><strong>PLEASE NOTE THAT WE ARE NOT RESPONSIBLE FOR ANY DATA LOSS</strong>.</p>';
        echo '</div>';
        echo '<div>';
        echo '<form id="newsletter-send-form" method="post" action="admin-ajax.php">';
        echo wp_referer_field(false);
        echo $this->get_form();
        echo '<p class="submit"><input id="import-demo-submit" type="submit" value="' . __('Import Demo', 'finesse') . '" class="button-primary"></p>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }

    private function get_form()
    {
        $content = '<table class="form-table"><tbody>';
        $content .= '<tr class="form-field form-required">';
        $content .= '<th scope="row"><label for="website-url"> ' . __('Site Address (URL)', 'finesse') . ' <span class="description">(required)</span></label></th>';
        $content .= '<td><input id="website-url" type="text" aria-required="true" value="" name="website-url" style="width: 25em"></td>';
        $content .= '</tr>';
        $content .= '<tr class="form-field form-required">';
        $content .= '<th scope="row"><label for="website-url"> ' . __('E-mail Address', 'finesse') . ' <span class="description">(required)</span></label></th>';
        $content .= '<td><input id="email-address" type="text" aria-required="true" value="" name="email-address" style="width: 25em"></td>';
        $content .= '</tr>';

        $content .= '</tbody></table>';

        $content .= '<input type="hidden" id="import-demo-action" name="action" value="finesse-import-demo" >';
        $content .= '<input type="hidden" id="import-demo-nonce" name="nonce" value="' . wp_create_nonce('finesse-import-demo') . '" />';
        return $content;
    }

    function get_title()
    {
        return __('Import Demo', 'finesse');
    }

    function get_slug()
    {
        return 'finesse-import-demo-manager';
    }

    function import_demo()
    {
        try {
            global $wpdb;
            $table_prefix = $wpdb->prefix;
            $website_url = trim($_REQUEST['website-url']);
            $email_address = trim($_REQUEST['email-address']);
            if (empty($website_url)) {
                throw new Exception('The Website URL must not empty');
            }
            if (empty($email_address)) {
                throw new Exception('The Email Address must not empty');
            }
            $props = array(
                'table_prefix' => $table_prefix,
                'website_url' => $website_url,
                'email_address' => $email_address,
            );
            $this->start_import_demo($props);
            die();
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Error");
            echo $e->getMessage();
            die();
        }
    }

    private function start_import_demo($props)
    {
        include_once (FINESSE_FUNCTIONS_FILE_PATH . '/api/admin/demo/demo_data.php');
        global $wpdb;
        $wp_tables = array(
            'wp_comments' => $wp_comments,
            'wp_finesse_fonts' => $wp_finesse_fonts,
            'wp_finesse_nl_subscription' => $wp_finesse_nl_subscription,
            'wp_links' => $wp_links,
            'wp_options' => $wp_options,
            'wp_postmeta' => $wp_postmeta,
            'wp_posts' => $wp_posts,
            'wp_terms' => $wp_terms,
            'wp_term_relationships' => $wp_term_relationships,
            'wp_term_taxonomy' => $wp_term_taxonomy,
            'wp_usermeta' => $wp_usermeta,
            'wp_users' => $wp_users,
        );
        $ok = true;
        foreach ($wp_tables as $table => $rows) {
            $initial_table = $table;
            $table = str_replace('wp_', $props['table_prefix'], $table);

            $this->clear_table_data($table);

            foreach ($rows as $row) {
                $column_values = array();
                $column_formats = array();

                foreach ($row as $column => $data) {
                    $column_values[$column] = $this->get_column_value($data, $props);
                    array_push($column_formats, $this->get_column_type($data));
                }

                $execute_sql = true;
                if (strtolower($initial_table) == 'wp_users' && $column_values['ID'] == 1) {
                    $execute_sql = false;
                }

                if ($execute_sql) {
                    $result = $wpdb->insert($table, $column_values, $column_formats);
                    if ($result === false) {
                        $ok = false;
                    }
                }
            }
        }

        if ($ok === false) {
            throw new Exception("The import of the demo database encountered some problems. To see what were the problems activate the WordPress debug level.");
        }
    }

    private function clear_table_data($table_name)
    {
        global $wpdb;
        if (end_with($table_name, '_users')) {
            $wpdb->query("DELETE FROM $table_name WHERE ID > 1");
        } else {
            $wpdb->query("DELETE FROM $table_name");
        }
    }

    private function get_column_type($data)
    {
        if (is_numeric($data)) {
            if (intval($data) == floatval($data)) {
                return '%d';
            }
            return '%f';
        } else {
            return '%s';
        }
    }

    private function get_column_value($data, $props)
    {
        if (is_numeric($data)) {
            if (intval($data) == floatval($data)) {
                return intval($data);
            }
            return floatval($data);
        } else {
            foreach ($props as $key => $value) {
                if ($key === 'email_address') {
                    $data = str_replace('info@somesite.com', $value, $data);
                } elseif ($key === 'website_url') {
                    $value = str_replace('http://', '', $value);
                    $data = str_replace('somesite.com/demo', $value, $data);
                } elseif ($key === 'table_prefix') {
                    $data = str_replace('wp_capabilities', $value . 'capabilities', $data);
                    $data = str_replace('wp_user_level', $value . 'user_level', $data);
                    $data = str_replace('wp_dashboard_quick_press_last_post_id', $value . 'dashboard_quick_press_last_post_id', $data);
                    $data = str_replace('wp_user-settings', $value . 'user-settings', $data);
                    $data = str_replace('wp_user-settings-time', $value . 'user-settings-time', $data);
                    $data = str_replace('wp_user_roles', $value . 'user_roles', $data);
                }
            }
            return $data;
        }
    }
}
