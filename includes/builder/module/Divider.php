<?php

class ET_Builder_Module_Divider extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Divider', 'et_builder' );
		$this->plural     = esc_html__( 'Dividers', 'et_builder' );
		$this->slug       = 'et_pb_divider';
		$this->vb_support = 'on';

		$style_option_name = sprintf( '%1$s-divider_style', $this->slug );
		$global_divider_style = ET_Global_Settings::get_value( $style_option_name );
		$position_option_name = sprintf( '%1$s-divider_position', $this->slug );
		$global_divider_position = ET_Global_Settings::get_value( $position_option_name );
		$weight_option_name = sprintf( '%1$s-divider_weight', $this->slug );
		$global_divider_weight = ET_Global_Settings::get_value( $weight_option_name );

		$this->defaults = array(
			'divider_style'    => $global_divider_style && '' !== $global_divider_style ? $global_divider_style : 'solid',
			'divider_position' => $global_divider_position && '' !== $global_divider_position ? $global_divider_position : 'top',
			'divider_weight'   => $global_divider_weight && '' !== $global_divider_weight ? $global_divider_weight : '1px',
		);

		// Show divider options is modifieable via customizer
		$this->show_divider_options = array(
			'off' => esc_html__( 'No', 'et_builder' ),
			'on'  => esc_html__( 'Yes', 'et_builder' ),
		);

		// Handle different default values for Builder Plugin
		if ( ! et_is_builder_plugin_active() && true === et_get_option( 'et_pb_divider-show_divider', false ) ) {
			$this->show_divider_options = array_reverse( $this->show_divider_options );
		}

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Visibility', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'line' => esc_html__( 'Line', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => '%%order_class%%',
							'border_styles' => '%%order_class%%',
						),
					),
					'defaults' => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'fonts'                 => false,
			'text'                  => false,
			'button'                => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'BL4CEVbDZfw' ),
				'name' => esc_html__( 'An introduction to the Divider module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'color' => array(
				'default'         => et_builder_accent_color(),
				'label'           => esc_html__( 'Line Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'tab_slug'        => 'advanced',
				'description'     => esc_html__( 'This will adjust the color of the 1px divider line.', 'et_builder' ),
				'depends_show_if' => 'on',
				'toggle_slug'     => 'line',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'show_divider' => array(
				'default'           => 'on',
				'label'             => esc_html__( 'Show Divider', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => $this->show_divider_options,
				'affects' => array(
					'divider_style',
					'divider_position',
					'divider_weight',
					'color',
				),
				'toggle_slug'       => 'main_content',
				'description'       => esc_html__( 'This settings turns on and off the 1px divider line, but does not affect the divider height.', 'et_builder' ),
			),
			'divider_style' => array(
				'label'             => esc_html__( 'Line Style', 'et_builder' ),
				'description'       => esc_html__( 'Select the shape of the dividing line used for the divider.', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_border_styles(),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'line',
				'default'           => $this->defaults['divider_style'],
				'mobile_options'    => true,
			),
			'divider_position' => array(
				'label'           => esc_html__( 'Line Position', 'et_builder' ),
				'description'     => esc_html__( 'The dividing line can be placed either above, below or in the center of the module.', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'top'    => esc_html__( 'Top', 'et_builder' ),
					'center' => esc_html__( 'Vertically Centered', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'line',
				'default'           => $this->defaults['divider_position'],
				'mobile_options'    => true,
			),
			'divider_weight' => array(
				'label'             => esc_html__( 'Divider Weight', 'et_builder' ),
				'description'       => esc_html__( 'Increasing the divider weight will increase the thickness of the dividing line.', 'et_builder' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'allowed_units'     => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'      => 'px',
				'default'           => $this->defaults['divider_weight'],
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
		);
		return $fields;
	}

	public function get_height_fields() {
		$defaults = array(
			'default' => '23px',
			'min'     => '1',
			'max'     => '100',
		);

		return ET_Builder_Module_Fields_Factory::get( 'Height' )->get_fields( $defaults );
	}

	public function get_max_height_fields() {
		$defaults = array(
			'min' => '1',
			'max' => '100',
		);

		return ET_Builder_Module_Fields_Factory::get( 'MaxHeight' )->get_fields( $defaults );
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['color'] = array( 'border' => '%%order_class%%:before' );
		$fields['divider_weight'] = array( 'border' => '%%order_class%%:before' );

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$show_divider                = $this->props['show_divider'];
		$divider_position_customizer = ! et_is_builder_plugin_active() ? et_get_option( 'et_pb_divider-divider_position', 'top' ) : 'top';
		$custom_padding              = $this->props['custom_padding'];
		$custom_padding_tablet       = $this->props['custom_padding_tablet'];
		$custom_padding_phone        = $this->props['custom_padding_phone'];

		$video_background            = $this->video_background();
		$parallax_image_background   = $this->get_parallax_image_background();

		$color                       = $this->props['color'];
		$color_hover                 = $this->get_hover_value( 'color' );
		$color_values                = et_pb_responsive_options()->get_property_values( $this->props, 'color' );
		$color_tablet                = isset( $color_values['tablet'] ) ? $color_values['tablet'] : '';
		$color_phone                 = isset( $color_values['phone'] ) ? $color_values['phone'] : '';

		$divider_style               = $this->props['divider_style'];
		$divider_style_hover         = $this->get_hover_value( 'divider_style' );
		$divider_style_values        = et_pb_responsive_options()->get_property_values( $this->props, 'divider_style' );
		$divider_style_tablet        = isset( $divider_style_values['tablet'] ) ? $divider_style_values['tablet'] : '';
		$divider_style_phone         = isset( $divider_style_values['phone'] ) ? $divider_style_values['phone'] : '';

		$divider_weight              = $this->props['divider_weight'];
		$divider_weight_hover        = $this->get_hover_value( 'divider_weight' );
		$divider_weight_values       = et_pb_responsive_options()->get_property_values( $this->props, 'divider_weight' );
		$divider_weight_tablet       = isset( $divider_weight_values['tablet'] ) ? $divider_weight_values['tablet'] : '';
		$divider_weight_phone        = isset( $divider_weight_values['phone'] ) ? $divider_weight_values['phone'] : '';

		$divider_position            = $this->props['divider_position'];
		$divider_position_values     = et_pb_responsive_options()->get_property_values( $this->props, 'divider_position' );
		$divider_position_tablet     = isset( $divider_position_values['tablet'] ) ? $divider_position_values['tablet'] : '';
		$divider_position_phone      = isset( $divider_position_values['phone'] ) ? $divider_position_values['phone'] : '';

		// In Divider module, divider color is really important. Basically, the divider won't be
		// displayed, unless we set divider color for Desktop. Divider color on desktop mode is
		// the key to display divider style and weight and set the position class.
		// Desktop Color is not empty, means:
		// - Render divider style and weight for all devices.
		// - Render divider position class for all devices.
		if ( 'on' === $show_divider ) {
			// Responsive Color, Divider Style, and Divider Weight.
			$divider_styles_values = array();

			foreach ( et_pb_responsive_options()->get_modes() as $device ) {
				$is_desktop = 'desktop' === $device;
				$suffix     = ! $is_desktop ? "_{$device}" : '';

				// Get divider color and set general color variables.
				$divider_color_value = '';
				if ( $is_desktop ) {
					$divider_color_value = $color;
				} else {
					$divider_color_value = 'tablet' === $device ? $color_tablet : $color_phone;
				}

				// Ensure color value is not empty. At least desktop color.
				if ( empty( $color ) && empty( $divider_color_value ) ) {
					continue;
				}

				$divider_style_value  = '';
				$divider_weight_value = '';
				if ( $is_desktop ) {
					$divider_style_value  = $divider_style;
					$divider_weight_value = $divider_weight;
				} else {
					$divider_style_value  = 'tablet' === $device ? $divider_style_tablet : $divider_style_phone;
					$divider_weight_value = 'tablet' === $device ? $divider_weight_tablet : $divider_weight_phone;
				}

				$divider_styles_values[ $device ] = array(
					'border-top-color' => esc_attr( $divider_color_value ),
					'border-top-style' => esc_attr( $divider_style_value ),
					'border-top-width' => ! empty( $divider_weight_value ) ? esc_attr( et_sanitize_input_unit( $divider_weight_value ) ) : '',
				);
			}

			et_pb_responsive_options()->generate_responsive_css( $divider_styles_values, '%%order_class%%:before', '', $render_slug, '', 'border' );

			// Divider Position Class.
			if ( ! empty( $color ) ) {
				if ( $this->defaults['divider_position'] !== $divider_position ) {
					$this->add_classname( "et_pb_divider_position_{$divider_position}" );
				} elseif ( $this->defaults['divider_position'] !== $divider_position_customizer ) {
					$this->add_classname( array(
						"et_pb_divider_position_{$divider_position_customizer}",
						'customized_et_pb_divider_position',
					) );
				}
			}

			if ( ! empty( $divider_position_tablet ) && ( ! empty( $color ) || ! empty( $color_tablet ) ) ) {
				$this->add_classname( "et_pb_divider_position_{$divider_position_tablet}_tablet" );
			}

			if ( ! empty( $divider_position_phone ) && ( ! empty( $color ) || ! empty( $color_phone ) ) ) {
				$this->add_classname( "et_pb_divider_position_{$divider_position_phone}_phone" );
			}
		}

		// Hover styles
		$hover_style = '';

		if ( et_builder_is_hover_enabled( 'color', $this->props ) && 'on' === $show_divider ) {
			$hover_style .= sprintf( ' border-top-color: %s;',
				esc_attr( $color_hover )
			);
		}

		if ( '' !== $divider_weight_hover && $divider_weight !== $divider_weight_hover ) {
			$divider_weight_hover_processed = false === strpos( $divider_weight_hover, 'px' ) ? $divider_weight_hover . 'px' : $divider_weight_hover;

			$hover_style .= sprintf( ' border-top-width: %1$s;',
				esc_attr( $divider_weight_hover_processed )
			);
		}

		if ( '' !== $hover_style ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover:before',
				'declaration' => ltrim( $hover_style )
			) );
		}

		if ( '' !== $custom_padding && '|||' !== $custom_padding ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:before',
				'declaration' => sprintf(
					'width: auto; top: %1$s; right: %2$s; left: %3$s;',
					esc_attr( et_pb_get_spacing( $custom_padding, 'top', '0px' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding, 'right', '0px' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding, 'left', '0px' ) )
				),
			) );
		}

		if ( '' !== $custom_padding_tablet && '|||' !== $custom_padding_tablet ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:before',
				'declaration' => sprintf(
					'width: auto; top: %1$s; right: %2$s; left: %3$s;',
					esc_attr( et_pb_get_spacing( $custom_padding_tablet, 'top', '0px' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding_tablet, 'right', '0px' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding_tablet, 'left', '0px' ) )
				),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			) );
		}

		if ( '' !== $custom_padding_phone && '|||' !== $custom_padding_phone ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:before',
				'declaration' => sprintf(
					'width: auto; top: %1$s; right: %2$s; left: %3$s;',
					esc_attr( et_pb_get_spacing( $custom_padding_phone, 'top', '0px' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding_phone, 'right', '0px' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding_phone, 'left', '0px' ) )
				),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			) );
		}

		// Module classnames
		$this->add_classname( 'et_pb_space' );

		if ( 'on' !== $show_divider ) {
			$this->remove_classname( 'et_pb_divider' );
			$this->add_classname( 'et_pb_divider_hidden' );
		}

		$output = sprintf(
			'<div%2$s class="%1$s">%4$s%3$s<div class="et_pb_divider_internal"></div></div>',
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Divider;
