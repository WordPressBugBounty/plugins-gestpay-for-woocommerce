<?php
/**
 * Gestpay Bancomatpay for WooCommerce - Blocks Integration
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
 * Gestpay Bancomatpay Blocks Integration
 *
 * @since 20250912
 */
final class Gestpay_Bancomatpay_Blocks_Integration extends AbstractPaymentMethodType {
    /**
     * Payment method name defined by payment methods extending this class.
     *
     * @var string
     */
    protected $name = 'wc_gateway_gestpay_bancomatpay';

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
        $this->settings = get_option( 'woocommerce_wc_gateway_gestpay_bancomatpay_settings', [] );
    }

    /**
     * Returns if this payment method should be active. If false, the scripts will not be enqueued.
     *
     * @return boolean
     */
    public function is_active() {
        // Check if Bancomatpay is enabled in GestPay Pro settings
        $bancomatpay_enabled = "yes" === get_option( 'wc_gestpaypro_bancomatpay', 'no' );
        
        // Check if payment types are enabled
        $payment_types_enabled = "yes" === get_option( 'wc_gestpay_param_payment_types', 'no' );
        
        return $bancomatpay_enabled && $payment_types_enabled;
    }

    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     *
     * @return array
     */
    public function get_payment_method_script_handles() {
        wp_register_script(
            'wc-payment-method-gestpay-bancomatpay',
            plugin_dir_url( GESTPAY_MAIN_FILE ) . 'assets/js/blocks/wc-payment-method-gestpay-bancomatpay.js',
            array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-components', 'wp-blocks', 'wp-data', 'wp-hooks', 'wp-i18n' ),
            '20251028',
            true
        );
        return [ 'wc-payment-method-gestpay-bancomatpay' ];
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script.
     *
     * @return array
     */
    public function get_payment_method_data() {
        // Get basic settings
        $title = $this->get_setting( 'title', 'Bancomatpay' );
        $description = $this->get_setting( 'description', 'Pay securely with Bancomatpay' );
        $account_type = get_option( 'wc_gestpay_account_type', 0 );
        $is_sandbox = "yes" === get_option( 'wc_gestpay_test_url', 'no' );
        
        // Get icon safely
        if ( class_exists( 'WC_Gateway_Gestpay_BANCOMATPAY' ) && defined( 'GESTPAY_MAIN_FILE' ) ) {
            try {
                $gateway = new WC_Gateway_Gestpay_BANCOMATPAY();
                $icon = $gateway->get_icon();
            } catch ( Exception $e ) {
                // Fallback
                $icon = '';
            }
        } else {
            // Fallback
            $icon = '';
        }
        
        $sandbox = '';
        if ( $is_sandbox ) {
            $sandbox = '
            <style type="text/css">
                #gestpay-bancomatpay-sandbox{padding:10px 20px;background:#ececec}
                #gestpay-bancomatpay-sandbox *{line-height:1.5;}
            </style>
            <p id="gestpay-bancomatpay-sandbox">
                <small>
                    <strong>Bancomatpay Test Mode</strong>
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
            'paymentType' => 'BANCOMATPAY',
            'redirectUrl' => $this->get_bancomatpay_redirect_url(),
        ];
    }

    /**
     * Returns an array of supported features.
     *
     * @return string[]
     */
    public function get_supported_features() {
        // Bancomatpay supports products and subscriptions
        $features = array( 'products' );
        
        // Check if WooCommerce Subscriptions is active
        if ( class_exists( 'WC_Subscriptions' ) || function_exists( 'wcs_is_subscription' ) ) {
            $features[] = 'subscriptions';
        }

        /**
         * Filter to control what features are available for Bancomatpay payment gateway.
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
     * Get Bancomatpay redirect URL for payment processing
     *
     * @return string
     */
    private function get_bancomatpay_redirect_url() {
        // Get the checkout URL
        $checkout_url = wc_get_checkout_url();
        
        // Add Bancomatpay-specific parameters
        $redirect_url = add_query_arg(
            array(
                'gestpay_payment_type' => 'BANCOMATPAY',
                'gestpay_blocks' => '1',
                'payment_method' => 'wc_gateway_gestpay_bancomatpay',
            ),
            $checkout_url
        );
        
        return $redirect_url;
    }
}
