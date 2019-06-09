<?php

class ET_Builder_Module_Toggle extends ET_Builder_Module {
	function init() {
		$this->name                       = esc_html__( 'Toggle', 'et_builder' );
		$this->plural                     = esc_html__( 'Toggles', 'et_builder' );
		$this->slug                       = 'et_pb_toggle';
		$this->vb_support                 = 'on';
		$this->additional_shortcode_slugs = array( 'et_pb_accordion_item' );
		$this->main_css_element = '%%order_class%%.et_pb_toggle';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'state'        => esc_html__( 'State', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'toggle' => esc_html__( 'Toggle', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => ".et_pb_module{$this->main_css_element}",
							'border_styles' => ".et_pb_module{$this->main_css_element}",
						)
					),
					'defaults' => array(
						'border_radii' => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#d9d9d9',
							'style' => 'solid',
						),
					)
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'important' => true,
					),
				),
			),
			'fonts'                 => array(
				'title' => array(
					'label'            => esc_html__( 'Title', 'et_builder' ),
					'css'              => array(
						'main'      => "{$this->main_css_element} h5, {$this->main_css_element} h1.et_pb_toggle_title, {$this->main_css_element} h2.et_pb_toggle_title, {$this->main_css_element} h3.et_pb_toggle_title, {$this->main_css_element} h4.et_pb_toggle_title, {$this->main_css_element} h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'header_level'     => array(
						'default' => 'h5',
					),
					'options_priority' => array(
						'title_text_color' => 9,
					),
				),
				'closed_title'         => array(
					'label'           => esc_html__( 'Closed Title', 'et_builder' ),
					'css'             => array(
						'main'      => "{$this->main_css_element}.et_pb_toggle_close h5, {$this->main_css_element}.et_pb_toggle_close h1.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h2.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h3.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h4.et_pb_toggle_title, {$this->main_css_element}.et_pb_toggle_close h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'hide_text_color' => true,
					'line_height'     => array(
						'default' => '1.7em',
					),
					'font_size'       => array(
						'default' => '16px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
				),
				'body'                 => array(
					'label'          => esc_html__( 'Body', 'et_builder' ),
					'css'            => array(
						'main'         => "{$this->main_css_element}",
						'limited_main' => "{$this->main_css_element}, {$this->main_css_element} p, {$this->main_css_element} .et_pb_toggle_content",
						'line_height'  => "{$this->main_css_element} p",
						'text_shadow'  => "{$this->main_css_element} .et_pb_toggle_content",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
					),
				),
			),
			'background'            => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button'                => false,
		);

		$this->custom_css_fields = array(
			'open_toggle' => array(
				'label'    => esc_html__( 'Open Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle.et_pb_toggle_open',
				'no_space_before_selector' => true,
			),
			'toggle_title' => array(
				'label'    => esc_html__( 'Toggle Title', 'et_builder' ),
				'selector' => '.et_pb_toggle_title',
			),
			'toggle_icon' => array(
				'label'    => esc_html__( 'Toggle Icon', 'et_builder' ),
				'selector' => '.et_pb_toggle_title:before',
			),
			'toggle_content' => array(
				'label'    => esc_html__( 'Toggle Content', 'et_builder' ),
				'selector' => '.et_pb_toggle_content',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'hFgp_A_u7mg' ),
				'name' => esc_html__( 'An introduction to the Toggle module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title will appear above the content and when the toggle is closed.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'open' => array(
				'label'           => esc_html__( 'State', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'Close', 'et_builder' ),
					'on'  => esc_html__( 'Open', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'toggle_slug'     => 'state',
				'description'     => esc_html__( 'Choose whether or not this toggle should start in an open or closed state.', 'et_builder' ),
			),
			'content' => array(
				'label'             => esc_html__( 'Body', 'et_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'dynamic_content'   => 'text',
			),
			'open_toggle_text_color' => array(
				'label'             => esc_html__( 'Open Title Text Color', 'et_builder' ),
				'description'       => esc_html__( 'You can pick unique text colors for toggle titles when they are open and closed. Choose the open state title color here.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'title',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'open_toggle_background_color' => array(
				'label'             => esc_html__( 'Open Toggle Background Color', 'et_builder' ),
				'description'       => esc_html__( 'You can pick unique background colors for toggles when they are in their open and closed states. Choose the open state background color here.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'closed_toggle_text_color' => array(
				'label'             => esc_html__( 'Closed Title Text Color', 'et_builder' ),
				'description'       => esc_html__( 'You can pick unique text colors for toggle titles when they are open and closed. Choose the closed state title color here.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'closed_title',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'closed_toggle_background_color' => array(
				'label'             => esc_html__( 'Closed Toggle Background Color', 'et_builder' ),
				'description'       => esc_html__( 'You can pick unique background colors for toggles when they are in their open and closed states. Choose the closed state background color here.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'description'       => esc_html__( 'Here you can define a custom color for the toggle icon.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
				'hover'             => 'tabs',
				'mobile_options'    => true,
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
		$title  = '%%order_class%% .et_pb_toggle .et_pb_toggle_title';

		$fields['icon_color']     = array( 'color' => '%%order_class%% .et_pb_toggle_title:before' );
		$fields['icon_font_size'] = array(
			'font-size'  => '%%order_class%% .et_pb_toggle_title:before',
			'margin-top' => '%%order_class%% .et_pb_toggle_title:before',
			'right'      => '%%order_class%% .et_pb_toggle_title:before',
		);

		$fields['toggle_text_color']        = array( 'color' => $title );
		$fields['toggle_font_size']         = array( 'font-size' => $title );
		$fields['toggle_letter_spacing']    = array( 'letter-spacing' => $title );
		$fields['toggle_line_height']       = array( 'line-height' => $title );
		$fields['toggle_text_shadow_style'] = array( 'text-shadow' => $title );

		$fields['closed_toggle_text_color']       = array( 'color' => '%%order_class%%.et_pb_toggle_close .et_pb_toggle_title' );
		$fields['closed_toggle_background_color'] = array( 'background-color' => '%%order_class%%.et_pb_toggle_close' );

		$fields['open_toggle_text_color']       = array( 'color' => '%%order_class%%.et_pb_toggle_open .et_pb_toggle_title' );
		$fields['open_toggle_background_color'] = array( 'background-color' => '%%order_class%%.et_pb_toggle_open' );

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$open                                  = $this->props['open'];
		$header_level                          = $this->props['title_level'];
		$open_toggle_background_color_values   = et_pb_responsive_options()->get_property_values( $this->props, 'open_toggle_background_color' );
		$open_toggle_background_color_hover    = $this->get_hover_value( 'open_toggle_background_color' );
		$closed_toggle_background_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'closed_toggle_background_color' );
		$closed_toggle_background_color_hover  = $this->get_hover_value( 'closed_toggle_background_color' );
		$icon_color_values                     = et_pb_responsive_options()->get_property_values( $this->props, 'icon_color' );
		$icon_color_hover                      = $this->get_hover_value( 'icon_color' );
		$use_icon_font_size                    = $this->props['use_icon_font_size'];
		$icon_font_size_values                 = et_pb_responsive_options()->get_property_values( $this->props, 'icon_font_size' );
		$icon_font_size_any_values             = et_pb_responsive_options()->get_property_values( $this->props, 'icon_font_size', '16px', true ); // 16px is default toggle icon size.
		$icon_font_size_hover                  = $this->get_hover_value( 'icon_font_size' );
		$closed_toggle_text_color_values       = et_pb_responsive_options()->get_property_values( $this->props, 'closed_toggle_text_color' );
		$closed_toggle_text_color_hover        = $this->get_hover_value( 'closed_toggle_text_color' );
		$open_toggle_text_color_values         = et_pb_responsive_options()->get_property_values( $this->props, 'open_toggle_text_color' );
		$open_toggle_text_color_hover          = $this->get_hover_value( 'open_toggle_text_color' );

		// Open Toggle Background Color.
		et_pb_responsive_options()->generate_responsive_css( $open_toggle_background_color_values, '%%order_class%%.et_pb_toggle.et_pb_toggle_open', 'background-color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'open_toggle_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_open:hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $open_toggle_background_color_hover )
				),
			) );
		}

		// Closed Toggle Background Color.
		et_pb_responsive_options()->generate_responsive_css( $closed_toggle_background_color_values, '%%order_class%%.et_pb_toggle.et_pb_toggle_close', 'background-color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'closed_toggle_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_close:hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $closed_toggle_background_color_hover )
				),
			) );
		}

		// Open Toggle Text Color.
		et_pb_responsive_options()->generate_responsive_css( $open_toggle_text_color_values, '%%order_class%%.et_pb_toggle.et_pb_toggle_open h5.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h1.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h2.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h3.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h4.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h6.et_pb_toggle_title', 'color', $render_slug, ' !important;', 'color' );

		if ( et_builder_is_hover_enabled( 'open_toggle_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_open h5.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_open h1.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_open:hover h2.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open:hover h3.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h4.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_open:hover h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $open_toggle_text_color_hover )
				),
			) );
		}

		// Closed Toggle Text Color.
		et_pb_responsive_options()->generate_responsive_css( $closed_toggle_text_color_values, '%%order_class%%.et_pb_toggle.et_pb_toggle_close h5.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h1.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h2.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h3.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h4.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h6.et_pb_toggle_title', 'color', $render_slug, ' !important;', 'color' );

		if ( et_builder_is_hover_enabled( 'closed_toggle_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_close h5.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_close h1.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_close:hover h2.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close:hover h3.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close:hover h4.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close:hover h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $closed_toggle_text_color_hover )
				),
			) );
		}

		// Icon Size.
		if ( 'off' !== $use_icon_font_size ) {
			et_pb_responsive_options()->generate_responsive_css( $icon_font_size_values, '%%order_class%% .et_pb_toggle_title:before', 'font-size', $render_slug );

			// Calculate right position.
			$is_icon_font_size_responsive = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'icon_font_size' );
			$icon_font_size_default       = '16px';  // Default toggle icon size.
			$icon_font_size_right_values  = array();

			foreach ( $icon_font_size_values as $device => $value ) {
				$icon_font_size_active = isset( $icon_font_size_any_values[ $device ] ) ? $icon_font_size_any_values[ $device ] : 0;
				if ( ! empty( $icon_font_size_active ) ) {
					$icon_font_size_active_int  = (int) $icon_font_size_active;
					$icon_font_size_active_unit = str_replace( $icon_font_size_active_int, '', $icon_font_size_active );
					$icon_font_size_active_diff = (int) $icon_font_size_default - $icon_font_size_active_int;

					// 2 is representation of left & right sides. 0 is default toggle icon right position.
					$icon_font_size_right_values[ $device ] = 0 !== $icon_font_size_active_diff ? round( $icon_font_size_active_diff / 2 ) . $icon_font_size_active_unit : 0;
				}
			}

			et_pb_responsive_options()->generate_responsive_css( $icon_font_size_right_values, '%%order_class%% .et_pb_toggle_title:before', 'right', $render_slug );

			// Hover.
			if ( et_builder_is_hover_enabled( 'icon_font_size', $this->props ) && '' !== $icon_font_size_hover ) {
				$icon_font_size_hover_int  = (int) $icon_font_size_hover;
				$icon_font_size_hover_unit = str_replace( $icon_font_size_hover_int, '', $icon_font_size_hover );
				$icon_font_size_hover_diff = (int) $icon_font_size_default - $icon_font_size_hover_int;

				// 2 is representation of left & right sides. 0 is default toggle icon right position.
				$icon_font_size_right_hover = 0 !== $icon_font_size_hover_diff ? round( $icon_font_size_hover_diff / 2 ) . $icon_font_size_hover_unit : 0;

				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%%:hover .et_pb_toggle_title:before',
					'declaration' => sprintf(
						'right:%1$s;',
						esc_html( $icon_font_size_right_hover )
					),
				) );

				// Hover Icon Size.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%%:hover .et_pb_toggle_title:before',
					'declaration' => sprintf(
						'font-size:%1$s;',
						esc_html( $icon_font_size_hover )
					),
				) );
			}
		}

		// Icon Color.
		et_pb_responsive_options()->generate_responsive_css( $icon_color_values, '%%order_class%% .et_pb_toggle_title:before', 'color', $render_slug, '', 'color', ET_Builder_Element::DEFAULT_PRIORITY + 1 );

		if ( et_builder_is_hover_enabled( 'icon_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_title:before',
				'priority'    => ET_Builder_Element::DEFAULT_PRIORITY + 1,
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $icon_color_hover )
				),
			) );
		}

		if ( 'et_pb_accordion_item' === $render_slug ) {
			global $et_pb_accordion_item_number, $et_pb_accordion_header_level;

			$open = 1 === $et_pb_accordion_item_number ? 'on' : 'off';

			$et_pb_accordion_item_number++;

			$header_level = $et_pb_accordion_header_level;

			$this->add_classname( 'et_pb_accordion_item' );
		}

		// Adding "_item" class for toggle module for customizer targetting. There's no proper selector
		// for toggle module styles since both accordion and toggle module use the same selector
		if( 'et_pb_toggle' === $render_slug ){
			$this->add_classname( 'et_pb_toggle_item' );
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$heading = sprintf(
			'<%1$s class="et_pb_toggle_title">%2$s</%1$s>',
			et_pb_process_header_level( $header_level, 'h5' ),
			$this->_esc_attr( 'title' )
		);

		// Module classnames
		$this->add_classname( array(
			$this->get_text_orientation_classname(),
		) );

		if ( 'on' === $open ) {
			$this->add_classname( 'et_pb_toggle_open' );
		} else {
			$this->add_classname( 'et_pb_toggle_close' );
		}

		$output = sprintf(
			'<div%4$s class="%2$s">
				%6$s
				%5$s
				%1$s
				<div class="et_pb_toggle_content clearfix">
					%3$s
				</div> <!-- .et_pb_toggle_content -->
			</div> <!-- .et_pb_toggle -->',
			$heading,
			$this->module_classname( $render_slug ),
			$this->content,
			$this->module_id(),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Toggle;
