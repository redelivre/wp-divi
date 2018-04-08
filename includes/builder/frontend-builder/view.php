<?php

/**
 * Boots Frond End Builder App,
 *
 * @return Front End Builder wrap if main query, $content otherwise.
 */
function et_fb_app_boot( $content ) {
	// Instances of React app
	static $instances = 0;

	// Don't boot the app if the builder is not in use
	if ( ! et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		return $content;
	}

	$class = apply_filters( 'et_fb_app_preloader_class', 'et-fb-page-preloading' );

	if ( '' !== $class ) {
		$class = sprintf( ' class="%1$s"', esc_attr( $class ) );
	}

	// Only return React app wrapper for the main query.
	if ( is_main_query() ) {
		// Keep track of instances in case is_main_query() is true multiple times for the same page
		// This happens in 2017 theme when multiple Divi enabled pages are assigned to Front Page Sections
		$instances++;
		$output = sprintf( '<div id="et-fb-app"%1$s></div>', $class );
		if ( $instances > 1 ) {
			// uh oh, we might have multiple React app in the same page, let's also add rendered content and deal with it later using JS
			$output .= sprintf( '<div class="et_fb_fallback_content" style="display: none">%s</div>', $content );
			// Stop shortcode object processor so that shortcode in the content are treated normaly.
			et_fb_reset_shortcode_object_processing();
		}
		return $output;
	}

	// Stop shortcode object processor so that shortcode in the content are treated normaly.
	et_fb_reset_shortcode_object_processing();

	return $content;
}

add_filter( 'the_content', 'et_fb_app_boot', 1 );

/**
 * Added frontend builder assets.
 * Note: loading assets on head is way too early, computedVars returns undefined on header.
 *
 * @return void
 */
function et_fb_wp_footer() {
	et_fb_enqueue_assets();

	// TODO: this is specific to Audio Module and we should conditionally call it once we have
	// $content set as an object, we can then to a check whether the audio module is
	// present.
	remove_all_filters( 'wp_audio_shortcode_library' );
	remove_all_filters( 'wp_audio_shortcode' );
	remove_all_filters( 'wp_audio_shortcode_class');
}
add_action( 'wp_footer', 'et_fb_wp_footer' );

/**
 * Added frontend builder specific body class
 * @todo load conditionally, only when the frontend builder is used
 *
 * @param array  initial <body> classes
 * @return array modified <body> classes
 */
function et_fb_add_body_class( $classes ) {
	$classes[] = 'et-fb';

	if ( is_rtl() && 'on' === et_get_option( 'divi_disable_translations', 'off' ) ) {
		$classes[] = 'et-fb-no-rtl';
	}

	return $classes;
}
add_filter( 'body_class', 'et_fb_add_body_class' );
