<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for Divi Layout Injector
 *
 * @since 3.0.62
 *
 * @link https://elegantmarketplace.com/downloads/divi-layout-injector
 */
class ET_Builder_Plugin_Compat_Divi_Layout_Injector extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'divi_layout_injector/divi_layout_injector.php';

		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'wp', array( $this, 'maybe_filter_builder_used' ), 9 );
		add_action( 'updated_option', array( $this, 'updated_option_cb' ), 10, 3 );
		add_action( 'updated_post_meta', array( $this, 'updated_post_meta_cb' ), 10, 4 );
	}

	function maybe_filter_builder_used() {
		$types       = array( 'layout', 'page' );
		$will_inject = is_404() && in_array( get_option( 'sb_divi_fe_404_type', 'layout' ), $types );

		if ( ! $will_inject ) {
			$will_inject = $this->will_inject_layout();
		}

		if ( $will_inject ) {
			add_filter( 'et_core_is_builder_used_on_current_request', '__return_true', 10, 0 );
		}
	}

	function updated_option_cb( $option, $old_value, $value ) {
		if ( 0 === strpos( $option, 'sb_divi_fe' ) ) {
			ET_Core_PageResource::remove_static_resources( 'all', 'all' );
		}
	}

	function updated_post_meta_cb( $meta_id, $object_id, $meta_key, $_meta_value ) {
		if ( 'sb_divi_fe_layout_overrides' === $meta_key ) {
			ET_Core_PageResource::remove_static_resources( $object_id, 'all' );
		}
	}

	function will_inject_layout() {
		$locations = array( 'pre-header', 'post-menu', 'pre-content', 'post-content', 'pre-footer' );

		foreach ( $locations as $location ) {
			if ( sb_divi_fe_get_layout( $location ) ) {
				return true;
			}
		}

		return false;
	}
}

new ET_Builder_Plugin_Compat_Divi_Layout_Injector();
