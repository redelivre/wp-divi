<?php

class ET_Builder_Module_Divider extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Divider', 'et_builder' );
		$this->slug       = 'et_pb_divider';
		$this->fb_support = true;

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

		if ( ! et_is_builder_plugin_active() && true === et_get_option( 'et_pb_divider-show_divider', false ) ) {
			$this->show_divider_options = array_reverse( $this->show_divider_options );
			$show_divider_default = 'on';
		} else {
			$show_divider_default = 'off';
		}

		$this->whitelisted_fields = array(
			'color',
			'show_divider',
			'height',
			'admin_label',
			'module_id',
			'module_class',
			'divider_style',
			'divider_position',
			'divider_weight',
		);

		$this->options_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Visibility', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'color'  => esc_html__( 'Color', 'et_builder' ),
					'styles' => esc_html__( 'Styles', 'et_builder' ),
				),
			),
		);

		$this->fields_defaults = array(
			'color'          => array( '#ffffff', 'only_default_setting' ),
			'show_divider'   => array( $show_divider_default ),
			'height'         => array( '23px' ),
		);

		$this->advanced_options = array(
			'background' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'max_width' => array(),
			'filters' => array(),
		);
	}

	function get_fields() {
		$fields = array(
			'color' => array(
				'label'           => esc_html__( 'Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'tab_slug'        => 'advanced',
				'description'     => esc_html__( 'This will adjust the color of the 1px divider line.', 'et_builder' ),
				'depends_show_if' => 'on',
				'toggle_slug'     => 'color',
			),
			'show_divider' => array(
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
				'label'             => esc_html__( 'Divider Style', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_border_styles(),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'styles',
			),
			'divider_position' => array(
				'label'           => esc_html__( 'Divider Position', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'top'    => esc_html__( 'Top', 'et_builder' ),
					'center' => esc_html__( 'Vertically Centered', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom', 'et_builder' ),
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'styles',
			),
			'divider_weight' => array(
				'label'             => esc_html__( 'Divider Weight', 'et_builder' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'height' => array(
				'label'           => esc_html__( 'Height', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'description'     => esc_html__( 'Define how much space should be added below the divider.', 'et_builder' ),
				'default'         => '23px',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id        = $this->shortcode_atts['module_id'];
		$module_class     = $this->shortcode_atts['module_class'];
		$color            = $this->shortcode_atts['color'];
		$show_divider     = $this->shortcode_atts['show_divider'];
		$height           = $this->shortcode_atts['height'];
		$divider_style    = $this->shortcode_atts['divider_style'];
		$divider_position = $this->shortcode_atts['divider_position'];
		$divider_position_customizer = ! et_is_builder_plugin_active() ? et_get_option( 'et_pb_divider-divider_position', 'top' ) : 'top';
		$divider_weight   = $this->shortcode_atts['divider_weight'];
		$custom_padding              = $this->shortcode_atts['custom_padding'];
		$custom_padding_tablet       = $this->shortcode_atts['custom_padding_tablet'];
		$custom_padding_phone        = $this->shortcode_atts['custom_padding_phone'];

		$module_class              = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$style = '';

		if ( '' !== $color && 'on' === $show_divider ) {
			$style .= sprintf( ' border-top-color: %s;',
				esc_attr( $color )
			);

			if ( '' !== $divider_style && $this->defaults['divider_style'] !== $divider_style ) {
				$style .= sprintf( ' border-top-style: %s;',
					esc_attr( $divider_style )
				);
			}

			if ( '' !== $divider_weight && $this->defaults['divider_weight'] !== $divider_weight ) {
				$divider_weight_processed = false === strpos( $divider_weight, 'px' ) ? $divider_weight . 'px' : $divider_weight;

				$style .= sprintf( ' border-top-width: %1$s;',
					esc_attr( $divider_weight_processed )
				);
			}

			if ( '' !== $style ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%:before',
					'declaration' => ltrim( $style )
				) );
			}

			if ( $this->defaults['divider_position'] !== $divider_position ) {
				$module_class .= " et_pb_divider_position_{$divider_position}";
			} elseif ( $this->defaults['divider_position'] !== $divider_position_customizer ) {
				$module_class .= " et_pb_divider_position_{$divider_position_customizer} customized_et_pb_divider_position";
			}
		}

		if ( '' !== $height ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'height: %s;',
					esc_attr( et_builder_process_range_value( $height ) )
				),
			) );
		}

		if ( '' !== $custom_padding && '|||' !== $custom_padding ) {
			ET_Builder_Element::set_style( $function_name, array(
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
			ET_Builder_Element::set_style( $function_name, array(
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
			ET_Builder_Element::set_style( $function_name, array(
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

		$output = sprintf(
			'<div%2$s class="et_pb_module et_pb_space%1$s%3$s%4$s%6$s">%7$s%5$s<div class="et_pb_divider_internal"></div></div>',
			( 'on' === $show_divider ? ' et_pb_divider' : ' et_pb_divider_hidden' ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}

	protected function _add_additional_border_fields() {
		return false;
	}

	function process_advanced_border_options( $function_name ) {
		return false;
	}


}

new ET_Builder_Module_Divider;
