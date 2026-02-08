<?php

namespace QuickEnquiryUs\App\Handler;

use QuickEnquiryUs\App\Services\PluginManager\LicenseManager;

class LicenseHandler
{
    public function registerEndpoints()
    {
        add_action('wp_ajax_qeus_admin_license_ajax', array($this, 'ajaxRoutes'));
    }

    public function ajaxRoutes()
    {
        // Validate nonce
        if (!isset($_REQUEST['nonce'])) {
            wp_send_json_error(['message' => __('Invalid nonce', 'quick-enquiry-us')], 403);
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), 'qeus_nonce')) {
            wp_send_json_error(['message' => __('Invalid Quick Enquiry Us nonce', 'quick-enquiry-us')], 403);
        }

        $route = sanitize_text_field(wp_unslash($_REQUEST['route'] ?? ''));

        // Sanitize all incoming inputs
        $validatedData = $this->validateRequestData($_REQUEST);

        $routes = [
            'get_license_status'  => 'getLicenseStatus',
            'save_license'        => 'saveLicense',
            'deactivated_license' => 'deactivateLicense',
           
        ];

        if (isset($routes[$route])) {
            do_action('quick-enquiry-us/qeus_licnese_doing_ajax_forms_' . $route);
            $this->{$routes[$route]}($validatedData);
        }

        do_action('quick-enquiry-us/qeus_licnese_ajax_handler_catch', $route);
    }

     /**
     * Validate and sanitize all request data
     */
    private function validateRequestData($requestData)
    {
        $validated = [];
        $skipKeys  = ['nonce', 'route'];

        // ✅ textarea fields ONLY if explicitly sent
        $textareaFields = isset($requestData['textarea_fields']) && is_array($requestData['textarea_fields']) ? $requestData['textarea_fields'] : [];

        foreach ($requestData as $key => $value) {

            if (in_array($key, $skipKeys, true)) {
                continue;
            }

            // ❌ never save textarea_fields itself
            if ($key === 'textarea_fields') {
                continue;
            }

            if (is_array($value)) {
                $validated[$key] = $this->sanitizeArrayData($value, $textareaFields);

            } elseif (is_numeric($value)) {
                $validated[$key] = $value + 0;

            } else {
                // ❌ no textarea_fields → no HTML
                $validated[$key] = sanitize_text_field(wp_unslash($value));
            }
        }

        return $validated;
    }

    /**
     * Recursive array sanitization
     */
    private function sanitizeArrayData(array $array, array $textareaFields)
    {
        $sanitized = [];

        foreach ( $array as $key => $value ) {

            if ( is_array($value) ) {
                $sanitized[$key] = $this->sanitizeArrayData($value, $textareaFields);
            } elseif ( is_numeric($value) ) {
                $sanitized[$key] = $value + 0;
            } elseif ( !empty($textareaFields) && in_array($key, $textareaFields, true) ) {
                // ✅ textarea field → allow HTML
                $sanitized[$key] = wp_kses_post(wp_unslash($value));
            } else {
                $sanitized[$key] = sanitize_text_field(wp_unslash($value));
            }
        }

        return $sanitized;
    }
  
    public function getLicenseStatus()
    {
        $licenseManager = new LicenseManager();
        $licenseManager->verifyRemoteLicense(true);

        $data   = $licenseManager->getLicenseDetails();
        $status = $data['status'] ?? '';

        // Renew URL if expired
        if ($status === 'expired') {
            $data['renew_url'] = $licenseManager->getRenewUrl($data['license_key']);
        }

        // Mask license key
        if (!empty($data['license_key'])) {
            $visible = 10;
            $data['license_key'] =
                substr($data['license_key'], 0, $visible)
                . str_repeat('*', max(0, strlen($data['license_key']) - $visible));
        }

        $data['purchase_url'] = $licenseManager->getVar('purchase_url');
        /*
        * ============================
        * Expiry handling (FIXED)
        * ============================
        */
        if (!empty($data['expires'])) {

            // ✅ Lifetime license
            if ($data['expires'] === 'lifetime') {

                $data['expires_formatted'] = __('Lifetime', 'quick-enquiry-us');
                $data['human_date']        = __('Never expires', 'quick-enquiry-us');
                $data['time_label']        = __('Validity', 'quick-enquiry-us');

            } else {

                // ✅ Normal date-based license
                $expires_ts = strtotime($data['expires']);

                if ($expires_ts) {

                    $data['expires_formatted'] = wp_date(
                        get_option('date_format') . ' ' . get_option('time_format'),
                        $expires_ts
                    );

                    if ($status === 'expired') {
                        $data['human_date'] = human_time_diff($expires_ts, time()) . ' ' . __('ago', 'quick-enquiry-us');
                        $data['time_label'] = __('Expired Since', 'quick-enquiry-us');
                    }

                    if ($status === 'valid') {
                        $data['human_date'] = human_time_diff(time(), $expires_ts) . ' ' . __('left', 'quick-enquiry-us');
                        $data['time_label'] = __('Remaining', 'quick-enquiry-us');
                    }
                }
            }
        }

        wp_send_json_success([
            'license_data' => $data,
            'message'      => __('Get Data', 'quick-enquiry-us')
        ]);
    }


    public function saveLicense($request)
    { 
        $licenseKey = $request['license_key'];
       
        $licenseManager = new LicenseManager();

        $response = $licenseManager->activateLicense($licenseKey);

        if (is_wp_error($response)) {
            wp_send_json_error([
                'message' => $response->get_error_message()
            ]);
        }

        wp_send_json_success([
            'license_data' => $response,
            'message' => __('Your license key has been successfully updated', 'quick-enquiry-us')
        ]);

    }

    public function deactivateLicense()
    {
        $licenseManager = new LicenseManager();
       
        $response = $licenseManager->deactivateLicense();

        if(is_wp_error($response)) {
            wp_send_json_error([
                'message' => $response->get_error_message()
            ]);
        }

        unset($response['license_key']);

        wp_send_json_success([
            'license_data' => $response,
            'message' => __('Your license key has been successfully deactivated', 'quick-enquiry-us')
        ]);
    }
}
