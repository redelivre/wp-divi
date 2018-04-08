<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WooCommerce
 * @since 3.0.65 (builder version)
 * @link https://wordpress.org/plugins/woocommerce/
 */
class ET_Builder_Plugin_Compat_WooCommerce extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'woocommerce/woocommerce.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 * Latest plugin version: 3.1.1
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found or needed functions do not exist
		if (
			! $this->get_plugin_version() ||
			! function_exists( 'is_cart' ) ||
			! function_exists( 'is_account_page' )
		) {
			return;
		}

		// Up to: latest theme version
		add_filter( 'et_grab_image_setting', array( $this, 'disable_et_grab_image_setting' ), 1 );
	}

	/**
	 * When an order is cancelled, WooCommerce cart shortcode changes the order status to prevent
	 * the 'Your order was cancelled.' notice from being shown multiple times.
	 * Since grab_image renders shortcodes twice, it must be disabled in the cart page or else the notice
	 * will not be shown at all.
	 * My Account Page is also affected by the same issue.
	 *
	 * @return bool
	 */
	function disable_et_grab_image_setting( $settings ) {
		return ( is_cart() || is_account_page() ) ? false : $settings;
	}
}
new ET_Builder_Plugin_Compat_WooCommerce;
