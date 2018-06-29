<?php

class ET_Builder_Module_Number_Counter extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Number Counter', 'et_builder' );
		$this->slug       = 'et_pb_number_counter';
		$this->vb_support = 'on';
		$this->custom_css_fields = array(
			'percent' => array(
				'label'    => esc_html__( 'Percent', 'et_builder' ),
				'selector' => '.percent',
			),
			'number_counter_title' => array(
				'label'    => esc_html__( 'Number Counter Title', 'et_builder' ),
				'selector' => 'h3',
			),
		);

		$this->main_css_element = '%%order_class%%.et_pb_number_counter';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
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
						'main'      => "{$this->main_css_element} h3, {$this->main_css_element} h1.title, {$this->main_css_element} h2.title, {$this->main_css_element} h4.title, {$this->main_css_element} h5.title, {$this->main_css_element} h6.title",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h3',
					),
				),
				'number'   => array(
					'label'    => esc_html__( 'Number', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .percent p",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'text_color' => array(
						'old_option_ref' => 'counter_color',
						'default' => et_builder_accent_color(),
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
					'important' => array( 'custom_margin' ),
				),
			),
			'max_width'             => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_number_counter.et_pb_module',
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'options' => array(
					'text_orientation'  => array(
						'default' => 'center',
					),
					'background_layout' => array(
						'default' => 'light',
					),
				),
			),
			'button'                => false,
		);

		if ( et_is_builder_plugin_active() ) {
			$this->advanced_fields['fonts']['number']['css']['important'] = 'all';
		}

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'qEE6z2t2oJ8' ),
				'name' => esc_html__( 'An introduction to the Number Counter module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for the counter.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'number' => array(
				'label'           => esc_html__( 'Number', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'value_type'      => 'float',
				'description'     => esc_html__( "Define a number for the counter. (Don't include the percentage sign, use the option below.)", 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'default_on_front' => '0',
			),
			'percent_sign' => array(
				'label'             => esc_html__( 'Percent Sign', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Here you can choose whether the percent sign should be added after the number set above.', 'et_builder' ),
			),
			'counter_color' => array(
				'type'              => 'hidden',
				'default'           => '',
				'tab_slug'          => 'advanced',
			),
		);
		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		wp_enqueue_script( 'easypiechart' );
		$number            = $this->props['number'];
		$percent_sign      = $this->props['percent_sign'];
		$title             = $this->props['title'];
		$counter_color     = $this->props['counter_color'];
		$background_layout = $this->props['background_layout'];
		$header_level      = $this->props['title_level'];

		if ( et_is_builder_plugin_active() ) {
			wp_enqueue_script( 'fittext' );
		}

		$separator                 = strpos( $number, ',' ) ? ',' : '';
		$number                    = str_ireplace( array( '%', ',' ), '', $number );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Module classnames
		$this->add_classname( array(
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(),
		) );

		if ( '' !== $title ) {
			$this->add_classname( 'et_pb_with_title' );
		}

		$output = sprintf(
			'<div%1$s class="%2$s" data-number-value="%3$s" data-number-separator="%7$s">
				%9$s
				%8$s
				<div class="percent" %4$s><p><span class="percent-value"></span>%5$s</p></div>
				%6$s
			</div><!-- .et_pb_number_counter -->',
			$this->module_id(),
			$this->module_classname( $render_slug ),
			esc_attr( $number ),
			( '' !== $counter_color ? sprintf( ' style="color:%s"', esc_attr( $counter_color ) ) : '' ),
			( 'on' == $percent_sign ? '%' : ''),
			( '' !== $title ? sprintf( '<%1$s class="title">%2$s</%1$s>', et_pb_process_header_level( $header_level, 'h3' ), esc_html( $title ) ) : '' ),
			esc_attr( $separator ),
			$video_background,
			$parallax_image_background
		 );

		return $output;
	}
}

new ET_Builder_Module_Number_Counter;
