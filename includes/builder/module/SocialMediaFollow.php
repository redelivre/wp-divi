<?php

class ET_Builder_Module_Social_Media_Follow extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Social Media Follow', 'et_builder' );
		$this->plural          = esc_html__( 'Social Media Follows', 'et_builder' );
		$this->slug            = 'et_pb_social_media_follow';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_social_media_follow_network';
		$this->child_item_text = esc_html__( 'Social Network', 'et_builder' );

		$this->main_css_element = 'ul%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'alignment' => esc_html__( 'Alignment', 'et_builder' ),
					'icon'      => esc_html__( 'Icon', 'et_builder' ),
					'text'      => esc_html__( 'Text', 'et_builder' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'before' => array(
				'label'    => esc_html__( 'Before', 'et_builder' ),
				'selector' => 'ul%%order_class%%:before',
			),
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'selector' => 'ul%%order_class%%',
			),
			'after' => array(
				'label'    => esc_html__( 'After', 'et_builder' ),
				'selector' => 'ul%%order_class%%:after',
			),
			'social_follow' => array(
				'label'    => esc_html__( 'Social Follow', 'et_builder' ),
				'selector' => 'li',
			),
			'social_icon' => array(
				'label'    => esc_html__( 'Social Icon', 'et_builder' ),
				'selector' => 'li a.icon',
			),
			'follow_button' => array(
				'label'    => esc_html__( 'Follow Button', 'et_builder' ),
				'selector' => 'li a.follow_button',
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} a.icon",
							'border_styles' => "{$this->main_css_element} a",
						),
					),
					'defaults' => array(
						'border_radii' => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .et_pb_social_icon a',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main' => 'ul%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'text_orientation' => array(
					'exclude_options' => array( 'justified' ),
				),
				'options' => array(
					'text_orientation' => array(
						'label'           => esc_html__( 'Module Alignment', 'et_builder' ),
						'toggle_slug'     => 'alignment',
						'options_icon'    => 'module_align',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
			),
			'fonts'                 => false,
			'button'                => array(
				'button' => array(
					'label'               => esc_html__( 'Follow Button', 'et_builder' ),
					'css'                 => array(
						'main' => "{$this->main_css_element} .follow_button",
					),
					'hide_icon'           => true,
					'hide_custom_padding' => true,
					'no_rel_attr'         => true,
					'text_size'           => array(
						'default' => '14px',
					),
					'border_width'        => array(
						'default' => '0px',
					),
					'box_shadow'          => array(
						'css' => array(
							'main' => "{$this->main_css_element} .follow_button",
						),
					),
				),
			),
			'link_options'          => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( '8b0BlM_rlHQ' ),
				'name' => esc_html__( 'An introduction to the Social Media Follow module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'url_new_window' => array(
				'label'           => esc_html__( 'Link Target', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'icon',
				'description'     => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
				'default_on_front' => 'on',
			),
			'follow_button' => array(
				'label'           => esc_html__( 'Follow Button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'toggle_slug'     => 'icon',
				'description'     => esc_html__( 'Here you can choose whether or not to include the follow button next to the icon.', 'et_builder' ),
			),
			'icon_color'            => array(
				'label'          => esc_html__( 'Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the social network icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
			),
			'use_icon_font_size'    => array(
				'label'            => esc_html__( 'Use Custom Icon Size', 'et_builder' ),
				'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'icon_font_size',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'option_category'  => 'font_option',
			),
			'icon_font_size'        => array(
				'label'            => esc_html__( 'Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '16px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'depends_show_if'  => 'on',
				'responsive'       => true,
				'hover'            => 'tabs',
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['icon_color']     = array( 'color' => '%%order_class%% li a.icon:before' );
		$fields['icon_font_size'] = array(
			'font-size'   => '%%order_class%% li a.icon:before',
			'line-height' => '%%order_class%% li a.icon:before',
			'height'      => '%%order_class%% li a.icon:before',
			'width'       => '%%order_class%% li a.icon:before',
			'height'      => '%%order_class%% li a.icon',
			'width'       => '%%order_class%% li a.icon',
		);

		return $fields;
	}

	function before_render() {
		global $et_pb_social_media_follow_link;

		$url_new_window    = $this->props['url_new_window'];
		$follow_button     = $this->props['follow_button'];

		$et_pb_social_media_follow_link = array(
			'url_new_window' => $url_new_window,
			'follow_button'  => $follow_button,
		);
	}

	function render( $attrs, $content = null, $render_slug ) {
		global $et_pb_social_media_follow_link;

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$use_icon_font_size        = $this->props['use_icon_font_size'];
		$icon_color_hover          = $this->get_hover_value('icon_color');
		$icon_color_values         = et_pb_responsive_options()->get_property_values( $this->props, 'icon_color' );
		$icon_font_size_hover      = $this->get_hover_value( 'icon_font_size' );
		$icon_font_size_values     = et_pb_responsive_options()->get_property_values( $this->props, 'icon_font_size' );

		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet        = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone         = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';


		// Icon Color.
		et_pb_responsive_options()->generate_responsive_css( $icon_color_values, '%%order_class%% li a.icon:before', 'color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'icon_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% li a.icon:hover:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $icon_color_hover )
				),
			) );
		}

		// Icon Size.
		if ( 'off' !== $use_icon_font_size ) {
			// Proccess for each devices.
			foreach ( $icon_font_size_values as $font_size_key => $font_size_value ) {
				if ( '' === $font_size_value ) {
					continue;
				}

				$media_query = 'general';
				if ( 'tablet' === $font_size_key ) {
					$media_query = ET_Builder_Element::get_media_query( 'max_width_980' );
				} elseif ( 'phone' === $font_size_key ) {
					$media_query = ET_Builder_Element::get_media_query( 'max_width_767' );
				}

				$font_size_value_int    = (int) $font_size_value;
				$font_size_value_unit   = str_replace( $font_size_value_int, '', $font_size_value );
				$font_size_value_double = 0 < $font_size_value_int ? $font_size_value_int * 2 : 0;
				$font_size_value_double = (string) $font_size_value_double . $font_size_value_unit;

				// Icon.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% li a.icon:before',
					'declaration' => sprintf(
						'font-size:%1$s; line-height:%2$s; height:%2$s; width:%2$s;',
						esc_html( $font_size_value ),
						esc_html( $font_size_value_double )
					),
					'media_query' => $media_query,
				) );

				// Icon Wrapper.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% li a.icon',
					'declaration' => sprintf(
						'height:%1$s; width:%1$s;',
						esc_html( $font_size_value_double )
					),
					'media_query' => $media_query,
				) );
			}

			// Icon hover styles.
			if ( et_builder_is_hover_enabled( 'icon_font_size', $this->props ) && '' !== $icon_font_size_hover ) {
				$icon_font_size_hover_int    = (int) $icon_font_size_hover;
				$icon_font_size_hover_unit   = str_replace( $icon_font_size_hover_int, '', $icon_font_size_hover );
				$icon_font_size_hover_double = 0 < $icon_font_size_hover_int ? $icon_font_size_hover_int * 2 : 0;
				$icon_font_size_hover_double = (string) $icon_font_size_hover_double . $icon_font_size_hover_unit;

				// Icon.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% li a.icon:hover:before',
					'declaration' => sprintf(
						'font-size:%1$s; line-height:%2$s; height:%2$s; width:%2$s;',
						esc_html( $icon_font_size_hover ),
						esc_html( $icon_font_size_hover_double )
					),
				) );

				// Icon Wrapper.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% li a.icon:hover',
					'declaration' => sprintf(
						'height:%1$s; width:%1$s;',
						esc_html( $icon_font_size_hover_double )
					),
				) );
			}
		}

		// Get custom borders, if any
		$attrs = $this->props;

		// Module classnames
		$this->add_classname( array(
			'clearfix',
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(),
		) );

		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}

		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		if ( 'on' === $et_pb_social_media_follow_link['follow_button'] ) {
			$this->add_classname( 'has_follow_button' );
		}

		$data_background_layout       = '';
		$data_background_layout_hover = '';
		if ( $background_layout_hover_enabled ) {
			$data_background_layout = sprintf(
				' data-background-layout="%1$s"',
				esc_attr( $background_layout )
			);
			$data_background_layout_hover = sprintf(
				' data-background-layout-hover="%1$s"',
				esc_attr( $background_layout_hover )
			);
		}

		$output = sprintf(
			'<ul%3$s class="%2$s"%6$s%7$s>
				%5$s
				%4$s
				%1$s
			</ul> <!-- .et_pb_counters -->',
			$this->content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background, // #5
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover )
		);

		return $output;
	}
}

new ET_Builder_Module_Social_Media_Follow;
