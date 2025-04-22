<?php

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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// this will be assigned to WC_Gateway_Gestpay->strings[]
return array(

    "gateway_enabled" =>
        __( "Enable/Disable", 'gestpay-for-woocommerce' ),

    "gateway_enabled_label" =>
        __( "Enable Gestpay when selected.", 'gestpay-for-woocommerce' ),

    "gateway_title" =>
        __( "Title", 'gestpay-for-woocommerce' ),

    "gateway_title_label" =>
        __( "The title of the payment method which the buyer sees at checkout.", 'gestpay-for-woocommerce' ),

    "gateway_desc" =>
        __( "Description", 'gestpay-for-woocommerce' ),

    "gateway_desc_label" =>
        __( "The description of the payment method which the buyer sees at checkout.", 'gestpay-for-woocommerce' ),

    "gateway_consel_id" =>
        __( "Consel Merchant ID", 'gestpay-for-woocommerce' ),

    "gateway_consel_code" =>
        __( "Cosel Merchant Code Convention", 'gestpay-for-woocommerce' ),

    "gateway_consel_merchant_pro" =>
        __( "Insert the code given by Consel, for example WIN, MPF, WIP or JMP.", 'gestpay-for-woocommerce' ),

    "gateway_overwrite_cards" =>
        __( "Overwrite card icons", 'gestpay-for-woocommerce' ),

    "gateway_overwrite_cards_label" =>
        __( "Select the cards you want to display as an icon (note: the fact that they are really active or not depends on the Gestpay settings)", 'gestpay-for-woocommerce' ),

    "crypted_string" =>
        __( "Crypted string", 'gestpay-for-woocommerce' ),

    "crypted_string_info" =>
        __( "You are forcing the re-encryption process: this may cause multiple calls to the GestPay webservice.", 'gestpay-for-woocommerce' ),

    "transaction_error" =>
        /* translators: 1: Order ID, 2: Error message */
        __( 'Transaction for order %1$s failed with error %2$s', 'gestpay-for-woocommerce' ),

    "transaction_thankyou" =>
        /* translators: %s: Transaction ID */
        __( "Thank you for shopping with us. Your transaction %s has been processed correctly. We will be shipping your order to you soon.", 'gestpay-for-woocommerce' ),

    "transaction_ok" =>
        /* translators: %s: Transaction ID */
        __( "Transaction for order %s has been completed successfully.", 'gestpay-for-woocommerce' ),

    "soap_req_error" =>
        /* translators: %s: Error message */
        __( "Fatal Error: Soap Client Request Exception with error %s", 'gestpay-for-woocommerce' ),

    "payment_error" =>
        /* translators: 1: Error code, 2: Error message */
        __( 'Gestpay Error #%1$s on Payment phase: %2$s', 'gestpay-for-woocommerce' ),

    "request_error" =>
        __( "There was an error with your request, please try again.", 'gestpay-for-woocommerce' ),

    "iframe_pay_progress" =>
        __( "Payment in progress...", 'gestpay-for-woocommerce' ),

    "iframe_loading" =>
        __( "Loading...", 'gestpay-for-woocommerce' ),

    "iframe_browser_err" =>
        __( "Error: Browser not supported", 'gestpay-for-woocommerce' ),

    "s2s_error" =>
        __( "Error", 'gestpay-for-woocommerce' ),

    "s2s_card" =>
        __( "Card", 'gestpay-for-woocommerce' ),

    "s2s_remove" =>
        __( "Remove", 'gestpay-for-woocommerce' ),

    "s2s_default" =>
        __( "Default", 'gestpay-for-woocommerce' ),

    "s2s_expire" =>
        __( "Expires", 'gestpay-for-woocommerce' ),

    "s2s_token_add_default" =>
        __( "Set as default", 'gestpay-for-woocommerce' ),

    "s2s_token_remove_default" =>
        __( "Remove from default", 'gestpay-for-woocommerce' ),

    "s2s_token_delete" =>
        __( "Delete", 'gestpay-for-woocommerce' ),

    "s2s_token_error" =>
        __( "Validation error: please double check required fields and try again. If this error persists, please contact the site administrator.", 'gestpay-for-woocommerce' ),

    "s2s_no_cards" =>
        __( "There is not yet any token of credit card saved.", 'gestpay-for-woocommerce' ),

    "s2s_cant_save_cards" =>
        __( "The storage of the credit card token is disabled.", 'gestpay-for-woocommerce' ),

    "s2s_confirm_token_delete" =>
        __( "Are you sure you want to delete this card?", 'gestpay-for-woocommerce' ),

    "s2s_card_expire" =>
        /* translators: 1: Card number, 2: Expiration month, 3: Expiration year */
        __( '%1$s (expires %2$s/%3$s)', 'gestpay-for-woocommerce' ),

    "s2s_card_exp_date" =>
        __( "Expiration Date", 'gestpay-for-woocommerce' ),

    "s2s_card_exp_month" =>
        __( "Month", 'gestpay-for-woocommerce' ),

    "s2s_card_exp_year" =>
        __( "Year", 'gestpay-for-woocommerce' ),

    "s2s_card_cvv" =>
        __( "Card Security Code", 'gestpay-for-woocommerce' ),

    "s2s_proceed" =>
        __( "Proceed", 'gestpay-for-woocommerce' ),

    "s2s_manage_cards" =>
        __( "Manage Your Cards", 'gestpay-for-woocommerce' ),

    "s2s_use_new_card" =>
        __( "Use a new credit card", 'gestpay-for-woocommerce' ),

    "s2s_ccn" =>
        __( "Credit Card Number", 'gestpay-for-woocommerce' ),

    "s2s_buyer_name" =>
        __( "Cardholder Name", 'gestpay-for-woocommerce' ),

    "refund_err_1" =>
        __( "Order can't be refunded: Bank Transaction ID not found.", 'gestpay-for-woocommerce' ),

    "refund_err_2" =>
        __( "Order can't be refunded: Failed to get the SOAP client.", 'gestpay-for-woocommerce' ),

    "refund_ok" =>
        /* translators: %s: Amount refunded */
        __( 'REFUND OK: Amount refunded %s', 'gestpay-for-woocommerce' ),

    "delete_ok" =>
        /* translators: %s: Bank Transaction ID */
        __( 'Authorized transaction deleted successfully [BankTransactionID: %s]', 'gestpay-for-woocommerce' ),

    "button_settle" =>
        __( "Settle", 'gestpay-for-woocommerce' ),

    "tip_settle" =>
        __( "You can do a financial confirmation of this authorized transaction if using the M.O.T.O. configuration with the separation between the authorization and the settlement phase.", 'gestpay-for-woocommerce' ),

    "confirm_settle" =>
        __( "Are you sure you want to settle this authorized transaction?", 'gestpay-for-woocommerce' ),

    "button_delete" =>
        __( "Delete", 'gestpay-for-woocommerce' ),

    "confirm_delete" =>
        __( "Are you sure you want to delete this authorized transaction?", 'gestpay-for-woocommerce' ),

    "tip_delete" =>
        __( "You can delete this authorized transaction if using the M.O.T.O. configuration with the separation between the authorization and the settlement phase.", 'gestpay-for-woocommerce' ),

    "subscr_approved" =>
        __( "GestPay Subscription Renewal Payment Approved", 'gestpay-for-woocommerce' ),

    "gestpay_cvv_help" =>
        __( "Where do I find the security code?", 'gestpay-for-woocommerce' ),

    "gestpay_cvv_help_h1_title" =>
        __( "Security code", 'gestpay-for-woocommerce' ),

    "gestpay_cvv_help_h1_text" =>
        __( "The security code (CVV2 or 4DDBC) is a number consisting of three or four digits kept separated from the main number of your credit card. The position of the security code may vary depending on the company that issued your credit card.", 'gestpay-for-woocommerce' ),

    "gestpay_cvv_help_visa_title" =>
        __( "Visa / Mastercard / Maestro", 'gestpay-for-woocommerce' ),

    "gestpay_cvv_help_visa_text" =>
        __( "For Visa and Mastercard the three-digit security number (CVV2) is printed on the back of the card right after the card number.", 'gestpay-for-woocommerce' ),

    "gestpay_cvv_help_amex_title" =>
        __( "American Express", 'gestpay-for-woocommerce' ),

    "gestpay_cvv_help_amex_text" =>
        __( "For American Express cards the four-digit security code (4DBC) is printed on the front of the card, either to the left or right of the American Express card number.", 'gestpay-for-woocommerce' ),

    "tls_text_error" =>
        __( "Warning! We are sorry, but the browser you are using is no longer supported. You cannot complete payment with this browser because it is not secure, but you can update it or use a modern browser:", 'gestpay-for-woocommerce' ),

    "mybank_payoff" =>
        __( "Secure payments through your online banking account.<br>Learn more", 'gestpay-for-woocommerce' ),

    "gestpay_mybank_list_intro" =>
        __( "Please select your bank from the list. You will be redirected to your home banking portal to complete the operation", 'gestpay-for-woocommerce' ),

    "gestpay_mybank_list" =>
        __( "Bank/Institute Selection:", 'gestpay-for-woocommerce' ),

    "gestpay_mybank_list_notfound" =>
        __( "Can't find your bank? Click here.", 'gestpay-for-woocommerce' ),

    "gestpay_mybank_list_must" =>
        __( "Please select a bank/institute to pay with MyBank.", 'gestpay-for-woocommerce' ),

);
