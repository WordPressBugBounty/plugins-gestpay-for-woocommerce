<?php
/**
 * Gestpay for WooCommerce - Blocks Integration
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
 * Gestpay Blocks Integration
 *
 * @since 20250912
 */
final class Gestpay_Blocks_Integration extends AbstractPaymentMethodType {
    /**
     * Payment method name defined by payment methods extending this class.
     *
     * @var string
     */
    protected $name = 'wc_gateway_gestpay';

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
        $this->settings = get_option( 'woocommerce_wc_gateway_gestpay_settings', [] );
    }

    /**
     * Returns if this payment method should be active. If false, the scripts will not be enqueued.
     *
     * @return boolean
     */
    public function is_active() {
        return filter_var( $this->get_setting( 'enabled', false ), FILTER_VALIDATE_BOOLEAN );
    }

    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     *
     * @return array
     */
    public function get_payment_method_script_handles() {
        wp_register_script(
            'wc-payment-method-gestpay',
            plugin_dir_url( GESTPAY_MAIN_FILE ) . 'assets/js/blocks/wc-payment-method-gestpay.js',
            array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-components', 'wp-blocks', 'wp-data', 'wp-hooks', 'wp-i18n' ),
            '20251028',
            true
        );
        return [ 'wc-payment-method-gestpay' ];
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script.
     *
     * @return array
     */
    public function get_payment_method_data() {
        // Get basic settings
        $title = $this->get_setting( 'title', 'Gestpay' );
        $description = $this->get_setting( 'description', '' );
        $account_type = get_option( 'wc_gestpay_account_type', 0 );
        $is_sandbox = "yes" === get_option( 'wc_gestpay_test_url', 'no' );
        $is_s2s = defined( 'GESTPAY_PRO_TOKEN_AUTH' ) && GESTPAY_PRO_TOKEN_AUTH == $account_type;
        $is_iframe = defined( 'GESTPAY_PRO_TOKEN_IFRAME' ) && GESTPAY_PRO_TOKEN_IFRAME == $account_type;
        $cvv_required = "yes" === get_option( 'wc_gestpay_param_tokenization_send_cvv', 'no' );
        $buyer_name = "yes" === get_option( 'wc_gestpay_param_buyer_name', 'no' );
        $can_save_cards = "yes" === get_option( 'wc_gestpay_param_tokenization_save_token', 'no' );
        
        // Get icon safely
        $icon = '';
        if ( class_exists( 'WC_Gateway_Gestpay' ) && defined( 'GESTPAY_MAIN_FILE' ) ) {
            try {
                $gateway = new WC_Gateway_Gestpay();
                $icon = $gateway->get_icon();
            } catch ( Exception $e ) {
                // Fallback to default icon
                $icon = '<img src="' . plugin_dir_url( GESTPAY_MAIN_FILE ) . 'images/gestpay-logo.png" alt="Gestpay" />';
            }
        } else {
            // Fallback to default icon
            $icon = '<img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'images/gestpay-logo.png" alt="Gestpay" />';
        }

        // Get strings
        $strings = include 'translatable-strings.php';
        
        $img_url = plugin_dir_url( GESTPAY_MAIN_FILE );
        $endpoint = get_permalink( wc_get_page_id( 'myaccount' ) ) . GESTPAY_ACCOUNT_TOKENS_ENDPOINT;

        $saved_cards = $can_save_cards ? get_user_meta( get_current_user_id(), GESTPAY_META_TOKEN, true ) : false;
        $default_token = 'new-card';
        if (is_array($saved_cards) && !empty($saved_cards)) {
            $wc_gestpay_cc_default = get_user_meta( get_current_user_id(), '_wc_gestpay_cc_default', true );
            $helper = new WC_Gateway_GestPay_Helper();
            foreach ($saved_cards as $k => $card) {
                $default = $card['token'] == $wc_gestpay_cc_default;
                $card['expir_str'] = sprintf( $strings['s2s_card_expire'],
                    esc_html( substr_replace( $card['token'], '**********', 2, -4 ) ),
                    esc_html( $card['month'] ),
                    esc_html( $card['year'] )
                );
                $card['token'] = esc_attr($helper->crypt_token($card['token']));
                if ($default) $default_token = $card['token'];
                $saved_cards[$k] = $card;
            }
        } else {
            $saved_cards = false;
        }

        $sandbox = '
        <style type="text/css">
            #gestpay-s2s-sandbox{float:left;padding:10px 20px;background:#ececec}
            #gestpay-s2s-sandbox *{float:left;line-height:1.5;}
        </style>
        <p id="gestpay-s2s-sandbox">
            <small>
                <strong>Test Mode</strong><br>
                <a href="https://docs.axerve.com/en/testing/testing-cards" target="_blank">gestpay test cards</a>
            </small>
        </p>';

        $infoModal = '
        <div style="display: none; width: 50%" id="gestpay-fancybox-cvv-modal">
            <div class="gestpay-fancybox-section">
                <h1>' . esc_html( $strings['gestpay_cvv_help_h1_title'] ) . '</h1>
                <p>' . esc_html( $strings['gestpay_cvv_help_h1_text'] ) . '</p>
            </div>
            <div class="gestpay-fancybox-section">
                <h3>' . esc_html( $strings['gestpay_cvv_help_visa_title'] ) . '</h3>
                <p>
                    <p class="gestpay-fancybox-cvv-textcard-text">' . esc_html( $strings['gestpay_cvv_help_visa_text'] ) . '</p>
                    <p class="gestpay-fancybox-cvv-textcard-card"><img src="' . esc_url( $img_url . '/images/CVV2.gif' ) . '"></p>
                </p>
            </div>
            <div class="gestpay-fancybox-section">
                <h3>' . esc_html( $strings['gestpay_cvv_help_amex_title'] ) . '</h3>
                <p>
                    <p class="gestpay-fancybox-cvv-textcard-text">' . esc_html( $strings['gestpay_cvv_help_amex_text'] ) . '</p>
                    <p class="gestpay-fancybox-cvv-textcard-card"><img src="' . esc_url( $img_url . '/images/4DBC.gif' ) . '"></p>
                </p>
            </div>
        </div>';
        
        return [
            'title'       => $title,
            'description' => $description,
            'supports'    => $this->get_supported_features(),
            'icon'        => $icon,
            'accountType' => $account_type,
            'isSandbox'   => $is_sandbox,
            'isS2S'       => $is_s2s,
            'isIframe'    => $is_iframe,
            'savedCards' => $saved_cards ? array_values($saved_cards) : false,
            'defaultToken' => $default_token,
            'newCardText' => $strings['s2s_use_new_card'],
            'manageCardsEndpoint' => $endpoint,
            'manageCardsText' => $strings['s2s_manage_cards'],
            'cvvRequired' => $cvv_required,
            'buyerName' => $buyer_name,
            'enabled'     => $this->is_active(),
            'cardNumberLabel' => $strings['s2s_ccn'],
            'cardCVVLabel' => $strings['s2s_card_cvv'],
            'expDateLabel' => $strings['s2s_card_exp_date'],
            'expMonthLabel' => $strings['s2s_card_exp_month'],
            'expYearLabel' => $strings['s2s_card_exp_year'],
            'cardholderNameLabel' => $strings['s2s_buyer_name'],
            'infoBox' => $strings['gestpay_cvv_help'],
            'infoModal' => $infoModal,
            'sandbox' => $sandbox,
        ];
    }

    /**
     * Returns an array of supported features.
     *
     * @return string[]
     */
    public function get_supported_features() {
        // Default features for Gestpay
        $features = array( 'products' );
        
        // Try to get features from the gateway if it exists
        if ( class_exists( 'WC_Gateway_Gestpay' ) ) {
            try {
                $gateway = new WC_Gateway_Gestpay();
                if ( isset( $gateway->supports ) && is_array( $gateway->supports ) ) {
                    $features = array_filter( $gateway->supports, array( $gateway, 'supports' ) );
                }
            } catch ( Exception $e ) {
                // Fallback to default features
            }
        }

        /**
         * Filter to control what features are available for each payment gateway.
         *
         * @since 4.4.0
         *
         * @param array $features List of supported features.
         * @param string $name Gateway name.
         * @return array Updated list of supported features.
         */
        return apply_filters( '__experimental_woocommerce_blocks_payment_gateway_features_list', $features, $this->get_name() );
    }
} 