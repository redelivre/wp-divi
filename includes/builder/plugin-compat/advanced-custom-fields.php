<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for the Advanced Custom Fields plugin.
 *
 * @since 3.17.2
 *
 * @link https://www.advancedcustomfields.com/
 */
class ET_Builder_Plugin_Compat_Advanced_Custom_Fields extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 3.17.2
	 */
	public function __construct() {
		$this->plugin_id = $this->_get_plugin_id();
		$this->init_hooks();
	}

	/**
	 * Get the currently activated ACF plugin id as the FREE and PRO versions are separate plugins.
	 *
	 * @since 3.18
	 *
	 * @return string
	 */
	protected function _get_plugin_id() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$pro  = 'advanced-custom-fields-pro/acf.php';
		$free = 'advanced-custom-fields/acf.php';

		return is_plugin_active( $pro ) ? $pro : $free;
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 3.17.2
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_builder_dynamic_content_meta_value', array( $this, 'maybe_filter_dynamic_content_meta_value' ), 10, 3 );
		add_filter( 'et_builder_custom_dynamic_content_fields', array( $this, 'maybe_filter_dynamic_content_fields'), 10, 3 );
	}

	/**
	 * Format ACF meta values accordingly.
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
		$acf_value = get_field( $meta_key, $post_id );

		if ( false === $acf_value ) {
			return $meta_value;
		}

		$acf_field = get_field_object( $meta_key, $post_id, array( 'load_value' => false ) );
		$acf_value = $this->format_field_value( $acf_value, $acf_field );

		if ( is_array( $acf_value ) || is_object( $acf_value ) ) {
			// Avoid exposing unformatted values.
			$acf_value = '';
		}

		return (string) $acf_value;
	}

	/**
	 * Format ACF dynamic content field.
	 *
	 * @since 3.17.2
	 *
	 * @param array<string, array> $custom_fields
	 * @param int   $post_id
	 * @param array<string, mixed> $raw_custom_fields
	 *
	 * @return array<string, array> modified $custom_fields
	 */
	public function maybe_filter_dynamic_content_fields( $custom_fields, $post_id, $raw_custom_fields ) {
		$_          = ET_Core_Data_Utils::instance();
		$acf_values = get_fields( $post_id );

		// If exist, loop ACF fields values and modify its field definition.
		if ( ! empty( $acf_values ) ) {
			foreach ( $acf_values as $key => $value ) {
				// Get field definition.
				$acf_field = get_field_object($key, $post_id );

				switch ( $acf_field['type'] ) {
					case 'taxonomy':
						// If enable_html option exist in taxonomy field, set enable_html default to `on` so builder
						// automatically render taxonomy list properly as unescaped HTML.
						if ( $_->array_get( $custom_fields, "custom_meta_{$key}.fields.enable_html.default", false ) ) {
							$_->array_set( $custom_fields, "custom_meta_{$key}.fields.enable_html.default", 'on' );
						}

						break;
				}
			}
		}

		return $custom_fields;
	}

	/**
	 * Format a field value based on the field type.
	 *
	 * @param mixed $value
	 * @param array $field
	 *
	 * @return mixed
	 */
	protected function format_field_value( $value, $field ) {
		if ( ! is_array( $field ) || empty( $field['type'] ) ) {
			return $value;
		}

		switch ( $field['type'] ) {
			case 'image':
				$format = isset( $field['return_format'] ) ? $field['return_format'] : 'url';
				switch ( $format ) {
					case 'array':
						$value = esc_url( wp_get_attachment_url( intval( $value['id'] ) ) );
						break;
					case 'id':
						$value = esc_url( wp_get_attachment_url( intval( $value ) ) );
						break;
				}
				break;

			case 'select':
			case 'checkbox':
				$value        = is_array( $value ) ? $value : array( $value );
				$value_labels = array();

				foreach ( $value as $value_key ) {
					$choice_label = isset( $field['choices'][ $value_key ] ) ? $field['choices'][ $value_key ] : '';
					if ( ! empty( $choice_label ) ) {
						$value_labels[] = $choice_label;
					}
				}

				$value = implode( ', ', $value_labels );
				break;

			case 'true_false':
				$value = $value ? __( 'Yes', 'et_builder' ) : esc_html__( 'No', 'et_builder' );
				break;

			case 'taxonomy':
				// If taxonomy configuration exist, get HTML output of given value (ids).
				if ( isset( $field['taxonomy'] ) ) {
					$terms     = get_terms( array( 'taxonomy' => $field['taxonomy'], 'include'  => $value ) );
					$link      = 'on';
					$separator = ', ';

					if ( is_array( $terms ) ) {
						$value = et_builder_list_terms( $terms, $link, $separator );
					}
				}
				break;

			default:
				// Handle multiple values for which a more appropriate formatting method is not available.
				if ( isset( $field['multiple'] ) && $field['multiple'] ) {
					$value = implode( ', ', $value );
				}
				break;
		}

		// Value escaping left to the user to decide since some fields hold rich content.
		$value = et_core_esc_previously( $value );

		return $value;
	}
}

new ET_Builder_Plugin_Compat_Advanced_Custom_Fields;
