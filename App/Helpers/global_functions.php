<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('QuickEnquiryUsQuery')) {
    function QuickEnquiryUsQuery()
    {
        // Load wp-fluent only once
        if (!class_exists('\WpFluent\QueryBuilder\QueryBuilderHandler')) {
            require_once QUICK_ENQUIRY_US_PLUGIN_DIR_PATH . 'App/Libs/wp-fluent/wp-fluent.php';
        }

        return QuickEnquiryUsDB();
    }
}