<?php

namespace Ruhel241\QuickProductEnquiry\Classes;

class Customization 
{
    public static function quickProductEnquiryAddSettings($settings)
    {
        $settings[] = include_once QPEFW_PLUGIN_DIR_PATH . 'App/Classes/QuickProductEnquirySettings.php';

        return $settings ;
    }
}