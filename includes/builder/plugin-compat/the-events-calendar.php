<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for The Events Calendar plugin.
 *
 * @since 3.10
 *
 * @link https://wordpress.org/plugins/the-events-calendar/
 */
class ET_Builder_Plugin_Compat_The_Events_Calendar extends ET_Builder_Plugin_Compat_Base {
	public $actual_post_query;
	public $spoofed_post_query;

	/**
	 * Constructor.
	 *
	 * @since 3.10
	 */
	public function __construct() {
		$this->plugin_id = 'the-events-calendar/the-events-calendar.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 * Latest plugin version: 4.6.19
	 *
	 * @todo once this issue is fixed in future version, run version_compare() to limit the scope of the hooked fix
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'wp', array( $this, 'register_spoofed_post_fix' ) );
	}

	/**
	 * The Events Calendar register Tribe__Events__Templates::maybeSpoofQuery() on wp_head (100) hook
	 * which modifies global $posts. This modified post object breaks anything that came after wp_head
	 * until the spoofed post is fixed. Anything that relies on $post global value on body_class is affected
	 * (ie Divi's hide nav until scroll because it adds classname to <body> to work)
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	function register_spoofed_post_fix() {
		// Bail if global $post doesn't exist for some reason. Just to be safe.
		if ( ! isset( $GLOBALS['post'] ) ) {
			return;
		}

		// Only apply spoofed post fix if builder is used in custom post type page
		if ( ! et_builder_post_is_of_custom_post_type( get_the_ID() ) || ! et_pb_is_pagebuilder_used( get_the_ID() ) ) {
			return;
		}

		// Get actual $post query before Tribe__Events__Templates::maybeSpoofQuery() modifies it
		$this->actual_post_query = $GLOBALS['post'];

		// Return spoofed $post into its actual post then re-return it into spoofed post object
		add_action( 'et_layout_body_class_before', array( $this, 'fix_post_query' ) );
		add_action( 'et_layout_body_class_after', array( $this, 'respoofed_post_query' ) );
	}

	/**
	 * Return spoofed $post into its actual post so anything that relies to $post object works as expected
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	function fix_post_query() {
		// Bail if global $post doesn't exist for some reason. Just to be safe.
		if ( ! isset( $GLOBALS['post'] ) ) {
			return;
		}

		$this->spoofed_post_query = $GLOBALS['post'];

		$GLOBALS['post'] = $this->actual_post_query; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
	}

	/*
	 * Re-return actual $post object into spoofed post so The Event Calendar works as expected
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	function respoofed_post_query() {
		$GLOBALS['post'] = $this->spoofed_post_query; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
	}
}

new ET_Builder_Plugin_Compat_The_Events_Calendar;
