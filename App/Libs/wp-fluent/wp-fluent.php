<?php defined('ABSPATH') or die;

// Autoload plugin.
require 'autoload.php';

if (! function_exists('QuickEnquiryUsDB')) {
    /**
     * @return \WpFluent\QueryBuilder\QueryBuilderHandler
     */
    function QuickEnquiryUsDB() {
        static $QuickEnquiryUsDB;

        if (! $QuickEnquiryUsDB) {
            global $wpdb;

            $connection = new \WpFluent\Connection($wpdb, ['prefix' => $wpdb->prefix]);

            $QuickEnquiryUsDB = new \WpFluent\QueryBuilder\QueryBuilderHandler($connection);
        }

        return $QuickEnquiryUsDB;
    }
}