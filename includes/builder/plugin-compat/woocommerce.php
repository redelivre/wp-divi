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

		// Hook before calling comments_template function in module.
		add_action( 'et_fb_before_comments_template', array( $this, 'remove_filter_comments_number_by_woo' ) );

		// Hook afer calling comments_template function in module.
		add_action( 'et_fb_after_comments_template', array( $this, 'restore_filter_comments_number_by_woo' ) );
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

	/**
	 * Remove comments_number filter added by Woo that caused missing comment
	 * count in Comment module
	 *
	 * @return void
	 */
	public function remove_filter_comments_number_by_woo() {
		if ( ! current_theme_supports( 'woocommerce' ) || ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) < 0 ) ) {
			remove_filter( 'comments_number', '__return_empty_string' );
		}
	}

	/**
	 * Restore comments_number that removed by remove_filter_comments_number_by_woo
	 *
	 * @return void
	 */
	public function restore_filter_comments_number_by_woo() {
		if ( ! current_theme_supports( 'woocommerce' ) || ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) < 0 ) ) {
			add_filter( 'comments_number', '__return_empty_string' );
		}
	}
}
new ET_Builder_Plugin_Compat_WooCommerce;
