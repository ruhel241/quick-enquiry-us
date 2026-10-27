<?php

/*
Plugin Name: Quick Enquiry Us
Description: A simple product enquiry plugin for WooCommerce with WhatsApp, SMS, and enquiry form support.
Version: 1.0.0
Requires at least: 6.0
Requires PHP: 7.4
Author: ruhel241
Author URI: https://wpcreativeidea.com/
Plugin URI: https://wpcreativeidea.com/quick-enquiry-us-for-woocommerce/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: quick-enquiry-us
Domain Path: /languages
*/

defined("ABSPATH") or die;
define('QEUS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));
define('QEUS_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));
define('QEUS_PLUGIN_VERSION', '1.0.0');
define('QEUS_LITE', 'quick-enquiry-us');

class WooQuickEnquiryUsLite
{
    /**
     * Initialize the plugin
     */
    public function init()
    {
        // Quick Shop Bundle Plugin is Active
        // if (defined('QUICK_SHOP_BUNDLE_PLUGIN')) {
        //     return;
        // }

        // Check if WooCommerce is active
        if (!$this->is_woocommerce_active()) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
      
        // Global Functions
        include 'App/Helpers/global_functions.php';

        require_once __DIR__ . '/vendor/autoload.php';

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
        add_filter( 'woocommerce_get_settings_pages', array('Ruhel241\QuickEnquiryUs\Classes\Customization', 'quickEnquiryUsAddSettings'), 15, 1 );
    
        $ajaxAdminAjaxHandler = new Ruhel241\QuickEnquiryUs\Handler\AdminAjaxHandler();
        $ajaxAdminAjaxHandler->registerEndpoints();
        
        if ( defined('QEUS_PRO') && class_exists( \Ruhel241\QuickEnquiryUsPro\Classes\LicenseController::class ) ) {
            $licenseController = new \Ruhel241\QuickEnquiryUsPro\Classes\LicenseController();
            $licenseController->registerEndpoints();
		}
    }

    /**
     * Public-facing hooks
     */
    public function publicHooks()
    {
    }

    public function commonHooks() {
       $ActionHandler = new Ruhel241\QuickEnquiryUs\Handler\ActionHandler();
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
});

register_activation_hook(__FILE__, function ($network_wide) {
    require_once(QEUS_PLUGIN_DIR_PATH . 'App/Handler/ActivationHandler.php');
    $activate = new Ruhel241\QuickEnquiryUs\Handler\ActivationHandler();
    $activate->activate($network_wide);
});