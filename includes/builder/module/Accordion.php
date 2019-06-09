<?php

class ET_Builder_Module_Accordion extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Accordion', 'et_builder' );
		$this->plural     = esc_html__( 'Accordions', 'et_builder' );
		$this->slug       = 'et_pb_accordion';
		$this->vb_support = 'on';
		$this->child_slug = 'et_pb_accordion_item';

		$this->main_css_element = '%%order_class%%.et_pb_accordion';

		$this->settings_modal_toggles = array(
			'advanced' => array(
				'toggles' => array(
					'icon'          => esc_html__( 'Icon', 'et_builder' ),
					'toggle_layout' => esc_html__( 'Toggle', 'et_builder' ),
					'text'          => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_accordion_item",
							'border_styles' => "{$this->main_css_element} .et_pb_accordion_item",
						),
					),
					'defaults' => array(
						'border_radii' => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#d9d9d9',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .et_pb_toggle',
					),
				),
			),
			'fonts'                 => array(
				'toggle'        => array(
					'label'            => esc_html__( 'Title', 'et_builder' ),
					'css'              => array(
						'main'      => "{$this->main_css_element} h5.et_pb_toggle_title, {$this->main_css_element} h1.et_pb_toggle_title, {$this->main_css_element} h2.et_pb_toggle_title, {$this->main_css_element} h3.et_pb_toggle_title, {$this->main_css_element} h4.et_pb_toggle_title, {$this->main_css_element} h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'header_level'     => array(
						'default' => 'h5',
					),
					'options_priority' => array(
						'toggle_text_color' => 9,
					),
				),
				'closed_toggle' => array(
					'label'           => esc_html__( 'Closed Title', 'et_builder' ),
					'css'             => array(
						'main'      => "{$this->main_css_element} .et_pb_toggle_close h5.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h1.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h2.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h3.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h4.et_pb_toggle_title, {$this->main_css_element} .et_pb_toggle_close h6.et_pb_toggle_title",
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
				'body'          => array(
					'label'          => esc_html__( 'Body', 'et_builder' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_toggle_content",
						'limited_main' => "{$this->main_css_element} .et_pb_toggle_content, {$this->main_css_element} .et_pb_toggle_content p",
						'line_height'  => "{$this->main_css_element} .et_pb_toggle_content p",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
					),
				),
			),
			'margin_padding' => array(
				'draggable_padding' => false,
				'css'        => array(
					'padding'   => "{$this->main_css_element} .et_pb_toggle_content",
					'margin'    => $this->main_css_element,
					'important' => 'all',
				),
			),
			'button'                => false,
		);

		$this->custom_css_fields = array(
			'toggle' => array(
				'label'    => esc_html__( 'Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle',
			),
			'open_toggle' => array(
				'label'    => esc_html__( 'Open Toggle', 'et_builder' ),
				'selector' => '.et_pb_toggle_open',
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
				'id'   => esc_html( 'OBbuKXTJyj8' ),
				'name' => esc_html__( 'An introduction to the Accordion module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'open_toggle_text_color' => array(
				'label'             => esc_html__( 'Open Title Text Color', 'et_builder' ),
				'description'       => esc_html__( 'You can pick unique text colors for toggle titles when they are open and closed. Choose the open state title color here.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'open_toggle_background_color' => array(
				'label'             => esc_html__( 'Open Toggle Background Color', 'et_builder' ),
				'description'       => esc_html__( 'You can pick unique background colors for toggles when they are in their open and closed states. Choose the open state background color here.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle_layout',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'closed_toggle_text_color' => array(
				'label'             => esc_html__( 'Closed Title Text Color', 'et_builder' ),
				'description'       => esc_html__( 'You can pick unique text colors for toggle titles when they are open and closed. Choose the closed state title color here.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'closed_toggle',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'closed_toggle_background_color' => array(
				'label'             => esc_html__( 'Closed Toggle Background Color', 'et_builder' ),
				'description'       => esc_html__( 'You can pick unique background colors for toggles when they are in their open and closed states. Choose the closed state background color here.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle_layout',
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
				'label'            => esc_html__( 'Use Icon Font Size', 'et_builder' ),
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
				'hover'            => 'tabs',
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();
		$title  = '%%order_class%% .et_pb_toggle .et_pb_toggle_title';

		$fields['icon_color']        = array( 'color' => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before' );
		$fields['icon_font_size']    = array(
			'font-size'  => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before',
			'margin-top' => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before',
			'right'      => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before',
		);

		$fields['toggle_text_color']        = array( 'color' => $title );
		$fields['toggle_font_size']         = array( 'font-size' => $title );
		$fields['toggle_letter_spacing']    = array( 'letter-spacing' => $title );
		$fields['toggle_line_height']       = array( 'line-height' => $title );
		$fields['toggle_text_shadow_style'] = array( 'text-shadow' => $title );

		$fields['closed_toggle_text_color']       = array( 'color' => '%%order_class%% .et_pb_toggle_close .et_pb_toggle_title' );
		$fields['closed_toggle_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_toggle_close' );

		$fields['open_toggle_text_color']       = array( 'color' => '%%order_class%% .et_pb_toggle_open .et_pb_toggle_title' );
		$fields['open_toggle_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_toggle_open' );

		return $fields;
	}

	function before_render() {
		global $et_pb_accordion_item_number, $et_pb_accordion_header_level;

		$et_pb_accordion_item_number = 1;
		$et_pb_accordion_header_level = $this->props['toggle_level'];
	}

	function render( $attrs, $content = null, $render_slug ) {
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

		global $et_pb_accordion_item_number;

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Open Toggle Background Color.
		et_pb_responsive_options()->generate_responsive_css( $open_toggle_background_color_values, '%%order_class%% .et_pb_toggle_open', 'background-color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'open_toggle_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_open',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $open_toggle_background_color_hover )
				),
			) );
		}

		// Closed Toggle Background Color.
		et_pb_responsive_options()->generate_responsive_css( $closed_toggle_background_color_values, '%%order_class%% .et_pb_toggle_close', 'background-color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'closed_toggle_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_close',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $closed_toggle_background_color_hover )
				),
			) );
		}

		// Open Toggle Text Color.
		et_pb_responsive_options()->generate_responsive_css( $open_toggle_text_color_values, '%%order_class%% .et_pb_toggle_open h5.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h1.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h2.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h3.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h4.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h6.et_pb_toggle_title',
		'color', $render_slug, ' !important;', 'color' );

		if ( et_builder_is_hover_enabled( 'open_toggle_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_open h5.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h1.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h2.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h3.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h4.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $open_toggle_text_color_hover )
				),
			) );
		}

		// Closed Toggle Text Color.
		et_pb_responsive_options()->generate_responsive_css( $closed_toggle_text_color_values, '%%order_class%% .et_pb_toggle_close h5.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h1.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h2.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h3.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h4.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h6.et_pb_toggle_title',
		'color', $render_slug, ' !important;', 'color' );

		if ( et_builder_is_hover_enabled( 'closed_toggle_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_close h5.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h1.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h2.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h3.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h4.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h6.et_pb_toggle_title',
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
				if ( ! empty( $icon_font_size_active ) && $icon_font_size_active !== $icon_font_size_default ) {
					$icon_font_size_active_int  = (int) $icon_font_size_active;
					$icon_font_size_active_unit = str_replace( $icon_font_size_active_int, '', $icon_font_size_active );
					$icon_font_size_active_diff = (int) $icon_font_size_default - $icon_font_size_active_int;
					if ( 0 !== $icon_font_size_active_diff ) {
						// 2 is representation of left & right sides.
						$icon_font_size_right_values[ $device ] = round( $icon_font_size_active_diff / 2 ) . $icon_font_size_active_unit;
					}
				}
			}

			et_pb_responsive_options()->generate_responsive_css( $icon_font_size_right_values, '%%order_class%% .et_pb_toggle_title:before', 'right', $render_slug );

			if ( et_builder_is_hover_enabled( 'icon_font_size', $this->props ) && '' !== $icon_font_size_hover ) {
				if ( ! empty( $icon_font_size_hover ) && $icon_font_size_hover !== $icon_font_size_default ) {
					$icon_font_size_hover_int  = (int) $icon_font_size_hover;
					$icon_font_size_hover_unit = str_replace( $icon_font_size_hover_int, '', $icon_font_size_hover );
					$icon_font_size_hover_diff = (int) $icon_font_size_default - $icon_font_size_hover_int;
					if ( 0 !== $icon_font_size_hover_diff ) {
						// 2 is representation of left & right sides.
						$icon_font_size_right_hover = round( $icon_font_size_hover_diff / 2 ) . $icon_font_size_hover_unit;
						ET_Builder_Element::set_style( $render_slug, array(
							'selector'    => '%%order_class%% .et_pb_toggle_title:hover:before',
							'declaration' => sprintf(
								'right:%1$s;',
								esc_html( $icon_font_size_right_hover )
							),
						) );
					}
				}

				// Hover Icon Size.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .et_pb_toggle_title:hover:before',
					'declaration' => sprintf(
						'font-size:%1$s;',
						esc_html( $icon_font_size_hover )
					),
				) );
			}
		}

		// Icon Color.
		et_pb_responsive_options()->generate_responsive_css( $icon_color_values, '%%order_class%% .et_pb_toggle_title:before', 'color', $render_slug, '', 'color', ET_Builder_Element::DEFAULT_PRIORITY );

		if ( et_builder_is_hover_enabled( 'icon_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_toggle_title:hover:before',
				'priority'    => ET_Builder_Element::DEFAULT_PRIORITY,
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $icon_color_hover )
				),
			) );
		}

		// Module classnames
		$this->add_classname( $this->get_text_orientation_classname() );

		$output = sprintf(
			'<div%3$s class="%2$s">
				%5$s
				%4$s
				%1$s
			</div> <!-- .et_pb_accordion -->',
			$this->content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}

	public function add_new_child_text() {
		return esc_html__( 'Add New Accordion Item', 'et_builder' );
	}
}

new ET_Builder_Module_Accordion;
