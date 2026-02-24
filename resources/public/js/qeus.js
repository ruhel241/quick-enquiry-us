jQuery(document).ready(function ($) {
    var QuickEnquiryUsPublicVarsSettings = QuickEnquiryUsPublicVars.quick_enquiry_us_settings.settings;

    var isLoading = false;
    function showLoading() {
        isLoading = true;
        $('#qeus-loading-view').show();
    }
    function hideLoading() {
        isLoading = false;
        setTimeout(function () {
            if (!isLoading) {
                $('#qeus-loading-view').hide();
            }
        }, 700);
    }

    var qeusQuickEnquiryUs = {
       // Modal open
        qeusQuickEnquiryUsInit: function () {
            let that = this;

            $('body').addClass('qeus-addons-wrapper-home');
            
            $('.qeus-addons-wrapper-home .products .product').each(function() {
                let $parentProduct      = $(this);
                // Detect Add to cart button
                let $addToCart = $parentProduct.find(
                    'a.button.product_type_simple, a.button.add_to_cart_button, .wc-block-components-product-button__button'
                ).not('.qvpu-view-button, .ast-on-card-button');

                // Detect View Product button
                let $viewProduct = $parentProduct.find('.button.product_type_variable, .button.product_type_external, .product_type_grouped');

                setTimeout(() => {
                    let $qvpuWrapper      = $parentProduct.find('.qvpu-add-to-cart-wrapper');
                    let $quickCheckoutBtn = $parentProduct.find('.qdc-direct-checkout-button');
                    let $enquiryShopBtn   = $parentProduct.find('.qeus-quick-enquiry-us-button');

                    /** ---------------------------------------------
                     * CASE 1: Wrapper already exists quick view
                     * --------------------------------------------- */
                    if ($qvpuWrapper.length) {
                        if ($enquiryShopBtn.length) {
                            $qvpuWrapper.append($enquiryShopBtn);
                        }
                        return;
                    }

                    /** ---------------------------------------------
                     * CASE 2: Wrapper already exists for direct checkout
                     * --------------------------------------------- */

                    if ($quickCheckoutBtn.length) {
                        if ($enquiryShopBtn.length) {
                            $quickCheckoutBtn.after($enquiryShopBtn);
                        }
                        return;
                    }

                    /** ---------------------------------------------
                     * CASE 3: Create new wrapper
                     * --------------------------------------------- */
    
                    let $newWrapper = $('<div class="qeus-add-to-cart-wrapper"></div>');

                    if ($addToCart.length) {
                        $addToCart.add($enquiryShopBtn).wrapAll($newWrapper);
                    } else {
                        if ($viewProduct.length) {
                            $viewProduct.insertAfter($enquiryShopBtn);
                            $viewProduct.add($enquiryShopBtn).wrapAll($newWrapper);
                        }
                    }
                }, 10);     
            });


            // Click Open modal
            $(document).on('click', '.qeus-quick-enquiry-us-button', function (e) {
                e.preventDefault();

                $("body").addClass("qeus-modal-open");
                $('.qeus-quick-enquiry-us-modal-wrapper').fadeIn();

                let $btn = $(this);
                let productId = $btn.data('product-id');
                let productSlug = $btn.data('product-slug') ? $btn.data('product-slug') : '';

                // set product ID
                $('input[name="product-id"]').val(productId);
                // Load dynamic form
                that.enquirUsForm(productId, productSlug);
            });

            // Tabs handler
            $(document).on('click', '.qeus-tab', function () {
                const tab = $(this).data('tab');
                const container = $(this).closest('.qeus-form-container');
                // Tabs UI
                container.find('.qeus-tab').removeClass('active');
                $(this).addClass('active');

                container.find('.qeus-tab-content').removeClass('active');
                container.find('#qeus-tab-' + tab).addClass('active');
            });

            // Trigger default state
            $('.qeus-tab.active').trigger('click');
        },

        enquirUsForm: function (productId, productSlug) {
            showLoading();

            $.post('/?wc-ajax=qeus_quick_enquiry_us_ajax_actions', {
                route: 'qeus_quick_enquiry_us_form',
                nonce: window.QuickEnquiryUsPublicVars.nonce,
                product_id: productId,
                product_slug: productSlug
            })
            .success(function (response) {
                if (response.success && response.data.fragments) {
                    // update form HTML from PHP
                    $('.qeus-form-container').replaceWith(
                        response.data.fragments['.qeus-form-container']
                    );
                }
            })
            .always(function () {
                hideLoading();
            });
        },

        // Modal close
        closeModal: function () {
            // Close button
            $(document).on('click', '.qeus-view-close', function () {
                $('.qeus-quick-enquiry-us-modal-wrapper').fadeOut();
                $("body").removeClass("qeus-modal-open");
            });

            // Click outside to close
            $(document).on('click', '.qeus-quick-enquiry-us-modal-wrapper', function (e) {
                if ($(e.target).hasClass('qeus-quick-enquiry-us-modal-wrapper')) {
                    $('.qeus-quick-enquiry-us-modal-wrapper').fadeOut();
                    $("body").removeClass("qeus-modal-open");
                }
            });

            // Escape key close
            $(document).on('keyup', function (e) {
                if (e.key === "Escape") {
                    $('.qeus-quick-enquiry-us-modal-wrapper').fadeOut();
                    $("body").removeClass("qeus-modal-open");
                }
            });
        },

        // Form submit handler
        enquireUsDetailsHandler: function () {
            let that = this;
            $(document).on('click', '.qeus-form-container .qeus-submit', function (e) {
                e.preventDefault();
                let $form = $(this).closest('form');

                let name         = $form.find('input[name="name"]').val()?.trim() || '';
                let email        = $form.find('input[name="email"]').val()?.trim() || '';
                let enquiry      = $form.find('textarea[name="enquiry"]').val()?.trim() || '';
                let product_id   = $form.find('input[name="product_id"]').val() || '';
                let phone_number = $form.find('input[name="phone_number"]').val()?.trim() || '';
                let country_code = $form.find('select[name="country_code"]').val() || '';
                let subscribe    = $form.find('input[name="subscribe"]').is(':checked') ? 'yes' : 'no';
                // get active tab (sms / enquire)
                let type = $('.qeus-tab.active').data('tab'); // sms | enquire
                let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                // helper function for notice
                function showError(msg) {
                    $('.qeus-notice .qeus-response-message')
                        .text(msg)
                        .css('color', 'red');
                    $('.qeus-notice').fadeIn();

                    setTimeout(function () {
                        $('.qeus-notice').fadeOut();
                    }, 4000);
                }
                /* ======================
                SMS TAB VALIDATION
                ======================= */
                if (type === 'sms') {
                    if (!name) {
                        showError('Name is required');
                        return;
                    }   
                    if (!country_code) {
                        showError('Country code is required');
                        return;
                    }
                    if (!phone_number) {
                        showError('Phone number is required');
                        return;
                    }
                    if (!enquiry) {
                        showError('enquiry is required');
                        return;
                    }
                }
                /* ======================
                ENQUIRE FORM VALIDATION
                ======================= */
                if (type === 'email') {
                    if (!name) {
                        showError('Name is required');
                        return;
                    }
                    if (!email) {
                        showError('Email is required');
                        return;
                    }
                    if (!emailPattern.test(email)) {
                        showError('Please enter a valid email address');
                        return;
                    }
                    if (!enquiry) {
                        showError('Enquiry is required');
                        return;
                    }
                }

                // send data
                let details = {
                    name,
                    email,
                    enquiry,
                    product_id,
                    phone_number,
                    country_code,
                    type,
                    subscribe
                };
                that.enquireUsDetailsAction(details);
            });
        },

        // AJAX request
        enquireUsDetailsAction: function (details) {
            showLoading();
            jQuery.post('/?wc-ajax=qeus_quick_enquiry_us_ajax_actions', {
                route: 'qeus_quick_enquiry_us_details',
                nonce: window.QuickEnquiryUsPublicVars.nonce,
                details: JSON.stringify(details),
            })
            .success(function(response) {
                if (response.success) {
                    $('.qeus-notice .qeus-response-message').text(response.data.message).css({'color': 'green', 'border-left': '4px solid green'});
                    $('.qeus-notice').fadeIn();
                    // Reset form
                    $('.qeus-form-container form')[0].reset();
                    setTimeout(function () {
                        $('.qeus-notice').fadeOut();
                        $('.qeus-quick-enquiry-us-modal-wrapper').fadeOut();
                        $("body").removeClass("qeus-modal-open");
                    }, 6000);
                } else {
                    $('.qeus-notice .qeus-response-message').text(response.data.message).css({'color': 'red', 'border-left': '4px solid red'});
                    $('.qeus-notice').fadeIn();
                    setTimeout(function () {
                        $('.qeus-notice').fadeOut();
                    }, 4000);
                } 
            })
            .fail(function(error) {
            })
            .always(function() {
                hideLoading();
            });
        },

        init: function () {
            this.qeusQuickEnquiryUsInit();
            this.closeModal();
            this.enquireUsDetailsHandler();
            // this.enquirUsShopButtonInit();
        }
    };

    qeusQuickEnquiryUs.init();
});
