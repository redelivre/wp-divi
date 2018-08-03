<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WPML Multilingual CMS
 *
 * @since 3.0.64
 *
 * @link https://wpml.org
 */
class ET_Builder_Plugin_Compat_WPML_Multilingual_CMS extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'sitepress-multilingual-cms/sitepress.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 3.7.1
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		// Override the configuration
		add_action( 'wpml_config_array', array( $this, 'override_wpml_configuration' ) );
		add_filter(
			'et_pb_module_shortcode_attributes',
			array( $this, '_filter_traslate_shop_module_categories_ids' ),
			10,
			5
		);
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	function override_wpml_configuration( $config ) {

		if ( ! empty( $config['wpml-config']['custom-fields']['custom-field'] ) ) {

			$missing_fields = array(
				array(
					'value' => '_et_pb_built_for_post_type',
					'attr' => array(
						'action' => 'copy',
					),
				),
			);

			$seen = array();
			$fields = $config['wpml-config']['custom-fields']['custom-field'];

			foreach ( $fields as $field ) {
				$seen[ $field['value'] ] = true;
			}

			foreach ( $missing_fields as $field ) {
				if ( empty( $seen[ $field['value'] ] ) ) {
					// The missing field is really missing, let's add it
					$fields[] = $field;
				}
			}

			$config['wpml-config']['custom-fields']['custom-field'] = $fields;

		}

		if ( ! empty( $config['wpml-config']['taxonomies']['taxonomy'] ) ) {

			$taxonomy_replacements = array(
				'scope' => array(
					'translate' => 0,
				),
				'layout_type' => array(
					'translate' => 0,
				),
				'module_width' => array(
					'translate' => 0,
				),
				'layout_category' => array(
					'translate' => 1,
				),
			);

			$fixed_taxonomies = array();
			$taxonomies = $config['wpml-config']['taxonomies']['taxonomy'];

			foreach ( $taxonomies as $taxonomy ) {
				if ( ! empty( $taxonomy_replacements[ $taxonomy['value'] ] ) ) {
					// Replace attributes
					$taxonomy['attr'] = $taxonomy_replacements[ $taxonomy['value'] ];
				}
				$fixed_taxonomies[] = $taxonomy;
			}

			$config['wpml-config']['taxonomies']['taxonomy'] = $fixed_taxonomies;

		}

		return $config;
	}

	/**
	 * Convert selected categories ids to translated ones.
	 *
	 * @internal
	 *
	 * @param array $shortcode_atts
	 * @param array $atts
	 * @param string $slug
	 * @param string $address
	 *
	 * @return array
	 **/
	public function _filter_traslate_shop_module_categories_ids( $shortcode_atts, $atts, $slug, $address ) {
		if (
			! is_admin() && $slug == 'et_pb_shop'
			&&
			! empty( $shortcode_atts['type'] )
			&&
			$shortcode_atts['type'] == 'product_category'
			&&
			! empty( $shortcode_atts['include_categories'] )
		) {
			$cats_array = explode( ',', $shortcode_atts['include_categories'] );
			$new_ids    = array();

			foreach ( $cats_array as $cat_id ) {
				$new_ids[] = apply_filters( 'wpml_object_id', $cat_id, 'product_cat' );
			}

			$shortcode_atts['include_categories'] = implode( ',', $new_ids );
		}

		return $shortcode_atts;
	}
}

new ET_Builder_Plugin_Compat_WPML_Multilingual_CMS();
