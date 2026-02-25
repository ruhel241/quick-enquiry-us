<?php

namespace QuickEnquiryUs\App\Database\Migrations;

class QuickEnquiryUsMigrator
{
    static $tableName = 'qeus_quick_enquiry_us';

    public static function migrate()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . static::$tableName;

        // Use prepared statement for the table check
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) != $table) {
            // For CREATE TABLE, we can use esc_sql for table name safety
            $safe_table = esc_sql($table);
            $sql = "CREATE TABLE `$safe_table` (
                `id` BIGINT(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `name` VARCHAR(255) NULL,
                `email` VARCHAR(255) NULL,
                `phone_number` VARCHAR(255) NULL,
                `enquiry` LONGTEXT NULL,
                `type` VARCHAR(120) NULL,
                `status` VARCHAR(120) NULL,
                `subscribe` VARCHAR(11) NULL,
                `settings` LONGTEXT NULL,
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL
            ) $charsetCollate;";

            // Make sure dbDelta is available
            dbDelta($sql);
        }
    }
}