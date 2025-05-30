<?php

/**
 * Gestpay for WooCommerce
 *
 * Copyright: © 2017-2021 Axerve S.p.A. - Gruppo Banca Sella (https://www.axerve.com - ecommerce@sella.it)
 *
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Gateway_Gestpay_COMPASS extends WC_Gateway_Gestpay {
    public function __construct() {
        $this->set_this_gateway_params( 'Gestpay Compass' );
        $this->paymentType = 'COMPASS';
        $this->Helper->init_gateway( $this );
        $this->set_this_gateway();
        $this->add_actions();
    }
}