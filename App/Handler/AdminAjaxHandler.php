<?php

namespace Ruhel241\QuickProductEnquiry\Handler;

use Ruhel241\QuickProductEnquiry\Settings;
use Ruhel241\QuickProductEnquiry\Models\QuickProductEnquiry;
use Ruhel241\QuickProductEnquiry\Helpers\SanitizeValidation;

class AdminAjaxHandler
{
    public function registerEndpoints()
    {
        add_action('wp_ajax_qpefw_admin_ajax', array($this, 'ajaxRoutes'));
    }

    public function ajaxRoutes()
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        // Nonce already verified above. POST overrides GET, cookies are excluded.
        // phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
        $rawData = array_merge($_GET, $_POST);
        // phpcs:enable

        $route = sanitize_key(wp_unslash($rawData['route'] ?? ''));

        if (!$route) {
            wp_send_json_error(['message' => 'Invalid route'], 400);
        }

        $validRoutes = [
            'get_settings'         => 'getSettings',
            'save_settings'        => 'saveSettings',
            'enable_active_module' => 'enableActiveModule',
            'get_active_module'    => 'getActiveModule',
            'reset_settings'       => 'resetSettings',
            'get_qpefw_details'     => 'getQuickProductEnquiryDetails',
            'get_qpefw_detail_view' => 'getDetailView',
            'maybe_delete_details' => 'maybeDeleteDetails',
        ];

        if (!isset($validRoutes[$route])) {
            wp_send_json_error(['message' => 'Invalid route'], 400);
        }

        $validatedData = (new SanitizeValidation)->validateRequestData($rawData);

        do_action('qpefw_admin_ajax_handler_catch', $route);

        $this->{$validRoutes[$route]}($validatedData);

        wp_die();
    }

    public function getSettings($request)
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        $type = $request['type'] ?? '';
        $tab  = $request['tab'] ?? '';

        $getConfigs = (new Settings())->getConfigs();

        // Whitelist check
        if (!isset($getConfigs[$type][$tab])) {
            wp_send_json_error([
                'message' => __('Invalid settings type or tab', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        $configs = get_option($type, $getConfigs[$type]);
        
        wp_send_json_success([
            'configs' => $configs[$tab] ?? $getConfigs[$type][$tab],
        ]);
    }

    public function saveSettings($request)
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        $type = $request['type'] ?? '';
        $tab  = $request['tab'] ?? '';

        $getConfigs = (new Settings())->getConfigs();

        // Whitelist check
        if (!isset($getConfigs[$type][$tab])) {
            wp_send_json_error([
                'message' => __('Invalid settings type or tab', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        // Already sanitized and unslashed in SanitizeValidation
        $newSettings = $request['tab_configs'] ?? [];

        if (!is_array($newSettings)) {
            wp_send_json_error([
                'message' => __('Invalid settings data', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        $currentSettings = get_option($type, $getConfigs[$type]);

        if (!isset($currentSettings[$tab]) || !is_array($currentSettings[$tab])) {
            $currentSettings[$tab] = [];
        }

        // Merge new values into the current tab
        $currentSettings[$tab] = array_merge($currentSettings[$tab], $newSettings);

        update_option($type, $currentSettings);

        wp_send_json_success([
			'message'  => sprintf(
				/* translators: %s: Settings tab name, e.g. "General" or "Email". */
				__('%s saved successfully', 'quick-product-enquiry-for-woocommerce'),
				ucfirst($tab)
			),
			'settings' => $currentSettings,
		]);
    }

    public function enableActiveModule($request)
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        $type   = $request['type'] ?? '';
        $status = $request['status'] ?? '';

        // Only yes/no allowed
        if (!in_array($status, ['yes', 'no'], true)) {
            wp_send_json_error([
                'message' => __('Invalid status', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        $defaultModules   = (new Settings())->getActiveModule();
        $qpefwActiveModule = get_option('qpefw_active_module', $defaultModules);

        // Whitelist check
        if (!isset($qpefwActiveModule[$type])) {
            wp_send_json_error([
                'message' => __('Invalid module type', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        $qpefwActiveModule[$type] = $status;
        update_option('qpefw_active_module', $qpefwActiveModule);

        $message = ($status === 'no')
            ? __('Successfully disabled active module', 'quick-product-enquiry-for-woocommerce')
            : __('Successfully enabled active module', 'quick-product-enquiry-for-woocommerce');

        wp_send_json_success([
            'message'       => $message,
            'active_module' => $status,
        ]);
    }

    public function getActiveModule($request)
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        $type = $request['type'] ?? '';

        $defaultModules   = (new Settings())->getActiveModule();
        $qpefwActiveModule = get_option('qpefw_active_module', $defaultModules);

        if (!isset($qpefwActiveModule[$type])) {
            wp_send_json_error([
                'message' => __('Invalid module type', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        wp_send_json_success([
            'message'       => __('Enable active module', 'quick-product-enquiry-for-woocommerce'),
            'active_module' => $qpefwActiveModule[$type],
        ]);
    }

    public function resetSettings($request)
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        $type = $request['type'] ?? '';
        $tab  = $request['tab'] ?? '';

        $getDefaultConfigs = (new Settings())->getConfigs();

        // Whitelist check
        if (!isset($getDefaultConfigs[$type][$tab]) || !is_array($getDefaultConfigs[$type][$tab])) {
            wp_send_json_error([
                'message' => __('Invalid reset request', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        $currentSettings = get_option($type, $getDefaultConfigs[$type]);

        if (!is_array($currentSettings)) {
            $currentSettings = $getDefaultConfigs[$type];
        }

        // Reset this tab to default values
        $currentSettings[$tab] = $getDefaultConfigs[$type][$tab];

        update_option($type, $currentSettings);

       wp_send_json_success([
			'message'  => sprintf(
				/* translators: %s: Settings tab name, e.g. "General" or "Email". */
				__('Reset %s successfully', 'quick-product-enquiry-for-woocommerce'),
				ucfirst($tab)
			),
			'settings' => $currentSettings,
		]);
    }

    // Get QuickEnquiry Us addon Details
    public function getQuickProductEnquiryDetails($request)
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        $QuickProductEnquiry = new QuickProductEnquiry();
        $details        = $QuickProductEnquiry->getDetails($request);

        wp_send_json_success([
            'message' => __('Quick Product Enquiry for WooCommerce details get successfully.', 'quick-product-enquiry-for-woocommerce'),
            'data'    => $details,
        ], 200);
    }

    public function getDetailView($request)
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        $detailId = absint($request['detail_id'] ?? 0);

        if (!$detailId) {
            wp_send_json_error([
                'message' => __('Invalid detail id', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        $QuickProductEnquiry = new QuickProductEnquiry();
        $detail         = $QuickProductEnquiry->getDetail($detailId);

        wp_send_json_success([
            'message' => __('QuickEnquiry Us detail get successfully.', 'quick-product-enquiry-for-woocommerce'),
            'detail'  => $detail,
        ], 200);
    }

    public function maybeDeleteDetails($request)
    {
        if (!check_ajax_referer('qpefw_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Invalid nonce', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('Unauthorized access', 'quick-product-enquiry-for-woocommerce')
            ], 403);
        }

        $actionType = $request['action_type'] ?? '';

        if ($actionType !== 'delete') {
            wp_send_json_error([
                'message' => __('Invalid action type', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        $detailsIds = $request['details_ids'] ?? [];

        if (!is_array($detailsIds)) {
            $detailsIds = [$detailsIds];
        }

        // Only positive integers, no duplicates, no zeros
        $detailsIds = array_values(array_unique(array_filter(array_map('absint', $detailsIds))));

        if (empty($detailsIds)) {
            wp_send_json_error([
                'message' => __('No enquiry selected', 'quick-product-enquiry-for-woocommerce')
            ], 400);
        }

        $QuickProductEnquiry = new QuickProductEnquiry();
        $QuickProductEnquiry->deleteInfo($detailsIds);

        wp_send_json_success([
            'message' => __('Selected enquiry successfully deleted', 'quick-product-enquiry-for-woocommerce')
        ], 200);
    }
}