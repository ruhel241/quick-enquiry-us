<?php

namespace QuickEnquiryUs\App\Handler;

use QuickEnquiryUs\App\Settings;
use QuickEnquiryUs\App\Models\QuickEnquiryUs;
use QuickEnquiryUs\App\Helpers\SanitizeValidation;

class AdminAjaxHandler
{
    public function registerEndpoints()
    {
        add_action('wp_ajax_qeus_admin_ajax', array($this, 'ajaxRoutes'));
    }

    public function ajaxRoutes()
    {
        // Validate nonce
        if (!isset($_REQUEST['nonce'])) {
            wp_send_json_error(['message' => __('Invalid nonce', 'quick-enquiry-us')], 403);
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), 'qeus_nonce')) {
            wp_send_json_error(['message' => __('Invalid quick enquiry us nonce', 'quick-enquiry-us')], 403);
        }

        $route = sanitize_text_field(wp_unslash($_REQUEST['route'] ?? ''));

        // Sanitize all incoming inputs and route, nonce, data validated to handlers
        $validatedData = (new SanitizeValidation)->validateRequestData($_REQUEST);

        $routes = [
            'get_settings'                     => 'getSettings',
            'save_settings'                    => 'saveSettings',
            'enable_active_module'             => 'enableActiveModule',
            'get_active_module'                => 'getActiveModule',
            'reset_settings'                   => 'resetSettings',
            'get_quick_enquiry_us_details'     => 'getQuickEnquiryUsDetails',
            'get_quick_enquiry_us_detail_view' => 'getDetailView',
            'maybe_delete_details'             => 'maybeDeleteDetails',
        ];

        if (isset($routes[$route])) {
            do_action('quick-enquiry-us/qeus_doing_ajax_forms_' . $route);
            $this->{$routes[$route]}($validatedData);
        }

        do_action('quick-enquiry-us/qeus_public_ajax_handler_catch', $route);
    }

    public function getSettings($request)
    {
        $getConfigs = (new Settings())->getConfigs();

        $type       = sanitize_text_field($request['type']);
        $tab        = sanitize_text_field($request['tab']);
        $configs    = get_option($type, $getConfigs[$type][$tab]);

        wp_send_json_success(array(
            'configs' => $configs[$tab]
        ));
    }

     public function saveSettings($request)
    {
        $newSettings = wp_unslash($request['tab_configs']);
        $type        = sanitize_text_field($request['type']); 
        $tab         = sanitize_text_field($request['tab']);  

        // Default config structure
        $getConfigs = (new Settings())->getConfigs();

        // Load existing option correctly
        $currentSettings = get_option($type, $getConfigs[$type]);

        // Ensure tab array exists
        if (!isset($currentSettings[$tab]) || !is_array($currentSettings[$tab])) {
            $currentSettings[$tab] = [];
        }

        // Merge new values
        $currentSettings[$tab] = array_merge($currentSettings[$tab], $newSettings);

        // ✅ Save back to SAME option name
        update_option($type, $currentSettings);

        $tabLabel = ucfirst($tab);

        wp_send_json_success([
            'message'  => sprintf(__('%s saved successfully', 'quick-enquiry-us'), $tabLabel),
            'settings' => get_option($type, $getConfigs),
        ]);
    }

    public function enableActiveModule($request) {
        $type   = sanitize_text_field($request['type']);
        $status = sanitize_text_field($request['status']);

        $getActiveModule = (new Settings())->getActiveModule();

        $qeusActiveModule = get_option('qeus_active_module', $getActiveModule);

        $message = __('Successfully enabled active module', 'quick-enquiry-us');

        // Update the specific module setting with the new value
        if (isset($qeusActiveModule[$type])) {
            $qeusActiveModule[$type] = $status;

            if ($status === 'no') {
                $message = __('Successfully disabled active module', 'quick-enquiry-us');
            }
        }

        update_option('qeus_active_module', $qeusActiveModule);

        $qeusActiveModule = get_option('qeus_active_module', $getActiveModule);

        wp_send_json_success(array(
            'message'  => $message,
            'active_module' => $qeusActiveModule[$type]
        ));
    }

    public function getActiveModule($request)
    {
        $type  = sanitize_text_field($request['type']);

        $getActiveDefaultModule = (new Settings())->getActiveModule();
        $qeusActiveModule        = get_option('qeus_active_module', $getActiveDefaultModule);

        wp_send_json_success(array(
            'message'  => __('Enable active module', 'quick-enquiry-us'),
            'active_module' => $qeusActiveModule[$type]
        ));
    }

    public function resetSettings() {
        $getDefaultConfigs = (new Settings())->getConfigs();

        $type = sanitize_text_field($_REQUEST['type']); 
        $tab  = sanitize_text_field($_REQUEST['tab']);  

        // Load saved option
        $currentSettings = get_option($type, $getDefaultConfigs[$type]);

        // Ensure tab exists in current settings and defaults
        if (
            isset($currentSettings[$tab]) && is_array($currentSettings[$tab]) &&
            isset($getDefaultConfigs[$type][$tab]) && is_array($getDefaultConfigs[$type][$tab])
        ) {

            // Reset this tab to default values
            $currentSettings[$tab] = $getDefaultConfigs[$type][$tab];

            // Save option
            update_option($type, $currentSettings);

            $tabLabel = ucfirst($tab);

            wp_send_json_success([
                'message'  => sprintf(__('Reset %s successfully', 'quick-enquiry-us'), $tabLabel),
                'settings' => get_option($type, $getDefaultConfigs[$type])
            ]);

        } else {

            wp_send_json_error([
                'message' => __('Invalid reset request', 'quick-enquiry-us')
            ]);
        }
    }
    
    // Get QuickEnquiry Us addon Details
    public function getQuickEnquiryUsDetails($request)
    {
        $QuickEnquiryUs = new QuickEnquiryUs();
        $details   = $QuickEnquiryUs->getDetails($request);

        wp_send_json_success([
            'message' => __('Quick enquiry us details get successfully.', 'quick-enquiry-us'),
            'data' => $details,
        ], 200);
    }

    public function getDetailView($request)
    {
        $QuickEnquiryUs = new QuickEnquiryUs();
        $detail   = $QuickEnquiryUs->getDetail($request['detail_id']);

        wp_send_json_success([
            'message' => __('QuickEnquiry Us detail get successfully.', 'quick-enquiry-us'),
            'detail' => $detail,
        ], 200);
    }

    public function maybeDeleteDetails($request)
    {
        $QuickEnquiryUs = new QuickEnquiryUs();

        $detailsIds    = $request['details_ids'];
        $actionType    = sanitize_text_field($request['action_type']);

        if ($actionType === 'delete') {
            $QuickEnquiryUs->deleteInfo($detailsIds);
            wp_send_json_success(array(
                'message' => __('Selected enquiry successfully deleted', 'quick-enquiry-us')
            ), 200);
        } else {
            wp_send_json_error(array(
                'message' => __('Invalid action type', 'quick-enquiry-us')
            ), 400);
        }
    }
}