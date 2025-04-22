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

class Gestpay_Helper {

    public function get_error_message( $error_code ) {
        $messages = array(
            'PAYMENT_DENIED' => __( 'Pagamento rifiutato dalla banca', 'gestpay-for-woocommerce' ),
            'PAYMENT_CANCELED' => __( 'Pagamento annullato', 'gestpay-for-woocommerce' ),
            'PAYMENT_EXPIRED' => __( 'Pagamento scaduto', 'gestpay-for-woocommerce' ),
            'PAYMENT_ERROR' => __( 'Errore durante il pagamento', 'gestpay-for-woocommerce' ),
            'PAYMENT_PENDING' => __( 'Pagamento in attesa', 'gestpay-for-woocommerce' ),
            'PAYMENT_REFUNDED' => __( 'Pagamento rimborsato', 'gestpay-for-woocommerce' ),
            'PAYMENT_VOIDED' => __( 'Pagamento annullato', 'gestpay-for-woocommerce' ),
            'PAYMENT_FAILED' => __( 'Pagamento fallito', 'gestpay-for-woocommerce' ),
            'PAYMENT_UNKNOWN' => __( 'Stato del pagamento sconosciuto', 'gestpay-for-woocommerce' ),
        );

        return isset( $messages[ $error_code ] ) ? $messages[ $error_code ] : __( 'Errore sconosciuto', 'gestpay-for-woocommerce' );
    }
} 