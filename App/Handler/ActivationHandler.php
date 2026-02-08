<?php

namespace QuickEnquiryUs\App\Handler;
use QuickEnquiryUs\App\Settings;

class ActivationHandler
{
    public function activate($network_wide)
    {
        require_once(QUICK_ENQUIRY_US_PLUGIN_DIR_PATH . 'App/Settings.php');
        
        update_option('qeus_version', QUICK_ENQUIRY_US_PLUGIN_VERSION, 'no');

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
    }
}