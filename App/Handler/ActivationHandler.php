<?php

namespace Ruhel241\QuickEnquiryUs\Handler;
use Ruhel241\QuickEnquiryUs\Settings;
use Ruhel241\QuickEnquiryUs\Database\DBMigrator;

class ActivationHandler
{
    public function activate($network_wide)
    {
        require_once(QEUS_PLUGIN_DIR_PATH . 'App/Settings.php');
        require_once(QEUS_PLUGIN_DIR_PATH . 'App/Database/DBMigrator.php');
        
        update_option('qeus_version', QEUS_PLUGIN_VERSION, 'no');

        $settings          = new Settings();
        $getConfigs        = $settings->getConfigs();
        $getActiveModule   = $settings->getActiveModule();
        $qeusLicense       = $settings->qeusLicense();

        foreach ($getConfigs as $key => $value) {
            if (!get_option($key)) {
                update_option($key, $value);
            }
        }

        if (!get_option('qeus_active_module')) {
            update_option('qeus_active_module', $getActiveModule);
        }

        if (!get_option('qeus_license')) {
            update_option('qeus_license', $qeusLicense);
        }

        // Run DB Migrations
        DBMigrator::run($network_wide);
    }
}