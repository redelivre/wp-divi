<?php


abstract class ET_Builder_Module_Type_PostBased extends ET_Builder_Module {

	public $is_post_based = true;

	/**
	 * Loads and returns the contents of the "No Results" template.
	 *
	 * @since 3.0.77
	 *
	 * @return string
	 */
	public static function get_no_results_template() {
		ob_start();

		if ( et_is_builder_plugin_active() ) {
			include( ET_BUILDER_PLUGIN_DIR . 'includes/no-results.php' );
		} else {
			get_template_part( 'includes/no-results', 'index' );
		}

		return ob_get_clean();
	}
}
