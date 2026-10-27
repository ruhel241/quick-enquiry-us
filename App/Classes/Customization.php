<?php

namespace WPCreativeIdea\QuickEnquiryUs\Classes;

class Customization 
{
    public static function quickEnquiryUsAddSettings($settings)
    {
        $settings[] = include_once QEUS_PLUGIN_DIR_PATH . 'App/Classes/QuickEnquiryUsSettings.php';

        return $settings ;
    }
}