<?php defined('ABSPATH') or die;

// Autoload plugin.
require 'autoload.php';

if (! function_exists('QEUS_DB')) {
    /**
     * @return \WpFluent\QueryBuilder\QueryBuilderHandler
     */
    function QEUS_DB() {
        static $QEUS_DB;

        if (! $QEUS_DB) {
            global $wpdb;

            $connection = new \WpFluent\Connection($wpdb, ['prefix' => $wpdb->prefix]);

            $QEUS_DB = new \WpFluent\QueryBuilder\QueryBuilderHandler($connection);
        }

        return $QEUS_DB;
    }
}