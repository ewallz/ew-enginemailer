<?php
// Enginemailer API integration for EW Enginemailer

function ew_enginemailer_send_via_api($to, $subject, $message, $headers, $attachments) {
    $options = ew_enginemailer_get_option();
    $api_key = isset($options['api_key']) ? $options['api_key'] : '';
    $api_url = 'https://api.enginemailer.com/RESTAPI/V2/Submission/SendEmail';
    if (empty($api_key)) {
        return null;
    }
    // Prepare payload according to Enginemailer docs
    $payload = array(
        'ToEmail' => is_array($to) ? reset($to) : $to,
        'Subject' => $subject,
        'SenderEmail' => isset($options['from_email']) ? $options['from_email'] : '',
        'SenderName' => isset($options['from_name']) ? $options['from_name'] : '',
        'SubmittedContent' => $message,
    );
    // Optionally add CC, BCC, CampaignName, Attachments, etc. if needed
    $args = array(
        'headers' => array(
            'APIKey' => $api_key,
            'Content-Type' => 'application/json',
        ),
        'body' => wp_json_encode($payload),
        'timeout' => 15,
    );
    $response = wp_remote_post($api_url, $args);
    if (is_wp_error($response)) {
        error_log('[EW Enginemailer] API request error: ' . $response->get_error_message());
        ew_enginemailer_log_delivery('failed', 'API', $to, $response->get_error_message());
        return false;
    }
    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    error_log('[EW Enginemailer] API response code: ' . $code);
    error_log('[EW Enginemailer] API response body: ' . $body);
    $status = 'failed';
    $resp = '';
    $body_data = json_decode($body, true);
    if ($code >= 200 && $code < 300 && isset($body_data['Result']['StatusCode'])) {
        $resp = 'Status: ' . $body_data['Result']['Status'] . ' | StatusCode: ' . $body_data['Result']['StatusCode'];
        if ((int)$body_data['Result']['StatusCode'] >= 200 && (int)$body_data['Result']['StatusCode'] < 300) {
            $status = 'success';
        }
    } else {
        $resp = $body;
    }
    ew_enginemailer_log_delivery($status, 'API', $to, $resp);
    return $status === 'success';
}
