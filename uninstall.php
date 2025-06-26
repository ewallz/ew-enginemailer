<?php
// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$options = get_option('ew_enginemailer_options');
if (isset($options['delete_on_uninstall']) && $options['delete_on_uninstall']) {
    delete_option('ew_enginemailer_options');
}
