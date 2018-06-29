<?php

class ET_Builder_Module_CTA extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Call To Action', 'et_builder' );
		$this->slug       = 'et_pb_cta';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_promo';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text'  => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'width' => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 80,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'header' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
						'plugin_main' => "{$this->main_css_element} p",
						'text_shadow' => "{$this->main_css_element} p",
					),
				),
			),
			'background'            => array(
				'has_background_color_toggle' => true,
				'use_background_color' => 'fields_only',
				'options' => array(
					'background_color' => array(
						'depends_show_if'  => 'on',
						'default'          => et_builder_accent_color(),
					),
					'use_background_color' => array(
						'default'          => 'on',
					),
				),
			),
			'max_width'             => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_promo.et_pb_module',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_promo_button.et_pb_button",
						'alignment'   => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment' => true,
					'box_shadow'    => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'css'      => array(
					'text_shadow' => '%%order_class%% .et_pb_promo_description',
				),
				'options' => array(
					'text_orientation'  => array(
						'default'          => 'center',
					),
					'background_layout' => array(
						'default' => 'dark',
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'promo_description' => array(
				'label'    => esc_html__( 'Promo Description', 'et_builder' ),
				'selector' => '.et_pb_promo_description',
			),
			'promo_button' => array(
				'label'    => esc_html__( 'Promo Button', 'et_builder' ),
				'selector' => '.et_pb_promo .et_pb_button.et_pb_promo_button',
				'no_space_before_selector' => true,
			),
			'promo_title' => array(
				'label'    => esc_html__( 'Promo Title', 'et_builder' ),
				'selector' => '.et_pb_promo_description h2',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'E3AEllqnCus' ),
				'name' => esc_html__( 'An introduction to the Call To Action module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your value to action title here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'button_url' => array(
				'label'           => esc_html__( 'Button URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for your CTA button.', 'et_builder' ),
				'toggle_slug'     => 'link',
			),
			'url_new_window' => array(
				'label'            => esc_html__( 'Url Opens', 'et_builder' ),
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
				'label'           => esc_html__( 'Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired button text, or leave blank for no button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'content' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
		);

		return $fields;
	}

	function get_max_width_additional_css() {
		$additional_css = 'center' === $this->get_text_orientation() ? '; margin: 0 auto;' : '';

		return $additional_css;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$title                = $this->props['title'];
		$button_url           = $this->props['button_url'];
		$button_rel           = $this->props['button_rel'];
		$button_text          = $this->props['button_text'];
		$background_color     = $this->props['background_color'];
		$background_layout    = $this->props['background_layout'];
		$use_background_color = $this->props['use_background_color'];
		$url_new_window       = $this->props['url_new_window'];
		$custom_icon          = $this->props['button_icon'];
		$button_custom        = $this->props['custom_button'];
		$header_level         = $this->props['header_level'];

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$button_url = trim( $button_url );

		// Module classnames
		$this->add_classname( array(
			'et_pb_promo',
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(),
		) );

		if ( 'on' !== $use_background_color ) {
			$this->add_classname( 'et_pb_no_bg' );
		}

		// Remove automatically added classname
		$this->remove_classname( 'et_pb_cta' );

		// Render button
		$button = $this->render_button( array(
			'button_classname' => array( 'et_pb_promo_button' ),
			'button_custom'    => $button_custom,
			'button_rel'       => $button_rel,
			'button_text'      => $button_text,
			'button_url'       => $button_url,
			'custom_icon'      => $custom_icon,
			'url_new_window'   => $url_new_window,
			'display_button'   => '' !== $button_url && '' !== $button_text,
		) );


		// Render module output
		$output = sprintf(
			'<div%6$s class="%4$s"%5$s>
				%8$s
				%7$s
				<div class="et_pb_promo_description">
					%1$s
					%2$s
				</div>
				%3$s
			</div>',
			( '' !== $title ? sprintf( '<%1$s class="et_pb_module_header">%2$s</%1$s>', et_pb_process_header_level( $header_level, 'h2' ), esc_html( $title ) ) : '' ),
			$this->content,
			$button,
			$this->module_classname( $render_slug ),
			( 'on' === $use_background_color
				? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
				: ''
			),
			$this->module_id(),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_CTA;
