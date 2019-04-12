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
					'icon' => esc_html__( 'Icon', 'et_builder' ),
					'text' => array(
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
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element} .et_pb_toggle_content",
						'limited_main' => "{$this->main_css_element} .et_pb_toggle_content, {$this->main_css_element} .et_pb_toggle_content p",
						'line_height' => "{$this->main_css_element} .et_pb_toggle_content p",
					),
				),
				'toggle' => array(
					'label'    => esc_html__( 'Toggle', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h5.et_pb_toggle_title, {$this->main_css_element} h1.et_pb_toggle_title, {$this->main_css_element} h2.et_pb_toggle_title, {$this->main_css_element} h3.et_pb_toggle_title, {$this->main_css_element} h4.et_pb_toggle_title, {$this->main_css_element} h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h5',
					),
				),
			),
			'margin_padding' => array(
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
				'label'             => esc_html__( 'Open Toggle Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle',
				'hover'             => 'tabs',
			),
			'open_toggle_background_color' => array(
				'label'             => esc_html__( 'Open Toggle Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle',
				'hover'             => 'tabs',
			),
			'closed_toggle_text_color' => array(
				'label'             => esc_html__( 'Closed Toggle Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle',
				'hover'             => 'tabs',
			),
			'closed_toggle_background_color' => array(
				'label'             => esc_html__( 'Closed Toggle Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'toggle',
				'hover'             => 'tabs',
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
				'hover'             => 'tabs',
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();
		$title  = '%%order_class%% .et_pb_toggle .et_pb_toggle_title';

		$fields['icon_color']        = array( 'color' => '%%order_class%% .et_pb_toggle .et_pb_toggle_title:before' );

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
		$open_toggle_background_color         = $this->props['open_toggle_background_color'];
		$open_toggle_background_color_hover   = $this->get_hover_value( 'open_toggle_background_color' );

		$closed_toggle_background_color       = $this->props['closed_toggle_background_color'];
		$closed_toggle_background_color_hover = $this->get_hover_value( 'closed_toggle_background_color' );

		$icon_color                           = $this->props['icon_color'];
		$icon_color_hover                     = $this->get_hover_value( 'icon_color' );

		$closed_toggle_text_color             = $this->props['closed_toggle_text_color'];
		$closed_toggle_text_color_hover       = $this->get_hover_value( 'closed_toggle_text_color' );

		$open_toggle_text_color               = $this->props['open_toggle_text_color'];
		$open_toggle_text_color_hover         = $this->get_hover_value( 'open_toggle_text_color' );

		global $et_pb_accordion_item_number;

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Open toggle background color
		if ( '' !== $open_toggle_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_toggle_open',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $open_toggle_background_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'open_toggle_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_open',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $open_toggle_background_color_hover )
				),
			) );
		}

		// Closed toggle background color
		if ( '' !== $closed_toggle_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_toggle_close',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $closed_toggle_background_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'closed_toggle_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_close',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $closed_toggle_background_color_hover )
				),
			) );
		}

		// Icon color
		if ( '' !== $icon_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_toggle_title:before',
				'priority'    => ET_Builder_Element::DEFAULT_PRIORITY,
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $icon_color )
				),
			) );
		}

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

		// Closed toggle text color
		if ( '' !== $closed_toggle_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_toggle_close h5.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h1.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h2.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h3.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h4.et_pb_toggle_title, %%order_class%% .et_pb_toggle_close h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $closed_toggle_text_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'closed_toggle_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_close h5.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h1.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h2.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h3.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h4.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_close h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $closed_toggle_text_color_hover )
				),
			) );
		}

		// Open toggle text color
		if ( '' !== $open_toggle_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_toggle_open h5.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h1.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h2.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h3.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h4.et_pb_toggle_title, %%order_class%% .et_pb_toggle_open h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $open_toggle_text_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'open_toggle_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover .et_pb_toggle_open h5.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h1.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h2.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h3.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h4.et_pb_toggle_title, %%order_class%%:hover .et_pb_toggle_open h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $open_toggle_text_color_hover )
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
