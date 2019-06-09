<?php

class ET_Builder_Module_Bar_Counters_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Bar Counter', 'et_builder' );
		$this->plural                      = esc_html__( 'Bar Counters', 'et_builder' );
		$this->slug                        = 'et_pb_counter';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'content';
		$this->advanced_setting_title_text = esc_html__( 'New Bar Counter', 'et_builder' );
		$this->settings_text               = esc_html__( 'Bar Counter Settings', 'et_builder' );
		$this->main_css_element            = '%%order_class%%';

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} span.et_pb_counter_container, {$this->main_css_element} span.et_pb_counter_amount",
							'border_styles' => "{$this->main_css_element} span.et_pb_counter_container",
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'main'    => '%%order_class%% span.et_pb_counter_container',
						'overlay' => 'inset',
					),
				),
			),
			'fonts'                 => array(
				'title'   => array(
					'label' => esc_html__( 'Title', 'et_builder' ),
					'css'   => array(
						'main' => ".et_pb_counters {$this->main_css_element} .et_pb_counter_title",
					),
				),
				'percent' => array(
					'label' => esc_html__( 'Percentage', 'et_builder' ),
					'css'   => array(
						'main'       => ".et_pb_counters {$this->main_css_element} .et_pb_counter_amount_number",
						'text_align' => ".et_pb_counters {$this->main_css_element} .et_pb_counter_amount",
					),
				),
			),
			'background'            => array(
				'use_background_color' => 'fields_only',
				'css'                  => array(
					'main' => ".et_pb_counters li{$this->main_css_element} .et_pb_counter_container",
				),
			),
			'margin_padding' => array(
				'draggable_margin'  => false,
				'draggable_padding' => false,
				'css' => array(
					'margin'  => ".et_pb_counters {$this->main_css_element}",
					'padding' => ".et_pb_counters {$this->main_css_element} .et_pb_counter_amount",
				),
			),
			'max_width'             => array(
				'css' => array(
					'module_alignment' => ".et_pb_counters {$this->main_css_element}",
				),
			),
			'text'                  => array(
				'css' => array(
					'text_orientation' => '%%order_class%% .et_pb_counter_title, %%order_class%% .et_pb_counter_amount',
				),
			),
			'button'                => false,
			'height'                => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_counter_container, %%order_class%% .et_pb_counter_container .et_pb_counter_amount'
				)
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'bar'        => esc_html__( 'Bar Counter', 'et_builder' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'counter_title' => array(
				'label'    => esc_html__( 'Counter Title', 'et_builder' ),
				'selector' => '.et_pb_counter_title',
			),
			'counter_container' => array(
				'label'    => esc_html__( 'Counter Container', 'et_builder' ),
				'selector' => '.et_pb_counter_container',
			),
			'counter_amount' => array(
				'label'    => esc_html__( 'Counter Amount', 'et_builder' ),
				'selector' => '.et_pb_counter_amount',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'content' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input a title for your bar.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'percent' => array(
				'label'            => esc_html__( 'Percent', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Define a percentage for this bar.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => '0',
			),
			'bar_background_color' => array(
				'label'          => esc_html__( 'Bar Background Color', 'et_builder' ),
				'description'    => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'hover'          => 'tabs',
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'bar',
				'mobile_options' => true,
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['background_layout'] = array( 'color' => '%%order_class%% .et_pb_counter_title' );
		$fields['bar_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_counter_amount' );

		return $fields;
	}

	function get_parallax_image_background( $base_name = 'background' ) {
		global $et_pb_counters_settings;

		// Parallax setting is only derived from parent if bar counter item has no background
		$use_counter_value   = '' !== $this->props['background_color'] || 'on' === $this->props['use_background_color_gradient'] || '' !== $this->props['background_image'] || '' !== $this->props['background_video_mp4'] || '' !== $this->props['background_video_webm'];
		$background_image    = $use_counter_value ? $this->props['background_image'] : $et_pb_counters_settings['background_image'];
		$parallax            = $use_counter_value ? $this->props['parallax'] : $et_pb_counters_settings['parallax'];
		$parallax_method     = $use_counter_value ? $this->props['parallax_method'] : $et_pb_counters_settings['parallax_method'];
		$parallax_background = '';

		if ( '' !== $background_image && 'on' == $parallax ) {
			$parallax_classname = array(
				'et_parallax_bg'
			);

			if ( 'off' === $parallax_method ) {
				$parallax_classname[] = 'et_pb_parallax_css';
			}

			$parallax_background = sprintf( '<div
					class="%1$s"
					style="background-image: url(%2$s);"
					></div>',
				esc_attr( implode( ' ', $parallax_classname ) ),
				esc_attr( $background_image )
			);
		}

		return $parallax_background;
	}

	function video_background( $args = array(), $base_name = 'background' ) {
		global $et_pb_counters_settings;

		$use_counter_value       = '' !== $this->props['background_color'] || 'on' === $this->props['use_background_color_gradient'] || '' !== $this->props['background_image'] || '' !== $this->props['background_video_mp4'] || '' !== $this->props['background_video_webm'];
		$background_video_mp4    = $use_counter_value && isset( $this->props['background_video_mp4'] ) ? $this->props['background_video_mp4'] : $et_pb_counters_settings['background_video_mp4'];
		$background_video_webm   = $use_counter_value && isset( $this->props['background_video_webm'] ) ? $this->props['background_video_webm'] : $et_pb_counters_settings['background_video_webm'];
		$background_video_width  = $use_counter_value && isset( $this->props['background_video_width'] ) ? $this->props['background_video_width'] : $et_pb_counters_settings['background_video_width'];
		$background_video_height = $use_counter_value && isset( $this->props['background_video_height'] ) ? $this->props['background_video_height'] : $et_pb_counters_settings['background_video_height'];

		if ( ! empty( $args ) ) {
			$background_video = self::get_video_background( $args );

			$allow_player_pause     = isset( $args['allow_player_pause'] ) ? $args['allow_player_pause' ] : 'off';
			$pause_outside_viewport = isset( $args['background_video_pause_outside_viewport'] ) ? $args['background_video_pause_outside_viewport'] : 'on';
		} else {
			$background_video = self::get_video_background( array(
				'background_video_mp4'    => $background_video_mp4,
				'background_video_webm'   => $background_video_webm,
				'background_video_width'  => $background_video_width,
				'background_video_height' => $background_video_height,
			) );

			$allow_player_pause          = $use_counter_value ? $this->props['allow_player_pause'] : $et_pb_counters_settings['allow_player_pause'];
			$pause_outside_viewport = $use_counter_value ? $this->props['background_video_pause_outside_viewport'] : $et_pb_counters_settings['background_video_pause_outside_viewport'];
		}

		$video_background = '';

		if ( $background_video ) {
			$video_background = sprintf(
				'<div class="et_pb_section_video_bg%2$s">
					%1$s
				</div>',
				$background_video,
				( 'on' === $allow_player_pause ? ' et_pb_allow_player_pause' : '' ),
				( 'off' === $pause_outside_viewport ? ' et_pb_video_play_outside_viewport' : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		// Added classname for module wrapper
		if ( '' !== $video_background ) {
			$this->add_classname( array( 'et_pb_section_video', 'et_pb_preload' ) );
		}

		return $video_background;
	}

	function render( $attrs, $content = null, $render_slug ) {
		global $et_pb_counters_settings;

		$percent                       = $this->props['percent'];
		$background_color              = self::$_->array_get( $this->props, 'background_color' );
		$background_color              = empty( $background_color ) ? $et_pb_counters_settings['background_color'] : $background_color;
		$background_color_hover        = self::get_hover_value( 'background_color' );
		$bar_background_color          = self::$_->array_get( $this->props, 'bar_background_color' );
		$bar_background_color          = empty( $bar_background_color ) ? $et_pb_counters_settings['bar_bg_color'] : $bar_background_color;
		$bar_background_hover_color    = et_pb_hover_options()->get_value( 'bar_background_color', $this->props );
		$background_image              = $this->props['background_image'];
		$use_background_color_gradient = $this->props['use_background_color_gradient'];

		// Bar background color responsive. First of all, check if value from bar counters item is
		// exist and responsive setting is enabled. If it doesn't exist, get it from bar counters
		// and also ensure responsive setting is enabled.
		$is_bar_background_color_responsive = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'bar_background_color' );

		$bar_background_color_tablet = $is_bar_background_color_responsive ? et_pb_responsive_options()->get_any_value( $this->props, 'bar_background_color_tablet' ) : '';
		$bar_background_color_tablet = '' === $bar_background_color_tablet ? $et_pb_counters_settings['bar_bg_color_tablet'] : $bar_background_color_tablet;

		$bar_background_color_phone = $is_bar_background_color_responsive ? et_pb_responsive_options()->get_any_value( $this->props, 'bar_background_color_phone' ) : '';
		$bar_background_color_phone = '' === $bar_background_color_phone ? $et_pb_counters_settings['bar_bg_color_phone'] : $bar_background_color_phone;

		// Add % only if it hasn't been added to the attribute
		if ( '%' !== substr( trim( $percent ), -1 ) ) {
			$percent .= '%';
		}

		if ( empty( $background_color_hover ) ) {
			$background_color_hover = $et_pb_counters_settings['background_color_hover'];
		}


		$background_color_style = $bar_bg_color_style = '';

		if ( '' !== $background_color ) {
			if ( empty( $background_image ) && 'on' !== $use_background_color_gradient ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '.et_pb_counters %%order_class%% .et_pb_counter_container',
					'declaration' => 'background-image: none;',
				) );
			}
		}

		if ( '' !== $background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_counter_container',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_color )
				),
			) );
		}

		if ( '' !== $background_color_hover ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_counter_container:hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_color_hover )
				),
			) );
		}

		// Bar background color.
		$bar_background_color_values = array(
			'desktop' => esc_html( $bar_background_color ),
			'tablet'  => esc_html( $bar_background_color_tablet ),
			'phone'   => esc_html( $bar_background_color_phone ),
		);
		et_pb_responsive_options()->generate_responsive_css( $bar_background_color_values, '%%order_class%% .et_pb_counter_amount', 'background-color', $render_slug, '', 'color' );
		et_pb_responsive_options()->generate_responsive_css( $bar_background_color_values, '%%order_class%% .et_pb_counter_amount.overlay', 'color', $render_slug, '', 'color' );

		if ( '' !== $bar_background_hover_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '.et_pb_counters %%order_class%%:hover .et_pb_counter_amount',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $bar_background_hover_color )
				),
			) );

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '.et_pb_counters %%order_class%%:hover .et_pb_counter_amount.overlay',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $bar_background_hover_color )
				),
			) );
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Module classname
		$this->add_classname( $this->get_text_orientation_classname() );

		// Remove automatically added classnames
		$this->remove_classname( array(
			'et_pb_module',
			$render_slug,
		) );

		$output = sprintf(
			'<li class="%6$s">
				<span class="et_pb_counter_title">%1$s</span>
				<span class="et_pb_counter_container"%4$s>
					%8$s
					%7$s
					<span class="et_pb_counter_amount" style="%5$s" data-width="%3$s"><span class="et_pb_counter_amount_number">%2$s</span></span>
					<span class="et_pb_counter_amount overlay" style="%5$s" data-width="%3$s"><span class="et_pb_counter_amount_number">%2$s</span></span>
				</span>
			</li>',
			sanitize_text_field( $content ),
			( isset( $et_pb_counters_settings['use_percentages'] ) && 'on' === $et_pb_counters_settings['use_percentages'] ? esc_html( $percent ) : '' ),
			esc_attr( $percent ),
			$background_color_style,
			$bar_bg_color_style,
			$this->module_classname( $render_slug ),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Bar_Counters_Item;
