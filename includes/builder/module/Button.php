<?php

class ET_Builder_Module_Button extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Button', 'et_builder' );
		$this->plural     = esc_html__( 'Buttons', 'et_builder' );
		$this->slug       = 'et_pb_button';
		$this->vb_support = 'on';
		$this->main_css_element = '%%order_class%%';

		$this->custom_css_fields = array(
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'no_space_before_selector' => true,
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'alignment'  => esc_html__( 'Alignment', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => false,
			),
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'main' => $this->main_css_element,
						'limited_main' => "{$this->main_css_element}.et_pb_button",
					),
					'box_shadow' => false,
				),
			),
			'margin_padding' => array(
				'css' => array(
					'padding' => "{$this->main_css_element}_wrapper {$this->main_css_element}, {$this->main_css_element}_wrapper {$this->main_css_element}:hover",
					'margin' => "{$this->main_css_element}_wrapper",
					'important' => 'all',
				),
			),
			'text'                  => array(
				'use_text_orientation' => false,
				'use_background_layout' => true,
				'options' => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover' => 'tabs',
					),
				),
			),
			'text_shadow'           => array(
				// Text Shadow settings are already included on button's advanced style
				'default' => false,
			),
			'background'            => false,
			'fonts'                 => false,
			'max_width'             => false,
			'link_options'          => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'XpM2G7tQQIE' ),
				'name' => esc_html__( 'An introduction to the Button module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'button_url' => array(
				'label'            => esc_html__( 'Button Link URL', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Input the destination URL for your button.', 'et_builder' ),
				'toggle_slug'      => 'link',
				'dynamic_content'  => 'url',
			),
			'url_new_window' => array(
				'label'            => esc_html__( 'Button Link Target', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'      => 'link',
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
				'default_on_front' => 'off',
			),
			'button_text' => array(
				'label'            => esc_html__( 'Button Text', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Input your desired button text.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'dynamic_content'  => 'text',
			),
			'button_alignment' => array(
				'label'            => esc_html__( 'Button Alignment', 'et_builder' ),
				'type'             => 'text_align',
				'option_category'  => 'configuration',
				'options'          => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'alignment',
				'description'      => esc_html__( 'Here you can define the alignment of Button', 'et_builder' ),
			),
		);

		return $fields;
	}

	public function get_button_alignment() {
		$text_orientation = isset( $this->props['button_alignment'] ) ? $this->props['button_alignment'] : '';

		return et_pb_get_alignment( $text_orientation );
	}

	public function get_transition_fields_css_props() {
		return array();
	}

	function render( $attrs, $content = null, $render_slug ) {
		$button_url                      = $this->props['button_url'];
		$button_rel                      = $this->props['button_rel'];
		$button_text                     = $this->_esc_attr( 'button_text', 'limited' );
		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$url_new_window                  = $this->props['url_new_window'];
		$custom_icon                     = $this->props['button_icon'];
		$button_custom                   = $this->props['custom_button'];
		$button_alignment                = $this->get_button_alignment();

		// Nothing to output if neither Button Text nor Button URL defined
		$button_url = trim( $button_url );

		if ( '' === $button_text && '' === $button_url ) {
			return '';
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

		// Module classnames
		$this->add_classname( "et_pb_bg_layout_{$background_layout}" );
		$this->remove_classname( 'et_pb_module' );

		// Render Button
		$button = $this->render_button( array(
			'button_id'           => $this->module_id( false ),
			'button_classname'    => explode( ' ', $this->module_classname( $render_slug ) ),
			'button_custom'       => $button_custom,
			'button_rel'          => $button_rel,
			'button_text'         => $button_text,
			'button_text_escaped' => true,
			'button_url'          => $button_url,
			'custom_icon'         => $custom_icon,
			'has_wrapper'         => false,
			'url_new_window'      => $url_new_window,
		) );

		// Render module output
		$output = sprintf(
			'<div class="et_pb_button_module_wrapper et_pb_button_%3$s_wrapper %2$s et_pb_module "%4$s%5$s>
				%1$s
			</div>',
			et_core_esc_previously( $button ),
			sprintf( 'et_pb_button_alignment_%1$s', esc_attr( $button_alignment ) ),
			esc_attr( $this->render_count() ),
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover )
		);

		self::set_style( $render_slug, array(
			'selector'    => '%%order_class%%, %%order_class%%:after',
			'declaration' => esc_html( $this->get_transition_style( array( 'all' ) ) )
		) );

		return $output;
	}
}

new ET_Builder_Module_Button;
