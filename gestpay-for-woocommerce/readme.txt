=== Gestpay for WooCommerce ===
Contributors: easynolo, netingweb
Tags: woocommerce, payment gateway, payment, credit card, gestpay, gestpay starter, gestpay pro, gestpay professional, banca sella, sella.it, easynolo, netingweb, axerve, netingweb, fabrick, iframe, direct payment gateway
Requires at least: 4.7
Requires PHP: 7.0
Tested up to: 6.8
Stable tag: 20250603
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 3.0
WC tested up to: 9.4.2

Axerve Free Plugin for Woocommerce extends WooCommerce providing the payment gateway Axerve.

== Description ==

Axerve Free Plugin for Woocommerce allows you to use [Axerve](https://www.axerve.com/ "Axerve Website") on your WooCommerce-powered website.

There are four operational modes in this plugin, which depends on Axerve version you are using:

* Axerve Starter
* Axerve Professional
* Axerve Professional On Site
* Axerve Professional iFrame

[Click here to read the full usage documentation on Axerve](https://docs.axerve.com/it/plugin/woocommerce/ "Axerve for WooCommerce - Usage Documentation").

== Actions and filters list ==

Here is a list of filters and actions used in this plugin:

= Actions =
* gestpay_before_processing_order
* gestpay_after_order_completed
* gestpay_after_order_failed
* gestpay_after_order_pending
* gestpay_before_order_settle
* gestpay_order_settle_success
* gestpay_order_settle_fail
* gestpay_before_order_refund
* gestpay_order_refund_success
* gestpay_order_refund_fail
* gestpay_before_order_delete
* gestpay_order_delete_success
* gestpay_order_delete_fail
* gestpay_after_s2s_order_failed
* gestpay_on_renewal_payment_failure
* gestpay_my_cards_template_before_table
* gestpay_my_cards_template_after_table

= Filters =
* gestpay_gateway_parameters
* gestpay_encrypt_parameters
* gestpay_settings_tab
* gestpay_my_cards_template
* gestpay_cvv_fancybox
* gestpay_gateway_cards_images
* gestpay_alter_order_id -> this can be used to add, for example, a prefix to the order ID
* gestpay_revert_order_id -> this must be used to revert back the order ID changed with the `gestpay_alter_order_id` filter
* gestpay_s2s_validate_payment_fields
* gestpay_s2s_payment_fields_error_strings

== Installation ==

1. Ensure you have the WooCommerce 3+ plugin installed
2. Search "Gestpay for WooCommerce" or upload and install the zip file, in the same way you'd install any other plugin.
3. Read the [usage documentation on Axerve](https://docs.axerve.com/it/plugin/woocommerce/ "Gestpay for WooCommerce - Usage Documentation").

== Changelog ==

= 20250603 =
* Fix: Removed unnecessary error_log messages that were causing log file inflation
* Fix: Cleaned up debug logging in the main gateway class
* Checks: Verified compatibility with WordPress 6.8, WooCommerce 9.4.2

= 20250530 =
* Security: Impreved Credit card tokens encryption
* Security: Improved AJAX/CSRF security
* Fix: 3DS2 payment flows issues fixed
* Fix: Improved TLS 1.2 connection handling
* Fix: Corrected IPv6 handling message
* Fix: PHP Warning: Undefined property: Gestpay_Cards::$current_user_id
* Fix: Notice: Function _load_textdomain_just_in_time was called incorrectly
* Checks: Verified compatibility with WordPress 6.8, WooCommerce 9.4.2

= 20250523 =
* Fix: Fixed CVV label HTML output in iframe mode
* Security: Nothing added
* Improvement: Nothing added
* Checks: Verified compatibility with WordPress 6.8, WooCommerce 9.4.2
* Note: Added wp_kses_post in output label iframe mode


= 20250522 =
* Fix: Rimosso il controllo TLS nel browser che causava problemi con alcuni browser
* Security: Nothing added
* Improvement: Nothing added
* Checks: Verified compatibility with WordPress 6.8, WooCommerce 9.4.2
* Note: Questa modifica rimuove il controllo TLS nel browser che era stato reintrodotto accidentalmente

= 20250521 =
* Fix: Improved iframe payment loading and stability
  - Fixed JavaScript loading order for payment scripts
  - Added robust BrowserEnabled check
  - Optimized iframe initialization code
  - Fixed "BrowserEnabled is not defined" error
* Security: Nothing added
* Improvement: Enhanced payment iframe reliability
* Checks: Verified compatibility with WordPress 6.8, WooCommerce 9.4.2
* Note: This update improves the payment iframe stability and fixes loading issues

= 20250520 =
* Security: enforced CSRF protection
* Fix: IPv6 warning (not fully supported yet)

= 20250508 =
* Fix: HPOS compatibility fixed and enhanced. Support to Woocommerce Subscriptions

= 20250418 =
* Security: All user inputs data have been sanitized and all outputs have been escaped
* License: Copyright headers updated
* Fix:
  - strip_tag to wp_strip_tag and date to gmdate
  - removed the use of the HEREDOCS/NNOWDOCS syntax as it's not allowd by Worpress guidelines
  - some remote images included int the images folder
  - some functions have been correctly prefixed (gestpay_)
* Improvement: Nothing added
* Checks: Nothing added
* Note: Questa modifica non influisce sulla funzionalità del plugin ma migliora la sicurezza complessiva e segue le linee guida di Wordpress

= 20250417 =
* Security: Migliorata la sicurezza nelle chiamate al servizio di identificazione IP
  - Aggiornato il protocollo da HTTP a HTTPS per le chiamate a icanhazip.com
  - Aggiunta documentazione sulla limitazione IPv6 del gateway
* Documentation: Aggiornata la documentazione sui servizi esterni
  - Aggiunta sezione "External services" nel readme
  - Documentato l'utilizzo del servizio icanhazip.com
  - Chiariti i domini di esempio nei file di test
* Fix: Nothing added
* Improvement: Nothing added
* Checks: Nothing added
* Note: Queste modifiche migliorano la sicurezza e la trasparenza del plugin senza influire sulla funzionalità principale

= 20250416 =
* Security: Aggiunta protezione contro accesso diretto ai file PHP
  - Implementato il controllo ABSPATH in tutti i file PHP del plugin
  - Migliorata la sicurezza prevenendo l'esecuzione diretta dei file al di fuori del contesto WordPress
  - File interessati: gestpay-for-woocommerce.php, sample/gestpay-pro-smistamento.php e file nella directory inc/
* License: Aggiornamento della licenza del plugin da GPLv3 a GPLv2
* Fix: Nothing added
* Improvement: Nothing added
* Checks: Nothing added
* Note: Questa modifica non influisce sulla funzionalità del plugin ma migliora la sicurezza complessiva

= 20250415 =
* Fix: Internazionalizzazione delle stringhe secondo le best practice WordPress
  - Corretto l'uso di variabili come testo o dominio di traduzione nelle funzioni gettext
  - Aggiunti commenti per i traduttori
  - Aggiornate le traduzioni in italiano
  - Migliorata la compatibilità con gli strumenti di traduzione WordPress
* Security: Nothing added 
* Improvement: Nothing added
* Checks: Verified compatibility WooCommerce 9.4.2
* Note: Nothing added

= 20250414 =
* Fix: Aggiunta dichiarazione formale della dipendenza da WooCommerce tramite header "Requires Plugins"
* Security: Nothing added
* Improvement: Nothing added
* Checks: Nothing added
* Note: Questa modifica migliora la gestione delle dipendenze a livello di WordPress senza modificare la funzionalità del plugin

= 20250412 =
* Fix: Aggiornati i requisiti minimi del plugin per riflettere le reali necessità:
  - WordPress: richiesta versione minima 4.7 per supporto REST API e funzionalità moderne
  - PHP: richiesta versione minima 7.0 per supporto HPOS, gestione moderna dei cookie e migliori performance
* Improvement: Allineata la dichiarazione dei requisiti tra file header del plugin e readme.txt
* Checks: Nessuna modifica alla compatibilità verificata (WordPress 6.7 e WooCommerce 9.4.2)
* Note: Questa modifica non influisce sulla funzionalità del plugin ma migliora la chiarezza dei requisiti di sistema

= 20241121 =
* Fix: Internazionalizzazione delle stringhe secondo le best practice WordPress
  - Corretto l'uso di variabili come testo o dominio di traduzione nelle funzioni gettext
  - Aggiunti commenti per i traduttori
  - Aggiornate le traduzioni in italiano
  - Migliorata la compatibilità con gli strumenti di traduzione WordPress
* Security: Nothing added 
* Improvement: Nothing added
* Checks: Verified compatibility WooCommerce 9.4.2
* Note: Nothing added

= 20241118 =
* Fix: Rewritten HPOS support from scratch
* Security: Nothing added 
* Improvement: Nothing added
* Checks: Verified compatibility with Wordpress 6.7, WooCommerce 9.4.1
* Note: Nothing added

= 20241107 =
* Fix: Payment icons visualization problem
* Security: Nothing added 
* Improvement: Nothing added
* Checks: Verified compatibility with Wordpress 6.6.2, WooCommerce 9.3.3
* Note: Nothing added

= 20240823 =
* Security: Nothing added 
* Improvement: Verified compatibility with Wordpress 6.6.1, WooCommerce 9.1.4 and Insert of HPOS Compatibility.
* Checks: Verified compatibility with Wordpress 6.6.1, WooCommerce 9.1.4 and Insert of HPOS Compatibility.
* Note: Nothing added

= 20240801 =
* Security: Added nonce check to front end card manager. 
* Improvement: Added Paypal seller protection
* Checks: Verified compatibility with Wordpress 6.6.1, WooCommerce 8.3.0
* Note: Rollback due to techinal issues

= 20240719 =
* Security: Nothing added
* Improvement: Nothing added
* Checks: Verified compatibility with Wordpress 6.5.5, WooCommerce 9.1.2

= 20240718 =
* Security: Nothing added
* Improvement: Fixed a functional issue on the main page of the plugin and fixed other minor bugs
* Checks: Verified compatibility with Wordpress 6.5.5, WooCommerce 9.1.1

= 20240712 =
* Security: Nothing added
* Improvement: Nothing added
* Checks: Verified compatibility with Wordpress 6.5.5, WooCommerce 9.1.1

= 20240627 =
* Security: Nothing added
* Improvement: Added WooCommerce HPOS Compatibility
* Checks: Verified compatibility with Wordpress 6.5.5, WooCommerce 9.0.2

= 20240307 =
* Security: Added nonce check to front end card manager
* Improvement: Added Paypal seller protection
* Checks: Verified compatibility with Wordpress 6.1.0, WooCommerce 7.1.0


= 20221130 =
* Improvement: Added Paypal Buy Now Pay Later button
* Improvement: Added Paypal seller protection
* Checks: Verified compatibility with Wordpress 6.1.0, WooCommerce 7.1.0

= 20220722 =
* Improvement: Added RBA fields
* Checks: Verified compatibility with Wordpress 6.0.0, WooCommerce 6.7.0

= 20220228 =
* Improvement: Fixed url for mybank payment system
* Checks: Verified compatibility with Wordpress 5.9.0, WooCommerce 6.2.1


= 20211031 =
* Improvement: Added BancomatPay payment system.
* Checks: Verified compatibility with Wordpress 5.8.1, WooCommerce 5.8.0

= 20210713 =
* Fix: Fix available_payment_gateways array warning
* Fix: wcs_order_contains_renewal missing function error
* Checks: Verified compatibility with Wordpress 5.7.2, WooCommerce 5.5.0

= 20210129 =
* Fix: iFrame Samesite Cookie
* Fix: SOAP client catch and log
* Fix: 3DS billing and shipping address up to 50 chars
* Checks: Verified compatibility with Wordpress 5.6, WooCommerce 4.9.2

= 20201212 =
* Fix: iFrame Samesite Cookie
* Fix: Link to documentation
* Fix: Update status to refunded only if is a full refund
* Fix: Added changes on how handle Tokens

= 20201018 =
* Improvement: added management of response cases XX (used with MyBank) and added the action gestpay_after_order_pending
* Improvement: Changed catch of Soap Fault Error.
* Improvement: removed "\r" from the CustomInfo parameter.
* Improvement: added actions gestpay_my_cards_template_before_table and gestpay_my_cards_template_after_table to add text before/after the list of saved card-tokens (s2s version)

= 20200811 =
* Fix: 3DS2 need authTimestamp to YYYYMMDDHHMM; removed ua informations from AuthData.
* Fix on payment method change for Subscriptions: allow to correctly change the associated token.
* Improvement: Added a second attempt if an error occurs when getting the SOAP client.

= 20200719 =
* Checks: Verified compatibility with Wordpress 5.4, WooCommerce 4.2-4.3 and WooCommerce Subscriptions 3.0.4
* New: Added ability to change the completed order status when using MOTO with separation and automatically handle the actions to be performed when the state of an order is manually changed.
* Fix: Prevent Fatal Error Call to undefined function wcs_is_subscription() when not using WooCommerce Subscriptions.
* Fix: Fixed ability to change the Gestpay multi-payments order: is_s2s must be true only when paymentType is `CREDITCARD`.
* Fix: the status of an active subscription must no change to failed if the cardholder abandons the card change.
* Improvement: Added more logging when adding 0_order_amount_fix.
* Improvement: Added action `gestpay_after_s2s_order_failed` to let developers add additional code.
* Improvement: Added validation for the S2S payment fields and a realated filters `gestpay_s2s_validate_payment_fields` and `gestpay_s2s_payment_fields_error_strings`

= 20191022 =
* Fixed return URL and message when the change of the tokenized card, related to a subscription, is failed.

= 20191012 =
* New: filters `gestpay_alter_order_id` and `gestpay_revert_order_id`
* Improvement for WooCommerce Subscriptions compatibility: added ability to change the tokenized card for an active Subscriptions: the customer will be able to change the card that will be used to pay the next recurring payment.
* Improvement for developers: tokenized cards will also have the expiry date stored on the post meta GESTPAY_META_TOKEN of the order_id.

= 20190909 =
* Feature PayPal - Added ability to retrieve a Token for Subscription payments (with external plugin WooCommerce Subscriptions).
* Added 3DS 2.0 support. [Read more](https://docs.gestpay.it/soap/3ds-2.0/how-change-integration/ "3DS 2.0")
* Fix WooCommerce 3.7.0 compatibility for the configuration page.

= 20190701 =
* Subscriptions - Fix token saving on the parent of a renewal order after is failed and is manually paid.

= 20190515 =
* Subscriptions - Added ability, for S2S and iFrame accounts, to use a second account with 3DS disabled. In this way it will be possible to use the main account with 3DS activated for the first payment and the second account (with 3DS disabled) for recurring payments.
* Added MyBank small icon in the card list
* Added filter `gestpay_gateway_cards_images`
* Cleaned up old code for WC < 3.x (which is not supported anymore)
* Checks - Verified compatibility with WooCommerce 3.6.2 and Wordpress 5.2

= 20190411 =
* Fix S2S - Show the input form for the card when tokens are disabled.
* Fix MyBank - When using MyBank on mobile devices, the bank/institute list must be shown and the Customer must select one of them before proceeding.
* Feature MyBank - Added MyBank text/logos/style to be compliant with the MyBank Style Guide requirements.
* Feature MyBank - Added an option for MyBank to be able to force also Customers on desktop devices to select a bank/institute from the website. Removed ability to change title and description for MyBank: these must be statically assigned.
* Cleaned up some of the old code for WC < 3.x (which is not supported anymore); payment types classes refactoring.

= 20190320 =
* Fix - flush rewrite rules causes issues with WPML: just flush only once, after plugin activation.
* Fix - On S2S if the customer select a default card, the new card form must be hidden.
* Fix - Changed costant name to force sending email to WC_GATEWAY_GESTPAY_FORCE_SEND_EMAIL.
* Fix - On S2S use the parent order id to handle failed recurring payments.
* Checks - Verified compatibility with WooCommerce 3.5.7 and Wordpress 5.1.1

= 20181129 =
* Feature - Added new available currencies
* Fix - Some currencies (JPY, PKR, IDR, KRW) does not allow decimals in the amount; VND allow just one decimal.
* Fix - On S2S (On-Site version) added Buyer Name field.
* Fix - Allow Google Analytics tracking (utm_nooverride)
* Checks - Verified compatibility with WooCommerce 3.5.1

= 20180927 =
* Feature - Added apiKey authentication method option
* Checks - Verified compatibility with WooCommerce 3.4.5

= 20180809 =
* Fix recurring payments with iFrame/Tokenization
* Checks - Verified compatibility with Wordpress 4.9.8, WooCommerce 3.4.4 and WooCommerce Subscriptions 2.3.3

= 20180606 =
* Fix - The JS on configuration page must distinguish between Pro and On-Site/iFrame options.
* Checks - Verified compatibility with Wordpress 4.9.6 and WooCommerce 3.4.2

= 20180516 =
* Fix - HTML slashes must be escaped inside JS.
* Fix - No need to instantiate the SOAP Client of order actions in the constructor.
* Feature - Added the ability to temporarily use unsecure Crypt URL when TLS 1.2 is not available.
* Feature - Added an option to enable On-Site merchants to set the withAuth parameter to "N".

= 20180426 =
* Fix typo in the JS of the TLS check

= 20180412 =
* Feature - Added compatibility with WC Sequential Order Numbers Pro.
* Security - Added TLS 1.2 checks for redirect and iFrame versions: prevent old and unsecure browsers to proceed with the payment.
* Fix - Show an error if required fields are not filled on the On-Site version (S2S).
* Fix - Prevent Fatal Errors if WooCommerce is inactive.
* Fix - Save transaction key on phase I
* Checks - Verified compatibility with Wordpress 4.9.4/.5 and WooCommerce 3.3.4/.5.

= 20180108 =
* Fix - Consel Merchant Pro parameter is now changed to be an input box on which the merchant can add the custom code given by Consel.

= 20171217 =
* Feature - Added help text near the CVV field (for it/en languages) for "on site" and iframe versions.
* Feature - Added Consel Customer Info parameter.

= 20171125 =
* Fix - Updated test URLs from testecomm.sella.it to sandbox.gestpay.net
* Checks - Verified compatibility with Wordpress 4.9 and WooCommerce 3.2.5

= 20170920 =
* Fix Custom Info parameter.

= 20170602 =
* Fix error "-1" that happens when using the S2S notify URL.
* Verified compatibility with WooCommerce Subscriptions 2.2.7

= 20170508 =
* Fix - Moved ini_set( 'serialize_precision', 2 ) to the Helper, to avoid rounding conflicts.
* Checks - Verified compatibility with WooCommerce v 3.0.5

= 20170502 =
* Fix - Verify if class WC_Subscriptions_Cart exists before disabling extra Gestpay payment types.

= 20170427 =
* Checks - Verified compatibility with WooCommerce version 2.6.14 and 3.0.4
* Checks - Verified compatibility with WooCommerce Subscriptions version 2.1.4 and 2.2.5
* Feature - Added support for Tokenization+Authorization (here called "On-Site") and iFrame services.
* Feature - Added support for 3D Secure and not 3D Secure payments.
* Feature - Added endpoint to handle cardholder's cards/tokens for the "On-Site" version.
* Feature - Added Refund/Settle/Delete S2S actions for transactions.
* Feature - Added more filters and actions.
* Feature - Disable extra Gestpay payment methods when paying a subscription.
* Fix - Correctly loading of plugin localization.
* Fix - Show/Hide Pro options on the configuration page.
* Fix - Removed extra payment "upmobile", which is not used anymore.

= 20170224 =
* First public release.

== Third Party Libraries ==

Questo plugin utilizza le seguenti librerie di terze parti:

* SOAP Client - Parte della libreria standard PHP, utilizzata per le comunicazioni con l'API Gestpay
* WooCommerce - Framework e-commerce per WordPress (GPLv3)
* WordPress - CMS principale (GPLv2 o successiva)
* jQuery - Libreria JavaScript per la manipolazione del DOM e la gestione degli eventi (MIT License)

== External services ==

Questo plugin si connette ai seguenti servizi esterni:

1. Axerve Payment Gateway (precedentemente Gestpay)
- Scopo: Elaborazione dei pagamenti tramite il gateway di Banca Sella
- Dati inviati: Informazioni sull'ordine, dati del cliente necessari per il pagamento
- Quando: Durante il processo di pagamento e per le operazioni di gestione degli ordini
- Privacy Policy: https://www.axerve.com/privacy-policy
- Termini di servizio: https://www.axerve.com/terms-conditions

2. icanhazip.com
- Scopo: Identificazione dell'indirizzo IP del server per la configurazione del gateway di pagamento
- Dati inviati: Nessun dato viene inviato, il servizio risponde solo con l'indirizzo IP pubblico
- Quando: Solo nell'area amministrativa durante la configurazione del plugin
- Privacy Policy: https://major.io/icanhazip-com-faq/
- Note: Questo servizio viene utilizzato solo per aiutare gli amministratori a configurare correttamente il gateway di pagamento nel backoffice di Axerve

3. Script JavaScript di verifica
- Scopo: Verifica della compatibilità del browser con il gateway di pagamento
- Dati inviati: Informazioni sul browser dell'utente per verificare la compatibilità TLS
- Quando: Durante il processo di pagamento
- Domini: gestpay.net, gestpay.it, ecomm.sella.it
- Privacy Policy: https://www.axerve.com/privacy-policy

4. MyBank
- Scopo: Integrazione con il sistema di pagamento MyBank
- Dati inviati: Informazioni necessarie per il pagamento tramite MyBank
- Quando: Solo quando l'utente sceglie MyBank come metodo di pagamento
- Privacy Policy: https://www.mybank.eu/privacy-policy/
- Termini di servizio: https://www.mybank.eu/terms-and-conditions/

Server di test e sviluppo
Nel codice di esempio (directory `sample/`) sono presenti riferimenti a domini fittizi (`site1.it` e `site2.it`) utilizzati solo come esempio per dimostrare la configurazione multi-sito. Questi domini sono puramente dimostrativi e non sono utilizzati nel codice di produzione.
