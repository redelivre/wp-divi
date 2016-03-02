<?php

require_once( ET_BUILDER_DIR . 'core.php' );

if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! is_customize_preview() ) {
	$builder_load_actions = array(
		'et_pb_get_backbone_template',
		'et_pb_get_backbone_templates',
	);

	if ( ! isset( $_REQUEST['action'] ) || ! in_array( $_REQUEST['action'], $builder_load_actions ) ) {
		return;
	}
}

function et_builder_load_global_functions_script() {
	wp_enqueue_script( 'et-builder-modules-global-functions-script', ET_BUILDER_URI . '/scripts/frontend-builder-global-functions.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'et_builder_load_global_functions_script', 7 );

function et_builder_load_modules_styles() {
	wp_register_script( 'google-maps-api', esc_url( add_query_arg( array( 'v' => 3, 'sensor' => 'false' ), is_ssl() ? 'https://maps-api-ssl.google.com/maps/api/js' : 'http://maps.google.com/maps/api/js' ) ), array(), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'divi-fitvids', ET_BUILDER_URI . '/scripts/jquery.fitvids.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'waypoints', ET_BUILDER_URI . '/scripts/waypoints.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'magnific-popup', ET_BUILDER_URI . '/scripts/jquery.magnific-popup.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_register_script( 'hashchange', ET_BUILDER_URI . '/scripts/jquery.hashchange.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_register_script( 'salvattore', ET_BUILDER_URI . '/scripts/salvattore.min.js', array(), ET_BUILDER_VERSION, true );
	wp_register_script( 'easypiechart', ET_BUILDER_URI . '/scripts/jquery.easypiechart.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

	if ( et_is_builder_plugin_active() ) {
		wp_register_script( 'fittext', ET_BUILDER_URI . '/scripts/jquery.fittext.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	}

	// Load main styles CSS file only if the Builder plugin is active
	if ( et_is_builder_plugin_active() ) {
		wp_enqueue_style( 'et-builder-modules-style', ET_BUILDER_URI . '/styles/frontend-builder-plugin-style.css', array(), ET_BUILDER_VERSION );
	}

	wp_enqueue_style( 'magnific-popup', ET_BUILDER_URI . '/styles/magnific_popup.css', array(), ET_BUILDER_VERSION );

	wp_enqueue_script( 'et-jquery-touch-mobile', ET_BUILDER_URI . '/scripts/jquery.mobile.custom.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	wp_enqueue_script( 'et-builder-modules-script', ET_BUILDER_URI . '/scripts/frontend-builder-scripts.js', array( 'jquery', 'et-jquery-touch-mobile' ), ET_BUILDER_VERSION, true );
	wp_localize_script( 'et-builder-modules-script', 'et_pb_custom', array(
		'ajaxurl'                => admin_url( 'admin-ajax.php' ),
		'images_uri'             => get_template_directory_uri() . '/images',
		'builder_images_uri'     => ET_BUILDER_URI . '/images',
		'et_frontend_nonce'      => wp_create_nonce( 'et_frontend_nonce' ),
		'subscription_failed'    => esc_html__( 'Please, check the fields below to make sure you entered the correct information.', 'et_builder' ),
		'fill_message'           => esc_html__( 'Please, fill in the following fields:', 'et_builder' ),
		'contact_error_message'  => esc_html__( 'Please, fix the following errors:', 'et_builder' ),
		'invalid'                => esc_html__( 'Invalid email', 'et_builder' ),
		'captcha'                => esc_html__( 'Captcha', 'et_builder' ),
		'prev'                   => esc_html__( 'Prev', 'et_builder' ),
		'previous'               => esc_html__( 'Previous', 'et_builder' ),
		'next'                   => esc_html__( 'Next', 'et_builder' ),
		'wrong_captcha'          => esc_html__( 'You entered the wrong number in captcha.', 'et_builder' ),
		'is_builder_plugin_used' => et_is_builder_plugin_active(),
		'is_divi_theme_used'     => function_exists( 'et_divi_fonts_url' ),
		'widget_search_selector' => apply_filters( 'et_pb_widget_search_selector', '.widget_search' ),
	) );

	/**
	 * Only load this during builder preview screen session
	 */
	if ( is_et_pb_preview() ) {
		// Set fixed protocol for preview URL to prevent cross origin issue
		$preview_scheme = is_ssl() ? 'https' : 'http';

		// Get home url, then parse it
		$preview_origin_component = parse_url( home_url( '', $preview_scheme ) );

		// Rebuild origin URL, strip sub-directory address if there's any (postMessage e.origin doesn't pass sub-directory address)
		$preview_origin = "";

		// Perform check, prevent unnecessary error
		if ( isset( $preview_origin_component['scheme'] ) && isset( $preview_origin_component['host'] ) ) {
			$preview_origin = "{$preview_origin_component['scheme']}://{$preview_origin_component['host']}";

			// Append port number if different port number is being used
			if ( isset( $preview_origin_component['port'] ) ) {
				$preview_origin = "{$preview_origin}:{$preview_origin_component['port']}";
			}
		}

		// Enqueue theme's style.css if it hasn't been enqueued (possibly being hardcoded by theme)
		if ( ! et_builder_has_theme_style_enqueued() && et_is_builder_plugin_active() ) {
			wp_enqueue_style( 'et-builder-theme-style-css', get_stylesheet_uri(), array() );
		}

		wp_enqueue_style( 'et-builder-preview-style', ET_BUILDER_URI . '/styles/preview.css', array(), ET_BUILDER_VERSION );
		wp_enqueue_script( 'et-builder-preview-script', ET_BUILDER_URI . '/scripts/frontend-builder-preview.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
		wp_localize_script( 'et-builder-preview-script', 'et_preview_params', array(
			'preview_origin' => esc_url( $preview_origin ),
			'alert_origin_not_matched' => sprintf(
				esc_html__( 'Unauthorized access. Preview cannot be accessed outside %1$s.', 'et_builder' ),
				esc_url( home_url( '', $preview_scheme ) )
			),
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'et_builder_load_modules_styles', 11 );

/**
 * Determine whether current page has enqueued theme's style.css or not
 * This is mainly used on preview screen to decide to enqueue theme's style nor not
 * @return bool
 */
function et_builder_has_theme_style_enqueued() {
	global $wp_styles;

	if ( ! empty( $wp_styles->queue  ) ) {
		$theme_style_uri = get_stylesheet_uri();

		foreach ( $wp_styles->queue as $handle) {
			if ( isset( $wp_styles->registered[$handle]->src ) && $theme_style_uri === $wp_styles->registered[$handle]->src ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Added specific body classes for builder related situation
 * This enables theme to adjust its case independently
 * @return array
 */
function et_builder_body_classes( $classes ) {
	if ( is_et_pb_preview() ) {
		$classes[] = 'et-pb-preview';
	}

	return $classes;
}
add_filter( 'body_class', 'et_builder_body_classes' );

if ( ! function_exists( 'et_builder_add_main_elements' ) ) :
function et_builder_add_main_elements() {
	require ET_BUILDER_DIR . 'main-structure-elements.php';
	require ET_BUILDER_DIR . 'main-modules.php';
	do_action( 'et_builder_ready' );
}
endif;

if ( ! function_exists( 'et_builder_should_load_framework' ) ) :
function et_builder_should_load_framework() {
	global $pagenow;

	$is_admin = is_admin();
	$action_hook = $is_admin ? 'wp_loaded' : 'wp';
	$required_admin_pages = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'customize.php', 'edit-tags.php', 'admin-ajax.php', 'export.php' ); // list of admin pages where we need to load builder files
	$specific_filter_pages = array( 'edit.php', 'admin.php', 'edit-tags.php' ); // list of admin pages where we need more specific filtering

	$is_edit_library_page = 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'];
	$is_role_editor_page = 'admin.php' === $pagenow && isset( $_GET['page'] ) && apply_filters( 'et_divi_role_editor_page', 'et_divi_role_editor' ) === $_GET['page'];
	$is_import_page = 'admin.php' === $pagenow && isset( $_GET['import'] ) && 'wordpress' === $_GET['import']; // Page Builder files should be loaded on import page as well to register the et_pb_layout post type properly
	$is_edit_layout_category_page = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'layout_category' === $_GET['taxonomy'];

	if ( ! $is_admin || ( $is_admin && in_array( $pagenow, $required_admin_pages ) && ( ! in_array( $pagenow, $specific_filter_pages ) || $is_edit_library_page || $is_role_editor_page || $is_edit_layout_category_page || $is_import_page ) ) ) {
		return true;
	} else {
		return false;
	}

}
endif;

if ( ! function_exists( 'et_builder_load_framework' ) ) :
function et_builder_load_framework() {

	require ET_BUILDER_DIR . 'functions.php';

	// load builder files on front-end and on specific admin pages only.
	if ( et_builder_should_load_framework() ) {

		require ET_BUILDER_DIR . 'layouts.php';
		require ET_BUILDER_DIR . 'class-et-builder-element.php';
		require ET_BUILDER_DIR . 'class-et-global-settings.php';

		do_action( 'et_builder_framework_loaded' );

		$action_hook = is_admin() ? 'wp_loaded' : 'wp';
		add_action( $action_hook, 'et_builder_init_global_settings' );
		add_action( $action_hook, 'et_builder_add_main_elements' );
	}
}
endif;

et_builder_load_framework();