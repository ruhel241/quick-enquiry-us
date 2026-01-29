<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!function_exists('QuickEnquiryUsQuery')) {
    function QuickEnquiryUsQuery()
    {
        if (!function_exists('QuickEnquiryUsDB')) {
            require_once QUICK_ENQUIRY_US_PLUGIN_DIR_PATH . 'app/Libs/wp-fluent/wp-fluent.php';
        }
       
        return QuickEnquiryUsDB();
    }
}
