<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Gestpay for WooCommerce
 *
 * Copyright: © 2013-2016 Mauro Mascia (info@mauromascia.com)
 * Copyright: © 2017-2021 Axerve S.p.A. - Gruppo Banca Sella (https://www.axerve.com - ecommerce@sella.it)
 * Copyright: © 2024-2025 Fabrick S.p.A. - Gruppo Banca Sella (https://www.fabrick.com - ecommerce@sella.it)
 * 
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Questo è un file di esempio che dimostra come gestire il routing dei pagamenti
 * in una configurazione multi-sito. I domini utilizzati sono puramente dimostrativi.
 * 
 * NON utilizzare questo file in produzione senza prima averlo adattato alle proprie necessità.
 */

if ( isset( $_GET['a'] ) && isset( $_GET['b'] ) ) {

  // Change this if using testing or real environment
  $is_test = false; // true

  // Set parameters to be decrypted
  $params = new stdClass();
  $params->shopLogin = sanitize_text_field( wp_unslash( $_GET['a'] ) );
  $params->CryptedString = sanitize_text_field( wp_unslash( $_GET['b'] ) );

  $crypt_url = $is_test
    ? "https://sandbox.gestpay.net/gestpay/GestPayWS/WsCryptDecrypt.asmx?WSDL"
    : "https://ecomms2s.sella.it/gestpay/GestPayWS/WSCryptDecrypt.asmx?WSDL";

  try {
    $client = new SoapClient( $crypt_url );
  }
  catch ( Exception $e ) {
    echo "Soap Client error: " . esc_html( $e->getMessage() );
    exit( 1 );
  }

  try {
    $objectresult = $client->Decrypt( $params );
  }
  catch ( Exception $e ) {
    echo "GestPay Decrypt error: " . esc_html( $e->getMessage() );
    exit( 1 );
  }

  $xml = simplexml_load_string( $objectresult->DecryptResult->any );

  $src = ( string ) $xml->CustomInfo; // for example "SITE=something"

if ( ! empty( $src ) && $src == 'SITE=site1' ) {
    $url = "https://example-site-1.test/"; // Example domain for demonstration purposes only
}
else {
    $url = "https://example-site-2.test/"; // Example domain for demonstration purposes only
}

  // Process the Payment into the right website.
  $full_url = $url . "?wc-api=WC_Gateway_Gestpay&a=" . $params->shopLogin . "&b=" . $params->CryptedString;

  if ( isset( $_GET['s2s'] ) ) {
    // s2s call, process in background
    $full_url = $full_url . "&s2s=1";
    $contents = file_get_contents( $full_url );
  }
  else {
    // Redirect the customer the right website.
    header( "Location: " . $full_url );
  }
}
?>
