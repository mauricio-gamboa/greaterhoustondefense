<?php
if (is_user_logged_in()) {
    add_action('wp_ajax_finesse_process_form', 'finesse_process_form');
} else {
    add_action('wp_ajax_nopriv_finesse_process_form', 'finesse_process_form');
}
function finesse_process_form()
{
    $user_action = $_POST['ua'];
    $method_name = 'finesse_' . $user_action;
    if (function_exists($method_name)) {
        call_user_func($method_name);
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
        echo "No callable function " . $method_name . " found";
        die();
    }
}

if (!function_exists('finesse_process_contact_form')) {
    function finesse_process_contact_form()
    {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $email = $_POST['email'];
        $website = isset($_POST['website']) ? $_POST['website'] : '';
        $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
        $message = isset($_POST['message']) ? $_POST['message'] : '';

        try {
            $body = "<html><body><p>$message</p><p><a href=\"$website\">$website</a></p></body></html>";
            Email_Util::send_email_to_me($email, $name, $subject, $body);
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
        }
        die();
    }
}

if (!function_exists('finesse_process_newsletter_subscription')) {
    function finesse_process_newsletter_subscription()
    {
        global $newsletter_manager;
        try {
            if (isset($_POST['email'])) {
                $newsletter_manager->subscribe($_POST['email']);
            } else {
                throw new Exception('No email address was specified.');
            }
        } catch (Exception $e) {
            header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
            echo $e->getMessage();
        }
        die();
    }
}

