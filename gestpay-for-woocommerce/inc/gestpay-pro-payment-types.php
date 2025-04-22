<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Gestpay for WooCommerce
 *
 * Copyright: © 2013-2016 Mauro Mascia (info@mauromascia.com)
 * Copyright: © 2017-2021 Axerve S.p.A. - Gruppo Banca Sella (https://www.axerve.com - ecommerce@sella.it)
 * Copyright: © 2024-2025 Fabrick S.p.A. - Gruppo Banca Sella (https://www.fabrick.com - ecommerce@sella.it)
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

add_filter( 'woocommerce_payment_gateways', 'gestpay_woocommerce_payment_gateways_add_gestpay_pro_payment_types' );
function gestpay_woocommerce_payment_gateways_add_gestpay_pro_payment_types( $methods ) {
    $payment_types = array(
        //'bon',
        'paypal',
        'paypal_bnpl',
        'mybank',
        'consel',
        'masterpass',
        'compass',
        'bancomatpay',
    );

    // Always add main class.
    $methods[] = 'WC_Gateway_Gestpay';

    if ( 'yes' == get_option( 'wc_gestpay_param_payment_types' ) ) {

        foreach ( $payment_types as $pt ) {
            $pt_class = 'WC_Gateway_Gestpay_' . strtoupper( $pt );
            if ( 'yes' == get_option( 'wc_gestpaypro_'. $pt ) ) {
                require_once 'payment_types/gestpay-' . $pt . '.php';
                $methods[] = $pt_class;
            }
        }

    }

    return $methods;
}
