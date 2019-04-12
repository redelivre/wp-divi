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
		add_action( 'et_builder_before_comments_number', array( $this, 'remove_filter_comments_number_by_woo' ) );

		// Hook afer calling comments_template function in module.
		add_action( 'et_fb_after_comments_template', array( $this, 'restore_filter_comments_number_by_woo' ) );
		add_action( 'et_builder_after_comments_number', array( $this, 'restore_filter_comments_number_by_woo' ) );

		// Dynamic Content
		add_filter( 'et_builder_dynamic_content_display_hidden_meta_keys', array( $this, 'filter_dynamic_content_display_hidden_meta_keys' ), 10, 2 );
		add_filter( 'et_builder_dynamic_content_custom_field_label', array( $this, 'filter_dynamic_content_custom_field_label' ), 10, 2 );
		add_filter( 'et_builder_dynamic_content_meta_value', array( $this, 'maybe_filter_dynamic_content_meta_value' ), 10, 3 );
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

	/**
	 * Whitelist hidden WooCommerce meta keys for dynamic content.
	 *
	 * @since 3.17.2
	 *
	 * @param array<string> $meta_keys
	 * @param integer $post_id
	 *
	 * @return array<string>
	 */
	public function filter_dynamic_content_display_hidden_meta_keys( $meta_keys, $post_id ) {
		return array_merge( $meta_keys, array(
			'_stock_status',
			'_regular_price',
			'_sale_price',
		) );
	}

	/**
	 * Rename label of known displayed hidden post meta fields in dynamic content.
	 *
	 * @since 3.17.2
	 *
	 * @param string $label
	 * @param string $key
	 *
	 * @return string
	 */
	public function filter_dynamic_content_custom_field_label( $label, $key ) {
		$custom_labels = array(
			'total_sales'    => esc_html__( 'Product Total Sales', 'et_builder' ),
			'_stock_status'  => esc_html__( 'Product Stock Status', 'et_builder' ),
			'_regular_price' => esc_html__( 'Product Regular Price', 'et_builder' ),
			'_sale_price'    => esc_html__( 'Product Sale Price', 'et_builder' ),
		);

		if ( isset( $custom_labels[ $key ] ) ) {
			return $custom_labels[ $key ];
		}

		return $label;
	}

	/**
	 * Format WooCommerce meta values accordingly.
	 *
	 * @since 3.17.2
	 *
	 * @param string $meta_value
	 * @param string $meta_key
	 * @param integer $post_id
	 *
	 * @return string
	 */
	public function maybe_filter_dynamic_content_meta_value( $meta_value, $meta_key, $post_id ) {
		switch ( $meta_key ) {
			case '_stock_status':
				// Check for function existance just in case
				if ( function_exists( 'wc_get_product_stock_status_options' ) ) {
					$stock_statuses = wc_get_product_stock_status_options();

					// Format meta value into human readable format
					if ( ! empty( $stock_statuses[ $meta_value ] ) ) {
						$meta_value = esc_html( $stock_statuses[ $meta_value ] );
					}
				}

				break;
		}

		return $meta_value;
	}
}
new ET_Builder_Plugin_Compat_WooCommerce;
