<?php

class ET_Builder_Module_Divider extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Divider', 'et_builder' );
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
					'color'  => esc_html__( 'Color', 'et_builder' ),
					'styles' => esc_html__( 'Styles', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => false,
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
				'label'           => esc_html__( 'Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'tab_slug'        => 'advanced',
				'description'     => esc_html__( 'This will adjust the color of the 1px divider line.', 'et_builder' ),
				'depends_show_if' => 'on',
				'toggle_slug'     => 'color',
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
				'label'             => esc_html__( 'Divider Style', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => et_builder_get_border_styles(),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'styles',
				'default'           => $this->defaults['divider_style'],
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
				'default'           => $this->defaults['divider_position'],
			),
			'divider_weight' => array(
				'label'             => esc_html__( 'Divider Weight', 'et_builder' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'default_unit'      => 'px',
				'default'           => $this->defaults['divider_weight'],
			),
			'height' => array(
				'label'           => esc_html__( 'Height', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'description'     => esc_html__( 'Define how much space should be added below the divider.', 'et_builder' ),
				'default'         => '23px',
				'default_unit'    => 'px',
				'default_on_front' => '23px',
			),
		);
		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$color            = $this->props['color'];
		$show_divider     = $this->props['show_divider'];
		$height           = $this->props['height'];
		$divider_style    = $this->props['divider_style'];
		$divider_position = $this->props['divider_position'];
		$divider_position_customizer = ! et_is_builder_plugin_active() ? et_get_option( 'et_pb_divider-divider_position', 'top' ) : 'top';
		$divider_weight   = $this->props['divider_weight'];
		$custom_padding              = $this->props['custom_padding'];
		$custom_padding_tablet       = $this->props['custom_padding_tablet'];
		$custom_padding_phone        = $this->props['custom_padding_phone'];

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
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%%:before',
					'declaration' => ltrim( $style )
				) );
			}

			if ( $this->defaults['divider_position'] !== $divider_position ) {
				$this->add_classname( "et_pb_divider_position_{$divider_position}" );
			} elseif ( $this->defaults['divider_position'] !== $divider_position_customizer ) {
				$this->add_classname( array(
					"et_pb_divider_position_{$divider_position_customizer}",
					'customized_et_pb_divider_position',
				) );
			}
		}

		if ( '' !== $height ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'height: %s;',
					esc_attr( et_builder_process_range_value( $height ) )
				),
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
