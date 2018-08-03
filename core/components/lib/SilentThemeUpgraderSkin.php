<?php
require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

if ( ! class_exists( 'ET_Core_LIB_SilentThemeUpgraderSkin' ) ):
/**
 * Theme Upgrader skin which does not output feedback
 *
 * @since ??
 *
 * @private
 */
class ET_Core_LIB_SilentThemeUpgraderSkin extends WP_Upgrader_Skin {
	/**
	 * @since ??
	 *
	 * @private
	 *
	 * @param string $string
	 */
	public function feedback( $string ) {
		return; // Suppress all feedback
	}
}
endif;
