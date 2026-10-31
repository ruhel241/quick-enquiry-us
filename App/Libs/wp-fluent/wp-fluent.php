<?php defined('ABSPATH') or die;

// Autoload plugin.
require 'autoload.php';

if (! function_exists('QPEFW_DB')) {
    /**
     * @return \WpFluent\QueryBuilder\QueryBuilderHandler
     */
    function QPEFW_DB() {
        static $QPEFW_DB;

        if (! $QPEFW_DB) {
            global $wpdb;

            $connection = new \WpFluent\Connection($wpdb, ['prefix' => $wpdb->prefix]);

            $QPEFW_DB = new \WpFluent\QueryBuilder\QueryBuilderHandler($connection);
        }

        return $QPEFW_DB;
    }
}