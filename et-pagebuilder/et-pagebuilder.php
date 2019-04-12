<?php

define( 'ET_BUILDER_THEME', true );
function et_setup_builder() {
	define( 'ET_BUILDER_DIR', get_template_directory() . '/includes/builder/' );
	define( 'ET_BUILDER_URI', get_template_directory_uri() . '/includes/builder' );
	define( 'ET_BUILDER_LAYOUT_POST_TYPE', 'et_pb_layout' );

	$theme_version = et_get_theme_version();
	define( 'ET_BUILDER_VERSION', $theme_version );

	load_theme_textdomain( 'et_builder', ET_BUILDER_DIR . 'languages' );
	require_once ET_BUILDER_DIR . 'framework.php';

	et_pb_register_posttypes();
}
add_action( 'init', 'et_setup_builder', 0 );

/**
 * Added custom data attribute to builder's section
 * @param array  initial custom data-* attributes for builder's section
 * @param array  section attributes
 * @param int    section order of appearances. zero based
 * @return array modified custom data-* attributes for builder's section
 */
function et_divi_section_data_attributes( $attributes, $atts, $num ) {
	$custom_padding        = isset( $atts['custom_padding'] ) ? $atts['custom_padding'] : '';
	$custom_padding_tablet = isset( $atts['custom_padding_tablet'] ) ? $atts['custom_padding_tablet'] : '';
	$custom_padding_phone  = isset( $atts['custom_padding_phone'] ) ? $atts['custom_padding_phone'] : '';
	$is_first_section      = 0 === $num;
	$is_transparent_nav    = et_divi_is_transparent_primary_nav();

	// Custom data-* attributes for transparent primary nav support.
	// Note: in customizer, the data-* attributes have to be printed for live preview purpose
	if ( $is_first_section && ( $is_transparent_nav || is_customize_preview() ) ) {
		if ( '' !== $custom_padding && 4 === count( explode( '|', $custom_padding ) ) ) {
			$attributes['padding'] = $custom_padding;
		}

		if ( '' !== $custom_padding_tablet && 4 === count( explode( '|', $custom_padding_tablet ) ) ) {
			$attributes['padding-tablet'] = $custom_padding_tablet;
		}

		if ( '' !== $custom_padding_phone && 4 === count( explode( '|', $custom_padding_phone ) ) ) {
			$attributes['padding-phone'] = $custom_padding_phone;
		}
	}

	return $attributes;
}
add_filter( 'et_pb_section_data_attributes', 'et_divi_section_data_attributes', 10, 3 );

/**
 * Switch the translation of Visual Builder interface to current user's language
 * @return void
 */
if ( ! function_exists( 'et_fb_set_builder_locale' ) ) :
function et_fb_set_builder_locale( $locale ) {
	// apply translations inside VB only
	if ( empty( $_GET['et_fb'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		return $locale;
	}

	$user = get_user_locale();

	if ( $user === $locale ) {
		return $locale;
	}

	if ( ! function_exists( 'switch_to_locale' ) ) {
		return $locale;
	}

	switch_to_locale( $user );

	return $user;
}
endif;
add_filter( 'theme_locale', 'et_fb_set_builder_locale' );

/**
 * Added custom post class
 * @param array $classes array of post classes
 * @param array $class   array of additional post classes
 * @param int   $post_id post ID
 * @return array modified array of post classes
 */
function et_pb_post_class( $classes, $class, $post_id ) {
	global $post;

	// Added specific class name if curent post uses comment module. Use global $post->post_content
	// instead of get_the_content() to retrieve the post's unparsed shortcode content
	if ( is_single() && has_shortcode( $post->post_content, 'et_pb_comments' ) ) {
		$classes[] = 'et_pb_no_comments_section';
	}

	return $classes;
}
add_filter( 'post_class', 'et_pb_post_class', 10, 3 );
