<?php

namespace QuickEnquiryUs\App\Database;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
use QuickEnquiryUs\App\Database\Migrations\QuickEnquiryUsMigrator;

class DBMigrator
{
    public static function run($network_wide = false)
    {
        global $wpdb;
        if ($network_wide) {
            // Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
            if (function_exists('get_sites') && function_exists('get_current_network_id')) {
                $site_ids = get_sites(array('fields' => 'ids', 'network_id' => get_current_network_id()));
            } else {
                $site_ids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM %s WHERE site_id = %s;", $wpdb->blogs, $wpdb->siteid ));
            }
            // Install the plugin for all these sites.
            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                static::migrate();
                restore_current_blog();
            }
        } else {
            static::migrate();
        }
    }

    private static function migrate()
    {
        require_once(QUICK_ENQUIRY_US_PLUGIN_DIR_PATH . 'App/Database/Migrations/QuickEnquiryUsMigrator.php');
        
        QuickEnquiryUsMigrator::migrate();
    }
}