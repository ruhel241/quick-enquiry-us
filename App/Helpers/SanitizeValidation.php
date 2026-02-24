<?php

namespace QuickEnquiryUs\App\Helpers;

class SanitizeValidation
{
     /**
     * Validate and sanitize all request data
     */
    public function validateRequestData($requestData)
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
    public function sanitizeArrayData(array $array, array $textareaFields)
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
}
