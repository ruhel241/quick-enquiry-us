<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('QPEFW_Query')) {
    function QPEFW_Query()
    {
        // Load wp-fluent only once
        if (!class_exists('\WpFluent\QueryBuilder\QueryBuilderHandler')) {
            require_once QPEFW_PLUGIN_DIR_PATH . 'App/Libs/wp-fluent/wp-fluent.php';
        }

        return QPEFW_DB();
    }
}