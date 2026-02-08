<?php

namespace QuickEnquiryUs\App\Handler;

class ActionHandler
{
    public function register() {
        // hide all notice without license notice
        add_action('admin_print_scripts', array($this, 'hideAdminNotices') );
        // admin notce
        add_action('admin_notices', array($this, 'pluginLicenseNotice'));
        
        if ( ! $this->getVerify() ) {
            return;
        }

        $this->activationAddons();
    }

    public function hideAdminNotices() {
        if ( isset($_GET['tab']) && $_GET['tab'] != 'quick-enquiry-us' ) {
           return;
        } 
        
        echo '<style>
            .notice { display: none !important; }
            .plugin-license-notice {
                display: block !important;
            }
        </style>';
    }


    public function getVerify($key = 'qeus_license') {

        $license = get_option($key, []);

        // Must have license key
        if ( empty($license['license_key']) ) {
            return false;
        }

        // Must be valid status
        if ( empty($license['status']) || $license['status'] !== 'valid' ) {
            return false;
        }

        // Lifetime license → always valid
        if ( isset($license['expires']) && $license['expires'] === 'lifetime' ) {
            return true;
        }

        // Normal expiry check
        if ( empty($license['expires']) ) {
            return false;
        }

        $expires = strtotime($license['expires']);
 
        if ( ! $expires ) {
            return false;
        }

        if ( $expires < time() ) {
            return false;
        }

        return true;
    }

    public function activationAddons() {
      
        $qeusActiveModule = get_option('qeus_active_module', []);

        if (isset($qeusActiveModule['quick_enquiry_us_module']) && $qeusActiveModule['quick_enquiry_us_module'] == 'yes') {
            $FrontendHandler = new FrontendHandler;
            $FrontendHandler->register();
        } 
    }

    public function pluginLicenseNotice() {

        $license = get_option('qeus_license', []);
        $status  = $license['status'] ?? '';

        if ( $status === 'valid' ) {
            return;
        }

        $message = __( 'Please activate your license key. Quick Enquiry Us Addons will not work without license activation.', 'quick-enquiry-us' );

        if ( $status === 'expired' ) {
            $message = __( 'Your license has expired, so "Quick Enquiry Us" Addons is currently inactive.', 'quick-enquiry-us' );
        }

        $buttonLink = admin_url( 'admin.php?page=wc-settings&tab=quick-enquiry-us#/license-management' );
        $buttonText = __( 'License Management', 'quick-enquiry-us' );
        ?>
        <div class="plugin-license-notice notice notice-error is-dismissible">
            <p>
                <?php echo esc_html( $message ); ?>
                <em>
                    <a href="<?php echo esc_url( $buttonLink ); ?>">
                        <?php echo esc_html( $buttonText ); ?>
                    </a>
                </em>
            </p>
        </div>
        <?php
    }

}
