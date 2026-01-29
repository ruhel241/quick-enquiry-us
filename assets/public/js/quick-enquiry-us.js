/******/ (() => { // webpackBootstrap
/*!*************************************!*\
  !*** ./resources/public/js/qeus.js ***!
  \*************************************/
jQuery(document).ready(function ($) {
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
    qeusQuickEnquiryUsInit: function qeusQuickEnquiryUsInit() {
      var that = this;

      // shop page button handler
      // Add extra body class if homepage
      //if ($('body').hasClass('home')) { 
      $('body').addClass('qeus-addons-wrapper-home');
      // quick cart modal open class
      // $("body").addClass("qcpu-modal-open");
      // }

      $('.qeus-addons-wrapper-home .products .product').each(function () {
        var $parentProduct = $(this);
        var $qvpuWrapper = $parentProduct.find('.qvpu-add-to-cart-wrapper');
        var $directCheckoutBtn = $parentProduct.find('.qdc-direct-checkout-button');
        var $enquiryShopBtn = $parentProduct.find('.qeus-quick-enquiry-us-button');
        // let $addToCart    = $product.find('.button.add_to_cart_button, .wc-block-components-product-button__button');
        var $addToCart = $parentProduct.find('a.button.product_type_simple, a.button.add_to_cart_button, .wc-block-components-product-button__button').not('.qvpu-view-button, .ast-on-card-button');

        // Detect View Product button
        var $viewProduct = $parentProduct.find('.button.product_type_variable, .button.product_type_external, .product_type_grouped');

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

        if ($directCheckoutBtn.length) {
          if ($enquiryShopBtn.length) {
            $directCheckoutBtn.after($enquiryShopBtn);
          }
          return;
        }

        /** ---------------------------------------------
         * CASE 3: Create new wrapper
         * --------------------------------------------- */

        var $newWrapper = $('<div class="qeus-add-to-cart-wrapper"></div>');
        if ($addToCart.length) {
          $addToCart.add($enquiryShopBtn).wrapAll($newWrapper);
        } else {
          if ($viewProduct.length) {
            $viewProduct.insertAfter($enquiryShopBtn);
            $viewProduct.add($enquiryShopBtn).wrapAll($newWrapper);
          }
        }
      });

      // Click Open modal
      $(document).on('click', '.qeus-quick-enquiry-us-button', function (e) {
        e.preventDefault();
        $("body").addClass("qeus-modal-open");
        $('.qeus-quick-enquiry-us-modal-wrapper').fadeIn();
        var $btn = $(this);
        var productId = $btn.data('product-id');
        var productSlug = $btn.data('product-slug') ? $btn.data('product-slug') : '';

        // set product ID
        $('input[name="product-id"]').val(productId);
        // Load dynamic form
        that.enquirUsForm(productId, productSlug);
      });

      // Tabs handler
      $(document).on('click', '.qeus-tab', function () {
        var tab = $(this).data('tab');
        var container = $(this).closest('.qeus-form-container');
        // Tabs UI
        container.find('.qeus-tab').removeClass('active');
        $(this).addClass('active');
        container.find('.qeus-tab-content').removeClass('active');
        container.find('#qeus-tab-' + tab).addClass('active');
      });

      // Trigger default state
      $('.qeus-tab.active').trigger('click');
    },
    enquirUsForm: function enquirUsForm(productId, productSlug) {
      showLoading();
      $.post('/?wc-ajax=qeus_quick_enquiry_us_ajax_actions', {
        route: 'qeus_quick_enquiry_us_form',
        nonce: window.QuickEnquiryUsPublicVars.nonce,
        product_id: productId,
        product_slug: productSlug
      }).success(function (response) {
        if (response.success && response.data.fragments) {
          // update form HTML from PHP
          $('.qeus-form-container').replaceWith(response.data.fragments['.qeus-form-container']);
        }
      }).always(function () {
        hideLoading();
      });
    },
    // Modal close
    closeModal: function closeModal() {
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
    enquireUsDetailsHandler: function enquireUsDetailsHandler() {
      var that = this;
      $(document).on('click', '.qeus-form-container .qeus-submit', function (e) {
        var _$form$find$val, _$form$find$val2, _$form$find$val3, _$form$find$val4;
        e.preventDefault();
        var $form = $(this).closest('form');
        var name = ((_$form$find$val = $form.find('input[name="name"]').val()) === null || _$form$find$val === void 0 ? void 0 : _$form$find$val.trim()) || '';
        var email = ((_$form$find$val2 = $form.find('input[name="email"]').val()) === null || _$form$find$val2 === void 0 ? void 0 : _$form$find$val2.trim()) || '';
        var enquiry = ((_$form$find$val3 = $form.find('textarea[name="enquiry"]').val()) === null || _$form$find$val3 === void 0 ? void 0 : _$form$find$val3.trim()) || '';
        var product_id = $form.find('input[name="product_id"]').val() || '';
        var phone_number = ((_$form$find$val4 = $form.find('input[name="phone_number"]').val()) === null || _$form$find$val4 === void 0 ? void 0 : _$form$find$val4.trim()) || '';
        var country_code = $form.find('select[name="country_code"]').val() || '';
        var subscribe = $form.find('input[name="subscribe"]').is(':checked') ? 'yes' : 'no';
        // get active tab (sms / enquire)
        var type = $('.qeus-tab.active').data('tab'); // sms | enquire
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        // helper function for notice
        function showError(msg) {
          $('.qeus-notice .qeus-response-message').text(msg).css('color', 'red');
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
        var details = {
          name: name,
          email: email,
          enquiry: enquiry,
          product_id: product_id,
          phone_number: phone_number,
          country_code: country_code,
          type: type,
          subscribe: subscribe
        };
        that.enquireUsDetailsAction(details);
      });
    },
    // AJAX request
    enquireUsDetailsAction: function enquireUsDetailsAction(details) {
      showLoading();
      jQuery.post('/?wc-ajax=qeus_quick_enquiry_us_ajax_actions', {
        route: 'qeus_quick_enquiry_us_details',
        nonce: window.QuickEnquiryUsPublicVars.nonce,
        details: JSON.stringify(details)
      }).success(function (response) {
        if (response.success) {
          $('.qeus-notice .qeus-response-message').text(response.data.message).css({
            'color': 'green',
            'border-left': '4px solid green'
          });
          $('.qeus-notice').fadeIn();
          // Reset form
          $('.qeus-form-container form')[0].reset();
          setTimeout(function () {
            $('.qeus-notice').fadeOut();
            $('.qeus-quick-enquiry-us-modal-wrapper').fadeOut();
          }, 6000);
        } else {
          $('.qeus-notice .qeus-response-message').text(response.data.message).css({
            'color': 'red',
            'border-left': '4px solid red'
          });
          $('.qeus-notice').fadeIn();
          setTimeout(function () {
            $('.qeus-notice').fadeOut();
          }, 4000);
        }
      }).fail(function (error) {}).always(function () {
        hideLoading();
      });
    },
    init: function init() {
      this.qeusQuickEnquiryUsInit();
      this.closeModal();
      this.enquireUsDetailsHandler();
      // this.enquirUsShopButtonInit();
    }
  };
  qeusQuickEnquiryUs.init();
});
/******/ })()
;