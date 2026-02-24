<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Classes
include 'App/Classes/Customization.php';
// Handler
include 'App/Handler/ActionHandler.php';
include 'App/Handler/ActivationHandler.php';
include 'App/Handler/AdminAjaxHandler.php';
include 'App/Handler/LicenseHandler.php';
// Helpers
include 'App/Helpers/ArrayHelper.php';
include 'App/Helpers/SanitizeValidation.php';
// Settings
include 'App/Settings.php';
// Services
include 'App/Services/PluginManager/LicenseManager.php';
include 'App/Services/PluginManager/Updater.php';
// Models
include 'App/Models/QuickEnquiryUs.php';
// Global Functions
include  'App/global_functions.php';