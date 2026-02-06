<?php
/**
 * Gestpay MyBank for WooCommerce - Blocks Integration
 *
 * @package Gestpay_For_WooCommerce
 * @since 20250912
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Only load if WooCommerce Blocks is available
if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
    return;
}

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use Automattic\WooCommerce\Blocks\Assets\Api;

/**
 * Gestpay MyBank Blocks Integration
 *
 * @since 20250912
 */
final class Gestpay_MyBank_Blocks_Integration extends AbstractPaymentMethodType {
    /**
     * Payment method name defined by payment methods extending this class.
     *
     * @var string
     */
    protected $name = 'wc_gateway_gestpay_mybank';

    /**
     * An instance of the Asset Api
     *
     * @var Api
     */
    private $asset_api;

    /**
     * Constructor
     *
     * @param Api $asset_api An instance of Api.
     */
    public function __construct( Api $asset_api ) {
        $this->asset_api = $asset_api;
    }

    /**
     * Initializes the payment method type.
     */
    public function initialize() {
        $this->settings = get_option( 'woocommerce_wc_gateway_gestpay_mybank_settings', [] );
    }

    /**
     * Returns if this payment method should be active. If false, the scripts will not be enqueued.
     *
     * @return boolean
     */
    public function is_active() {
        // Check if MyBank is enabled in GestPay Pro settings
        $mybank_enabled = "yes" === get_option( 'wc_gestpaypro_mybank', 'no' );
        
        // Check if payment types are enabled
        $payment_types_enabled = "yes" === get_option( 'wc_gestpay_param_payment_types', 'no' );
        
        return $mybank_enabled && $payment_types_enabled;
    }

    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     *
     * @return array
     */
    public function get_payment_method_script_handles() {
        // selectWoo is declared as dependency for the payment method script, but it is not loaded in the admin area.
        // This is a workaround to ensure selectWoo is loaded in the admin area and solves the incompatibility alert issue.
        if (is_admin()) {
            wp_register_script(
                'selectWoo',
                '/wp-content/plugins/woocommerce/assets/js/selectWoo/selectWoo.full.min.js',
                [],
                false,
                true
            );
        }
        
        wp_enqueue_style( 'select2' );
        
        wp_register_script(
            'wc-payment-method-gestpay-mybank',
            plugin_dir_url( GESTPAY_MAIN_FILE ) . 'assets/js/blocks/wc-payment-method-gestpay-mybank.js',
            array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-components', 'wp-blocks', 'wp-data', 'wp-hooks', 'wp-i18n', 'selectWoo' ),
            '20251028',
            true
        );
        return [ 'wc-payment-method-gestpay-mybank' ];
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script.
     *
     * @return array
     */
    public function get_payment_method_data() {
        // Get basic settings
        $title = $this->get_setting( 'title', 'MyBank' );
        $description = $this->get_setting( 'description', __('Pay securely with MyBank', 'gestpay-for-woocommerce') );
        $account_type = get_option( 'wc_gestpay_account_type', 0 );
        $is_sandbox = "yes" === get_option( 'wc_gestpay_test_url', 'no' );
        
        // Get icon safely
        if ( class_exists( 'WC_Gateway_Gestpay_MYBANK' ) && defined( 'GESTPAY_MAIN_FILE' ) ) {
            try {
                $gateway = new WC_Gateway_Gestpay_MYBANK();
                $icon = $gateway->get_icon();
            } catch ( Exception $e ) {
                // Fallback
                $icon = '';
            }
        } else {
            // Fallback
            $icon = '';
        }
        
        // Only get MyBank specific data on checkout page or when blocks are active
        $mybank_data = array();
        if ( $this->should_load_mybank_data() ) {
            $mybank_data = $this->get_mybank_data();
        }
        
        $sandbox = '';
        if ( $is_sandbox ) {
            $sandbox = '
            <style type="text/css">
                #gestpay-mybank-sandbox{padding:10px 20px;background:#ececec}
                #gestpay-mybank-sandbox *{line-height:1.5;}
            </style>
            <p id="gestpay-mybank-sandbox">
                <small>
                    <strong>MyBank Test Mode</strong>
                </small>
            </p>';
        }
        
        return [
            'title'       => $title,
            'description' => $description,
            'supports'    => $this->get_supported_features(),
            'icon'        => $icon,
            'accountType' => $account_type,
            'isSandbox'   => $is_sandbox,
            'enabled'     => $this->is_active(),
            'sandbox'     => $sandbox,
            'paymentType' => 'MYBANK',
            'redirectUrl' => $this->get_mybank_redirect_url(),
            'mybankData'  => $mybank_data,
        ];
    }

    /**
     * Returns an array of supported features.
     *
     * @return string[]
     */
    public function get_supported_features() {
        // MyBank supports products and subscriptions
        $features = array( 'products' );
        
        // Check if WooCommerce Subscriptions is active
        if ( class_exists( 'WC_Subscriptions' ) || function_exists( 'wcs_is_subscription' ) ) {
            $features[] = 'subscriptions';
        }

        /**
         * Filter to control what features are available for MyBank payment gateway.
         *
         * @since 4.4.0
         *
         * @param array $features List of supported features.
         * @param string $name Gateway name.
         * @return array Updated list of supported features.
         */
        return apply_filters( '__experimental_woocommerce_blocks_payment_gateway_features_list', $features, $this->get_name() );
    }

    /**
     * Get MyBank redirect URL for payment processing
     *
     * @return string
     */
    private function get_mybank_redirect_url() {
        // Get the checkout URL
        $checkout_url = wc_get_checkout_url();
        
        // Add MyBank-specific parameters
        $redirect_url = add_query_arg(
            array(
                'gestpay_payment_type' => 'MYBANK',
                'gestpay_blocks' => '1',
                'payment_method' => 'wc_gateway_gestpay_mybank',
            ),
            $checkout_url
        );
        
        return $redirect_url;
    }

    /**
     * Determine if MyBank data should be loaded
     * Only load on checkout page or when blocks are active
     *
     * @return bool
     */
    private function should_load_mybank_data() {
        // Check if we're on checkout page
        if ( function_exists( 'is_checkout' ) && is_checkout() ) {
            return true;
        }
        
        // Check if we're in admin area (for blocks editor)
        if ( is_admin() ) {
            return false;
        }
        
        // Check if WooCommerce blocks are being used
        if ( function_exists( 'has_block' ) && has_block( 'woocommerce/checkout' ) ) {
            return true;
        }
        
        // Check if this is an AJAX request for blocks
        if ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && strpos( $_REQUEST['action'], 'woocommerce' ) !== false ) {
            return true;
        }
        
        return false;
    }

    /**
     * Get MyBank specific data (banks list, etc.)
     *
     * @return array
     */
    private function get_mybank_data() {
        $data = array();
        
        // Try to get MyBank gateway instance to access its methods
        if ( class_exists( 'WC_Gateway_Gestpay_MYBANK' ) ) {
            try {
                $gateway = new WC_Gateway_Gestpay_MYBANK();
                
                // Get banks list
                $banks = $gateway->get_mybanks();
                if ( is_array( $banks ) && ! isset( $banks['is_error'] ) ) {
                    $data['banks'] = $banks;
                } else {
                    $data['banks'] = array();
                    if ( isset( $banks['error_message'] ) ) {
                        $data['error'] = $banks['error_message'];
                    }
                }
                
                // Get required selection setting
                $data['requiredSelection'] = wp_is_mobile() || "yes" == $gateway->get_option( 'param_mybank_select_required_on_desktop' );
                
                // Get strings - initialize strings if not available
                if ( ! isset( $gateway->strings ) ) {
                    $gateway->init_strings();
                }
                
                if ( isset( $gateway->strings ) ) {
                    $data['strings'] = array(
                        'mybank_payoff' => isset( $gateway->strings['mybank_payoff'] ) ? $gateway->strings['mybank_payoff'] : '',
                        'gestpay_mybank_list_intro' => isset( $gateway->strings['gestpay_mybank_list_intro'] ) ? $gateway->strings['gestpay_mybank_list_intro'] : '',
                        'gestpay_mybank_list_notfound' => isset( $gateway->strings['gestpay_mybank_list_notfound'] ) ? $gateway->strings['gestpay_mybank_list_notfound'] : '',
                        'gestpay_mybank_list_must' => isset( $gateway->strings['gestpay_mybank_list_must'] ) ? $gateway->strings['gestpay_mybank_list_must'] : '',
                    );
                }
                
            } catch ( Exception $e ) {
                $data['error'] = 'Failed to initialize MyBank gateway: ' . $e->getMessage();
            }
        }
        
        return $data;
    }
}
