<?php

namespace QuickEnquiryUs\App\Classes;

class Customization 
{
    public static function quickEnquiryUsAddSettings($settings)
    {
        $settings[] = include_once 'QuickEnquiryUsSettings.php';
        return $settings ;
    }
}