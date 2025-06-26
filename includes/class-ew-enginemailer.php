<?php
// Core plugin class for EW Enginemailer

class EW_ENGINEMAILER {
    var $plugin_version = '1.0.0';
    var $plugin_url;
    var $plugin_path;

    function __construct() {
        define('EW_ENGINEMAILER_VERSION', $this->plugin_version);
        define('EW_ENGINEMAILER_SITE_URL', site_url());
        define('EW_ENGINEMAILER_HOME_URL', home_url());
        define('EW_ENGINEMAILER_URL', $this->plugin_url());
        define('EW_ENGINEMAILER_PATH', $this->plugin_path());
        $this->plugin_includes();
        $this->loader_operations();
    }

    function plugin_includes() {
        // Additional includes if needed
    }

    function loader_operations() {
        if (is_admin()) {
           # include_once(plugin_dir_path(__FILE__).'../addons/ew-enginemailer-addons.php');
        }
        add_action('plugins_loaded', array($this, 'plugins_loaded_handler'));
        add_action('admin_menu', array($this, 'options_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_filter('pre_wp_mail', 'ew_enginemailer_pre_wp_mail', 10, 2);
    }

    function enqueue_admin_scripts($hook) {
        if('settings_page_ew-enginemailer-settings' != $hook) {
            return;
        }
        #wp_register_style('ew-enginemailer-addons-menu', EW_ENGINEMAILER_URL.'/addons/ew-enginemailer-addons.css');
        #wp_enqueue_style('ew-enginemailer-addons-menu');
    }

    function plugins_loaded_handler() {
        if(is_admin() && current_user_can('manage_options')){
            add_filter('plugin_action_links', array($this, 'add_plugin_action_links'), 10, 2);
        }
        load_plugin_textdomain('ew-enginemailer', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    function plugin_url() {
        if ($this->plugin_url){
            return $this->plugin_url;
        }
        return $this->plugin_url = plugins_url(basename(plugin_dir_path(__DIR__)), basename(dirname(__DIR__)));
    }

    function plugin_path() {
        if ($this->plugin_path){
            return $this->plugin_path;
        }
        return $this->plugin_path = untrailingslashit(plugin_dir_path(__DIR__));
    }

    function add_plugin_action_links($links, $file) {
        if ($file == plugin_basename(dirname(__DIR__) . '/ew-enginemailer.php')) {
            $links[] = '<a href="options-general.php?page=ew-enginemailer-settings">'.__('Settings', 'ew-enginemailer').'</a>';
        }
        return $links;
    }

    function options_menu() {
        add_options_page(__('EW Enginemailer', 'ew-enginemailer'), __('EW Enginemailer', 'ew-enginemailer'), 'manage_options', 'ew-enginemailer-settings', array($this, 'options_page'));
    }

    function options_page() {
        // This will be handled in includes/settings.php
        do_action('ew_enginemailer_render_settings_page');
    }
}
