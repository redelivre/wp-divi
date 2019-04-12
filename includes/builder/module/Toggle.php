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
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h5, {$this->main_css_element} h1.et_pb_toggle_title, {$this->main_css_element} h2.et_pb_toggle_title, {$this->main_css_element} h3.et_pb_toggle_title, {$this->main_css_element} h4.et_pb_toggle_title, {$this->main_css_element} h6.et_pb_toggle_title",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h5',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'         => "{$this->main_css_element}",
						'limited_main' => "{$this->main_css_element}, {$this->main_css_element} p, {$this->main_css_element} .et_pb_toggle_content",
						'line_height'  => "{$this->main_css_element} p",
						'text_shadow'  => "{$this->main_css_element} .et_pb_toggle_content",
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
				'label'             => esc_html__( 'Content', 'et_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'dynamic_content'   => 'text',
			),
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

		$fields['icon_color']             = array( 'color' => '%%order_class%% .et_pb_toggle_title:before' );

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
		$open                                 = $this->props['open'];

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

		$header_level                         = $this->props['title_level'];

		if ( '' !== $open_toggle_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_open',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $open_toggle_background_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'open_toggle_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_open:hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $open_toggle_background_color_hover )
				),
			) );
		}

		if ( '' !== $closed_toggle_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_close',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $closed_toggle_background_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'closed_toggle_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_close:hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $closed_toggle_background_color_hover )
				),
			) );
		}

		if ( '' !== $icon_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_toggle_title:before',
				'priority'    => ET_Builder_Element::DEFAULT_PRIORITY + 1,
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $icon_color )
				),
			) );
		}

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

		if ( '' !== $closed_toggle_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_close h5.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h1.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h2.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h3.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h4.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $closed_toggle_text_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'closed_toggle_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_close h5.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_close h1.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_close:hover h2.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close:hover h3.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close:hover h4.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_close:hover h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $closed_toggle_text_color_hover )
				),
			) );
		}

		if ( '' !== $open_toggle_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_open h5.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h1.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h2.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h3.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h4.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $open_toggle_text_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'open_toggle_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_toggle.et_pb_toggle_open h5.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_open h1.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_open:hover h2.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open:hover h3.et_pb_toggle_title, %%order_class%%.et_pb_toggle.et_pb_toggle_open h4.et_pb_toggle_title:hover, %%order_class%%.et_pb_toggle.et_pb_toggle_open:hover h6.et_pb_toggle_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $open_toggle_text_color_hover )
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
