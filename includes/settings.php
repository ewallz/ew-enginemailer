<?php
// Settings page and tab handlers for EW Enginemailer

add_action('ew_enginemailer_render_settings_page', 'ew_enginemailer_render_settings_page');

function ew_enginemailer_render_settings_page() {
    $plugin_tabs = array(
        'ew-enginemailer-settings' => __('General', 'ew-enginemailer'),
        'ew-enginemailer-settings&action=api-settings' => __('API Settings', 'ew-enginemailer'),
        'ew-enginemailer-settings&action=smtp-backup' => __('SMTP Backup', 'ew-enginemailer'),
        'ew-enginemailer-settings&action=test-email' => __('Test Email', 'ew-enginemailer'),
        'ew-enginemailer-settings&action=debug-info' => __('Debug Info', 'ew-enginemailer'),
        #'ew-enginemailer-settings&action=addons' => __('Add-ons', 'ew-enginemailer'),
        #'ew-enginemailer-settings&action=advanced' => __('Advanced', 'ew-enginemailer'),
    );
    $url = "https://www.ewallsolutions.com";
    $link_text = sprintf(__('Please visit the <a target="_blank" href="%s">EW Enginemailer</a> documentation page for setup instructions.', 'ew-enginemailer'), esc_url($url));
    $allowed_html_tags = array(
        'a' => array('href' => array(), 'target' => array())
    );
    echo '<div class="wrap"><h2>EW Enginemailer v' . EW_ENGINEMAILER_VERSION . '</h2>';
    echo '<div class="update-nag">'.wp_kses($link_text, $allowed_html_tags).'</div>';
    $current = '';
    $action = '';
    if (isset($_GET['page'])) {
        $current = sanitize_text_field($_GET['page']);
        if (isset($_GET['action'])) {
            $action = sanitize_text_field($_GET['action']);
            $current .= "&action=" . $action;
        }
    }
    $content = '';
    $content .= '<h2 class="nav-tab-wrapper">';
    foreach ($plugin_tabs as $location => $tabname) {
        $class = ($current == $location) ? ' nav-tab-active' : '';
        $content .= '<a class="nav-tab' . $class . '" href="?page=' . $location . '">' . $tabname . '</a>';
    }
    $content .= '</h2>';
    $allowed_html_tags = array(
        'a' => array('href' => array(), 'class' => array()),
        'h2' => array('href' => array(), 'class' => array())
    );
    echo wp_kses($content, $allowed_html_tags);
    if(!empty($action)) {
        switch($action) {
            case 'smtp-backup':
                ew_enginemailer_smtp_backup_settings();
                break;
            case 'test-email':
                ew_enginemailer_test_email_settings();
                break;
            case 'debug-info':
                ew_enginemailer_debug_info_settings();
                break;
            case 'addons':
                ew_enginemailer_display_addons();
                break;
            case 'advanced':
                ew_enginemailer_advanced_settings();
                break;
            case 'api-settings':
                ew_enginemailer_api_settings();
                break;
        }
    } else {
        ew_enginemailer_general_settings();
    }
    echo '</div>';
}

// Placeholders for tab handlers (to be filled in next steps)
function ew_enginemailer_test_email_settings() {
    $options = ew_enginemailer_get_option();
    if (!is_array($options)) {
        $options = ew_enginemailer_get_empty_options_array();
    }
    $default_subject = __('Test Email from EW Enginemailer', 'ew-enginemailer');
    $default_message = __('Hello,

This is a test email sent from your WordPress site using EW Enginemailer. If you received this message, your email configuration is working correctly.

Thank you!', 'ew-enginemailer');
    if(isset($_POST['ew_enginemailer_send_test_email'])){
        check_admin_referer('ew_enginemailer_test_email');
        $to = isset($_POST['ew_enginemailer_to_email']) ? sanitize_email($_POST['ew_enginemailer_to_email']) : '';
        $subject = isset($_POST['ew_enginemailer_email_subject']) ? sanitize_text_field($_POST['ew_enginemailer_email_subject']) : $default_subject;
        $message = isset($_POST['ew_enginemailer_email_body']) ? sanitize_text_field($_POST['ew_enginemailer_email_body']) : $default_message;
        $result = wp_mail($to, $subject, $message);
        if ($result) {
            echo '<div id="message" class="updated fade"><p><strong>';
            echo __('Test email sent successfully!', 'ew-enginemailer');
            echo '</strong></p></div>';
        } else {
            echo '<div id="message" class="error fade"><p><strong>';
            echo __('Failed to send test email.', 'ew-enginemailer');
            echo '</strong></p></div>';
        }
    } else {
        $subject = $default_subject;
        $message = $default_message;
    }
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ew_enginemailer_test_email'); ?>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="ew_enginemailer_to_email"><?php _e('To', 'ew-enginemailer');?></label></th>
                    <td><input name="ew_enginemailer_to_email" type="text" id="ew_enginemailer_to_email" value="" class="regular-text">
                        <p class="description"><?php _e('Email address of the recipient', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ew_enginemailer_email_subject"><?php _e('Subject', 'ew-enginemailer');?></label></th>
                    <td><input name="ew_enginemailer_email_subject" type="text" id="ew_enginemailer_email_subject" value="<?php echo esc_attr($subject); ?>" class="regular-text">
                        <p class="description"><?php _e('Subject of the email', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ew_enginemailer_email_body"><?php _e('Message', 'ew-enginemailer');?></label></th>
                    <td><textarea name="ew_enginemailer_email_body" id="ew_enginemailer_email_body" rows="6" class="regular-text"><?php echo esc_textarea($message); ?></textarea>
                        <p class="description"><?php _e('Email body', 'ew-enginemailer');?></p></td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="ew_enginemailer_send_test_email" id="ew_enginemailer_send_test_email" class="button button-primary" value="<?php _e('Send Email', 'ew-enginemailer');?>"></p>
    </form>
    <?php
}

function ew_enginemailer_debug_info_settings() {
    $options = ew_enginemailer_get_option();
    if (!is_array($options)) {
        $options = ew_enginemailer_get_empty_options_array();
    }
    // Server info
    $server_info = '';
    $server_info .= sprintf('PHP version: %s%s', PHP_VERSION, PHP_EOL);
    $server_info .= sprintf('WordPress version: %s%s', get_bloginfo('version'), PHP_EOL);
    $server_info .= sprintf('WordPress multisite: %s%s', (is_multisite() ? 'Yes' : 'No'), PHP_EOL);
    $openssl_status = 'Available';
    $openssl_text = '';
    if(!extension_loaded('openssl') && !defined('OPENSSL_ALGO_SHA1')){
        $openssl_status = 'Not available';
        $openssl_text = ' (openssl extension is required in order to use any kind of encryption like TLS or SSL)';
    }
    $server_info .= sprintf('openssl: %s%s%s', $openssl_status, $openssl_text, PHP_EOL);
    $server_info .= sprintf('allow_url_fopen: %s%s', (ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled'), PHP_EOL);
    $stream_socket_client_status = 'Not Available';
    $fsockopen_status = 'Not Available';
    $socket_enabled = false;
    if(function_exists('stream_socket_client')){
        $stream_socket_client_status = 'Available';
        $socket_enabled = true;
    }
    if(function_exists('fsockopen')){
        $fsockopen_status = 'Available';
        $socket_enabled = true;
    }
    $socket_text = '';
    if(!$socket_enabled){
        $socket_text = ' (In order to make a SMTP connection your server needs to have either stream_socket_client or fsockopen)';
    }
    $server_info .= sprintf('stream_socket_client: %s%s', $stream_socket_client_status, PHP_EOL);
    $server_info .= sprintf('fsockopen: %s%s%s', $fsockopen_status, $socket_text, PHP_EOL);
    // Delivery log
    $log = get_option('ew_enginemailer_delivery_log', array());
    if (!is_array($log)) $log = array();
    $log_lines = array();
    foreach ($log as $entry) {
        $log_lines[] = $entry['datetime'] . ' | ' . strtoupper($entry['status']) . ' | ' . $entry['method'] . ' | ' . $entry['to'] . ($entry['response'] ? ' | ' . $entry['response'] : '');
    }
    $log_text = implode("\n", $log_lines);
    ?>
    <h3><?php _e('Server Info', 'ew-enginemailer'); ?></h3>
    <textarea rows="10" cols="50" class="large-text code" readonly><?php echo esc_textarea($server_info);?></textarea>
    <h3><?php _e('Email Delivery Log (Latest 20 Entries)', 'ew-enginemailer'); ?></h3>
    <textarea rows="10" cols="50" class="large-text code" readonly><?php echo esc_textarea($log_text);?></textarea>
    <?php
}

function ew_enginemailer_advanced_settings() {
    echo '<div class="update-nag">' . __('Settings from add-ons will appear here.', 'ew-enginemailer') . '</div>';
    do_action('ew_enginemailer_advanced_settings_fields');
}

function ew_enginemailer_api_settings() {
    if (isset($_POST['ew_enginemailer_update_api_settings'])) {
        check_admin_referer('ew_enginemailer_api_settings');
        $api_key = isset($_POST['ew_enginemailer_api_key']) ? sanitize_text_field($_POST['ew_enginemailer_api_key']) : '';
        $options = ew_enginemailer_get_option();
        if (!is_array($options)) {
            $options = ew_enginemailer_get_empty_options_array();
        }
        $options['api_key'] = $api_key;
        ew_enginemailer_update_option($options);
        echo '<div id="message" class="updated fade"><p><strong>';
        echo __('API Settings Saved!', 'ew-enginemailer');
        echo '</strong></p></div>';
    }
    $options = ew_enginemailer_get_option();
    if (!is_array($options)) {
        $options = ew_enginemailer_get_empty_options_array();
    }
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ew_enginemailer_api_settings'); ?>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="ew_enginemailer_api_key"><?php _e('Enginemailer API Key', 'ew-enginemailer');?></label></th>
                    <td><input name="ew_enginemailer_api_key" type="text" id="ew_enginemailer_api_key" value="<?php echo esc_attr($options['api_key']); ?>" class="regular-text code">
                        <p class="description"><?php _e('Your Enginemailer API Key.', 'ew-enginemailer');?></p></td>
                </tr>
                <!-- API URL field removed, endpoint is now hardcoded in the code -->
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="ew_enginemailer_update_api_settings" id="ew_enginemailer_update_api_settings" class="button button-primary" value="<?php _e('Save Changes', 'ew-enginemailer')?>"></p>
    </form>
    <?php
}
function ew_enginemailer_general_settings() {
    $options = ew_enginemailer_get_option();
    if (!is_array($options)) {
        $options = ew_enginemailer_get_empty_options_array();
    }
    if (isset($_POST['ew_enginemailer_update_settings'])) {
        check_admin_referer('ew_enginemailer_general_settings');
        $from_email = isset($_POST['from_email']) ? sanitize_email($_POST['from_email']) : '';
        $from_name = isset($_POST['from_name']) ? sanitize_text_field(stripslashes($_POST['from_name'])) : '';
        $force_from_name = isset($_POST['force_from_name']) ? sanitize_text_field($_POST['force_from_name']) : '';
        $force_from_address = isset($_POST['force_from_address']) ? sanitize_text_field($_POST['force_from_address']) : '';
        $disable_ssl_verification = isset($_POST['disable_ssl_verification']) ? sanitize_text_field($_POST['disable_ssl_verification']) : '';
        $delete_on_uninstall = isset($_POST['ew_enginemailer_delete_on_uninstall']) ? 1 : 0;
        $options['from_email'] = $from_email;
        $options['from_name'] = $from_name;
        $options['force_from_name'] = $force_from_name;
        $options['force_from_address'] = $force_from_address;
        $options['disable_ssl_verification'] = $disable_ssl_verification;
        $options['delete_on_uninstall'] = $delete_on_uninstall;
        ew_enginemailer_update_option($options);
        echo '<div id="message" class="updated fade"><p><strong>';
        echo __('Settings Saved!', 'ew-enginemailer');
        echo '</strong></p></div>';
    }
    if (!isset($options['force_from_address'])) {
        $options['force_from_address'] = '';
    }
    if (!isset($options['disable_ssl_verification'])) {
        $options['disable_ssl_verification'] = '';
    }
    if (!isset($options['delete_on_uninstall'])) {
        $options['delete_on_uninstall'] = 0;
    }
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ew_enginemailer_general_settings'); ?>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="from_email"><?php _e('From Email Address', 'ew-enginemailer');?></label></th>
                    <td><input name="from_email" type="text" id="from_email" value="<?php echo esc_attr($options['from_email']); ?>" class="regular-text code">
                        <p class="description"><?php _e('The email address which will be used as the From Address if it is not supplied to the mail function.', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="from_name"><?php _e('From Name', 'ew-enginemailer');?></label></th>
                    <td><input name="from_name" type="text" id="from_name" value="<?php echo esc_attr($options['from_name']); ?>" class="regular-text code">
                        <p class="description"><?php _e('The name which will be used as the From Name if it is not supplied to the mail function.', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="force_from_name"><?php _e('Force From Name', 'ew-enginemailer');?></label></th>
                    <td><input name="force_from_name" type="checkbox" id="force_from_name" <?php checked($options['force_from_name'], 1); ?> value="1">
                        <p class="description"><?php _e('The From name in the settings will be set for all outgoing email messages.', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="force_from_address"><?php _e('Force From Address', 'ew-enginemailer');?></label></th>
                    <td><input name="force_from_address" type="checkbox" id="force_from_address" <?php checked($options['force_from_address'], 1); ?> value="1">
                        <p class="description"><?php _e('The From address in the settings will be set for all outgoing email messages.', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="disable_ssl_verification"><?php _e('Disable SSL Certificate Verification', 'ew-enginemailer');?></label></th>
                    <td><input name="disable_ssl_verification" type="checkbox" id="disable_ssl_verification" <?php checked($options['disable_ssl_verification'], 1); ?> value="1">
                        <p class="description"><?php _e('As of PHP 5.6 you will get a warning/error if the SSL certificate on the server is not properly configured. You can check this option to disable that default behaviour. Please note that PHP 5.6 made this change for a good reason. So you should get your host to fix the SSL configurations instead of bypassing it', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="ew_enginemailer_delete_on_uninstall"><?php _e('Delete all plugin data upon uninstall', 'ew-enginemailer');?></label></th>
                    <td><input name="ew_enginemailer_delete_on_uninstall" type="checkbox" id="ew_enginemailer_delete_on_uninstall" <?php checked($options['delete_on_uninstall'], 1); ?> value="1">
                        <p class="description"><?php _e('If enabled, all plugin settings will be removed from the database when the plugin is uninstalled.', 'ew-enginemailer');?></p></td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="ew_enginemailer_update_settings" id="ew_enginemailer_update_settings" class="button button-primary" value="<?php _e('Save Changes', 'ew-enginemailer')?>"></p>
    </form>
    <?php
}

function ew_enginemailer_smtp_backup_settings() {
    $options = ew_enginemailer_get_option();
    if (!is_array($options)) {
        $options = ew_enginemailer_get_empty_options_array();
    }
    if (isset($_POST['ew_enginemailer_update_smtp_backup'])) {
        check_admin_referer('ew_enginemailer_smtp_backup');
        $enable_smtp_backup = isset($_POST['ew_enginemailer_enable_smtp_backup']) ? 1 : 0;
        $smtp_host = isset($_POST['smtp_host']) ? sanitize_text_field($_POST['smtp_host']) : '';
        $smtp_auth = isset($_POST['smtp_auth']) ? sanitize_text_field($_POST['smtp_auth']) : '';
        $smtp_username = isset($_POST['smtp_username']) ? sanitize_text_field($_POST['smtp_username']) : '';
        $smtp_password = isset($_POST['smtp_password']) && !empty($_POST['smtp_password']) ? base64_encode(wp_unslash(trim($_POST['smtp_password']))) : '';
        $type_of_encryption = isset($_POST['type_of_encryption']) ? sanitize_text_field($_POST['type_of_encryption']) : '';
        $smtp_port = isset($_POST['smtp_port']) ? sanitize_text_field($_POST['smtp_port']) : '';
        $options['enable_smtp_backup'] = $enable_smtp_backup;
        $options['smtp_host'] = $smtp_host;
        $options['smtp_auth'] = $smtp_auth;
        $options['smtp_username'] = $smtp_username;
        if (!empty($smtp_password)) {
            $options['smtp_password'] = $smtp_password;
        }
        $options['type_of_encryption'] = $type_of_encryption;
        $options['smtp_port'] = $smtp_port;
        ew_enginemailer_update_option($options);
        echo '<div id="message" class="updated fade"><p><strong>';
        echo __('SMTP Backup Settings Saved!', 'ew-enginemailer');
        echo '</strong></p></div>';
    }
    $options = ew_enginemailer_get_option();
    if (!is_array($options)) {
        $options = ew_enginemailer_get_empty_options_array();
    }
    if (!isset($options['enable_smtp_backup'])) {
        $options['enable_smtp_backup'] = 0;
    }
    ?>
    <form method="post" action="">
        <?php wp_nonce_field('ew_enginemailer_smtp_backup'); ?>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label for="ew_enginemailer_enable_smtp_backup"><strong><?php _e('Enable SMTP Backup', 'ew-enginemailer');?></strong></label></th>
                    <td><input name="ew_enginemailer_enable_smtp_backup" type="checkbox" id="ew_enginemailer_enable_smtp_backup" <?php checked($options['enable_smtp_backup'], 1); ?> value="1">
                        <p class="description"><?php _e('If enabled, SMTP will be used as a fallback if the API is not working or unable to connect.', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="smtp_host"><?php _e('SMTP Host', 'ew-enginemailer');?></label></th>
                    <td><input name="smtp_host" type="text" id="smtp_host" value="<?php echo esc_attr($options['smtp_host']); ?>" class="regular-text code">
                        <p class="description"><?php _e('The SMTP server which will be used to send email. For example: smtp.gmail.com', 'ew-enginemailer');?></p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="smtp_auth"><?php _e('SMTP Authentication', 'ew-enginemailer');?></label></th>
                    <td>
                        <select name="smtp_auth" id="smtp_auth">
                            <option value="true" <?php echo selected($options['smtp_auth'], 'true', false);?>><?php _e('True', 'ew-enginemailer');?></option>
                            <option value="false" <?php echo selected($options['smtp_auth'], 'false', false);?>><?php _e('False', 'ew-enginemailer');?></option>
                        </select>
                        <p class="description"><?php _e('Whether to use SMTP Authentication when sending an email (recommended: True).', 'ew-enginemailer');?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="smtp_username"><?php _e('SMTP Username', 'ew-enginemailer');?></label></th>
                    <td><input name="smtp_username" type="text" id="smtp_username" value="<?php echo esc_attr($options['smtp_username']); ?>" class="regular-text code">
                        <p class="description"><?php _e('Your SMTP Username.', 'ew-enginemailer');?></p></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="smtp_password"><?php _e('SMTP Password', 'ew-enginemailer');?></label></th>
                    <td><input name="smtp_password" type="password" id="smtp_password" value="" class="regular-text code">
                        <p class="description"><?php _e('Your SMTP Password (The saved password is not shown for security reasons. If you do not want to update the saved password, you can leave this field empty when updating other options).', 'ew-enginemailer');?></p></td>
                </tr>
                <tr>
                    <th scope="row"><label for="type_of_encryption"><?php _e('Type of Encryption', 'ew-enginemailer');?></label></th>
                    <td>
                        <select name="type_of_encryption" id="type_of_encryption">
                            <option value="tls" <?php echo selected($options['type_of_encryption'], 'tls', false);?>><?php _e('TLS', 'ew-enginemailer');?></option>
                            <option value="ssl" <?php echo selected($options['type_of_encryption'], 'ssl', false);?>><?php _e('SSL', 'ew-enginemailer');?></option>
                            <option value="none" <?php echo selected($options['type_of_encryption'], 'none', false);?>><?php _e('No Encryption', 'ew-enginemailer');?></option>
                        </select>
                        <p class="description"><?php _e('The encryption which will be used when sending an email (recommended: TLS).', 'ew-enginemailer');?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="smtp_port"><?php _e('SMTP Port', 'ew-enginemailer');?></label></th>
                    <td><input name="smtp_port" type="text" id="smtp_port" value="<?php echo esc_attr($options['smtp_port']); ?>" class="regular-text code">
                        <p class="description"><?php _e('The port which will be used when sending an email (587/465/25). If you choose TLS it should be set to 587. For SSL use port 465 instead.', 'ew-enginemailer');?></p></td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="ew_enginemailer_update_smtp_backup" id="ew_enginemailer_update_smtp_backup" class="button button-primary" value="<?php _e('Save Changes', 'ew-enginemailer')?>"></p>
    </form>
    <?php
}
