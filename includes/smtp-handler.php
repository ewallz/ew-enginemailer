<?php
// SMTP handling logic for EW Enginemailer

add_filter('pre_wp_mail', 'ew_enginemailer_pre_wp_mail', 10, 2);

function ew_enginemailer_pre_wp_mail($null, $atts)
{
    $to = isset($atts['to']) ? $atts['to'] : '';
    if (!is_array($to)) {
        $to = explode(',', $to);
    }
    $subject = isset($atts['subject']) ? $atts['subject'] : '';
    $message = isset($atts['message']) ? $atts['message'] : '';
    $headers = isset($atts['headers']) ? $atts['headers'] : array();
    $attachments = isset($atts['attachments']) ? $atts['attachments'] : array();
    if (!is_array($attachments)) {
        $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
    }
    $options = ew_enginemailer_get_option();
    // If API key is set, try Enginemailer API first
    if (!empty($options['api_key'])) {
        $api_result = ew_enginemailer_send_via_api($to, $subject, $message, $headers, $attachments);
        if ($api_result === true) {
            return true;
        } elseif (!empty($options['enable_smtp_backup'])) {
            // If SMTP backup is enabled, try SMTP fallback
            return ew_enginemailer_send_via_smtp($to, $subject, $message, $headers, $attachments);
        } else {
            // If API fails and no SMTP backup, fallback to WP default
            return null;
        }
    }
    // If no API key, always use WP default
    return null;
}

function ew_enginemailer_send_via_smtp($to, $subject, $message, $headers, $attachments) {
    $options = ew_enginemailer_get_option();
    global $phpmailer;
    if (!($phpmailer instanceof PHPMailer\PHPMailer\PHPMailer)) {
        require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
        require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
        require_once ABSPATH . WPINC . '/class-wp-phpmailer.php';
        $phpmailer = new WP_PHPMailer(true);
        $phpmailer::$validator = static function ($email) {
            return (bool) is_email($email);
        };
    }
    $phpmailer->clearAllRecipients();
    $phpmailer->clearAttachments();
    $phpmailer->clearCustomHeaders();
    $phpmailer->clearReplyTos();
    $phpmailer->Body    = '';
    $phpmailer->AltBody = '';
    $from_name = isset($options['from_name']) ? $options['from_name'] : '';
    $from_email = isset($options['from_email']) ? $options['from_email'] : '';
    if(isset($options['force_from_address']) && !empty($options['force_from_address'])){
        $from_name = $options['from_name'];
        $from_email = $options['from_email'];
    }
    try {
        $phpmailer->setFrom($from_email, $from_name, false);
    } catch (PHPMailer\PHPMailer\Exception $e) {
        return false;
    }
    $phpmailer->Subject = $subject;
    $phpmailer->isHTML(true);
    $phpmailer->Body = $message;
    foreach ((array)$to as $address) {
        $phpmailer->addAddress(trim($address));
    }
    $phpmailer->isSMTP();
    $phpmailer->Host = $options['smtp_host'];
    if(isset($options['smtp_auth']) && $options['smtp_auth'] == "true"){
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $options['smtp_username'];
        $phpmailer->Password = base64_decode($options['smtp_password']);
    }
    $type_of_encryption = $options['type_of_encryption'];
    if($type_of_encryption=="none"){
        $type_of_encryption = '';
    }
    $phpmailer->SMTPSecure = $type_of_encryption;
    $phpmailer->Port = $options['smtp_port'];
    $phpmailer->SMTPAutoTLS = false;
    if(isset($_POST['ew_enginemailer_send_test_email'])){
        $phpmailer->SMTPDebug = 4;
        // Suppress PHPMailer debug output on the page. To show as HTML, use: $phpmailer->Debugoutput = 'html';
        // To log to error_log, use: $phpmailer->Debugoutput = 'error_log';
        $phpmailer->Debugoutput = function($str, $level) { /* suppressed */ };
    }
    if(isset($options['disable_ssl_verification']) && !empty($options['disable_ssl_verification'])){
        $phpmailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    }
    $phpmailer->ContentType = 'text/plain';
    if (isset($headers) && is_array($headers)) {
        foreach ($headers as $name => $content) {
            if (!in_array($name, array('MIME-Version', 'X-Mailer'), true)) {
                try {
                    $phpmailer->addCustomHeader(sprintf('%1$s: %2$s', $name, $content));
                } catch (PHPMailer\PHPMailer\Exception $e) {
                    continue;
                }
            }
        }
    }
    if (!empty($attachments)) {
        foreach ($attachments as $filename => $attachment) {
            $filename = is_string($filename) ? $filename : '';
            try {
                $phpmailer->addAttachment($attachment, $filename);
            } catch (PHPMailer\PHPMailer\Exception $e) {
                continue;
            }
        }
    }
    try {
        $send = $phpmailer->send();
        ew_enginemailer_log_delivery($send ? 'success' : 'failed', 'SMTP', $to, $send ? '' : $phpmailer->ErrorInfo);
        return $send;
    } catch (PHPMailer\PHPMailer\Exception $e) {
        ew_enginemailer_log_delivery('failed', 'SMTP', $to, $e->getMessage());
        return false;
    }
}

