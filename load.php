<?php

spl_autoload_register( function ( $class ) {
    $match = 'QuickEnquiryUs';
    if ( ! preg_match( "/\b{$match}\b/", $class ) ) {
        return;
    }

    $path = plugin_dir_path( __FILE__ );
    $file = str_replace(
        [ 'QuickEnquiryUs', '\\', '/App/' ],
        [ '', DIRECTORY_SEPARATOR, 'app/' ],
        $class
    );

    // Sanitize the file path to prevent directory traversal
    $file = preg_replace('/[^A-Za-z0-9_\-\/]/', '', $file);
    $file = str_replace('../', '', $file);

    $filePath = trailingslashit( $path ) . trim( $file, '/' ) . '.php';

    // Verify the file exists and is within the plugin directory
    if (file_exists($filePath) && strpos($filePath, realpath($path)) === 0) {
        require($filePath);
    }
});

if (!class_exists('ArrayHelper')) {
    require_once QUICK_ENQUIRY_US_PLUGIN_DIR_PATH . 'App/Helpers/ArrayHelper.php';
}
