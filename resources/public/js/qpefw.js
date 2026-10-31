jQuery(document).ready(function ($) {
    var QuickProductEnquiryPublicVarsSettings = QuickProductEnquiryPublicVars.qpefw_settings.settings;

    var isLoading = false;
    function showLoading() {
        isLoading = true;
        $('#qpefw-loading-view').show();
    }
    function hideLoading() {
        isLoading = false;
        setTimeout(function () {
            if (!isLoading) {
                $('#qpefw-loading-view').hide();
            }
        }, 700);
    }

    var qpefwQuickProductEnquiry = {
       // Modal open
        qpefwQuickProductEnquiryInit: function () {
            let that = this;

            $('body').addClass('qpefw-addons-wrapper-home');
            
            $('.qpefw-addons-wrapper-home .products .product').each(function() {
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
                    let $enquiryShopBtn   = $parentProduct.find('.qpefw-button');

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
    
                    let $newWrapper = $('<div class="qpefw-add-to-cart-wrapper"></div>');

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
            $(document).on('click', '.qpefw-button', function (e) {
                e.preventDefault();

                $("body").addClass("qpefw-modal-open");
                $('.qpefw-modal-wrapper').fadeIn();

                let $btn = $(this);
                let productId = $btn.data('product-id');
                let productSlug = $btn.data('product-slug') ? $btn.data('product-slug') : '';

                // set product ID
                $('input[name="product-id"]').val(productId);
                // Load dynamic form
                that.enquirUsForm(productId, productSlug);
            });

            // Tabs handler
            $(document).on('click', '.qpefw-tab', function () {
                const tab = $(this).data('tab');
                const container = $(this).closest('.qpefw-form-container');
                // Tabs UI
                container.find('.qpefw-tab').removeClass('active');
                $(this).addClass('active');

                container.find('.qpefw-tab-content').removeClass('active');
                container.find('#qpefw-tab-' + tab).addClass('active');
            });

            // Trigger default state
            $('.qpefw-tab.active').trigger('click');
        },

        enquirUsForm: function (productId, productSlug) {
            showLoading();

            $.post('/?wc-ajax=qpefw_ajax_actions', {
                route: 'qpefw_form',
                nonce: window.QuickProductEnquiryPublicVars.nonce,
                product_id: productId,
                product_slug: productSlug
            })
            .success(function (response) {
                if (response.success && response.data.fragments) {
                    // update form HTML from PHP
                    $('.qpefw-form-container').replaceWith(
                        response.data.fragments['.qpefw-form-container']
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
            $(document).on('click', '.qpefw-view-close', function () {
                $('.qpefw-modal-wrapper').fadeOut();
                $("body").removeClass("qpefw-modal-open");
            });

            // Click outside to close
            $(document).on('click', '.qpefw-modal-wrapper', function (e) {
                if ($(e.target).hasClass('qpefw-modal-wrapper')) {
                    $('.qpefw-modal-wrapper').fadeOut();
                    $("body").removeClass("qpefw-modal-open");
                }
            });

            // Escape key close
            $(document).on('keyup', function (e) {
                if (e.key === "Escape") {
                    $('.qpefw-modal-wrapper').fadeOut();
                    $("body").removeClass("qpefw-modal-open");
                }
            });
        },

        // Form submit handler
        enquireUsDetailsHandler: function () {
            let that = this;
            $(document).on('click', '.qpefw-form-container .qpefw-submit', function (e) {
                e.preventDefault();
                let $form = $(this).closest('form');

                let name         = $form.find('input[name="name"]').val()?.trim() || '';
                let email        = $form.find('input[name="email"]').val()?.trim() || '';
                let enquiry      = $form.find('textarea[name="enquiry"]').val()?.trim() || '';
                let product_id   = $form.find('input[name="product_id"]').val() || '';
                let phone_number = $form.find('input[name="phone_number"]').val()?.trim() || '';
                let country_code = $form.find('select[name="country_code"]').val() || '';
                let subscribe    = $form.find('input[name="subscribe"]').is(':checked') ? 'yes' : 'no';
                // get active tab (enquire)
                let type = $('.qpefw-tab.active').data('tab'); // enquire
                let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                // helper function for notice
                function showError(msg) {
                    $('.qpefw-notice .qpefw-response-message')
                        .text(msg)
                        .css('color', 'red');
                    $('.qpefw-notice').fadeIn();

                    setTimeout(function () {
                        $('.qpefw-notice').fadeOut();
                    }, 4000);
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
            jQuery.post('/?wc-ajax=qpefw_ajax_actions', {
                route: 'qpefw_details',
                nonce: window.QuickProductEnquiryPublicVars.nonce,
                details: JSON.stringify(details),
            })
            .success(function(response) {
                if (response.success) {
                    $('.qpefw-notice .qpefw-response-message').text(response.data.message).css({'color': 'green', 'border-left': '4px solid green'});
                    $('.qpefw-notice').fadeIn();
                    // Reset form
                    $('.qpefw-form-container form')[0].reset();
                    setTimeout(function () {
                        $('.qpefw-notice').fadeOut();
                        $('.qpefw-modal-wrapper').fadeOut();
                        $("body").removeClass("qpefw-modal-open");
                    }, 6000);
                } else {
                    $('.qpefw-notice .qpefw-response-message').text(response.data.message).css({'color': 'red', 'border-left': '4px solid red'});
                    $('.qpefw-notice').fadeIn();
                    setTimeout(function () {
                        $('.qpefw-notice').fadeOut();
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
            this.qpefwQuickProductEnquiryInit();
            this.closeModal();
            this.enquireUsDetailsHandler();
            // this.enquirUsShopButtonInit();
        }
    };

    qpefwQuickProductEnquiry.init();
});
