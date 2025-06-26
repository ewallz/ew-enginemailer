<?php
// Options management for EW Enginemailer

function ew_enginemailer_get_option() {
    $options = get_option('ew_enginemailer_options');
    return $options;
}

function ew_enginemailer_update_option($new_options) {
    $empty_options = ew_enginemailer_get_empty_options_array();
    $options = ew_enginemailer_get_option();
    if(is_array($options)){
        $current_options = array_merge($empty_options, $options);
        $updated_options = array_merge($current_options, $new_options);
        update_option('ew_enginemailer_options', $updated_options);
    } else {
        $updated_options = array_merge($empty_options, $new_options);
        update_option('ew_enginemailer_options', $updated_options);
    }
}

function ew_enginemailer_get_empty_options_array() {
    $options = array();
    $options['smtp_host'] = '';
    $options['smtp_auth'] = '';
    $options['smtp_username'] = '';
    $options['smtp_password'] = '';
    $options['type_of_encryption'] = '';
    $options['smtp_port'] = '';
    $options['from_email'] = '';
    $options['from_name'] = '';
    $options['force_from_address'] = '';
    $options['disable_ssl_verification'] = '';
    $options['api_key'] = '';
    $options['api_url'] = '';
    $options['delete_on_uninstall'] = 0;
    $options['force_from_name'] = '';
    $options['enable_smtp_backup'] = 0;
    return $options;
}

function ew_enginemailer_is_configured() {
    $options = ew_enginemailer_get_option();
    $configured = true;
    if(!isset($options['smtp_host']) || empty($options['smtp_host'])){
        $configured = false;
    }
    if(!isset($options['smtp_auth']) || empty($options['smtp_auth'])){
        $configured = false;
    }
    if(isset($options['smtp_auth']) && $options['smtp_auth'] == "true"){
        if(!isset($options['smtp_username']) || empty($options['smtp_username'])){
            $configured = false;
        }
        if(!isset($options['smtp_password']) || empty($options['smtp_password'])){
            $configured = false;
        }
    }
    if(!isset($options['type_of_encryption']) || empty($options['type_of_encryption'])){
        $configured = false;
    }
    if(!isset($options['smtp_port']) || empty($options['smtp_port'])){
        $configured = false;
    }
    if(!isset($options['from_email']) || empty($options['from_email'])){
        $configured = false;
    }
    if(!isset($options['from_name']) || empty($options['from_name'])){
        $configured = false;
    }
    return $configured;
}

function ew_enginemailer_log_delivery($status, $method, $to, $response = '') {
    $log = get_option('ew_enginemailer_delivery_log', array());
    if (!is_array($log)) $log = array();
    $entry = array(
        'datetime' => date('Y-m-d H:i:s'),
        'status' => $status,
        'method' => $method,
        'to' => is_array($to) ? implode(',', $to) : $to,
        'response' => $response,
    );
    array_unshift($log, $entry);
    if (count($log) > 20) {
        $log = array_slice($log, 0, 20);
    }
    update_option('ew_enginemailer_delivery_log', $log);
}
