<?php
/**
 * End-of-life admin notice for the legacy plugin.
 *
 * @package    Gestpay_For_WooCommerce
 * @subpackage Admin
 * @since      20261022
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Ecommerce Fabrick
 *
 * Copyright: © 2013-2016 Mauro Mascia (info@mauromascia.com)
 * Copyright: © 2017-2021 Axerve S.p.A. - Gruppo Banca Sella (https://www.axerve.com - ecommerce@sella.it)
 * Copyright: © 2024-2025 Fabrick S.p.A. - Gruppo Banca Sella (https://www.fabrick.com - ecommerce@sella.it)
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! class_exists( 'Gestpay_Eol_Notice' ) ) :

	/**
	 * Renders the end-of-life notice on WordPress admin screens.
	 *
	 * @since 20261022
	 */
	class Gestpay_Eol_Notice {

		const PLUGIN_URL = 'https://wordpress.org/plugins/fabrick-payment-orchestra/';
		const DOCS_URL   = 'https://docs.paymentorchestra.fabrick.com/it/plugin/woocommerce/';

		/**
		 * Register the admin notice hook.
		 *
		 * @since 20261022
		 *
		 * @return void
		 */
		public static function init() {
			add_action( 'admin_notices', array( __CLASS__, 'render' ) );
		}

		/**
		 * Print the notice for shop managers and administrators.
		 *
		 * @since 20261022
		 *
		 * @return void
		 */
		public static function render() {
			if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
				return;
			}

			if ( ! self::current_user_can_see_notice() ) {
				return;
			}

			self::load_textdomain();

			$message = __(
				'Action required: the Ecommerce Fabrick plugin (Gestpay Payment) is EOL (end-of-life) and will only be supported until January 31, 2027. Plan and complete the installation of the new payment plugin Fabrick Payment Orchestra (https://wordpress.org/plugins/fabrick-payment-orchestra/) as soon as possible. For further details and the migration guide, read the official documentation: https://docs.paymentorchestra.fabrick.com/it/plugin/woocommerce/',
				'gestpay-for-woocommerce'
			);

			$allowed_html = array(
				'a' => array(
					'href'   => true,
					'target' => true,
					'rel'    => true,
				),
			);

			echo '<div class="notice notice-warning"><p>';
			echo wp_kses( self::linkify_urls( $message ), $allowed_html );
			echo '</p></div>';
		}

		/**
		 * Whether the current user may see the notice.
		 *
		 * @since 20261022
		 *
		 * @return bool
		 */
		private static function current_user_can_see_notice() {
			if ( current_user_can( 'manage_woocommerce' ) ) {
				return true;
			}

			return current_user_can( 'manage_options' );
		}

		/**
		 * Load translations before rendering, even if the gateway helper has not run yet.
		 *
		 * @since 20261022
		 *
		 * @return void
		 */
		private static function load_textdomain() {
			load_plugin_textdomain(
				'gestpay-for-woocommerce',
				false,
				dirname( plugin_basename( GESTPAY_MAIN_FILE ) ) . '/languages'
			);
		}

		/**
		 * Turn the two canonical URLs in the message into escaped links.
		 *
		 * Visible text stays the URL so the English wording matches the locked notice.
		 *
		 * @since 20261022
		 *
		 * @param string $message Translated notice text.
		 * @return string
		 */
		private static function linkify_urls( $message ) {
			$escaped = esc_html( $message );

			$replacements = array(
				self::PLUGIN_URL => self::make_link( self::PLUGIN_URL ),
				self::DOCS_URL   => self::make_link( self::DOCS_URL ),
			);

			return str_replace( array_keys( $replacements ), array_values( $replacements ), $escaped );
		}

		/**
		 * Build a safe HTML anchor whose label is the URL itself.
		 *
		 * @since 20261022
		 *
		 * @param string $url Absolute HTTPS URL.
		 * @return string
		 */
		private static function make_link( $url ) {
			return '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $url ) . '</a>';
		}
	}

endif;
