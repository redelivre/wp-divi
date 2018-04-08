<?php

class ET_Builder_Module_Bar_Counters extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Bar Counters', 'et_builder' );
		$this->slug            = 'et_pb_counters';
		$this->fb_support      = true;
		$this->child_slug      = 'et_pb_counter';
		$this->child_item_text = esc_html__( 'Bar Counter', 'et_builder' );

		$this->whitelisted_fields = array(
			'background_layout',
			'background_color',
			'bar_bg_color',
			'use_percentages',
			'admin_label',
			'module_id',
			'module_class',
		);

		$this->fields_defaults = array(
			'background_layout' => array( 'light' ),
			'background_color'  => array( '#dddddd', 'add_default_setting' ),
			'bar_bg_color'      => array( et_builder_accent_color(), 'add_default_setting' ),
			'use_percentages'   => array( 'on' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_counters';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'   => esc_html__( 'Elements', 'et_builder' ),
					'background' => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'bar' => esc_html__( 'Bar Counter', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'border' => array(
				'css' => array(
					'main' => array(
						'border_radii'  => "%%order_class%% .et_pb_counter_container, %%order_class%% .et_pb_counter_amount",
						'border_styles' => "%%order_class%% .et_pb_counter_container",
					),
				),
			),
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_counter_title",
					),
				),
				'percent'   => array(
					'label'    => esc_html__( 'Percentage', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_counter_amount",
					),
				),
			),
			'background' => array(
				'use_background_color' => false,
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_counter_container",
				),
			),
			'custom_margin_padding' => array(
				'css'           => array(
					'margin'    => "{$this->main_css_element}",
					'padding'   => "{$this->main_css_element} .et_pb_counter_amount",
					'important' => array( 'custom_margin' ),
				),
			),
			'max_width' => array(),
			'text'      => array(),
			'filters'   => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
		);
		$this->custom_css_options = array(
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
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'toggle_slug'       => 'background',
				'description'       => esc_html__( 'This will adjust the color of the empty space in the bar (currently gray).', 'et_builder' ),
			),
			'bar_bg_color' => array(
				'label'             => esc_html__( 'Bar Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'bar',
				'description'       => esc_html__( 'This will change the fill color for the bar.', 'et_builder' ),
			),
			'use_percentages' => array(
				'label'             => esc_html__( 'Use Percentages', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'On', 'et_builder' ),
					'off' => esc_html__( 'Off', 'et_builder' ),
				),
				'toggle_slug'       => 'elements',
			),
			'border_radius' => array(),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);

		return $fields;
	}

	function pre_shortcode_content() {
		global $et_pb_counters_settings;

		$background_color          = $this->shortcode_atts['background_color'];
		$background_image          = $this->shortcode_atts['background_image'];
		$parallax                  = $this->shortcode_atts['parallax'];
		$parallax_method           = $this->shortcode_atts['parallax_method'];
		$background_video_mp4      = $this->shortcode_atts['background_video_mp4'];
		$background_video_webm     = $this->shortcode_atts['background_video_webm'];
		$background_video_width    = $this->shortcode_atts['background_video_width'];
		$background_video_height   = $this->shortcode_atts['background_video_height'];
		$allow_player_pause        = $this->shortcode_atts['allow_player_pause'];
		$bar_bg_color              = $this->shortcode_atts['bar_bg_color'];
		$use_percentages           = $this->shortcode_atts['use_percentages'];
		$background_video_pause_outside_viewport = $this->shortcode_atts['background_video_pause_outside_viewport'];

		$et_pb_counters_settings = array(
			'background_color'          => $background_color,
			'background_image'          => $background_image,
			'parallax'                  => $parallax,
			'parallax_method'           => $parallax_method,
			'background_video_mp4'      => $background_video_mp4,
			'background_video_webm'     => $background_video_webm,
			'background_video_width'    => $background_video_width,
			'background_video_height'   => $background_video_height,
			'allow_player_pause'        => $allow_player_pause,
			'bar_bg_color'              => $bar_bg_color,
			'use_percentages'           => $use_percentages,
			'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport,
		);
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$background_layout  = $this->shortcode_atts['background_layout'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		$video_background = $this->video_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}";

		$output = sprintf(
			'<ul%3$s class="et_pb_counters et-waypoint%2$s%4$s%5$s%6$s">
				%1$s
			</ul> <!-- .et_pb_counters -->',
			$this->shortcode_content,
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$this->get_text_orientation_classname()
		);

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		/**
		 * @var ET_Builder_Module_Field_BoxShadow $boxShadow
		 */
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$selector = sprintf( '.%1$s .et_pb_counter_container', self::get_module_order_class( $function_name ) );

		self::set_style( $function_name, $boxShadow->get_style(
			$selector,
			$this->shortcode_atts
		) );
	}
}

new ET_Builder_Module_Bar_Counters;
