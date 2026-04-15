<?php

/*
Plugin Name: Quick Enquiry Us - WooCommerce
Description: The Best Quick Enquiry Us Plugin for WooCommerce.
Version: 1.0.0
Author: wpcreativeidea
Author URI: https://wpcreativeidea.com/home
Plugin URI: https://wpcreativeidea.com/quick-enquiry-us
License: GPLv2 or later
Text Domain: quick-enquiry-us
Domain Path: /languages
*/

defined("ABSPATH") or die;
define('QUICK_ENQUIRY_US_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('QUICK_ENQUIRY_US_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('QUICK_ENQUIRY_US_PLUGIN_VERSION', '1.0.0');

class WooQuickEnquiryUsLite
{
    /**
     * Initialize the plugin
     */
    public function init()
    {
        // Quick Shop Bundle Plugin is Active
        if (defined('QUICK_SHOP_BUNDLE_PLUGIN')) {
            return;
        }
      
        // Global Functions
        include 'App/Helpers/global_functions.php';

        require_once __DIR__ . '/vendor/autoload.php';

        if (!$this->is_woocommerce_active()) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        $this->setup_hooks();
    }

    /**
     * Setup WordPress hooks
     */
    public function setup_hooks()
    {
        if (is_admin()) {
            $this->adminHooks();
        }

        $this->publicHooks();
        $this->commonHooks();
    }

    /**
     * Admin-specific hooks
     */
    public function adminHooks()
    {
       // woocommerce settings page add Quick Enquiry Us settings tab
        add_filter( 'woocommerce_get_settings_pages', array('QuickEnquiryUs\App\Classes\Customization', 'quickEnquiryUsAddSettings'), 15, 1 );
    
        $ajaxAdminAjaxHandler = new QuickEnquiryUs\App\Handler\AdminAjaxHandler();
        $ajaxAdminAjaxHandler->registerEndpoints();
        
        $licenseHandler = new QuickEnquiryUs\App\Handler\LicenseHandler();
        $licenseHandler->registerEndpoints();
    }

    /**
     * Public-facing hooks
     */
    public function publicHooks()
    {
    }

    public function commonHooks() {
       $ActionHandler = new QuickEnquiryUs\App\Handler\ActionHandler();
       $ActionHandler->register();
    }

    /**
     * Check if WooCommerce is active
     */
    public function is_woocommerce_active()
    {
        return class_exists('WooCommerce');
    }

    /**
     * Display notice if WooCommerce is not active
     */
    public function woocommerce_missing_notice()
    {
        ?>
        <div class="notice notice-error">
            <p><?php esc_html_e('WooCommerce Quick Enquiry Us requires WooCommerce to be installed and active.', 'quick-enquiry-us'); ?></p>
        </div>
        <?php
    }
};

add_action('plugins_loaded', function () {
    $WooQuickEnquiryUsLite = new WooQuickEnquiryUsLite();
    $WooQuickEnquiryUsLite->init();

    // Added textdomain
    load_plugin_textdomain( 'quick-enquiry-us', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
});

register_activation_hook(__FILE__, function ($network_wide) {
    require_once(QUICK_ENQUIRY_US_PLUGIN_DIR_PATH . 'App/Handler/ActivationHandler.php');
    $activate = new QuickEnquiryUs\App\Handler\ActivationHandler();
    $activate->activate($network_wide);
});