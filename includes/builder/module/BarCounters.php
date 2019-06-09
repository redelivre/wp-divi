<?php

class ET_Builder_Module_Bar_Counters extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Bar Counters', 'et_builder' );
		$this->plural          = esc_html__( 'Bar Counters', 'et_builder' );
		$this->slug            = 'et_pb_counters';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_counter';
		$this->child_item_text = esc_html__( 'Bar Counter', 'et_builder' );

		$this->main_css_element = '%%order_class%%.et_pb_counters';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'   => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'bar'    => esc_html__( 'Bar', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .et_pb_counter_container, %%order_class%% .et_pb_counter_amount",
							'border_styles' => "%%order_class%% .et_pb_counter_container",
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'main'    => '%%order_class%% .et_pb_counter_container',
						'overlay' => 'inset',
					),
				),
			),
			'fonts'                 => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_counter_title",
					),
				),
				'percent'   => array(
					'label'    => esc_html__( 'Percentage', 'et_builder' ),
					'css'      => array(
						'main'       => "{$this->main_css_element} .et_pb_counter_amount_number",
						'text_align' => "{$this->main_css_element} .et_pb_counter_amount",
					),
				),
			),
			'background'            => array(
				'use_background_color' => 'fields_only',
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_counter_container",
				),
				'options' => array(
					'background_color' => array(
						'default'          => '#dddddd',
					),
				),
			),
			'margin_padding' => array(
				'draggable_padding' => false,
				'css'           => array(
					'margin'    => "{$this->main_css_element}",
					'padding'   => "{$this->main_css_element} .et_pb_counter_amount",
					'important' => array( 'custom_margin' ),
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'options' => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover' => 'tabs',
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

		$this->help_videos = array(
			array(
				'id'   => esc_html( '2QLX8Lwr3cs' ),
				'name' => esc_html__( 'An introduction to the Bar Counter module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'bar_bg_color' => array(
				'label'             => esc_html__( 'Bar Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'bar',
				'hover'             => 'tabs',
				'description'       => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
				'default'           => et_builder_accent_color(),
				'mobile_options'    => true,
			),
			'use_percentages' => array(
				'label'             => esc_html__( 'Show Percentage', 'et_builder' ),
				'description'       => esc_html__( 'Turning off percentages will remove the percentage text from within the filled portion of the bar.', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
				'default_on_front'  => 'on',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['background_layout'] = array( 'color' => '%%order_class%% .et_pb_counter_title' );
		$fields['bar_bg_color'] = array( 'background-color' => '%%order_class%% .et_pb_counter_amount' );

		return $fields;
	}

	function before_render() {
		global $et_pb_counters_settings;

		$background_color          = $this->props['background_color'];
		$background_image          = $this->props['background_image'];
		$parallax                  = $this->props['parallax'];
		$parallax_method           = $this->props['parallax_method'];
		$background_video_mp4      = $this->props['background_video_mp4'];
		$background_video_webm     = $this->props['background_video_webm'];
		$background_video_width    = $this->props['background_video_width'];
		$background_video_height   = $this->props['background_video_height'];
		$allow_player_pause        = $this->props['allow_player_pause'];
		$bar_bg_color_values       = et_pb_responsive_options()->get_property_values( $this->props, 'bar_bg_color' );
		$use_percentages           = $this->props['use_percentages'];
		$background_video_pause_outside_viewport = $this->props['background_video_pause_outside_viewport'];

		$et_pb_counters_settings = array(
			'background_color'          => $background_color,
			'background_color_hover'    => self::get_hover_value( 'background_color' ),
			'background_image'          => $background_image,
			'parallax'                  => $parallax,
			'parallax_method'           => $parallax_method,
			'background_video_mp4'      => $background_video_mp4,
			'background_video_webm'     => $background_video_webm,
			'background_video_width'    => $background_video_width,
			'background_video_height'   => $background_video_height,
			'allow_player_pause'        => $allow_player_pause,
			'bar_bg_color'              => isset( $bar_bg_color_values['desktop'] ) ? $bar_bg_color_values['desktop'] : '',
			'bar_bg_color_tablet'       => isset( $bar_bg_color_values['tablet'] ) ? $bar_bg_color_values['tablet'] : '',
			'bar_bg_color_phone'        => isset( $bar_bg_color_values['phone'] ) ? $bar_bg_color_values['phone'] : '',
			'use_percentages'           => $use_percentages,
			'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport,
		);
	}

	function render( $attrs, $content = null, $render_slug ) {
		$background_layout               = $this->props['background_layout'];
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$bar_bg_hover_color              = et_pb_hover_options()->get_value( 'bar_bg_color', $this->props );
		$video_background = $this->video_background();

		// Module classname
		$this->add_classname( array(
			'et-waypoint',
			"et_pb_bg_layout_{$background_layout}",
		) );
		
		$background_layout_tablet = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}

		$background_layout_phone = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';
		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		$this->add_classname( $this->get_text_orientation_classname() );

		$data_background_layout       = '';
		$data_background_layout_hover = '';

		if ( et_pb_hover_options()->is_enabled( 'background_layout', $this->props ) ) {
			$data_background_layout = sprintf(
				' data-background-layout="%1$s"',
				esc_attr( $background_layout )
			);
			$data_background_layout_hover = sprintf(
				' data-background-layout-hover="%1$s"',
				esc_attr( $background_layout_hover )
			);
		}

		if ( ! empty( $bar_bg_hover_color ) ) {
			self::set_style( $render_slug,
				array(
					'selector'    => '%%order_class%%:hover .et_pb_counter_amount',
					'declaration' => sprintf( 'background-color: %1$s;', esc_attr( $bar_bg_hover_color ) ),
				) );
		}

		$output = sprintf(
			'<ul%3$s class="%2$s"%4$s%5$s>
				%1$s
			</ul> <!-- .et_pb_counters -->',
			$this->content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover ) // #5
		);

		return $output;
	}
}

new ET_Builder_Module_Bar_Counters;
