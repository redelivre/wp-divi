<?php

class ET_Builder_Module_Field_TextShadow extends ET_Builder_Module_Field_Base {


	/**
	 * True when Divi plugin is active.
	 *
	 * @var bool
	 */
	public $is_plugin_active = false;

	/**
	 * Text shadow properties.
	 *
	 * @var array
	 */
	public $properties;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->is_plugin_active = et_is_builder_plugin_active();
		$this->properties       = array(
			'horizontal_length',
			'vertical_length',
			'blur_strength',
			'color',
		);
	}//end __construct()

	/**
	 * Returns prefixed field names.
	 *
	 * @param string $prefix Prefix.
	 *
	 * @return array
	 */
	public function get_prefixed_field_names( $prefix ) {
		$prefix = $prefix ? "{$prefix}_" : '';

		return array(
			"{$prefix}text_shadow_style",
			"{$prefix}text_shadow_horizontal_length",
			"{$prefix}text_shadow_vertical_length",
			"{$prefix}text_shadow_blur_strength",
			"{$prefix}text_shadow_color",
		);
	}//end get_prefixed_names()

	/**
	 * Returns Text Shadow presets.
	 *
	 * @param string $prefix Prefix.
	 *
	 * @return array
	 */
	public function get_presets( $prefix, $suffix = '' ) {
		list(
			$text_shadow_style,
			$text_shadow_horizontal_length,
			$text_shadow_vertical_length,
			$text_shadow_blur_strength,
			$text_shadow_color
		) = $this->get_prefixed_field_names( $prefix );

		return array(
			array(
				'icon'  => 'none',
				'value' => 'none',
			),
			array(
				'value'   => 'preset1',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset1',
				),
				'fields' => array(
					$text_shadow_horizontal_length => '0em',
					$text_shadow_vertical_length   => '0.1em',
					$text_shadow_blur_strength     => '0.1em',
				),
			),
			array(
				'value'   => 'preset2',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset2',
				),
				'fields' => array(
					$text_shadow_horizontal_length => '0.08em',
					$text_shadow_vertical_length   => '0.08em',
					$text_shadow_blur_strength     => '0.08em',
				),
			),
			array(
				'value'   => 'preset3',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset3',
				),
				'fields' => array(
					$text_shadow_horizontal_length => '0em',
					$text_shadow_vertical_length   => '0em',
					$text_shadow_blur_strength     => '0.3em',
				),
			),
			array(
				'value'   => 'preset4',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset4',
				),
				'fields' => array(
					$text_shadow_horizontal_length => '0em',
					$text_shadow_vertical_length   => '0.08em',
					$text_shadow_blur_strength     => '0em',
				),
			),
			array(
				'value'   => 'preset5',
				'content' => array(
					'content' => 'aA',
					'class'   => 'preset preset5',
				),
				'fields' => array(
					$text_shadow_horizontal_length => '0.08em',
					$text_shadow_vertical_length   => '0.08em',
					$text_shadow_blur_strength     => '0em',
				),
			),
		);
	}//end get_presets()

	/**
	 * Returns conditional defaults array.
	 *
	 * @param string $prefix Prefix.
	 * @param string $depend Field whose value controls which default should be used.
	 * @param string $field Field for which we're generating the defaults array.
	 * @param string $default Default value to be used when a Preset doesn't include a value for $field.
	 *
	 * @return array
	 */
	public function get_defaults( $prefix, $depend, $field, $default ) {
		$presets  = $this->get_presets( $prefix );
		$defaults = array();
		foreach ( $presets as $preset ) {
			$value              = $preset['value'];
			$defaults[ $value ] = isset( $preset['fields'][ $field ] ) ? $preset['fields'][ $field ] : $default;
		}
		return array(
			$depend,
			$defaults,
		);
	}//end get_defaults()

	/**
	 * Returns fields definition.
	 *
	 * @param array $args Field configuration.
	 *
	 * @return array
	 */
	public function get_fields( array $args = array() ) {

		$config = shortcode_atts(
			array(
				'label'               => '',
				'prefix'              => '',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'text',
				'sub_toggle'          => false,
				'option_category'     => 'configuration',
				'depends_show_if'     => '',
				'depends_show_if_not' => '',
			),
			$args
		);

		$prefix             = $config['prefix'];

		list(
			$text_shadow_style,
			$text_shadow_horizontal_length,
			$text_shadow_vertical_length,
			$text_shadow_blur_strength,
			$text_shadow_color
		) = $this->get_prefixed_field_names( $prefix );

		$tab_slug        = $config['tab_slug'];
		$toggle_slug     = $config['toggle_slug'];
		$sub_toggle      = $config['sub_toggle'];
		$option_category = $config['option_category'];
		// Some option categories (like font) have custom logic that involves changing default values and we don't want that to interfere with conditional defaults. This might change in future so, for now, I'm just overriding the value while leaving the possibility to remove this line afterwards and provide custom option_category via $config.
		$option_category = 'configuration';

		$label = $config['label'];
		if ( $label ) {
			$labels = array(
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow', 'et_builder' ), $label ),
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow Horizontal Length', 'et_builder' ), $label ),
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow Vertical Length', 'et_builder' ), $label ),
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow Blur Strength', 'et_builder' ), $label ),
				// translators: text shadow group label
				sprintf( esc_html__( '%1$s Text Shadow Color', 'et_builder' ), $label ),
			);
		} else {
			$labels = array(
				esc_html__( 'Text Shadow', 'et_builder' ),
				esc_html__( 'Text Shadow Horizontal Length', 'et_builder' ),
				esc_html__( 'Text Shadow Vertical Length', 'et_builder' ),
				esc_html__( 'Text Shadow Blur Strength', 'et_builder' ),
				esc_html__( 'Text Shadow Color', 'et_builder' ),
			);
		}
		$fields = array(
			$text_shadow_style => array(
				'label'            => $labels[0],
				'description'      => esc_html__( 'Pick a text shadow style to enable text shadow for this element. Once enabled, you will be able to customize your text shadow style further. To disable custom text shadow style, choose the None option.', 'et_builder' ),
				'type'             => 'presets_shadow',
				'option_category'  => $option_category,
				'default'          => 'none',
				'default_on_child' => true,
				'presets'          => $this->get_presets( $prefix ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'sync_affects'     => array(
					$text_shadow_horizontal_length,
					$text_shadow_vertical_length,
					$text_shadow_blur_strength,
					$text_shadow_color,
				),
				'affects' => array(
					$text_shadow_horizontal_length,
					$text_shadow_vertical_length,
					$text_shadow_blur_strength,
					$text_shadow_color,
				),
				'copy_with' => array(
					$text_shadow_horizontal_length,
					$text_shadow_vertical_length,
					$text_shadow_blur_strength,
					$text_shadow_color,
				),
			),
			$text_shadow_horizontal_length => array(
				'label'           => $labels[1],
				'description'     => esc_html__( 'Shadow\'s horizontal distance from the text. A negative value places the shadow to the left of the text.', 'et_builder' ),
				'type'            => 'range',
				'hover'           => 'tabs',
				'option_category' => $option_category,
				'range_settings'  => array(
					'min'  => -2,
					'max'  => 2,
					'step' => 0.01,
				),
				'default'             => $this->get_defaults( $prefix, $text_shadow_style, $text_shadow_horizontal_length, '0em' ),
				'default_on_child'    => true,
				'hide_sync'           => true,
				'validate_unit'       => true,
				'fixed_unit'          => 'em',
				'fixed_range'         => true,
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'none',
			),
			$text_shadow_vertical_length => array(
				'label'           => $labels[2],
				'description'     => esc_html__( 'Shadow\'s vertical distance from the text. A negative value places the shadow above the text.', 'et_builder' ),
				'type'            => 'range',
				'hover'           => 'tabs',
				'option_category' => $option_category,
				'range_settings'  => array(
					'min'  => -2,
					'max'  => 2,
					'step' => 0.01,
				),
				'default'             => $this->get_defaults( $prefix, $text_shadow_style, $text_shadow_vertical_length, '0em' ),
				'default_on_child'    => true,
				'hide_sync'           => true,
				'validate_unit'       => true,
				'fixed_unit'          => 'em',
				'fixed_range'         => true,
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'none',
			),
			$text_shadow_blur_strength => array(
				'label'           => $labels[3],
				'description'     => esc_html__( 'The higher the value, the bigger the blur; the shadow becomes wider and lighter.', 'et_builder' ),
				'type'            => 'range',
				'hover'           => 'tabs',
				'option_category' => $option_category,
				'range_settings'  => array(
					'min'  => 0,
					'max'  => 2,
					'step' => 0.01,
				),
				'default'             => $this->get_defaults( $prefix, $text_shadow_style, $text_shadow_blur_strength, '0em' ),
				'default_on_child'    => true,
				'hide_sync'           => true,
				'validate_unit'       => true,
				'fixed_unit'          => 'em',
				'fixed_range'         => true,
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'none',
			),
			$text_shadow_color => array(
				'label'               => $labels[4],
				'description'         => esc_html__( 'The color of the shadow.', 'et_builder' ),
				'type'                => 'color-alpha',
				'hover'               => 'tabs',
				'option_category'     => $option_category,
				'default'             => 'rgba(0,0,0,0.4)',
				'default_on_child'    => true,
				'hide_sync'           => true,
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'none',
			),
		);

		// Only add sub_toggle to fields if defined
		if ( false !== $sub_toggle ) {
			$fields[ $text_shadow_style ]['sub_toggle']             = $sub_toggle;
			$fields[ $text_shadow_vertical_length ]['sub_toggle']   = $sub_toggle;
			$fields[ $text_shadow_horizontal_length ]['sub_toggle'] = $sub_toggle;
			$fields[ $text_shadow_blur_strength ]['sub_toggle']     = $sub_toggle;
			$fields[ $text_shadow_color ]['sub_toggle']             = $sub_toggle;
		}

		// add conditional settings if defined
		if ( '' !== $config['depends_show_if'] ) {
			$fields[ $text_shadow_style ]['depends_show_if'] = $config['depends_show_if'];
		}

		if ( '' !== $config['depends_show_if_not'] ) {
			$fields[ $text_shadow_style ]['depends_show_if_not'] = $config['depends_show_if_not'];
		}

		return $fields;
	}//end get_fields()

	/**
	 * Returns whether a declaration should be added !important or not.
	 *
	 * @param array $options Field definition.
	 * @param string $key Property name.
	 *
	 * @return bool
	 */
	public function get_important( $options, $key = false ) {
		if ( ! isset( $options['css']['important'] ) ) {
			// nothing to do, bye
			return false;
		}

		$important = $options['css']['important'];
		if ( 'all' === $important || ($this->is_plugin_active && 'plugin_only' === $important) ) {
			return true;
		}

		if ( is_array( $important ) ) {
			if ( $this->is_plugin_active && in_array( 'plugin_all', $important ) ) {
				return true;
			}
			if ( false !== $key && in_array( $key, $important ) ) {
				return true;
			}
		}

		return false;
	}//end get_important()

	/**
	 * Returns the text-shadow declaration
	 *
	 * @param string $label Prefix.
	 * @param bool $important Whether to add !important or not.
	 * @param array $all_values All shortcode values.
	 *
	 * @return string
	 */
	public function get_declaration( $label, $important, $all_values, $is_hover = false ) {
		$prefix = $label ? "{$label}_" : '';
		$hover  = et_pb_hover_options();
		$utils  = ET_Core_Data_Utils::instance();

		$text_shadow = array();
		foreach ( $this->properties as $property ) {
			$prop = "{$prefix}text_shadow_{$property}";
			$normal = $utils->array_get( $all_values, $prop, '' );
			$text_shadow[] = $is_hover ? $hover->get_value( $prop, $all_values, $normal ) : $normal;
		}

		return sprintf(
			'text-shadow: %s%s;',
			et_core_esc_previously( join( ' ', array_filter( $text_shadow ) ) ),
			$important ? '!important' : ''
		);
	}//end get_declaration()

	/**
	 * Adds CSS rule.
	 *
	 * @param ET_Builder_Element $module Module object.
	 * @param string $label Label.
	 * @param array $font Field definition.
	 * @param string $function_name Shortcode function.
	 *
	 * @return void
	 */
	public function update_styles( $module, $label, $font, $function_name, $is_hover = false ) {
		$utils                 = ET_Core_Data_Utils::instance();
		$all_values            = $module->props;
		$main_element_selector = $module->main_css_element;
		// Use a different selector for plugin
		$css_element = $this->is_plugin_active && isset( $font['css']['limited_main'] ) ? 'css.limited_main' : 'css.main';
		// Use 'text_shadow' selector if defined, fallback to $css_element or default selector
		$selector = $utils->array_get( $font, 'css.text_shadow', $utils->array_get( $font, $css_element, $main_element_selector ) );

		if ( $is_hover ) {
			if ( is_array( $selector ) ) {
				$selector = array_map( array( 'ET_Builder_Module_Hover_Options', 'add_hover_to_selectors' ), $selector );
			} else {
				$selector = et_pb_hover_options()->add_hover_to_order_class( $selector );
			}

			$selector = $utils->array_get( $font, 'css.text_shadow_hover', $utils->array_get( $font, 'css.hover', $selector ) );
		}

		// Get the text-shadow declaration (horizontal vertical blur color)
		$declaration = $this->get_declaration(
			$label,
			$this->get_important( $font, 'text-shadow' ),
			$all_values,
			$is_hover
		);

		// Do not provide hover style if it is the same as normal style
		if ( $is_hover ) {
			$normal = $this->get_declaration(
				$label,
				$this->get_important( $font, 'text-shadow' ),
				$all_values,
				false
			);

			if ( $declaration === $normal ) {
				return;
			}
		}

		if ( is_array( $selector ) ) {
			foreach ( $selector as $single_selector ) {
				ET_Builder_Element::set_style(
					$function_name, array(
						'selector'    => $single_selector,
						'declaration' => $declaration,
						'priority'    => $module->get_style_priority(),
					)
				);
			}
		} else {
			ET_Builder_Element::set_style(
				$function_name, array(
					'selector'    => $selector,
					'declaration' => $declaration,
					'priority'    => $module->get_style_priority(),
				)
			);
		}

	}//end update_styles()

	/**
	 * Process Text Shadow options and adds CSS rules.
	 *
	 * @param ET_Builder_Element $module Module object.
	 * @param string $function_name Shortcode function.
	 *
	 * @return void
	 */
	public function process_advanced_css( $module, $function_name ) {
		$utils            = ET_Core_Data_Utils::instance();
		$all_values       = $module->props;
		$advanced_fields = $module->advanced_fields;

		// Disable if module doesn't set advanced_fields property and has no VB support
		if ( ! $module->has_vb_support() && ! $module->has_advanced_fields ) {
			return;
		}

		$suffixes = array( '', et_pb_hover_options()->get_suffix() );

		foreach ( $suffixes as $suffix ) {
			$is_hover = et_pb_hover_options()->get_suffix() === $suffix;

			// Check for text shadow settings in font-options
			if ( ! empty( $advanced_fields['fonts'] ) ) {
				// We have a 'fonts' section, fetch its values
				foreach ( $advanced_fields['fonts'] as $label => $font ) {
					// label can be header / body / toggle / etc
					$shadow_style = "{$label}_text_shadow_style";

					if ( 'none' !== $utils->array_get( $all_values, $shadow_style, 'none' ) ) {
						// We have a preset selected which isn't none, need to add text-shadow style
						$this->update_styles( $module, $label, $font, $function_name, $is_hover );
					}
				}
			}

			// Check for text shadow settings in Advanced/Text toggle
			if ( isset( $advanced_fields['text'] ) && 'none' !== $utils->array_get( $all_values, 'text_shadow_style', 'none' ) ) {
				// We have a preset selected which isn't none, need to add text-shadow style
				$text = $advanced_fields['text'];
				$this->update_styles( $module, '', $text, $function_name, $is_hover );
			}

			// Check for text shadow settings in Advanced/Fields toggle
			if ( isset( $advanced_fields['fields'] ) && 'none' !== $utils->array_get( $all_values, 'fields_text_shadow_style', 'none' ) ) {
				// We have a preset selected which isn't none, need to add text-shadow style
				$fields = $advanced_fields['fields'];
				$this->update_styles( $module, 'fields', $fields, $function_name, $is_hover );
			}

			// Check for text shadow settings in Advanced/Button toggle
			if ( ! empty( $advanced_fields['button'] ) ) {
				// We have a 'button' section, fetch its values
				foreach ( $advanced_fields['button'] as $label => $button ) {
					// label can be header / body / toggle / etc
					$shadow_style = "{$label}_text_shadow_style";

					if ( 'none' !== $utils->array_get( $all_values, $shadow_style, 'none' ) ) {
						// We have a preset selected which isn't none, need to add text-shadow style
						// Build a selector to only target the button
						$css_element = $utils->array_get( $button, 'css.main', "{$module->main_css_element} .et_pb_button" );
						// Make sure it has highest priority
						$utils->array_set( $button, 'css.text_shadow', $css_element );

						if ( ! isset( $button['css.hover'] ) ) {
							$utils->array_set( $button, 'css.hover', et_pb_hover_options()->add_hover_to_selectors( $css_element ) );
						}

						$this->update_styles( $module, $label, $button, $function_name, $is_hover );
					}
				}
			}
		}

	}//end process_advanced_css()

}

return new ET_Builder_Module_Field_TextShadow();
