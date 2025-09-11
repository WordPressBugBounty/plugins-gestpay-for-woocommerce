<?php

/**
 * Gestpay for WooCommerce
 *
 * Copyright: © 2013-2016 Mauro Mascia (info@mauromascia.com)
 * Copyright: © 2017-2021 Axerve S.p.A. - Gruppo Banca Sella (https://www.axerve.com - ecommerce@sella.it)
 * Copyright: © 2024-2025 Fabrick S.p.A. - Gruppo Banca Sella (https://www.fabrick.com - ecommerce@sella.it)
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handle the iFrame version.
 */
class Gestpay_Iframe {

    public function __construct( $gestpay ) {

        // Get a pointer to the main class and to the helper.
        $this->Gestpay = $gestpay;
        $this->Helper = $gestpay->Helper;
        $this->can_have_cards = FALSE;

        if ( $this->Gestpay->save_token && $this->Helper->is_subscriptions_active() ) {
            // Add subscription features
            include_once 'class-gestpay-subscriptions.php';
            $this->Subscr = new Gestpay_Subscriptions( $this->Gestpay );
        }

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Enqueue additional Javascript
     */
    public function enqueue_scripts() {

        $fancybox_path = $this->Helper->plugin_url . 'lib/jquery.fancybox';
        wp_enqueue_style( 'gestpay-for-woocommerce-fancybox-css', $fancybox_path . '.min.css' );
        wp_enqueue_script( 'gestpay-for-woocommerce-fancybox-js', $fancybox_path . '.min.js', array( 'jquery' ), WC_VERSION, true );
    }

    public function set_cookie( $name, $content, $time = false ) {
        if ( ! $time ) {
            $time = time()+1200;
        }

        if ( ! is_ssl() ) {
            return setcookie( $name, $content, $time, COOKIEPATH, COOKIE_DOMAIN );
        }
        else {
            if (PHP_VERSION_ID < 70300) {
                return setcookie(
                    $name,
                    $content,
                    $time,
                    COOKIEPATH,
                    COOKIE_DOMAIN . '; SameSite=None',
                    true,
                    true
                );
            } else {
                return setcookie(
                    $name,
                    $content,
                    [
                        'expires' => $time,
                        'path' => COOKIEPATH,
                        'domain' => COOKIE_DOMAIN,
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'None',
                    ]
                );
            }
        }
    }

    /**
     * Load the encoded string.
     *
     * At the first page loading, the encoded string is generated calling the Gestpay WS
     * and saved into a Cookie.
     * Then, after the 3DSecure authentication, the page is loaded again, and to prevent
     * another generation of the encoded string, we load it from the previously created Cookie.
     * In the check_gateway_response() of gestpay-for-woocommerce.php we must be sure the Cookie
     * is cleaned up after the payment or there will be an error in the next order (if the cookie
     * is not already expired by itself).
     */
    public function retrieve_encoded_string( $order ) {

        if ( empty( $_COOKIE['GestPayEncString'] ) ) {
            // First call
            $input_params = $this->Gestpay->get_ab_params( $order );

            if ( empty( $input_params['b'] ) ) {
                return FALSE;
            }

            $this->set_cookie( 'GestPayEncString', $input_params['b'] );

            return $input_params['b'];
        }
        else {
            // Second call
            return sanitize_text_field( wp_unslash( $_COOKIE['GestPayEncString'] ) );
        }
    }

    /**
     * Generate the receipt page
     */
    public function receipt_page( $order ) {
        //error_log('Gestpay iFrame - Inizio receipt_page');
        $encString = $this->retrieve_encoded_string( $order );
        //error_log('Gestpay iFrame - Stringa crittografata: ' . $encString);

        // Maybe get the paRes parameter for 2nd call, due to 3D enrolled credit card
        $paRes = ! empty( $_REQUEST["PaRes"] ) ? sanitize_text_field( wp_unslash( $_REQUEST["PaRes"] ) ) : "";
        $transKey = ! empty( $_COOKIE['TransKey'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['TransKey'] ) ) : "";
        //error_log('Gestpay iFrame - paRes: ' . $paRes . ', transKey: ' . $transKey);

        // Output the HTML for the iFrame payment box.
        require_once 'checkout-payment-fields.php';
        wp_enqueue_script( 'gestpay-for-woocommerce-iframe-js', $this->Gestpay->iframe_url );
        wp_enqueue_script( 'gestpay-iframe-ready', $this->Helper->plugin_url . "assets/js/iFrameReady.js", ['jquery'], '' , true);
        $localize = [
            'shopLogin' => esc_js( $this->Gestpay->shopLogin ),
            'encString' => esc_js( $encString ),
            'paRes' => esc_js( $paRes ),
            'transKey' => esc_js( $transKey ),
            'iframe_pay_progress' => esc_js( $this->Gestpay->strings['iframe_pay_progress'] ),
            'ws_S2S_resp_url' => esc_js( $this->Gestpay->ws_S2S_resp_url ),
            'sameSite' => is_ssl() ? '; SameSite=None; Secure' : '',
            'pagam3d_url' => $this->Gestpay->pagam3d_url,
            'is_cvv_required' => $this->Gestpay->is_cvv_required,
            'iframe_loading' => $this->Gestpay->strings['iframe_loading'],
            'iframe_browser_err' => $this->Gestpay->strings['iframe_browser_err']
        ];
        wp_localize_script( "gestpay-iframe-ready", "gestpayReadyObject", $localize);
        //error_log('Gestpay iFrame - Script iframe caricato: ' . $this->Gestpay->iframe_url);
        
    }

    /**
     * Clean up iframe cookies.
     */
    function delete_cookies() {

        $this->set_cookie( 'GestPayEncString', '', 1 );
        $this->set_cookie( 'TransKey', '', 1 );
    }

}
