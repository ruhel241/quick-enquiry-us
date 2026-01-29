<?php

namespace QuickEnquiryUs\App\Handler;

use QuickEnquiryUs\App\Helpers\ArrayHelper as Arr;
use QuickEnquiryUs\App\Models\QuickEnquiryUs;

class FrontendHandler
{
    public function register()
    {
        add_action('wp_footer', [$this, 'initHook']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('wc_ajax_qeus_quick_enquiry_us_ajax_actions', [$this, 'ajaxRoutes']);
        add_action('wc_ajax_nopriv_qeus_quick_enquiry_us_ajax_actions', [$this, 'ajaxRoutes']);
        // shop page add enquiry button
        add_action('woocommerce_after_shop_loop_item', [$this, 'addShopPageQuickEnquirysButton'], 30);
        // single page add enquiry button
        add_action('woocommerce_single_product_summary', [$this, 'addSinglePageQuickEnquirysButton']);
        // Add enquires ue button cart page and order summary
        add_filter('woocommerce_get_item_data',[$this, 'addQuickEnquirysButtonCartPageAndOrderSummary'], 10, 2);
       
        // shortcode register
        $this->registerShortcodes();

    }

    private function resolveProduct($productId = null, $productSlug = null)
    {
        // By ID
        if (!empty($productId) && is_numeric($productId)) {
            $product = wc_get_product((int) $productId);
            if ($product) {
                return $product;
            }
        }

        // By slug
        if (!empty($productSlug)) {
            $post = get_page_by_path($productSlug, OBJECT, 'product');
            if ($post) {
                return wc_get_product($post->ID);
            }
        }

        return null; // default enquiry
    }
    
    // shop page Enquiry Button
    public function addShopPageQuickEnquirysButton() {
        $quickEnquiryUsSettings = get_option('quick_enquiry_us_settings', []);
        $settings             = $quickEnquiryUsSettings['settings'];
        $shopPageEnable       = Arr::get($settings, 'shop_page_enable', 'yes' );
        $inStockButtonText    = Arr::get($settings, 'in_stock_shop_button_text', 'Enquiry Us');
        $outOfStockButtonText = Arr::get($settings, 'out_of_stock_shop_button_text', 'Email when stock available');
        $backOrderButtonText  = Arr::get($settings, 'backorder_shop_button_text', 'Pre-order Enquiry');

        if ( $shopPageEnable !== 'yes' ) {
            return;
        }

        global $product;

        if ( ! $product instanceof \WC_Product ) {
            return;
        }

        $productId   = $product->get_id();
        $title       = $product->get_title();
        $stockStatus = $product->get_stock_status();

        // Replace {title}
        $inStockButtonText    = str_replace('{title}', $title, $inStockButtonText);
        $outOfStockButtonText = str_replace('{title}', $title, $outOfStockButtonText);
        $backOrderButtonText  = str_replace('{title}', $title, $backOrderButtonText);

        /** OUT OF STOCK */
        if ( $stockStatus === 'outofstock' ) {
            echo '<a href="#" class="button qeus-quick-enquiry-us-button qeus-enquire-shop-btn-outofstock" data-product-id="' . esc_attr($productId) . '">'
                . esc_html($outOfStockButtonText) .
            '</a>';
            return;
        }

        /** BACKORDER */
        if ( $stockStatus === 'onbackorder' ) {
            echo '<a href="#" class="button qeus-quick-enquiry-us-button qeus-enquire-shop-btn-backorder" data-product-id="' . esc_attr($productId) . '">'
                . esc_html($backOrderButtonText) .
            '</a>';
            return;
        }

        /** IN STOCK */
        if ( $stockStatus === 'instock' ) {
            echo '<a href="#" class="button qeus-quick-enquiry-us-button qeus-enquire-shop-btn-instock" data-product-id="' . esc_attr($productId) . '">'
                . esc_html($inStockButtonText) .
            '</a>';
            return;
        }
    }

    // single page Enquiry Button
    public function addSinglePageQuickEnquirysButton() {

        $quickEnquiryUsSettings = get_option('quick_enquiry_us_settings', []);
        $settings             = $quickEnquiryUsSettings['settings'];
        $singlePageEnable     = Arr::get($settings, 'single_page_enable', 'yes');
        $inStockButtonText    = Arr::get($settings, 'in_stock_single_button_text', 'QuickEnquiry Us');
        $outOfStockButtonText = Arr::get($settings, 'out_of_stock_single_button_text', 'Email when stock available');
        $backOrderButtonText  = Arr::get($settings, 'backorder_single_button_text', 'Pre-order Enquiry');

        if ( $singlePageEnable !== 'yes' ) {
            return;
        }

        global $product;

        if ( ! $product instanceof \WC_Product ) {
            return;
        }

        $productId   = $product->get_id();
        $title       = $product->get_title();
        $stockStatus = $product->get_stock_status();

        // Replace {title}
        $inStockButtonText    = str_replace('{title}', $title, $inStockButtonText);
        $outOfStockButtonText = str_replace('{title}', $title, $outOfStockButtonText);
        $backOrderButtonText  = str_replace('{title}', $title, $backOrderButtonText);

        /** OUT OF STOCK */
        if ( $stockStatus === 'outofstock' ) {
            add_filter('woocommerce_get_availability_text', function ($availability) use ($outOfStockButtonText, $productId) {
                $button = '<br><br><a href="#" class="button qeus-quick-enquiry-us-button qeus-enquire-single-btn-outofstock" data-product-id="' . esc_attr($productId) . '">'
                            . esc_html($outOfStockButtonText) .
                        '</a>';
                return $availability . $button;
            }, 20);

            return;
        }

        /** BACKORDER */
        if ( $stockStatus === 'onbackorder' ) {
            add_action('woocommerce_after_add_to_cart_button', function () use ($backOrderButtonText, $productId) {
                echo '<a href="#" class="button qeus-quick-enquiry-us-button qeus-enquire-single-btn-backorder" data-product-id="' . esc_attr($productId) . '">'
                        . esc_html($backOrderButtonText) .
                    '</a>';
            }, 20);

            return;
        }

        /** IN STOCK */
        if ( $stockStatus === 'instock' ) {
            add_action('woocommerce_after_add_to_cart_button', function () use ($inStockButtonText, $productId) {
                echo '<a href="#" class="button qeus-quick-enquiry-us-button qeus-enquire-single-btn-instock" data-product-id="' . esc_attr($productId) . '">'
                        . esc_html($inStockButtonText) .
                    '</a>';
            }, 20);
            return;
        }
    }

    public function addQuickEnquirysButtonCartPageAndOrderSummary($item_data, $cart_item) {
        $quickEnquiryUsSettings         = get_option('quick_enquiry_us_settings', []);
        $settings                       = $quickEnquiryUsSettings['settings'];
        $cartPageEnquiryUsBtnText       = Arr::get($settings, 'cart_page_enquiry_us_button_text', 'QuickEnquiry us' );
        $cartPageEnquiryUsBtnEnable     = Arr::get($settings, 'cart_page_enquiry_us_btn_enable', 'yes' );
        $orderSummaryEnquiryUsBtnText   = Arr::get($settings, 'order_summary_enquiry_us_button_text', 'QuickEnquiry us' );
        $orderSummaryEnquiryUsBtnEnable = Arr::get($settings, 'order_summary_enquiry_us_btn_enable', 'yes' );
        
        if ( empty($cart_item['data']) ) {
            return $item_data;
        }

        // Detect contexts
        $isCart       = is_cart();
        $isCheckout   = is_checkout();
        $activeModules     = get_option('qeus_active_module', []);
        $isQuickCartPopupPluginActive = defined('QUICKCART_PLUGIN_VERSION') || ( $activeModules['quick_cart_popup_module'] ?? '' ) === 'yes';
       
        $buttonText = "";
        $buttonClass = "";

        // ---- Cart Page ----
        if ( $isCart ) {
            if ($cartPageEnquiryUsBtnEnable !== 'yes') {
                return $item_data;
            }
            $buttonText = $cartPageEnquiryUsBtnText;
            $buttonClass = 'qeus-enquiry-us-cart-page-btn';
        }

        // ---- Checkout Page ----
        elseif ( $isCheckout ) {
            if ($orderSummaryEnquiryUsBtnEnable !== 'yes') {
                return $item_data;
            }
            $buttonText = $orderSummaryEnquiryUsBtnText;
            $buttonClass = 'qeus-enquiry-us-order-summary-btn';
        }

        // ---- Quick Cart Popup ----
        elseif ( $isQuickCartPopupPluginActive ) {
            if ($orderSummaryEnquiryUsBtnEnable !== 'yes') {
                return $item_data;
            }
            $buttonText = $orderSummaryEnquiryUsBtnText;
            $buttonClass = 'qeus-enquiry-us-order-summary-btn';
        }

    
        if ( ! empty($buttonText) ) {
            $item_data[] = [
                'key'   => __('&nbsp;', 'quick-enquiry-us'),
                'value' => '<a href="#" class="button qeus-quick-enquiry-us-button '.esc_attr($buttonClass).'" data-product-slug="' . esc_attr($cart_item['data']->get_slug()) . '"> '.wp_kses_post($buttonText). '</a>'
            ];
        }

        return $item_data;
    }

    /**
     * Initialize modal HTML output
    */
    public function initHook()
    {   // enquiry us modal open handler
        $this->enquireUsModalHandler();
    }

    public function registerShortcodes()
    {   
        add_shortcode('quick_enquiry_us_button', function ($attr) {
            $builder =  $this->buttonRender($attr);
            return $builder; 
        });
    }

    public function buttonRender($attr)
    {
        $quickEnquiryUsSettings = get_option('quick_enquiry_us_settings', []);
        $settings      = $quickEnquiryUsSettings['settings'] ?? [];

        $buttonText = Arr::get($settings, 'shortcode_enquiry_us_button_text', 'QuickEnquiry us');

        // Default shortcode attributes
        $attr = shortcode_atts([
            'product_id'   => '',
            'product_slug' => '',
        ], $attr);

        ob_start();
        ?>
            <a href="#"
            class="button qeus-quick-enquiry-us-button qeus-shortcode-enquiry-us-btn"
            data-product-id="<?php echo intval($attr['product_id']); ?>"
            data-product-slug="<?php echo esc_attr($attr['product_slug']); ?>">
                <?php echo esc_html($buttonText); ?>
            </a>
        <?php
        return ob_get_clean();
    }

    /**
     * Master AJAX Router
     */
    public function ajaxRoutes()
    {
        // Validate nonce
        if (!isset($_REQUEST['nonce'])) {
            wp_send_json_error(['message' => __('Invalid nonce', 'quick-enquiry-us')], 403);
        }

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['nonce'])), 'quick_enquiry_us_nonce')) {
            wp_send_json_error(['message' => __('Invalid enquire us nonce', 'quick-enquiry-us')], 403);
        }

        $route = sanitize_text_field(wp_unslash($_REQUEST['route'] ?? ''));

        // Sanitize all incoming inputs
        $validatedData = $this->validateRequestData($_REQUEST);

        $routes = [
            'qeus_quick_enquiry_us_details' => 'quickEnquiryUsDetails',
            'qeus_quick_enquiry_us_form'    => 'quickEnquiryUsModalForm'
        ];

        if (isset($routes[$route])) {
            do_action('quick-enquiry-us/qeus_quick_enquiry_us_doing_ajax_forms_' . $route);
            $this->{$routes[$route]}($validatedData);
        }

        do_action('quick-enquiry-us/qeus_quick_enquiry_us_public_ajax_handler_catch', $route);
    }

    /**
     * Validate and sanitize all request data
     */
    private function validateRequestData($requestData)
    {
        $validated = [];
        $skipKeys = ['nonce', 'route'];

        foreach ($requestData as $key => $value) {
            if (in_array($key, $skipKeys, true)) {
                continue;
            }

            if (is_array($value)) {
                $validated[$key] = $this->sanitizeArrayData($value);
            } elseif (is_numeric($value)) {
                $validated[$key] = $value + 0;
            } else {
                $validated[$key] = sanitize_text_field(wp_unslash($value));
            }
        }

        return $validated;
    }

    /**
     * Recursive array sanitization
     */
    private function sanitizeArrayData($array)
    {
        $sanitized = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArrayData($value);
            } elseif (is_numeric($value)) {
                $sanitized[$key] = $value + 0;
            } else {
                $sanitized[$key] = sanitize_text_field(wp_unslash($value));
            }
        }

        return $sanitized;
    }

    /**
     * Enqueue CSS & JS
     */
    public function enqueueScripts()
    {
        wp_enqueue_style(
            "quick_enquiry_us_css",
            QUICK_ENQUIRY_US_PLUGIN_DIR_URL . "assets/public/css/quick-enquiry-us.css",
            [],
            QUICK_ENQUIRY_US_PLUGIN_VERSION
        );

        wp_enqueue_script(
            "quick_enquiry_us_js",
            QUICK_ENQUIRY_US_PLUGIN_DIR_URL . "assets/public/js/quick-enquiry-us.js",
            ['jquery'],
            QUICK_ENQUIRY_US_PLUGIN_VERSION,
            true
        );

        $this->styleHandler();

        $settings          = get_option('quick_enquiry_us_settings', []);
        $activeModules     = get_option('qeus_active_module', []);

        wp_localize_script('quick_enquiry_us_js', 'QuickEnquiryUsPublicVars', [
            'ajaxurl'            => admin_url('admin-ajax.php'),
            'site_url'           => get_site_url(),
            'nonce'              => wp_create_nonce('quick_enquiry_us_nonce'),
            'get_active_modules' => $activeModules,
            'quick_enquiry_us_settings'=> $settings,
        ]);
    }

    public function quickEnquiryUsModalForm($request)
    {
        $productId   = $request['product_id'] ?? null;
        $productSlug = $request['product_slug'] ?? null;

        $product = $this->resolveProduct($productId, $productSlug);

        wp_send_json_success([
            'message'   => 'QuickEnquiry form loaded',
            'fragments' => [
                '.qeus-form-container' => $this->enquiryUsHtml($product),
            ],
        ]);
    }


    /**
     * Modal HTML Output
    */
    public function enquireUsModalHandler()
    {   ?>
            <div class="qeus-quick-enquiry-us-modal-wrapper" style="display:none;">
                <div class="qeus-modal-content">
                    <?php echo $this->enquiryUsHtml(); ?>
                    <!-- loading -->
                    <div id="qeus-loading-view" style="display: none;">
                        <div class="qeus-loading-spinner">
                            <img src="<?php echo QUICK_ENQUIRY_US_PLUGIN_DIR_URL . 'assets/public/images/loading.gif' ?>" alt="">
                        </div>
                        <h2 class="qeus-loading-title">Loading...</h2>
                    </div>
                </div>
            </div>
        <?php
    }

    public function enquiryUsHtml($product = null) 
    {
        
        $quickEnquiryUsSettings = get_option('quick_enquiry_us_settings', []);
        $settings      =  $quickEnquiryUsSettings['settings'];

        // -------- SAFE PRODUCT HANDLING --------
        $isDefaultEnquiry = ! ($product instanceof \WC_Product);

        if ($isDefaultEnquiry) {
            $title     = __('General Enquiry', 'quick-enquiry-us');
            $productId = 0;
        } else {
            $title     = $product->get_title();
            $productId = $product->get_id();
        }

        $inStockFormHeading    = Arr::get($settings, 'in_stock_form_heading', 'QuickEnquiry Us');
        $outOfStockFormHeading = Arr::get($settings, 'out_of_stock_form_heading', 'Email when stock available');
        $backOrderFormHeading  = Arr::get($settings, 'backorder_form_heading', 'Pre-order Enquiry');
        $enquiryFromTabEnable  = Arr::get($settings, 'enquiry_from_tab_enable', 'no' );
        $enquirySubmitButtonText = Arr::get($settings, 'enquiry_submit_button_text', 'Submit Enquiry' );
        $whatsappTabEnable     = Arr::get($settings, 'whatsapp_tab_enable', 'no' );
        $smsTabEnable          = Arr::get($settings, 'sms_tab_enable', 'no' );
        $enquiryTabText        = Arr::get($settings, 'enquiry_tab_text', 'Enquiry Form' );
        $whatsappTabText       = Arr::get($settings, 'whatsapp_tab_text', 'Contact on whatsapp' );
        $whatsappTitleText     = Arr::get($settings, 'whatsapp_title_text', 'Chat with us on WhatsApp' );
        $whatsappButtonText    = Arr::get($settings, 'whatsapp_button_text', 'Chat on WhatsApp' );
        $whatsappNumbers       = Arr::get($settings, 'whatsapp_numbers', []);
        $smsTabText            = Arr::get($settings, 'sms_tab_text', 'Send SMS' );
        $smsSubmitButtonText   = Arr::get($settings, 'sms_submit_button_text', 'Send SMS' );

        // /** Replace {title} placeholder */
        $inStockFormHeading    = str_replace('{title}', $title, $inStockFormHeading);
        $outOfStockFormHeading = str_replace('{title}', $title, $outOfStockFormHeading);
        $backOrderFormHeading  = str_replace('{title}', $title, $backOrderFormHeading);

        // Button Text
        if (!$isDefaultEnquiry && $product->is_on_backorder(1)) {
            $buttonText = $backOrderFormHeading;
        } elseif (!$isDefaultEnquiry && !$product->is_in_stock()) {
            $buttonText = $outOfStockFormHeading;
        } else {
            $buttonText = $inStockFormHeading;
        }

        // Decide first active tab (priority order)
        $activeTab = '';

        if ($enquiryFromTabEnable === 'yes') {
            $activeTab = 'email';
        } elseif ($whatsappTabEnable === 'yes') {
            $activeTab = 'whatsapp';
        } elseif ($smsTabEnable === 'yes') {
            $activeTab = 'sms';
        }

         /** Modal HTML */

        ob_start();
        ?>
            <div class="qeus-form-container">
                <div class="qeus-header">
                    <h2 class="qeus-title"><?php echo esc_html($buttonText);?></h2>
                    <span class="qeus-view-close">x</span>
                </div>
                 <!-- Tabs -->
                <div class="qeus-tabs">
                    <?php if ($enquiryFromTabEnable === 'yes'): ?>
                        <div class="qeus-tab <?php echo esc_attr( $activeTab === 'email' ? 'active' : '' ); ?>" data-tab="email">
                            📝 <?php echo esc_html($enquiryTabText); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($whatsappTabEnable === 'yes'): ?> 
                        <div class="qeus-tab <?php echo esc_attr( $activeTab === 'whatsapp' ? 'active' : '' ); ?>" data-tab="whatsapp">
                            📞 <?php echo esc_html($whatsappTabText); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($smsTabEnable === 'yes'): ?>
                        <div class="qeus-tab <?php echo esc_attr( $activeTab === 'sms' ? 'active' : '' ); ?>" data-tab="sms">
                            💬 <?php echo esc_html($smsTabText); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="qeus-tab-content <?php echo esc_attr( $activeTab === 'email' ? 'active' : '' ); ?>" id="qeus-tab-email">
                    <?php echo $this->enquireFormHtml($product, 'email', $enquirySubmitButtonText);?>
                </div>
                <div class="qeus-tab-content <?php echo esc_attr( $activeTab === 'whatsapp' ? 'active' : '' ); ?>" id="qeus-tab-whatsapp">
                    <div class="qeus-tab-box">
                        <p><?php echo esc_html($whatsappTitleText); ?></p>
                        <div class="qeus-social-numbers">
                            <?php 
                                foreach ($whatsappNumbers as $number) : 
                                    $receiverName   = isset($number['receiver_name']) ? $number['receiver_name'] : 'Support';
                                    $receiverNumber = isset($number['receiver_number']) ? $number['receiver_number'] : '';  
                            ?>
                                <div class="qeus-social-number">
                                    <p><?php echo esc_html($receiverName); ?></p>
                                    <a href="<?php echo esc_url( $this->whatsAppApi($receiverName, $receiverNumber, $product, $settings) ); ?>" target="_blank" class="qeus-action-btn whatsapp">
                                        📞 <?php echo esc_html($whatsappButtonText); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="qeus-tab-content <?php echo $activeTab === 'sms' ? 'active' : ''; ?>" id="qeus-tab-sms">
                    <?php echo $this->enquireFormHtml($product, 'sms', $smsSubmitButtonText);?>
                </div>
                <div class="qeus-tab-content <?php echo empty($activeTab) ? 'active' : ''; ?>" id="qeus-tab-empty">
                    <p>No enquiry options are available at the moment. Please check back shortly.</p>
                </div>
            </div>
        <?php
        return ob_get_clean();
    }

    private function enquireFormHtml($product, $tab, $submitButtonText) {
        // Logged user
        $current_user = wp_get_current_user();
        $userName     = is_user_logged_in() ? $current_user->display_name : '';
        $userEmail    = is_user_logged_in() ? $current_user->user_email   : '';
        // Product ID
      
        $productId = ($product instanceof \WC_Product) ? $product->get_id() : 0;

        ob_start();
        
      ?>
        <div class="qeus-notice">
            <p class="qeus-response-message"></p>
        </div>
        <form action="#" method="post" class="qeus-enquire-form">
            <div class="qeus-form-field">
                <label class="qeus-label-name" for="qeus-name-<?php echo esc_attr($tab);?>">Your Name <span class="queb-required-filed">*</span></label>
                <input type="text" name="name"
                    id="qeus-name-<?php echo esc_attr($tab);?>"
                    placeholder="Enter Your Name"
                    value="<?php echo esc_attr($userName); ?>"
                    <?php echo $userName ? '' : 'required'; ?>>
            </div>
            <div class="qeus-form-field">
                <label class="qeus-label-email" for="qeus-email-<?php echo esc_attr($tab);?>">
                    Email Address
                    <?php if ($tab == 'email') : ?>
                        <span class="queb-required-filed">*</span>
                    <?php endif; ?>
                </label>
                <input type="email" name="email"
                    id="qeus-email-<?php echo esc_attr($tab);?>"
                    placeholder="Email Address"
                    value="<?php echo esc_attr($userEmail); ?>"
                    <?php echo ($tab == 'email') ? 'required' : ''; ?>>
            </div>
            <div class="qeus-form-field">
                <label class="qeus-label-phone" for="qeus-phone-<?php echo esc_attr($tab);?>">
                    Phone Number 
                    <?php if ($tab == 'sms') : ?>
                        <span class="queb-required-filed">*</span>
                    <?php endif; ?>
                </label>
                <?php
                    $getCountries = $this->getCountries();
                ?>
                <div class="qeus-phone-number">
                    <select name="country_code">
                        <option value=""> Select Country</option>
                        <?php foreach($getCountries as $country) : ?>
                            <option value="<?php echo esc_attr($country['code']); ?>">
                                <?php echo esc_html($country['flag']); ?>
                                (<?php echo esc_html($country['code']); ?>)
                                <?php echo esc_html($country['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="phone_number"
                        id="qeus-phone-<?php echo esc_attr($tab);?>"
                        placeholder="Enter Your Phone Number"
                        pattern="[0-9]*"
                        <?php echo ($tab == 'sms') ? 'required' : ''; ?>>
                </div>
            </div>
            <div class="qeus-form-field">
                <input type="hidden" name="product_id" value="<?php echo esc_attr($productId); ?>">
            </div>
            <div class="qeus-form-field">
                <label class="qeus-label-enquiry" for="qeus-enquiry-<?php echo esc_attr($tab);?>">Enquiry <span class="queb-required-filed">*</span></label>
                <textarea name="enquiry" id="qeus-enquiry-<?php echo esc_attr($tab);?>" placeholder="Write Your Enquiry..." required></textarea>
            </div>
            <div class="qeus-form-field qeus-subscribe-field">
                <input type="checkbox" id="subscribe-<?php echo esc_attr($tab);?>" name="subscribe" value="yes">
                <label class="qeus-label-subscribe" for="subscribe-<?php echo esc_attr($tab);?>">Subscribe to our newsletter for updates and offers.</label>
            </div>
            <div class="qeus-form-field qeus-submit-btn">
                <button type="submit" class="qeus-submit"><?php echo esc_html($submitButtonText); ?></button>
            </div>
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * Handle form submission
     */
    public function quickEnquiryUsDetails($request)
    {
        $QuickEnquiryUs = new QuickEnquiryUs();
        $quickEnquiryUsSettings = get_option('quick_enquiry_us_settings', []);
        $settings      =  $quickEnquiryUsSettings['settings'];
        
        // If you stringified in JS, decode here
        $details = Arr::get($request, 'details', []);

        if (is_string($details)) {
            $details = json_decode(stripslashes($details), true);
        }

        $type = sanitize_text_field(Arr::get($details, 'type'));

        /* VALIDATION */
        if ($type === 'email') {
            if (empty($details['name']) || empty($details['email']) || empty($details['enquiry'])) {
                wp_send_json_error([
                    'message' => __('Name, Email and Enquiry are required.', 'quick-enquiry-us'),
                ]);
            }
        }

        if ($type === 'sms') {
            if (empty($details['name']) || empty($details['phone_number']) || empty($details['enquiry'])) {
                wp_send_json_error([
                    'message' => __('Name, Phone number and Enquiry are required.', 'quick-enquiry-us'),
                ]);
            }
        }

        /* PRODUCT */
        $productId  = intval(Arr::get($details, 'product_id'));
        $product    = wc_get_product($productId);
        $title      = $product ? $product->get_name() : '';

        // Get phone number and country code
        $phoneNumber = isset($details['phone_number']) ? sanitize_text_field($details['phone_number']) : '';
        $countryCode = isset($details['country_code']) ? sanitize_text_field($details['country_code']) : '';

        // Normalize the phone number with the selected country code
        $phonNumberWithCountryCode = $this->normalizePhoneNumber($phoneNumber, $countryCode);

        /* DATA PREPARE */
        $data = [
            'name'         => sanitize_text_field(Arr::get($details, 'name')),
            'email'        => sanitize_email(Arr::get($details, 'email')),
            'phone_number' => $phonNumberWithCountryCode,
            'enquiry'      => sanitize_textarea_field(Arr::get($details, 'enquiry')),
            'type'         => $type,
            'subscribe'    => sanitize_text_field(Arr::get($details, 'subscribe', 'no')),
            'settings'     => wp_json_encode([
                'product_id' => $productId,
                'title'      => $title,
                'product_url'=> get_permalink($productId),
            ]),
            'created_at' => gmdate('Y-m-d H:i:s'),
            'updated_at' => gmdate('Y-m-d H:i:s'),
        ];

        /* INSERT DATABASE */
        $insertId = $QuickEnquiryUs->insertGetId($data);

        if (!$insertId) {
            wp_send_json_error([
                'message' => __('Enquiry could not be saved. Please try again.', 'quick-enquiry-us'),
            ]);
        }

        $getEnquiry = $QuickEnquiryUs->getDetail($insertId);
      
        // SMS status
        $status = 'not_sent';

        /* SEND SMS */
        if (Arr::get($settings, 'sms_enable') === 'yes') {
            if ($type === 'sms') {
                $smsSent = $this->smsAPI($phonNumberWithCountryCode, $getEnquiry->enquiry, $settings);
                $status  = $smsSent ? 'sent' : 'failed';
            }
        }
        

        /* SEND EMAIL */
        if (Arr::get($settings, 'email_enable') === 'yes') {
            if ($type === 'email') {
                $emailSent = $this->emailAPI($getEnquiry, $product, $settings);
                $status    = $emailSent ? 'sent' : 'failed';
            }
        }
       
        $QuickEnquiryUs->update($getEnquiry->id, [
            'status'     => $status,
            'updated_at'=> gmdate('Y-m-d H:i:s'),
        ]);

        /* SUCCESS RESPONSE */
        $message = sprintf(
            __('Thank you %s. Your enquiry has been received successfully.', 'quick-enquiry-us'),
            esc_html($data['name'])
        );

        wp_send_json_success([
            'message'    => $message
        ], 200);
    }

    public function normalizePhoneNumber($phoneNumber, $countryCode) {

        if ( empty($phoneNumber) ) {
            return '';
        }

        // Remove all spaces, dashes, and other formatting
        $phoneNumber = preg_replace('/[\s\-\(\)]/', '', $phoneNumber);
        
        // If number already starts with +, return as is (already has country code)
        if (strpos($phoneNumber, $countryCode) === 0) {
            return $phoneNumber;
        }
        
        // Remove any + symbols that might be in the middle
        $phoneNumber = str_replace('+', '', $phoneNumber);
        
        // Remove leading zeros (common in local format like 01676633288)
        $phoneNumber = ltrim($phoneNumber, '0');
        
        // Keep only digits
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Prepend the selected country code
        return $countryCode . $phoneNumber;
    }

    private function whatsAppApi($receiverName, $whatsAppNumber, $product, $settings)
    {
        // Sanitize WhatsApp number (digits only)
        $whatsAppNumber = preg_replace('/\D/', '', $whatsAppNumber);

        if (empty($whatsAppNumber)) {
            return '';
        }

        // Product-safe handling
        $productTitle = ($product instanceof \WC_Product)
            ? $product->get_name()
            : __('General Enquiry', 'quick-enquiry-us');

        $productUrl = ($product instanceof \WC_Product)
            ? get_permalink($product->get_id())
            : get_site_url();

        // Custom message
        $whatsappMessage = Arr::get($settings, 'whatsapp_message');

        if (empty($whatsappMessage)) {
            $whatsappMessage =
                "Hello 👋\n\n" .
                "I want to enquire about:\n" .
                "{title}\n\n" .
                "Link:\n{url}";
        }

        // Replace placeholders
        $message = str_replace(
            ['{receiver_name}', '{title}', '{url}'],
            [$receiverName, $productTitle, $productUrl],
            $whatsappMessage
        );

        return "https://wa.me/{$whatsAppNumber}?text=" . rawurlencode($message);
    }

    private function smsAPI($phoneNumber, $enquiry, $settings)
    {
        $accountSid = Arr::get($settings, 'twilio_account_sid');
        $authToken  = Arr::get($settings, 'twilio_auth_token');
        $from       = Arr::get($settings, 'twilio_phone_number');
        $to         = $phoneNumber; //user phone number
        $body       = $enquiry; //user enquiry

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json";

        $response = wp_remote_post($url, [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode($accountSid . ':' . $authToken),
            ],
            'body'    => [
                'From' => $from,
                'To'   => $to,
                'Body' => $body,
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            error_log('Twilio Error: ' . $response->get_error_message());
            return false;
        }

        $httpCode = wp_remote_retrieve_response_code($response);

        return ($httpCode >= 200 && $httpCode < 300);
    }

    private function emailAPI($getEnquiry, $product, $settings)
    {
        $userName   = $getEnquiry->name;
        $userEmail  = $getEnquiry->email ?? '';
        $adminEmail = Arr::get($settings, 'admin_email', get_option('admin_email'));
        $title      = $product ? $product->get_name() : 'Product Enquiry';

        $to      = $adminEmail;
        $subject = 'New Product Enquiry – ' . $title;

        $body  = '<div style="font-family: Arial, sans-serif; line-height:1.6; color:#333;">';
        $body .= '<h2 style="margin-bottom:10px;">📩 New Product Enquiry</h2>';

        $body .= '<table cellpadding="6" cellspacing="0" style="border-collapse:collapse;">';
        $body .= '<tr><td><strong>Product</strong></td><td>:</td><td>' . esc_html($title) . '</td></tr>';
        $body .= '<tr><td><strong>Name</strong></td><td>:</td><td>' . esc_html($userName) . '</td></tr>';

        if (!empty($userEmail)) {
            $body .= '<tr><td><strong>Email</strong></td><td>:</td><td>' . esc_html($userEmail) . '</td></tr>';
        }

        $body .= '</table>';

        $body .= '<p style="margin-top:15px;"><strong>Message:</strong></p>';
        $body .= '<div style="background:#f7f7f7; padding:12px; border-left:4px solid #0073aa;">'
            . nl2br(esc_html($getEnquiry->enquiry))
            . '</div>';

        $body .= '<p style="margin-top:20px; font-size:13px; color:#777;">';
        $body .= 'This enquiry was sent from your website enquiry form.</p>';
        $body .= '</div>';

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            !empty($userEmail) ? 'Reply-To: ' . $userName . ' <' . $userEmail . '>' : '',
        ];

        return wp_mail($to, $subject, $body, array_filter($headers));
    }

    //  All Countries
    public function getCountries() {
        $countries = [
            ["name" => "Afghanistan", "code" => "+93", "flag" => "🇦🇫"],
            ["name" => "Albania", "code" => "+355", "flag" => "🇦🇱"],
            ["name" => "Algeria", "code" => "+213", "flag" => "🇩🇿"],
            ["name" => "Andorra", "code" => "+376", "flag" => "🇦🇩"],
            ["name" => "Angola", "code" => "+244", "flag" => "🇦🇴"],
            ["name" => "Argentina", "code" => "+54", "flag" => "🇦🇷"],
            ["name" => "Armenia", "code" => "+374", "flag" => "🇦🇲"],
            ["name" => "Australia", "code" => "+61", "flag" => "🇦🇺"],
            ["name" => "Austria", "code" => "+43", "flag" => "🇦🇹"],
            ["name" => "Azerbaijan", "code" => "+994", "flag" => "🇦🇿"],
            ["name" => "Bahamas", "code" => "+1-242", "flag" => "🇧🇸"],
            ["name" => "Bahrain", "code" => "+973", "flag" => "🇧🇭"],
            ["name" => "Bangladesh", "code" => "+880", "flag" => "🇧🇩"],
            ["name" => "Barbados", "code" => "+1-246", "flag" => "🇧🇧"],
            ["name" => "Belarus", "code" => "+375", "flag" => "🇧🇾"],
            ["name" => "Belgium", "code" => "+32", "flag" => "🇧🇪"],
            ["name" => "Belize", "code" => "+501", "flag" => "🇧🇿"],
            ["name" => "Benin", "code" => "+229", "flag" => "🇧🇯"],
            ["name" => "Bhutan", "code" => "+975", "flag" => "🇧🇹"],
            ["name" => "Bolivia", "code" => "+591", "flag" => "🇧🇴"],
            ["name" => "Bosnia and Herzegovina", "code" => "+387", "flag" => "🇧🇦"],
            ["name" => "Botswana", "code" => "+267", "flag" => "🇧🇼"],
            ["name" => "Brazil", "code" => "+55", "flag" => "🇧🇷"],
            ["name" => "Brunei", "code" => "+673", "flag" => "🇧🇳"],
            ["name" => "Bulgaria", "code" => "+359", "flag" => "🇧🇬"],
            ["name" => "Burkina Faso", "code" => "+226", "flag" => "🇧🇫"],
            ["name" => "Burundi", "code" => "+257", "flag" => "🇧🇮"],
            ["name" => "Cambodia", "code" => "+855", "flag" => "🇰🇭"],
            ["name" => "Cameroon", "code" => "+237", "flag" => "🇨🇲"],
            ["name" => "Canada", "code" => "+1", "flag" => "🇨🇦"],
            ["name" => "Cape Verde", "code" => "+238", "flag" => "🇨🇻"],
            ["name" => "Central African Republic", "code" => "+236", "flag" => "🇨🇫"],
            ["name" => "Chad", "code" => "+235", "flag" => "🇹🇩"],
            ["name" => "Chile", "code" => "+56", "flag" => "🇨🇱"],
            ["name" => "China", "code" => "+86", "flag" => "🇨🇳"],
            ["name" => "Colombia", "code" => "+57", "flag" => "🇨🇴"],
            ["name" => "Comoros", "code" => "+269", "flag" => "🇰🇲"],
            ["name" => "Congo (Brazzaville)", "code" => "+242", "flag" => "🇨🇬"],
            ["name" => "Congo (Kinshasa)", "code" => "+243", "flag" => "🇨🇩"],
            ["name" => "Costa Rica", "code" => "+506", "flag" => "🇨🇷"],
            ["name" => "Croatia", "code" => "+385", "flag" => "🇭🇷"],
            ["name" => "Cuba", "code" => "+53", "flag" => "🇨🇺"],
            ["name" => "Cyprus", "code" => "+357", "flag" => "🇨🇾"],
            ["name" => "Czech Republic", "code" => "+420", "flag" => "🇨🇿"],
            ["name" => "Denmark", "code" => "+45", "flag" => "🇩🇰"],
            ["name" => "Djibouti", "code" => "+253", "flag" => "🇩🇯"],
            ["name" => "Dominica", "code" => "+1-767", "flag" => "🇩🇲"],
            ["name" => "Dominican Republic", "code" => "+1-809", "flag" => "🇩🇴"],
            ["name" => "Ecuador", "code" => "+593", "flag" => "🇪🇨"],
            ["name" => "Egypt", "code" => "+20", "flag" => "🇪🇬"],
            ["name" => "El Salvador", "code" => "+503", "flag" => "🇸🇻"],
            ["name" => "Equatorial Guinea", "code" => "+240", "flag" => "🇬🇶"],
            ["name" => "Eritrea", "code" => "+291", "flag" => "🇪🇷"],
            ["name" => "Estonia", "code" => "+372", "flag" => "🇪🇪"],
            ["name" => "Eswatini", "code" => "+268", "flag" => "🇸🇿"],
            ["name" => "Ethiopia", "code" => "+251", "flag" => "🇪🇹"],
            ["name" => "Fiji", "code" => "+679", "flag" => "🇫🇯"],
            ["name" => "Finland", "code" => "+358", "flag" => "🇫🇮"],
            ["name" => "France", "code" => "+33", "flag" => "🇫🇷"],
            ["name" => "Gabon", "code" => "+241", "flag" => "🇬🇦"],
            ["name" => "Gambia", "code" => "+220", "flag" => "🇬🇲"],
            ["name" => "Georgia", "code" => "+995", "flag" => "🇬🇪"],
            ["name" => "Germany", "code" => "+49", "flag" => "🇩🇪"],
            ["name" => "Ghana", "code" => "+233", "flag" => "🇬🇭"],
            ["name" => "Greece", "code" => "+30", "flag" => "🇬🇷"],
            ["name" => "Grenada", "code" => "+1-473", "flag" => "🇬🇩"],
            ["name" => "Guatemala", "code" => "+502", "flag" => "🇬🇹"],
            ["name" => "Guinea", "code" => "+224", "flag" => "🇬🇳"],
            ["name" => "Guinea-Bissau", "code" => "+245", "flag" => "🇬🇼"],
            ["name" => "Guyana", "code" => "+592", "flag" => "🇬🇾"],
            ["name" => "Haiti", "code" => "+509", "flag" => "🇭🇹"],
            ["name" => "Honduras", "code" => "+504", "flag" => "🇭🇳"],
            ["name" => "Hungary", "code" => "+36", "flag" => "🇭🇺"],
            ["name" => "Iceland", "code" => "+354", "flag" => "🇮🇸"],
            ["name" => "India", "code" => "+91", "flag" => "🇮🇳"],
            ["name" => "Indonesia", "code" => "+62", "flag" => "🇮🇩"],
            ["name" => "Iran", "code" => "+98", "flag" => "🇮🇷"],
            ["name" => "Iraq", "code" => "+964", "flag" => "🇮🇶"],
            ["name" => "Ireland", "code" => "+353", "flag" => "🇮🇪"],
            ["name" => "Israel", "code" => "+972", "flag" => "🇮🇱"],
            ["name" => "Italy", "code" => "+39", "flag" => "🇮🇹"],
            ["name" => "Jamaica", "code" => "+1-876", "flag" => "🇯🇲"],
            ["name" => "Japan", "code" => "+81", "flag" => "🇯🇵"],
            ["name" => "Jordan", "code" => "+962", "flag" => "🇯🇴"],
            ["name" => "Kazakhstan", "code" => "+7", "flag" => "🇰🇿"],
            ["name" => "Kenya", "code" => "+254", "flag" => "🇰🇪"],
            ["name" => "Kiribati", "code" => "+686", "flag" => "🇰🇮"],
            ["name" => "Kuwait", "code" => "+965", "flag" => "🇰🇼"],
            ["name" => "Kyrgyzstan", "code" => "+996", "flag" => "🇰🇬"],
            ["name" => "Laos", "code" => "+856", "flag" => "🇱🇦"],
            ["name" => "Latvia", "code" => "+371", "flag" => "🇱🇻"],
            ["name" => "Lebanon", "code" => "+961", "flag" => "🇱🇧"],
            ["name" => "Lesotho", "code" => "+266", "flag" => "🇱🇸"],
            ["name" => "Liberia", "code" => "+231", "flag" => "🇱🇷"],
            ["name" => "Libya", "code" => "+218", "flag" => "🇱🇾"],
            ["name" => "Liechtenstein", "code" => "+423", "flag" => "🇱🇮"],
            ["name" => "Lithuania", "code" => "+370", "flag" => "🇱🇹"],
            ["name" => "Luxembourg", "code" => "+352", "flag" => "🇱🇺"],
            ["name" => "Madagascar", "code" => "+261", "flag" => "🇲🇬"],
            ["name" => "Malawi", "code" => "+265", "flag" => "🇲🇼"],
            ["name" => "Malaysia", "code" => "+60", "flag" => "🇲🇾"],
            ["name" => "Maldives", "code" => "+960", "flag" => "🇲🇻"],
            ["name" => "Mali", "code" => "+223", "flag" => "🇲🇱"],
            ["name" => "Malta", "code" => "+356", "flag" => "🇲🇹"],
            ["name" => "Marshall Islands", "code" => "+692", "flag" => "🇲🇭"],
            ["name" => "Mauritania", "code" => "+222", "flag" => "🇲🇷"],
            ["name" => "Mauritius", "code" => "+230", "flag" => "🇲🇺"],
            ["name" => "Mexico", "code" => "+52", "flag" => "🇲🇽"],
            ["name" => "Micronesia", "code" => "+691", "flag" => "🇫🇲"],
            ["name" => "Moldova", "code" => "+373", "flag" => "🇲🇩"],
            ["name" => "Monaco", "code" => "+377", "flag" => "🇲🇨"],
            ["name" => "Mongolia", "code" => "+976", "flag" => "🇲🇳"],
            ["name" => "Montenegro", "code" => "+382", "flag" => "🇲🇪"],
            ["name" => "Morocco", "code" => "+212", "flag" => "🇲🇦"],
            ["name" => "Mozambique", "code" => "+258", "flag" => "🇲🇿"],
            ["name" => "Myanmar", "code" => "+95", "flag" => "🇲🇲"],
            ["name" => "Namibia", "code" => "+264", "flag" => "🇳🇦"],
            ["name" => "Nauru", "code" => "+674", "flag" => "🇳🇷"],
            ["name" => "Nepal", "code" => "+977", "flag" => "🇳🇵"],
            ["name" => "Netherlands", "code" => "+31", "flag" => "🇳🇱"],
            ["name" => "New Zealand", "code" => "+64", "flag" => "🇳🇿"],
            ["name" => "Nicaragua", "code" => "+505", "flag" => "🇳🇮"],
            ["name" => "Niger", "code" => "+227", "flag" => "🇳🇪"],
            ["name" => "Nigeria", "code" => "+234", "flag" => "🇳🇬"],
            ["name" => "North Korea", "code" => "+850", "flag" => "🇰🇵"],
            ["name" => "North Macedonia", "code" => "+389", "flag" => "🇲🇰"],
            ["name" => "Norway", "code" => "+47", "flag" => "🇳🇴"],
            ["name" => "Oman", "code" => "+968", "flag" => "🇴🇲"],
            ["name" => "Pakistan", "code" => "+92", "flag" => "🇵🇰"],
            ["name" => "Palau", "code" => "+680", "flag" => "🇵🇼"],
            ["name" => "Palestine", "code" => "+970", "flag" => "🇵🇸"],
            ["name" => "Panama", "code" => "+507", "flag" => "🇵🇦"],
            ["name" => "Papua New Guinea", "code" => "+675", "flag" => "🇵🇬"],
            ["name" => "Paraguay", "code" => "+595", "flag" => "🇵🇾"],
            ["name" => "Peru", "code" => "+51", "flag" => "🇵🇪"],
            ["name" => "Philippines", "code" => "+63", "flag" => "🇵🇭"],
            ["name" => "Poland", "code" => "+48", "flag" => "🇵🇱"],
            ["name" => "Portugal", "code" => "+351", "flag" => "🇵🇹"],
            ["name" => "Qatar", "code" => "+974", "flag" => "🇶🇦"],
            ["name" => "Romania", "code" => "+40", "flag" => "🇷🇴"],
            ["name" => "Russia", "code" => "+7", "flag" => "🇷🇺"],
            ["name" => "Rwanda", "code" => "+250", "flag" => "🇷🇼"],
            ["name" => "Saint Kitts and Nevis", "code" => "+1-869", "flag" => "🇰🇳"],
            ["name" => "Saint Lucia", "code" => "+1-758", "flag" => "🇱🇨"],
            ["name" => "Saint Vincent and the Grenadines", "code" => "+1-784", "flag" => "🇻🇨"],
            ["name" => "Samoa", "code" => "+685", "flag" => "🇼🇸"],
            ["name" => "San Marino", "code" => "+378", "flag" => "🇸🇲"],
            ["name" => "Sao Tome and Principe", "code" => "+239", "flag" => "🇸🇹"],
            ["name" => "Saudi Arabia", "code" => "+966", "flag" => "🇸🇦"],
            ["name" => "Senegal", "code" => "+221", "flag" => "🇸🇳"],
            ["name" => "Serbia", "code" => "+381", "flag" => "🇷🇸"],
            ["name" => "Seychelles", "code" => "+248", "flag" => "🇸🇨"],
            ["name" => "Sierra Leone", "code" => "+232", "flag" => "🇸🇱"],
            ["name" => "Singapore", "code" => "+65", "flag" => "🇸🇬"],
            ["name" => "Slovakia", "code" => "+421", "flag" => "🇸🇰"],
            ["name" => "Slovenia", "code" => "+386", "flag" => "🇸🇮"],
            ["name" => "Solomon Islands", "code" => "+677", "flag" => "🇸🇧"],
            ["name" => "Somalia", "code" => "+252", "flag" => "🇸🇴"],
            ["name" => "South Africa", "code" => "+27", "flag" => "🇿🇦"],
            ["name" => "South Korea", "code" => "+82", "flag" => "🇰🇷"],
            ["name" => "South Sudan", "code" => "+211", "flag" => "🇸🇸"],
            ["name" => "Spain", "code" => "+34", "flag" => "🇪🇸"],
            ["name" => "Sri Lanka", "code" => "+94", "flag" => "🇱🇰"],
            ["name" => "Sudan", "code" => "+249", "flag" => "🇸🇩"],
            ["name" => "Suriname", "code" => "+597", "flag" => "🇸🇷"],
            ["name" => "Sweden", "code" => "+46", "flag" => "🇸🇪"],
            ["name" => "Switzerland", "code" => "+41", "flag" => "🇨🇭"],
            ["name" => "Syria", "code" => "+963", "flag" => "🇸🇾"],
            ["name" => "Taiwan", "code" => "+886", "flag" => "🇹🇼"],
            ["name" => "Tajikistan", "code" => "+992", "flag" => "🇹🇯"],
            ["name" => "Tanzania", "code" => "+255", "flag" => "🇹🇿"],
            ["name" => "Thailand", "code" => "+66", "flag" => "🇹🇭"],
            ["name" => "Timor-Leste", "code" => "+670", "flag" => "🇹🇱"],
            ["name" => "Togo", "code" => "+228", "flag" => "🇹🇬"],
            ["name" => "Tonga", "code" => "+676", "flag" => "🇹🇴"],
            ["name" => "Trinidad and Tobago", "code" => "+1-868", "flag" => "🇹🇹"],
            ["name" => "Tunisia", "code" => "+216", "flag" => "🇹🇳"],
            ["name" => "Turkey", "code" => "+90", "flag" => "🇹🇷"],
            ["name" => "Turkmenistan", "code" => "+993", "flag" => "🇹🇲"],
            ["name" => "Tuvalu", "code" => "+688", "flag" => "🇹🇻"],
            ["name" => "Uganda", "code" => "+256", "flag" => "🇺🇬"],
            ["name" => "Ukraine", "code" => "+380", "flag" => "🇺🇦"],
            ["name" => "United Arab Emirates", "code" => "+971", "flag" => "🇦🇪"],
            ["name" => "United Kingdom", "code" => "+44", "flag" => "🇬🇧"],
            ["name" => "United States", "code" => "+1", "flag" => "🇺🇸"],
            ["name" => "Uruguay", "code" => "+598", "flag" => "🇺🇾"],
            ["name" => "Uzbekistan", "code" => "+998", "flag" => "🇺🇿"],
            ["name" => "Vanuatu", "code" => "+678", "flag" => "🇻🇺"],
            ["name" => "Vatican City", "code" => "+379", "flag" => "🇻🇦"],
            ["name" => "Venezuela", "code" => "+58", "flag" => "🇻🇪"],
            ["name" => "Vietnam", "code" => "+84", "flag" => "🇻🇳"],
            ["name" => "Yemen", "code" => "+967", "flag" => "🇾🇪"],
            ["name" => "Zambia", "code" => "+260", "flag" => "🇿🇲"],
            ["name" => "Zimbabwe", "code" => "+263", "flag" => "🇿🇼"],
        ];
        return $countries;
    }

    /**
     * Inline Style Handler
     */
    public function styleHandler()
    {
        $quickEnquiryUsSettings = get_option('quick_enquiry_us_settings', []);
        $styles        = $quickEnquiryUsSettings['styles'];

        $inStockShopButtonColor           = Arr::get($styles, 'instock_shop_button_color');
        $outOfStockShopButtonColor        = Arr::get($styles, 'out_of_stock_shop_button_color');
        $backorderShopButtonColor         = Arr::get($styles, 'backorder_shop_button_color');
        $inStockSingleButtonColor         = Arr::get($styles, 'instock_single_button_color');
        $outOfStockSingleButtonColor      = Arr::get($styles, 'out_of_stock_single_button_color');
        $backorderSingleButtonColor       = Arr::get($styles, 'backorder_single_button_color');
        $headerBackgroundColor            = Arr::get($styles, 'header_background_color');
        $activeTabColor                   = Arr::get($styles, 'active_tab_color');
        $submitButtonBackgroundColor      = Arr::get($styles, 'submit_button_background_color');
        $shortcodeEnquiryUsButtonColor    = Arr::get($styles, 'shortcode_enquiry_us_button_color');
        $cartPageEnquiryUsButtonColor     = Arr::get($styles, 'cart_page_enquiry_us_button_color');
        $orderSummaryEnquiryUsButtonColor = Arr::get($styles, 'order_summary_enquiry_us_button_color');

        $custom_css = "";

        // shop button color
        if (!empty($inStockShopButtonColor)) {
            $custom_css .= "
                .qeus-enquire-shop-btn-instock {
                    background-color: {$inStockShopButtonColor} !important;
                }
            ";
        }
        if (!empty($outOfStockShopButtonColor)) {
            $custom_css .= "
                .qeus-enquire-shop-btn-outofstock {
                    background-color: {$outOfStockShopButtonColor} !important;
                }
            ";
        }
        if (!empty($backorderShopButtonColor)) {
            $custom_css .= "
                .qeus-enquire-shop-btn-backorder {
                    background-color: {$backorderShopButtonColor} !important;
                }
            ";
        }

        // single button color
        if (!empty($inStockSingleButtonColor)) {
            $custom_css .= "
                .qeus-enquire-single-btn-instock {
                    background-color: {$inStockSingleButtonColor} !important;
                }
            ";
        }
        if (!empty($outOfStockSingleButtonColor)) {
            $custom_css .= "
                .qeus-enquire-single-btn-outofstock {
                    background-color: {$outOfStockSingleButtonColor} !important;
                }
            ";
        }
        if (!empty($backorderSingleButtonColor)) {
            $custom_css .= "
                .qeus-enquire-single-btn-backorder {
                    background-color: {$backorderSingleButtonColor} !important;
                }
            ";
        }
       
        // header background color
        if (!empty($headerBackgroundColor)) {
            $custom_css .= "
                .qeus-header {
                    background-color: {$headerBackgroundColor} !important;
                }
            ";
        }
        // active tab color
        if (!empty($activeTabColor)) {
            $custom_css .= "    
                .qeus-tabs .qeus-tab.active {
                    color: {$activeTabColor} !important;
                    border-bottom-color: {$activeTabColor} !important;
                }
            ";
        }
        // submit button background color
        if (!empty($submitButtonBackgroundColor)) {
            $custom_css .= "
                .qeus-submit {
                    background-color: {$submitButtonBackgroundColor} !important;
                }
            ";
        }

        // shortcode enquire us button color
        if (!empty($shortcodeEnquiryUsButtonColor)) {
            $custom_css .= "
                .qeus-shortcode-enquiry-us-btn  {               
                    background-color: {$shortcodeEnquiryUsButtonColor} !important;
                }
            ";
        }

        // cart page enquiry us button
        if (!empty($cartPageEnquiryUsButtonColor)) {
            $custom_css .= "
                .qeus-enquiry-us-cart-page-btn {               
                    background-color: {$cartPageEnquiryUsButtonColor} !important;
                }
            ";
        }

        // order summary page enquiry us button
        if (!empty($orderSummaryEnquiryUsButtonColor)) {
            $custom_css .= "
                .qeus-enquiry-us-order-summary-btn {               
                    background-color: {$orderSummaryEnquiryUsButtonColor} !important;
                }
            ";
        }

        if (!empty($custom_css)) {
            wp_add_inline_style('quick_enquiry_us_css', $custom_css);
        }
    }
}
