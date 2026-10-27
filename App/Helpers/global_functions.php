<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('QEUS_Query')) {
    function QEUS_Query()
    {
        // Load wp-fluent only once
        if (!class_exists('\WpFluent\QueryBuilder\QueryBuilderHandler')) {
            require_once QEUS_PLUGIN_DIR_PATH . 'App/Libs/wp-fluent/wp-fluent.php';
        }

        return QEUS_DB();
    }
}