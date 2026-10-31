<?php

namespace Ruhel241\QuickProductEnquiry\Handler;
use Ruhel241\QuickProductEnquiry\Settings;
use Ruhel241\QuickProductEnquiry\Database\DBMigrator;

class ActivationHandler
{
    public function activate($network_wide)
    {
        require_once(QPEFW_PLUGIN_DIR_PATH . 'App/Settings.php');
        require_once(QPEFW_PLUGIN_DIR_PATH . 'App/Database/DBMigrator.php');
        
        update_option('qpefw_version', QPEFW_PLUGIN_VERSION, 'no');

        $settings          = new Settings();
        $getConfigs        = $settings->getConfigs();
        $getActiveModule   = $settings->getActiveModule();
        $qpefwLicense       = $settings->qpefwLicense();

        foreach ($getConfigs as $key => $value) {
            if (!get_option($key)) {
                update_option($key, $value);
            }
        }

        if (!get_option('qpefw_active_module')) {
            update_option('qpefw_active_module', $getActiveModule);
        }

        if (!get_option('qpefw_license')) {
            update_option('qpefw_license', $qpefwLicense);
        }

        // Run DB Migrations
        DBMigrator::run($network_wide);
    }
}