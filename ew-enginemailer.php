<?php
/*
Plugin Name: EW Enginemailer
Version: 1.0.0
Plugin URI: https://www.ewallsolutions.com
Author: eWall Solutions
Author URI: https://www.ewallsolutions.com
Description: Configure and route all WordPress emails via Enginemailer API or SMTP.
Text Domain: ew-enginemailer
Domain Path: /languages
*/

if (!defined('ABSPATH')){
    exit;
}

// Load core files
require_once plugin_dir_path(__FILE__) . 'includes/class-ew-enginemailer.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/smtp-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/addons-loader.php';
require_once plugin_dir_path(__FILE__) . 'includes/options.php';

// Initialize the main plugin class
$GLOBALS['ew_enginemailer'] = new EW_ENGINEMAILER();
