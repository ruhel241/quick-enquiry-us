<?php

namespace QuickEnquiryUs\App\Classes;

use WC_Settings_Page;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
 /**
 * Settings class
 *
 * @since 1.0.0
 */
class QuickEnquiryUsSettings extends WC_Settings_Page
{
    /**
     * Setup settings class
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->id = 'quick-enquiry-us';
        $this->label = __('Quick Enquiry Us', 'quick-enquiry-us');

        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);
        add_action('woocommerce_settings_' . $this->id, array($this, 'output'));

        // Enqueue Vue.js for admin
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
    }

    /**
     * Output the settings
     *
     * @since 1.0
     */
    public function output()
    {
        echo wp_kses_post('<div id="wp_quick_enquiry_us_settings_app"></div>');
    }

    public function enqueueAdminScripts()
    {
        if (isset($_GET['tab']) && $_GET['tab'] === $this->id) {

            $assetsUrl = QUICK_ENQUIRY_US_PLUGIN_DIR_URL . 'assets/';

            wp_enqueue_script('qeus_admin_boot', $assetsUrl . 'admin/js/boot.js', array('jquery'), QUICK_ENQUIRY_US_PLUGIN_VERSION, false);
            wp_enqueue_script('qeus_admin_app', $assetsUrl . 'admin/js/start.js', array('jquery'), QUICK_ENQUIRY_US_PLUGIN_VERSION, false);
            wp_enqueue_style('qeus_admin_css', $assetsUrl . 'admin/css/admin.css', array(), QUICK_ENQUIRY_US_PLUGIN_VERSION);

            wp_localize_script('qeus_admin_app', 'QuickEnquiryUsAdminVars', array(
                'assets_url'  => $assetsUrl,
                'images_url'  => $assetsUrl.'admin/images/',
                'ajaxurl'     => admin_url('admin-ajax.php'),
                'admin_url'   => admin_url('post.php'),
                'slug'        => 'quick-enquiry-us',
                'site_url'    => get_site_url(),
                'nonce'       => wp_create_nonce('qeus_nonce'),
                'server_time' => current_time('mysql'),
                'woo_active'  => defined('WC_VERSION')
            ));
        }
    }
}

return new QuickEnquiryUsSettings();
