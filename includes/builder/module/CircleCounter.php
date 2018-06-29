<?php

class ET_Builder_Module_Circle_Counter extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Circle Counter', 'et_builder' );
		$this->slug       = 'et_pb_circle_counter';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_circle_counter';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'circle' => esc_html__( 'Circle', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h3, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h2.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h3',
					),
				),
				'number'   => array(
					'label'    => esc_html__( 'Number', 'et_builder' ),
					'hide_line_height' => true,
					'css'      => array(
						'main' => "{$this->main_css_element} .percent p",
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ),
				),
			),
			'max_width'             => array(
				'options' => array(
					'max_width' => array(
						'default' => '225px',
						'range_settings'  => array(
							'min'  => '0',
							'max'  => '450',
							'step' => '1',
						),
					),
					'module_alignment' => array(
						'depends_show_if_not' => array(
							'',
							'225px',
						),
					),
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'options' => array(
					'text_orientation'  => array(
						'default_on_front' => 'center',
					),
					'background_layout' => array(
						'default_on_front' => 'light',
					),
				),
			),
			'filters'               => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'button'                => false,
		);

		$this->custom_css_fields = array(
			'percent' => array(
				'label'    => esc_html__( 'Percent Container', 'et_builder' ),
				'selector' => '.percent',
			),
			'circle_counter_title' => array(
				'label'    => esc_html__( 'Circle Counter Title', 'et_builder' ),
				'selector' => 'h3',
			),
			'percent_text' => array(
				'label'    => esc_html__( 'Percent Text', 'et_builder' ),
				'selector' => '.percent p',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'GTslkWWbda0' ),
				'name' => esc_html__( 'An introduction to the Circle Counter module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for the circle counter.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'number' => array(
				'label'             => esc_html__( 'Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'number_validation' => true,
				'value_type'        => 'int',
				'value_min'         => 0,
				'value_max'         => 100,
				'description'       => et_get_safe_localization( __( "Define a number for the circle counter. (Don't include the percentage sign, use the option below.). <strong>Note: You can use only natural numbers from 0 to 100</strong>", 'et_builder' ) ),
				'toggle_slug'       => 'main_content',
				'default_on_front'  => '0',
			),
			'percent_sign' => array(
				'label'            => esc_html__( 'Percent Sign', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether the percent sign should be added after the number set above.', 'et_builder' ),
				'default_on_front' => 'on',
			),
			'bar_bg_color' => array(
				'default'           => et_builder_accent_color(),
				'label'             => esc_html__( 'Bar Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'circle',
				'description'       => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
			),
			'circle_color' => array(
				'label'             => esc_html__( 'Circle Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'circle',
			),
			'circle_color_alpha' => array(
				'label'           => esc_html__( 'Circle Color Opacity', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'range_settings'  => array(
					'min'  => '0.1',
					'max'  => '1.0',
					'step' => '0.05',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'circle',
				'unitless'        => true,
			),
		);
		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		wp_enqueue_script( 'easypiechart' );
		$number                      = $this->props['number'];
		$percent_sign                = $this->props['percent_sign'];
		$title                       = $this->props['title'];
		$background_layout           = $this->props['background_layout'];
		$bar_bg_color                = $this->props['bar_bg_color'];
		$circle_color                = $this->props['circle_color'];
		$circle_color_alpha          = $this->props['circle_color_alpha'];
		$custom_padding              = $this->props['custom_padding'];
		$custom_padding_tablet       = $this->props['custom_padding_tablet'];
		$custom_padding_phone        = $this->props['custom_padding_phone'];
		$header_level                = $this->props['title_level'];

		if ( '' !== $custom_padding ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% canvas',
				'declaration' => sprintf(
					'top: %1$s; left: %2$s;',
					esc_attr( et_pb_get_spacing( $custom_padding, 'top' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding, 'left' ) )
				),
			) );
		}

		if ( '' !== $custom_padding_tablet ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% canvas',
				'declaration' => sprintf(
					'top: %1$s; left: %2$s;',
					esc_attr( et_pb_get_spacing( $custom_padding_tablet, 'top' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding_tablet, 'left' ) )
				),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			) );
		}

		if ( '' !== $custom_padding_phone ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% canvas',
				'declaration' => sprintf(
					'top: %1$s; left: %2$s;',
					esc_attr( et_pb_get_spacing( $custom_padding_phone, 'top' ) ),
					esc_attr( et_pb_get_spacing( $custom_padding_phone, 'left' ) )
				),
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			) );
		}

		$number = str_ireplace( '%', '', $number );

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$circle_color_data = '' !== $circle_color ?
			sprintf( ' data-color="%1$s"', esc_attr( $circle_color ) )
			: '';
		$circle_color_alpha_data = '' !== $circle_color_alpha ?
			sprintf( ' data-alpha="%1$s"', esc_attr( $circle_color_alpha ) )
			: '';

		// Module classnames
		$this->add_classname( array(
			"et_pb_bg_layout_{$background_layout}",
			'container-width-change-notify',
			$this->get_text_orientation_classname(),
		) );

		if ( '' !== $title ) {
			$this->add_classname( 'et_pb_with_title' );
		}

		$output = sprintf(
			'<div%1$s class="%2$s" data-number-value="%3$s" data-bar-bg-color="%4$s"%7$s%8$s>
				%10$s
				%9$s
					<div class="percent"><p><span class="percent-value"></span>%5$s</p></div>
					%6$s
			</div><!-- .et_pb_circle_counter -->',
			$this->module_id(),
			$this->module_classname( $render_slug ),
			esc_attr( $number ),
			esc_attr( $bar_bg_color ),
			( 'on' == $percent_sign ? '%' : ''),
			( '' !== $title ?  sprintf( '<%1$s class="et_pb_module_header">%2$s</%1$s>', et_pb_process_header_level( $header_level, 'h3' ), esc_html( $title ) ) : '' ),
			$circle_color_data,
			$circle_color_alpha_data,
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Circle_Counter;
