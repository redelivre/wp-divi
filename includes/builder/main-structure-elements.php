<?php
/**
 * Section Element class
 *
 * @since [version]
 */
class ET_Builder_Section extends ET_Builder_Structure_Element {
	function init() {
		$this->name       = esc_html__( 'Section', 'et_builder' );
		$this->plural     = esc_html__( 'Sections', 'et_builder' );
		$this->slug       = 'et_pb_section';
		$this->vb_support = 'on';

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'background'     => array(
						'title'       => esc_html__( 'Background', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
						),
						'priority' => 80,
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'          => esc_html__( 'Layout', 'et_builder' ),
					'width'           => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
					'margin_padding'  => array(
						'title'       => esc_html__( 'Spacing', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
						),
						'priority'   => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'classes' => array(
						'title'  => esc_html__( 'CSS ID & Classes', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
						),
					),
					'custom_css' => array(
						'title'  => esc_html__( 'Custom CSS', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
						),
					),
				),
			),
		);

		$this->advanced_fields = array(
			'background' => array(
				'use_background_color'          => 'fields_only',
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'use_background_video'          => true,
				'css'                           => array(
					'important' => 'all',
					'main'      => 'div.et_pb_section%%order_class%%',
				),
				'options'    => array(
					'background_color' => array(
						'default' => '',
						'hover' => 'tabs',
					),
					'allow_player_pause' => array(
						'default_on_front' => 'off',
					),
					'background_video_pause_outside_viewport' => array(
						'default_on_front' => 'on',
					),
					'parallax' => array(
						'default_on_front' => 'off',
					),
					'parallax_method' => array(
						'default_on_front' => 'on',
					),
				),
			),
			'max_width'  => array(
				'css' => array(
					'module_alignment' => '%%order_class%%',
				),
				'options' => array(
					'module_alignment' => array(
						'label' => esc_html__( 'Section Alignment', 'et_builder' ),
					),
				),
			),
			'fonts'      => false,
			'text'       => false,
			'button'     => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( '3kmJ_mMVB1w' ),
				'name' => esc_html__( 'An introduction to Sections', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'inner_shadow' => array(
				'label'           => esc_html__( 'Show Inner Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'         => 'off',
				'description'     => esc_html__( 'Here you can select whether or not your section has an inner shadow. This can look great when you have colored backgrounds or background images.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'default_on_front'=> 'off',
			),
			'make_fullwidth' => array(
				'label'             => esc_html__( 'Make This Section Fullwidth', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'use_custom_width' => array(
				'label'             => esc_html__( 'Use Custom Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'make_fullwidth',
					'custom_width',
					'width_unit',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'width_unit' => array(
				'label'             => esc_html__( 'Unit', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'px', 'et_builder' ),
					'off' => '%',
				),
				'default'           => 'on',
				'button_options'    => array(
					'button_type' => 'equal',
				),
				'depends_show_if'   => 'on',
				'affects'           => array(
					'custom_width_px',
					'custom_width_percent',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'custom_width_px' => array(
				'default'             => '1080px',
				'label'               => esc_html__( 'Custom Width', 'et_builder' ),
				'type'                => 'range',
				'option_category'     => 'layout',
				'depends_show_if_not' => 'off',
				'validate_unit'       => true,
				'fixed_unit'          => 'px',
				'range_settings'      => array(
					'min'  => 500,
					'max'  => 2600,
					'step' => 1,
				),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'width',
				'specialty_only'      => 'yes',
				'hover'               => 'tabs',
			),
			'custom_width_percent' => array(
				'default'         => '80%',
				'label'           => esc_html__( 'Custom Width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'depends_show_if' => 'off',
				'validate_unit'   => true,
				'fixed_unit'      => '%',
				'range_settings'  => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'specialty_only'  => 'yes',
				'hover'           => 'tabs',
			),
			'make_equal' => array(
				'label'             => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'use_custom_gutter' => array(
				'label'             => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'gutter_width',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
				'specialty_only'    => 'yes',
			),
			'gutter_width' => array(
				'label'            => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'       => 1,
					'max'       => 4,
					'step'      => 1,
					'min_limit' => 1,
					'max_limit' => 4,
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'specialty_only'   => 'yes',
				'validate_unit'    => false,
				'fixed_range'      => true,
				'default_on_front' => et_get_option( 'gutter_width', 3 ),
				'hover'            => 'tabs',
			),
			'columns_background' => array(
				'type'            => 'column_settings_background',
				'option_category' => 'configuration',
				'toggle_slug'     => 'background',
				'specialty_only'  => 'yes',
				'priority'        => 99,
			),
			'columns_padding' => array(
				'type'            => 'column_settings_padding',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
				'specialty_only'  => 'yes',
				'priority'        => 99,
			),
			'fullwidth' => array(
				'type'    => 'hidden',
				'default_on_front' => 'off',
			),
			'specialty' => array(
				'type'    => 'skip',
				'default_on_front' => 'off',
			),
			'columns_css' => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'custom_css',
				'priority'        => 20,
			),
			'columns_css_fields' => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'priority'        => 20,
			),
			'custom_padding_last_edited' => array(
				'type'           => 'skip',
				'tab_slug'       => 'advanced',
				'specialty_only' => 'yes',
			),
			'__video_background' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Section', 'get_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum' => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
			'prev_background_color' => array(
				'type' => 'skip',
			),
			'next_background_color' => array(
				'type' => 'skip',
			),
		);

		$column_fields = $this->get_column_fields( 3, array(
			'parallax'                                   => array(
				'default_on_front' => 'off',
			),
			'parallax_method'                            => array(
				'default_on_front' => 'on',
			),
			'background_color'                           => array(),
			'bg_img'                                     => array(),
			'background_size'                            => array(),
			'background_position'                        => array(),
			'background_repeat'                          => array(),
			'background_blend'                           => array(),
			'padding_top_bottom_link'                    => array(),
			'padding_left_right_link'                    => array(),
			'use_background_color_gradient'              => array(),
			'background_color_gradient_start'            => array(),
			'background_color_gradient_end'              => array(),
			'background_color_gradient_type'             => array(),
			'background_color_gradient_direction'        => array(),
			'background_color_gradient_direction_radial' => array(),
			'background_color_gradient_start_position'   => array(),
			'background_color_gradient_end_position'     => array(),
			'background_color_gradient_overlays_image'   => array(),
			'background_video_mp4'                       => array(
				'computed_affects' => array(
					'__video_background',
				),
			),
			'background_video_webm'                      => array(
				'computed_affects' => array(
					'__video_background',
				),
			),
			'background_video_width'                     => array(
				'computed_affects' => array(
					'__video_background',
				),
			),
			'background_video_height'                    => array(
				'computed_affects' => array(
					'__video_background',
				),
			),
			'allow_player_pause'                         => array(
				'computed_affects' => array(
					'__video_background',
				),
			),
			'background_video_pause_outside_viewport'    => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'__video_background'                         => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Column',
					'get_column_video_background'
				),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum'    => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
			'padding_top'                                => array( 'tab_slug' => 'advanced' ),
			'padding_right'                              => array( 'tab_slug' => 'advanced' ),
			'padding_bottom'                             => array( 'tab_slug' => 'advanced' ),
			'padding_left'                               => array( 'tab_slug' => 'advanced' ),
			'padding_top_bottom_link'                    => array( 'tab_slug' => 'advanced' ),
			'padding_left_right_link'                    => array( 'tab_slug' => 'advanced' ),
			'padding_%column_index%_tablet'              => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
			),
			'padding_%column_index%_phone'               => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
			),
			'padding_%column_index%_last_edited'         => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
			),
			'module_id'                                  => array( 'tab_slug' => 'custom_css' ),
			'module_class'                               => array( 'tab_slug' => 'custom_css' ),
			'custom_css_before'                          => array( 'tab_slug' => 'custom_css' ),
			'custom_css_main'                            => array( 'tab_slug' => 'custom_css' ),
			'custom_css_after'                           => array( 'tab_slug' => 'custom_css' ),
		) );

		return array_merge( $fields, $column_fields );
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['custom_width_px'] = array( 'max-width' => '%%order_class%%' );
		$fields['custom_width_percent'] = array(
			'max-width' => '%%order_class%% > .et_pb_row',
			'width'     => '%%order_class%% > .et_pb_row',
		);

		// Section Dividers Height
		foreach ( array( 'top', 'bottom' ) as $placement ) {
			// Inside sprintf, the double %% prints a literal '%' character
			$selector                                = sprintf(
				'%%%%order_class%%%%.section_has_divider.et_pb_%1$s_divider .et_pb_%1$s_inside_divider',
				$placement
			);
			$fields[ "{$placement}_divider_height" ] = array(
				'height'          => $selector,
				'background-size' => $selector,
			);
		}

		return $fields;
	}

	function render( $atts, $content = null, $function_name ) {
		$background_image                             = $this->props['background_image'];
		$background_color                             = $this->props['background_color'];
		$background_video_mp4                         = $this->props['background_video_mp4'];
		$background_video_webm                        = $this->props['background_video_webm'];
		$inner_shadow                                 = $this->props['inner_shadow'];
		$parallax                                     = $this->props['parallax'];
		$parallax_method                              = $this->props['parallax_method'];
		$fullwidth                                    = $this->props['fullwidth'];
		$specialty                                    = $this->props['specialty'];
		$background_color_1                           = $this->props['background_color_1'];
		$background_color_2                           = $this->props['background_color_2'];
		$background_color_3                           = $this->props['background_color_3'];
		$bg_img_1                                     = $this->props['bg_img_1'];
		$bg_img_2                                     = $this->props['bg_img_2'];
		$bg_img_3                                     = $this->props['bg_img_3'];
		$background_size_1                            = $this->props['background_size_1'];
		$background_size_2                            = $this->props['background_size_2'];
		$background_size_3                            = $this->props['background_size_3'];
		$background_position_1                        = $this->props['background_position_1'];
		$background_position_2                        = $this->props['background_position_2'];
		$background_position_3                        = $this->props['background_position_3'];
		$background_repeat_1                          = $this->props['background_repeat_1'];
		$background_repeat_2                          = $this->props['background_repeat_2'];
		$background_repeat_3                          = $this->props['background_repeat_3'];
		$background_blend_1                           = $this->props['background_blend_1'];
		$background_blend_2                           = $this->props['background_blend_2'];
		$background_blend_3                           = $this->props['background_blend_3'];
		$parallax_1                                   = $this->props['parallax_1'];
		$parallax_2                                   = $this->props['parallax_2'];
		$parallax_3                                   = $this->props['parallax_3'];
		$parallax_method_1                            = $this->props['parallax_method_1'];
		$parallax_method_2                            = $this->props['parallax_method_2'];
		$parallax_method_3                            = $this->props['parallax_method_3'];
		$padding_top_1                                = $this->props['padding_top_1'];
		$padding_right_1                              = $this->props['padding_right_1'];
		$padding_bottom_1                             = $this->props['padding_bottom_1'];
		$padding_left_1                               = $this->props['padding_left_1'];
		$padding_top_2                                = $this->props['padding_top_2'];
		$padding_right_2                              = $this->props['padding_right_2'];
		$padding_bottom_2                             = $this->props['padding_bottom_2'];
		$padding_left_2                               = $this->props['padding_left_2'];
		$padding_top_3                                = $this->props['padding_top_3'];
		$padding_right_3                              = $this->props['padding_right_3'];
		$padding_bottom_3                             = $this->props['padding_bottom_3'];
		$padding_left_3                               = $this->props['padding_left_3'];
		$padding_1_tablet                             = $this->props['padding_1_tablet'];
		$padding_2_tablet                             = $this->props['padding_2_tablet'];
		$padding_3_tablet                             = $this->props['padding_3_tablet'];
		$padding_1_phone                              = $this->props['padding_1_phone'];
		$padding_2_phone                              = $this->props['padding_2_phone'];
		$padding_3_phone                              = $this->props['padding_3_phone'];
		$padding_1_last_edited                        = $this->props['padding_1_last_edited'];
		$padding_2_last_edited                        = $this->props['padding_2_last_edited'];
		$padding_3_last_edited                        = $this->props['padding_3_last_edited'];
		$gutter_width                                 = $this->props['gutter_width'];
		$gutter_width_hover                           = $this->get_hover_value( 'gutter_width' );
		$use_custom_width                             = $this->props['use_custom_width'];
		$custom_width_px                              = $this->props['custom_width_px'];
		$custom_width_px_hover                        = $this->get_hover_value( 'custom_width_px' );
		$custom_width_percent                         = $this->props['custom_width_percent'];
		$custom_width_percent_hover                   = $this->get_hover_value( 'custom_width_percent' );
		$width_unit                                   = $this->props['width_unit'];
		$make_equal                                   = $this->props['make_equal'];
		$make_fullwidth                               = $this->props['make_fullwidth'];
		$global_module                                = $this->props['global_module'];
		$use_custom_gutter                            = $this->props['use_custom_gutter'];
		$module_id_1                                  = $this->props['module_id_1'];
		$module_id_2                                  = $this->props['module_id_2'];
		$module_id_3                                  = $this->props['module_id_3'];
		$module_class_1                               = $this->props['module_class_1'];
		$module_class_2                               = $this->props['module_class_2'];
		$module_class_3                               = $this->props['module_class_3'];
		$custom_css_before_1                          = $this->props['custom_css_before_1'];
		$custom_css_before_2                          = $this->props['custom_css_before_2'];
		$custom_css_before_3                          = $this->props['custom_css_before_3'];
		$custom_css_main_1                            = $this->props['custom_css_main_1'];
		$custom_css_main_2                            = $this->props['custom_css_main_2'];
		$custom_css_main_3                            = $this->props['custom_css_main_3'];
		$custom_css_after_1                           = $this->props['custom_css_after_1'];
		$custom_css_after_2                           = $this->props['custom_css_after_2'];
		$custom_css_after_3                           = $this->props['custom_css_after_3'];
		$custom_css_before_1_hover                    = $this->get_hover_value( 'custom_css_before_1' );
		$custom_css_before_2_hover                    = $this->get_hover_value( 'custom_css_before_2' );
		$custom_css_before_3_hover                    = $this->get_hover_value( 'custom_css_before_3' );
		$custom_css_main_1_hover                      = $this->get_hover_value( 'custom_css_main_1' );
		$custom_css_main_2_hover                      = $this->get_hover_value( 'custom_css_main_2' );
		$custom_css_main_3_hover                      = $this->get_hover_value( 'custom_css_main_3' );
		$custom_css_after_1_hover                     = $this->get_hover_value( 'custom_css_after_1' );
		$custom_css_after_2_hover                     = $this->get_hover_value( 'custom_css_after_2' );
		$custom_css_after_3_hover                     = $this->get_hover_value( 'custom_css_after_3' );
		$use_background_color_gradient_1              = $this->props['use_background_color_gradient_1'];
		$use_background_color_gradient_2              = $this->props['use_background_color_gradient_2'];
		$use_background_color_gradient_3              = $this->props['use_background_color_gradient_3'];
		$background_color_gradient_type_1             = $this->props['background_color_gradient_type_1'];
		$background_color_gradient_type_2             = $this->props['background_color_gradient_type_2'];
		$background_color_gradient_type_3             = $this->props['background_color_gradient_type_3'];
		$background_color_gradient_direction_1        = $this->props['background_color_gradient_direction_1'];
		$background_color_gradient_direction_2        = $this->props['background_color_gradient_direction_2'];
		$background_color_gradient_direction_3        = $this->props['background_color_gradient_direction_3'];
		$background_color_gradient_direction_radial_1 = $this->props['background_color_gradient_direction_radial_1'];
		$background_color_gradient_direction_radial_2 = $this->props['background_color_gradient_direction_radial_2'];
		$background_color_gradient_direction_radial_3 = $this->props['background_color_gradient_direction_radial_3'];
		$background_color_gradient_start_1            = $this->props['background_color_gradient_start_1'];
		$background_color_gradient_start_2            = $this->props['background_color_gradient_start_2'];
		$background_color_gradient_start_3            = $this->props['background_color_gradient_start_3'];
		$background_color_gradient_end_1              = $this->props['background_color_gradient_end_1'];
		$background_color_gradient_end_2              = $this->props['background_color_gradient_end_2'];
		$background_color_gradient_end_3              = $this->props['background_color_gradient_end_3'];
		$background_color_gradient_start_position_1   = $this->props['background_color_gradient_start_position_1'];
		$background_color_gradient_start_position_2   = $this->props['background_color_gradient_start_position_2'];
		$background_color_gradient_start_position_3   = $this->props['background_color_gradient_start_position_3'];
		$background_color_gradient_end_position_1     = $this->props['background_color_gradient_end_position_1'];
		$background_color_gradient_end_position_2     = $this->props['background_color_gradient_end_position_2'];
		$background_color_gradient_end_position_3     = $this->props['background_color_gradient_end_position_3'];
		$background_color_gradient_overlays_image_1   = $this->props['background_color_gradient_overlays_image_1'];
		$background_color_gradient_overlays_image_2   = $this->props['background_color_gradient_overlays_image_2'];
		$background_color_gradient_overlays_image_3   = $this->props['background_color_gradient_overlays_image_3'];
		$background_video_mp4_1                       = $this->props['background_video_mp4_1'];
		$background_video_mp4_2                       = $this->props['background_video_mp4_2'];
		$background_video_mp4_3                       = $this->props['background_video_mp4_3'];
		$background_video_webm_1                      = $this->props['background_video_webm_1'];
		$background_video_webm_2                      = $this->props['background_video_webm_2'];
		$background_video_webm_3                      = $this->props['background_video_webm_3'];
		$background_video_width_1                     = $this->props['background_video_width_1'];
		$background_video_width_2                     = $this->props['background_video_width_2'];
		$background_video_width_3                     = $this->props['background_video_width_3'];
		$background_video_height_1                    = $this->props['background_video_height_1'];
		$background_video_height_2                    = $this->props['background_video_height_2'];
		$background_video_height_3                    = $this->props['background_video_height_3'];
		$allow_player_pause_1                         = $this->props['allow_player_pause_1'];
		$allow_player_pause_2                         = $this->props['allow_player_pause_2'];
		$allow_player_pause_3                         = $this->props['allow_player_pause_3'];
		$background_video_pause_outside_viewport_1    = $this->props['background_video_pause_outside_viewport_1'];
		$background_video_pause_outside_viewport_2    = $this->props['background_video_pause_outside_viewport_2'];
		$background_video_pause_outside_viewport_3    = $this->props['background_video_pause_outside_viewport_3'];
		$prev_background_color                        = $this->props['prev_background_color'];
		$next_background_color                        = $this->props['next_background_color'];

		$hover = et_pb_hover_options();

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module, '', $prev_background_color, $next_background_color );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$gutter_class = '';
		$gutter_hover_data = '';

		if ( 'on' === $specialty ) {
			global $et_pb_all_column_settings, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

			$et_pb_all_column_settings_backup = $et_pb_all_column_settings;

			$et_pb_all_column_settings = ! isset( $et_pb_all_column_settings ) ?  array() : $et_pb_all_column_settings;

			if ('on' === $make_equal) {
				$this->add_classname( 'et_pb_equal_columns' );
			}

			if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
				$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter to 1 if 0 entered by user
				$gutter_class .= ' et_pb_gutters' . $gutter_width;

				if ( et_builder_is_hover_enabled( 'gutter_width', $this->props ) ) {
					$gutter_class .= ' et_pb_gutter_hover';

					$gutter_hover_data = sprintf(
						' data-original_gutter="%1$s" data-hover_gutter="%2$s"',
						esc_attr($gutter_width),
						esc_attr($gutter_width_hover)
					);
				}
			}

			// Column hover backgrounds
			$column_hover_backgrounds = array();

			for ( $i = 0; $i <= 3; $i ++ ) {
				$column_hover_backgrounds = array_merge( $column_hover_backgrounds, array(
					"column_{$i}_color_hover"         => $hover->get_value( "background_color_{$i}", $this->props, false ),
					"column_{$i}_color_hover_enabled" => $hover->is_enabled( "background_color_{$i}", $this->props ),
				) );
			}

			$et_pb_columns_counter = 0;
			$et_pb_column_backgrounds = array(
				array(
					'color'               => $background_color_1,
					'color_hover'         => $column_hover_backgrounds['column_1_color_hover'],
					'color_hover_enabled' => $column_hover_backgrounds['column_1_color_hover_enabled'],
					'image'               => $bg_img_1,
					'image_size'          => $background_size_1,
					'image_position'      => $background_position_1,
					'image_repeat'        => $background_repeat_1,
					'image_blend'         => $background_blend_1,
				),
				array(
					'color'               => $background_color_2,
					'color_hover'         => $column_hover_backgrounds['column_2_color_hover'],
					'color_hover_enabled' => $column_hover_backgrounds['column_2_color_hover_enabled'],
					'image'               => $bg_img_2,
					'image_size'          => $background_size_2,
					'image_position'      => $background_position_2,
					'image_repeat'        => $background_repeat_2,
					'image_blend'         => $background_blend_2,
				),
				array(
					'color'               => $background_color_3,
					'color_hover'         => $column_hover_backgrounds['column_3_color_hover'],
					'color_hover_enabled' => $column_hover_backgrounds['column_3_color_hover_enabled'],
					'image'               => $bg_img_3,
					'image_size'          => $background_size_3,
					'image_position'      => $background_position_3,
					'image_repeat'        => $background_repeat_3,
					'image_blend'         => $background_blend_3,
				),
			);

			$et_pb_column_backgrounds_gradient = array(
				array(
					'active'           => $use_background_color_gradient_1,
					'type'             => $background_color_gradient_type_1,
					'direction'        => $background_color_gradient_direction_1,
					'radial_direction' => $background_color_gradient_direction_radial_1,
					'color_start'      => $background_color_gradient_start_1,
					'color_end'        => $background_color_gradient_end_1,
					'start_position'   => $background_color_gradient_start_position_1,
					'end_position'     => $background_color_gradient_end_position_1,
					'overlays_image'   => $background_color_gradient_overlays_image_1,
				),
				array(
					'active'           => $use_background_color_gradient_2,
					'type'             => $background_color_gradient_type_2,
					'direction'        => $background_color_gradient_direction_2,
					'radial_direction' => $background_color_gradient_direction_radial_2,
					'color_start'      => $background_color_gradient_start_2,
					'color_end'        => $background_color_gradient_end_2,
					'start_position'   => $background_color_gradient_start_position_2,
					'end_position'     => $background_color_gradient_end_position_2,
					'overlays_image'   => $background_color_gradient_overlays_image_2,
				),
				array(
					'active'           => $use_background_color_gradient_3,
					'type'             => $background_color_gradient_type_3,
					'direction'        => $background_color_gradient_direction_3,
					'radial_direction' => $background_color_gradient_direction_radial_3,
					'color_start'      => $background_color_gradient_start_3,
					'color_end'        => $background_color_gradient_end_3,
					'start_position'   => $background_color_gradient_start_position_3,
					'end_position'     => $background_color_gradient_end_position_3,
					'overlays_image'   => $background_color_gradient_overlays_image_3,
				),
			);

			$et_pb_column_backgrounds_video = array(
				array(
					'background_video_mp4'         => $background_video_mp4_1,
					'background_video_webm'        => $background_video_webm_1,
					'background_video_width'       => $background_video_width_1,
					'background_video_height'      => $background_video_height_1,
					'background_video_allow_pause' => $allow_player_pause_1,
					'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_1,
				),
				array(
					'background_video_mp4'         => $background_video_mp4_2,
					'background_video_webm'        => $background_video_webm_2,
					'background_video_width'       => $background_video_width_2,
					'background_video_height'      => $background_video_height_2,
					'background_video_allow_pause' => $allow_player_pause_2,
					'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_2,
				),
				array(
					'background_video_mp4'         => $background_video_mp4_3,
					'background_video_webm'        => $background_video_webm_3,
					'background_video_width'       => $background_video_width_3,
					'background_video_height'      => $background_video_height_3,
					'background_video_allow_pause' => $allow_player_pause_3,
					'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_3,
				),
			);

			// Column hover paddings
			$column_hover_paddings = array();

			for ( $i = 0; $i <= 3; $i++ ) {
				$column_hover_paddings = array_merge( $column_hover_paddings, array(
					"column_{$i}_padding_hover_enabled" => $hover->is_enabled( "padding_{$i}", $this->props ),
					"column_{$i}_padding_top"           => $hover->get_compose_value( "padding_top_{$i}", "padding_{$i}", $this->props ),
					"column_{$i}_padding_right"         => $hover->get_compose_value( "padding_right_{$i}", "padding_{$i}", $this->props ),
					"column_{$i}_padding_bottom"        => $hover->get_compose_value( "padding_bottom_{$i}", "padding_{$i}", $this->props ),
					"column_{$i}_padding_left"          => $hover->get_compose_value( "padding_left_{$i}", "padding_{$i}", $this->props ),
				) );
			}

			$et_pb_column_paddings = array(
				array(
					'padding-top'           => $padding_top_1,
					'padding-right'         => $padding_right_1,
					'padding-bottom'        => $padding_bottom_1,
					'padding-left'          => $padding_left_1,
					'padding-hover-enabled' => $column_hover_paddings['column_1_padding_hover_enabled'],
					'padding-top-hover'     => $column_hover_paddings['column_1_padding_top'],
					'padding-right-hover'   => $column_hover_paddings['column_1_padding_right'],
					'padding-bottom-hover'  => $column_hover_paddings['column_1_padding_bottom'],
					'padding-left-hover'    => $column_hover_paddings['column_1_padding_left'],
				),
				array(
					'padding-top'           => $padding_top_2,
					'padding-right'         => $padding_right_2,
					'padding-bottom'        => $padding_bottom_2,
					'padding-left'          => $padding_left_2,
					'padding-hover-enabled' => $column_hover_paddings['column_2_padding_hover_enabled'],
					'padding-top-hover'     => $column_hover_paddings['column_2_padding_top'],
					'padding-right-hover'   => $column_hover_paddings['column_2_padding_right'],
					'padding-bottom-hover'  => $column_hover_paddings['column_2_padding_bottom'],
					'padding-left-hover'    => $column_hover_paddings['column_2_padding_left'],
				),
				array(
					'padding-top'           => $padding_top_3,
					'padding-right'         => $padding_right_3,
					'padding-bottom'        => $padding_bottom_3,
					'padding-left'          => $padding_left_3,
					'padding-hover-enabled' => $column_hover_paddings['column_3_padding_hover_enabled'],
					'padding-top-hover'     => $column_hover_paddings['column_3_padding_top'],
					'padding-right-hover'   => $column_hover_paddings['column_3_padding_right'],
					'padding-bottom-hover'  => $column_hover_paddings['column_3_padding_bottom'],
					'padding-left-hover'    => $column_hover_paddings['column_3_padding_left'],
				),
			);

			$et_pb_column_paddings_mobile = array(
				array(
					'tablet' => explode( '|', $padding_1_tablet ),
					'phone'  => explode( '|', $padding_1_phone ),
					'last_edited' => $padding_1_last_edited,
				),
				array(
					'tablet' => explode( '|', $padding_2_tablet ),
					'phone'  => explode( '|', $padding_2_phone ),
					'last_edited' => $padding_2_last_edited,
				),
				array(
					'tablet' => explode( '|', $padding_3_tablet ),
					'phone'  => explode( '|', $padding_3_phone ),
					'last_edited' => $padding_3_last_edited,
				),
			);

			$et_pb_column_parallax = array(
				array( $parallax_1, $parallax_method_1 ),
				array( $parallax_2, $parallax_method_2 ),
				array( $parallax_3, $parallax_method_3 ),
			);

			if ( 'on' === $make_fullwidth && 'off' === $use_custom_width ) {
				$this->add_classname('et_pb_specialty_fullwidth');
			}

			if ( 'on' === $use_custom_width ) {
				// Override the fullwidth post row width styles so rows do not have different
				// widths on posts compared to other post types.
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% > .et_pb_row, .et_pb_pagebuilder_layout.single.et_full_width_page #page-container %%order_class%% .et_pb_row',
					'declaration' => sprintf(
						'max-width:%1$s !important;
						%2$s',
						'on' === $width_unit ? esc_attr( sprintf( '%1$spx', intval( $custom_width_px ) ) ) : esc_attr( sprintf( '%1$s%%', intval( $custom_width_percent ) ) ),
						'on' !== $width_unit ? esc_attr( sprintf( 'width: %1$s%%;', intval( $custom_width_percent ) ) ) : ''
					),
				) );

				switch ( $width_unit ) {
					case 'on':
						if ( ! et_builder_is_hover_enabled( 'custom_width_px', $this->props ) ) {
							break;
						}

						ET_Builder_Element::set_style( $function_name, array(
							'selector'    => '%%order_class%% > .et_pb_row:hover',
							'declaration' => sprintf(
								'max-width:%1$s !important;',
								esc_attr( sprintf( '%1$spx', intval( $custom_width_px_hover ) ) )
							),
						) );

						break;
					default:
						if ( ! et_builder_is_hover_enabled( 'custom_width_percent', $this->props ) ) {
							break;
						}

						ET_Builder_Element::set_style( $function_name, array(
							'selector'    => '%%order_class%% > .et_pb_row:hover',
							'declaration' => sprintf(
								'max-width:%1$s !important;
							%2$s',
								esc_attr( sprintf( '%1$s%%', intval( $custom_width_percent_hover ) ) ),
								esc_attr( sprintf( 'width: %1$s%%;', intval( $custom_width_percent_hover ) ) )
							),
						) );

						break;
				}
			}

			$et_pb_column_css = array(
				'css_class'               => array( $module_class_1, $module_class_2, $module_class_3 ),
				'css_id'                  => array( $module_id_1, $module_id_2, $module_id_3 ),
				'custom_css_before'       => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3 ),
				'custom_css_main'         => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3 ),
				'custom_css_after'        => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3 ),
				'custom_css_before_hover' => array( $custom_css_before_1_hover, $custom_css_before_2_hover, $custom_css_before_3_hover ),
				'custom_css_main_hover'   => array( $custom_css_main_1_hover, $custom_css_main_2_hover, $custom_css_main_3_hover ),
				'custom_css_after_hover'  => array( $custom_css_after_1_hover, $custom_css_after_2_hover, $custom_css_after_3_hover ),
			);

			$internal_columns_settings_array = array(
				'keep_column_padding_mobile' => 'on',
				'et_pb_column_backgrounds' => $et_pb_column_backgrounds,
				'et_pb_column_backgrounds_gradient' => $et_pb_column_backgrounds_gradient,
				'et_pb_column_backgrounds_video' => $et_pb_column_backgrounds_video,
				'et_pb_column_parallax' => $et_pb_column_parallax,
				'et_pb_columns_counter' => $et_pb_columns_counter,
				'et_pb_column_paddings' => $et_pb_column_paddings,
				'et_pb_column_paddings_mobile' => $et_pb_column_paddings_mobile,
				'et_pb_column_css' => $et_pb_column_css,
			);

			$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

			$et_pb_all_column_settings[ $current_row_position ] = $internal_columns_settings_array;

			if ( $et_pb_rendering_column_content ) {
				$et_pb_rendering_column_content_row = true;
			}
		}

		$background_video = '';

		if ( '' !== $background_video_mp4 || '' !== $background_video_webm ) {
			$background_video = $this->video_background();
		}

		if ( '' !== $background_color && 'rgba(255,255,255,0)' !== $background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_section',
				'declaration' => sprintf(
					'background-color:%s !important;',
					esc_attr( $background_color )
				),
			) );
		}

		// Background hover styles
		$bg_color = $hover->get_value( 'background_color', $this->props );
		if ( $hover->is_enabled( 'background', $this->props ) && !empty( $bg_color ) ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_section:hover',
				'declaration' => sprintf(
					'background-color:%s !important;',
					esc_attr( $bg_color )
				),
			) );
		}

		// Transparent is default for Builder Plugin, but not for theme
		$page_setting_section_background = et_builder_settings_get( 'et_pb_section_background_color', get_the_ID() );
		$is_transparent_background = 'rgba(255,255,255,0)' === $background_color || ( et_is_builder_plugin_active() && '' === $background_color && '' === $page_setting_section_background );

		if ( '' !== $background_video_mp4 || '' !== $background_video_webm || ( '' !== $background_color && ! $is_transparent_background ) || '' !== $background_image ) {
			$this->add_classname( 'et_pb_with_background' );
		}

		// Background UI
		if ( 'on' === $parallax ) {
			$this->add_classname( 'et_pb_section_parallax' );
		}

		// CSS Filters
		$this->add_classname( $this->generate_css_filters( $function_name ) );

		if ( 'on' === $inner_shadow && ! ( '' !== $background_image && 'on' === $parallax && 'off' === $parallax_method ) ) {
			$this->add_classname( 'et_pb_inner_shadow' );
		}

		if ( 'on' === $fullwidth ) {
			$this->add_classname( 'et_pb_fullwidth_section' );
		}

		if ( 'on' === $specialty ) {
			$this->add_classname( 'et_section_specialty' );
		} else {
			$this->add_classname( 'et_section_regular' );
		}

		if ( $is_transparent_background ) {
			$this->add_classname( 'et_section_transparent' );
		}

		// Setup for SVG.
		$bottom  = '';
		$top     = '';
		$divider = ET_Builder_Module_Fields_Factory::get( 'Divider' );
		// pass section number for background color usage.
		$divider->count = $this->render_count();

		// Check if style is not default.
		if ( '' !== $this->props['bottom_divider_style'] ) {
			// get an svg for using in ::before
			$divider->process_svg( 'bottom', $this->props );

			// apply responsive styling
			$bottom_divider_responsive = et_pb_get_responsive_status( $this->props['bottom_divider_height_last_edited'] ) || et_pb_get_responsive_status( $this->props['bottom_divider_repeat_last_edited'] );

			if ( $bottom_divider_responsive ) {
				$divider->process_svg( 'bottom', $this->props, 'tablet' );
				$divider->process_svg( 'bottom', $this->props, 'phone' );
			}

			// get the placeholder for the bottom
			$bottom = $divider->get_svg( 'bottom' );

			// add a corresponding class
			$this->add_classname( $divider->classes );
		}

		// Check if style is not default.
		if ( '' !== $this->props['top_divider_style'] ) {
			// process the top section divider.
			$divider->process_svg( 'top', $this->props );

			// apply responsive styling
			$top_divider_responsive = et_pb_get_responsive_status( $this->props['top_divider_height_last_edited'] ) || et_pb_get_responsive_status( $this->props['top_divider_repeat_last_edited'] );

			if ( $top_divider_responsive ) {
				$divider->process_svg( 'top', $this->props, 'tablet' );
				$divider->process_svg( 'top', $this->props, 'phone' );
			}

			// get the placeholder for the top
			$top = $divider->get_svg( 'top' );

			// add a corresponding class
			$this->add_classname( $divider->classes );
		}

		// Remove automatically added classnames
		$this->remove_classname( 'et_pb_module' );

		// Save module classes into variable BEFORE processing the content with `do_shortcode()`
		// Otherwise order classes messed up with internal sections if exist
		$module_classes = $this->module_classname( $function_name );

		$output = sprintf(
			'<div%4$s class="%3$s"%8$s>
				%9$s
				%7$s
				%2$s
				%5$s
					%1$s
				%6$s
				%10$s
			</div> <!-- .et_pb_section -->',
			do_shortcode( et_pb_fix_shortcodes( $content ) ), // 1
			$background_video, // 2
			$module_classes, // 3
			$this->module_id(), // 4
			( 'on' === $specialty ?
				sprintf( '<div class="et_pb_row%1$s"%2$s>', $gutter_class, et_core_esc_previously( $gutter_hover_data ) )
				: '' ), // 5
			( 'on' === $specialty ? '</div> <!-- .et_pb_row -->' : '' ), // 6
			( '' !== $background_image && 'on' === $parallax
				? sprintf(
					'%4$s<div class="et_parallax_bg%2$s%3$s" style="background-image: url(%1$s);"></div>%5$s',
					esc_attr( $background_image ),
					( 'off' === $parallax_method ? ' et_pb_parallax_css' : '' ),
					( ( 'off' !== $inner_shadow && 'off' === $parallax_method ) ? ' et_pb_inner_shadow' : '' ),
					!et_core_is_fb_enabled() ? '' : '<div class="et_parallax_bg_wrap">',
					!et_core_is_fb_enabled() ? '' : '</div>'
				)
				: ''
			), // 7
			$this->get_module_data_attributes(), // 8
			et_core_esc_previously( $top ), // 9
			et_core_esc_previously( $bottom ) // 10
		);

		if ( 'on' === $specialty ) {
			// reset the global column settings to make sure they are not affected by internal content
			$et_pb_all_column_settings = $et_pb_all_column_settings_backup;

			if ( $et_pb_rendering_column_content_row ) {
				$et_pb_rendering_column_content_row = false;
			}
		}

		return $output;

	}

	public function process_box_shadow( $function_name ) {
		/**
		 * @var ET_Builder_Module_Field_BoxShadow $boxShadow
		 */
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$style = $boxShadow->get_value( $this->props );
		$hover_style = $boxShadow->get_value( $this->props, array( 'hover' => true ) );

		if ( ! empty( $style ) && 'none' !== $style && false === strpos( $style, 'inset' ) ) {
			// Make section z-index higher if it has outer box shadow #4762
			self::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => 'z-index: 10;'
			) );
		}

		if ( ! empty( $hover_style ) && 'none' !== $hover_style && false === strpos( $hover_style, 'inset' ) ) {
			// Make section z-index higher if it has outer box shadow #4762
			self::set_style( $function_name, array(
				'selector'    => '%%order_class%%:hover',
				'declaration' => 'z-index: 10;'
			) );
		}

		parent::process_box_shadow( $function_name );
	}

	private function _keep_box_shadow_compatibility( $function_name ) {
		/**
		 * @var ET_Builder_Module_Field_BoxShadow $box_shadow
		 */
		$box_shadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$utils      = ET_Core_Data_Utils::instance();
		$atts       = $this->props;
		$style      = $box_shadow->get_value( $atts );

		if (
			! empty( $style )
			&&
			! is_admin()
			&&
			version_compare( $utils->array_get( $atts, '_builder_version', '3.0.93' ), '3.0.94', 'lt' )
			&&
			! $box_shadow->is_inset( $box_shadow->get_value( $atts ) )
		) {
			$class = '.' . self::get_module_order_class( $function_name );

			return sprintf(
				'<style type="text/css">%1$s</style>',
				sprintf( '%1$s { z-index: 11; %2$s }', esc_html( $class ), esc_html( $style ) )
			);
		}

		return '';
	}
}
new ET_Builder_Section;

class ET_Builder_Row extends ET_Builder_Structure_Element {
	function init() {
		$this->name       = esc_html__( 'Row', 'et_builder' );
		$this->plural     = esc_html__( 'Rows', 'et_builder' );
		$this->slug       = 'et_pb_row';
		$this->vb_support = 'on';

		$this->advanced_fields = array(
			'background'            => array(
				'use_background_color' => true,
				'use_background_image' => true,
				'use_background_color_gradient' => true,
				'use_background_video' => true,
				'options' => array(
					'background_color' => array(
						'default' => '',
						'hover' => 'tabs',
					),
					'allow_player_pause' => array(
						'default_on_front' => 'off',
					),
					'parallax' => array(
						'default_on_front' => 'off',
					),
					'parallax_method' => array(
						'default_on_front' => 'on',
					),
				),
			),
			'max_width'             => array(
				'use_max_width' => false,
				'css'           => array(
					'module_alignment' => '%%order_class%%.et_pb_row',
				),
				'options' => array(
					'module_alignment' => array(
						'label' => esc_html__( 'Row Alignment', 'et_builder' ),
					),
				),
				'toggle_slug'     => 'alignment',
				'toggle_title'    => esc_html__( 'Alignment', 'et_builder' ),
				'toggle_priority' => 50,
			),
			'margin_padding' => array(
				'use_padding'       => false,
				'custom_margin'     => array(
					'priority' => 1,
				),
				'css' => array(
					'main' => '%%order_class%%.et_pb_row',
					'important' => 'all',
				),
			),
			'fonts'                 => false,
			'text'                  => false,
			'button'                => false,
		);

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'background'     => array(
						'title'       => esc_html__( 'Background', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
							'column_4' => array( 'name' => esc_html__( 'Column 4', 'et_builder' ) ),
							'column_5' => array( 'name' => esc_html__( 'Column 5', 'et_builder' ) ),
							'column_6' => array( 'name' => esc_html__( 'Column 6', 'et_builder' ) ),
						),
						'priority' => 80,
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'width'          => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
					'margin_padding' => array(
						'title'       => esc_html__( 'Spacing', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
							'column_4' => array( 'name' => esc_html__( 'Column 4', 'et_builder' ) ),
							'column_5' => array( 'name' => esc_html__( 'Column 5', 'et_builder' ) ),
							'column_6' => array( 'name' => esc_html__( 'Column 6', 'et_builder' ) ),
						),
						'priority' => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'classes' => array(
						'title'  => esc_html__( 'CSS ID & Classes', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
							'column_4' => array( 'name' => esc_html__( 'Column 4', 'et_builder' ) ),
							'column_5' => array( 'name' => esc_html__( 'Column 5', 'et_builder' ) ),
							'column_6' => array( 'name' => esc_html__( 'Column 6', 'et_builder' ) ),
						),
					),
					'custom_css' => array(
						'title'  => esc_html__( 'Custom CSS', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
							'column_4' => array( 'name' => esc_html__( 'Column 4', 'et_builder' ) ),
							'column_5' => array( 'name' => esc_html__( 'Column 5', 'et_builder' ) ),
							'column_6' => array( 'name' => esc_html__( 'Column 6', 'et_builder' ) ),
						),
					),
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'R9ds7bEaHE8' ),
				'name' => esc_html__( 'An introduction to Rows', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'make_fullwidth' => array(
				'label'             => esc_html__( 'Make This Row Fullwidth', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'depends_show_if'   => 'off',
				'description'       => esc_html__( 'Enable this option to extend the width of this row to the edge of the browser window.', 'et_builder' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'use_custom_width' => array(
				'label'             => esc_html__( 'Use Custom Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'make_fullwidth',
					'custom_width',
					'width_unit',
				),
				'description'       => esc_html__( 'Change to Yes if you would like to adjust the width of this row to a non-standard width.', 'et_builder' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'width_unit' => array(
				'label'             => esc_html__( 'Unit', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'px', 'et_builder' ),
					'off' => '%',
				),
				'default'           => 'on',
				'button_options'    => array(
					'button_type' => 'equal',
				),
				'depends_show_if'   => 'on',
				'affects'           => array(
					'custom_width_px',
					'custom_width_percent',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'custom_width_px' => array(
				'default'             => '1080px',
				'label'               => esc_html__( 'Custom Width', 'et_builder' ),
				'type'                => 'range',
				'option_category'     => 'layout',
				'depends_show_if_not' => 'off',
				'validate_unit'       => true,
				'fixed_unit'          => 'px',
				'range_settings'      => array(
					'min'  => 500,
					'max'  => 2600,
					'step' => 1,
				),
				'description'         => esc_html__( 'Define custom width for this Row', 'et_builder' ),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'width',
				'hover'               => 'tabs',
			),
			'custom_width_percent' => array(
				'default'         => '80%',
				'label'           => esc_html__( 'Custom Width', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'depends_show_if' => 'off',
				'validate_unit'   => true,
				'fixed_unit'      => '%',
				'range_settings'  => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'description'     => esc_html__( 'Define custom width for this Row', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'hover'           => 'tabs',
			),
			'use_custom_gutter' => array(
				'label'             => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'gutter_width',
				),
				'description'       => esc_html__( 'Enable this option to define custom gutter width for this row.', 'et_builder' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'gutter_width' => array(
				'label'            => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'       => 1,
					'max'       => 4,
					'step'      => 1,
					'min_limit' => 1,
					'max_limit' => 4,
				),
				'depends_show_if'  => 'on',
				'description'      => esc_html__( 'Adjust the spacing between each column in this row.', 'et_builder' ),
				'validate_unit'    => false,
				'fixed_range'      => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'default_on_front' => et_get_option( 'gutter_width', 3 ),
				'hover'            => 'tabs',
			),
			'custom_padding' => array(
				'label'           => esc_html__( 'Custom Padding', 'et_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
				'hover'           => 'tabs',
			),
			'custom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
				'default_on_front' => '',
			),
			'custom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
				'default_on_front' => '',
			),
			'padding_mobile' => array(
				'label' => esc_html__( 'Keep Custom Padding on Mobile', 'et_builder' ),
				'type'        => 'skip', // Remaining attribute for backward compatibility
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
				'default_on_front' => '',
			),
			'custom_margin' => array(
				'label'           => esc_html__( 'Custom Margin', 'et_builder' ),
				'type'            => 'custom_margin',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'hover'           => 'tabs',
				'toggle_slug'     => 'margin_padding',
			),
			'make_equal' => array(
				'label'             => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'columns_background' => array(
				'type'            => 'column_settings_background',
				'option_category' => 'configuration',
				'toggle_slug'     => 'background',
				'priority'        => 99,
			),
			'columns_padding' => array(
				'type'            => 'column_settings_padding',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
				'priority'        => 99,
			),
			'column_padding_mobile' => array(
				'label'            => esc_html__( 'Keep Column Padding on Mobile', 'et_builder' ),
				'tab_slug'         => 'advanced',
				'type'             => 'skip', // Remaining attribute for backward compatibility
				'default_on_front' => '',
			),
			'columns_css' => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'custom_css',
				'priority'        => 20,
			),
			'columns_css_fields' => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'priority'        => 20,
			),
			'custom_padding_last_edited' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'__video_background' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Row', 'get_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum' => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
		);

		$column_fields = $this->get_column_fields( 6, array(
			'background_color'                           => array(),
			'bg_img'                                     => array(),
			'padding_top_bottom_link'                    => array(),
			'padding_left_right_link'                    => array(),
			'parallax'                                   => array(
				'default_on_front' => 'off',
			),
			'parallax_method'                            => array(
				'default_on_front' => 'on',
			),
			'background_size'                            => array(),
			'background_position'                        => array(),
			'background_repeat'                          => array(),
			'background_blend'                           => array(),
			'use_background_color_gradient'              => array(),
			'background_color_gradient_start'            => array(),
			'background_color_gradient_end'              => array(),
			'background_color_gradient_type'             => array(),
			'background_color_gradient_direction'        => array(),
			'background_color_gradient_direction_radial' => array(),
			'background_color_gradient_start_position'   => array(),
			'background_color_gradient_end_position'     => array(),
			'background_color_gradient_overlays_image'   => array(),
			'background_video_mp4'                       => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'background_video_webm'                      => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'background_video_width'                     => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'background_video_height'                    => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'allow_player_pause'                         => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'background_video_pause_outside_viewport'    => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'__video_background'                         => array(
				'type' => 'computed',
				'computed_callback' => array(
					'ET_Builder_Column',
					'get_column_video_background'
				),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum' => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
			'padding_top'                                => array( 'tab_slug' => 'advanced' ),
			'padding_right'                              => array( 'tab_slug' => 'advanced' ),
			'padding_bottom'                             => array( 'tab_slug' => 'advanced' ),
			'padding_left'                               => array( 'tab_slug' => 'advanced' ),
			'padding_top_bottom_link'                    => array( 'tab_slug' => 'advanced' ),
			'padding_left_right_link'                    => array( 'tab_slug' => 'advanced' ),
			'padding_%column_index%_tablet'              => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
			),
			'padding_%column_index%_phone'               => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
			),
			'padding_%column_index%_last_edited'         => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
			),
			'module_id'                                  => array( 'tab_slug' => 'custom_css' ),
			'module_class'                               => array( 'tab_slug' => 'custom_css' ),
			'custom_css_before'                          => array( 'tab_slug' => 'custom_css' ),
			'custom_css_main'                            => array( 'tab_slug' => 'custom_css' ),
			'custom_css_after'                           => array( 'tab_slug' => 'custom_css' ),
		) );

		return array_merge( $fields, $column_fields );
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['custom_width_px'] = array( 'max-width' => '%%order_class%%' );
		$fields['custom_width_percent'] = array(
			'max-width' => '%%order_class%%',
			'width'     => '%%order_class%%',
		);

		for ( $i = 1; $i <= 6; $i ++ ) {
			$selector = "%%order_class%% > .et_pb_column:nth-child({$i})";
			$fields["background_color_{$i}"] = array( 'background-color' => $selector );
			$fields["padding_{$i}"] = array( 'padding' => $selector );
		}

		return $fields;
	}

	function render( $atts, $content = null, $function_name ) {
		$custom_padding                               = $this->props['custom_padding'];
		$custom_padding_tablet                        = $this->props['custom_padding_tablet'];
		$custom_padding_phone                         = $this->props['custom_padding_phone'];
		$custom_padding_last_edited                   = $this->props['custom_padding_last_edited'];
		$column_padding_mobile                        = $this->props['column_padding_mobile'];
		$make_fullwidth                               = $this->props['make_fullwidth'];
		$make_equal                                   = $this->props['make_equal'];
		$background_color_1                           = $this->props['background_color_1'];
		$background_color_2                           = $this->props['background_color_2'];
		$background_color_3                           = $this->props['background_color_3'];
		$background_color_4                           = $this->props['background_color_4'];
		$background_color_5                           = $this->props['background_color_5'];
		$background_color_6                           = $this->props['background_color_6'];
		$bg_img_1                                     = $this->props['bg_img_1'];
		$bg_img_2                                     = $this->props['bg_img_2'];
		$bg_img_3                                     = $this->props['bg_img_3'];
		$bg_img_4                                     = $this->props['bg_img_4'];
		$bg_img_5                                     = $this->props['bg_img_5'];
		$bg_img_6                                     = $this->props['bg_img_6'];
		$background_size_1                            = $this->props['background_size_1'];
		$background_size_2                            = $this->props['background_size_2'];
		$background_size_3                            = $this->props['background_size_3'];
		$background_size_4                            = $this->props['background_size_4'];
		$background_size_5                            = $this->props['background_size_5'];
		$background_size_6                            = $this->props['background_size_6'];
		$background_position_1                        = $this->props['background_position_1'];
		$background_position_2                        = $this->props['background_position_2'];
		$background_position_3                        = $this->props['background_position_3'];
		$background_position_4                        = $this->props['background_position_4'];
		$background_position_5                        = $this->props['background_position_5'];
		$background_position_6                        = $this->props['background_position_6'];
		$background_repeat_1                          = $this->props['background_repeat_1'];
		$background_repeat_2                          = $this->props['background_repeat_2'];
		$background_repeat_3                          = $this->props['background_repeat_3'];
		$background_repeat_4                          = $this->props['background_repeat_4'];
		$background_repeat_5                          = $this->props['background_repeat_5'];
		$background_repeat_6                          = $this->props['background_repeat_6'];
		$background_blend_1                           = $this->props['background_blend_1'];
		$background_blend_2                           = $this->props['background_blend_2'];
		$background_blend_3                           = $this->props['background_blend_3'];
		$background_blend_4                           = $this->props['background_blend_4'];
		$background_blend_5                           = $this->props['background_blend_5'];
		$background_blend_6                           = $this->props['background_blend_6'];
		$padding_top_1                                = $this->props['padding_top_1'];
		$padding_right_1                              = $this->props['padding_right_1'];
		$padding_bottom_1                             = $this->props['padding_bottom_1'];
		$padding_left_1                               = $this->props['padding_left_1'];
		$padding_top_2                                = $this->props['padding_top_2'];
		$padding_right_2                              = $this->props['padding_right_2'];
		$padding_bottom_2                             = $this->props['padding_bottom_2'];
		$padding_left_2                               = $this->props['padding_left_2'];
		$padding_top_3                                = $this->props['padding_top_3'];
		$padding_right_3                              = $this->props['padding_right_3'];
		$padding_bottom_3                             = $this->props['padding_bottom_3'];
		$padding_left_3                               = $this->props['padding_left_3'];
		$padding_top_4                                = $this->props['padding_top_4'];
		$padding_right_4                              = $this->props['padding_right_4'];
		$padding_bottom_4                             = $this->props['padding_bottom_4'];
		$padding_left_4                               = $this->props['padding_left_4'];
		$padding_top_5                                = $this->props['padding_top_5'];
		$padding_right_5                              = $this->props['padding_right_5'];
		$padding_bottom_5                             = $this->props['padding_bottom_5'];
		$padding_left_5                               = $this->props['padding_left_5'];
		$padding_top_6                                = $this->props['padding_top_6'];
		$padding_right_6                              = $this->props['padding_right_6'];
		$padding_bottom_6                             = $this->props['padding_bottom_6'];
		$padding_left_6                               = $this->props['padding_left_6'];
		$padding_1_tablet                             = $this->props['padding_1_tablet'];
		$padding_2_tablet                             = $this->props['padding_2_tablet'];
		$padding_3_tablet                             = $this->props['padding_3_tablet'];
		$padding_4_tablet                             = $this->props['padding_4_tablet'];
		$padding_5_tablet                             = $this->props['padding_5_tablet'];
		$padding_6_tablet                             = $this->props['padding_6_tablet'];
		$padding_1_phone                              = $this->props['padding_1_phone'];
		$padding_2_phone                              = $this->props['padding_2_phone'];
		$padding_3_phone                              = $this->props['padding_3_phone'];
		$padding_4_phone                              = $this->props['padding_4_phone'];
		$padding_5_phone                              = $this->props['padding_5_phone'];
		$padding_6_phone                              = $this->props['padding_6_phone'];
		$padding_1_last_edited                        = $this->props['padding_1_last_edited'];
		$padding_2_last_edited                        = $this->props['padding_2_last_edited'];
		$padding_3_last_edited                        = $this->props['padding_3_last_edited'];
		$padding_4_last_edited                        = $this->props['padding_4_last_edited'];
		$padding_5_last_edited                        = $this->props['padding_5_last_edited'];
		$padding_6_last_edited                        = $this->props['padding_6_last_edited'];
		$padding_mobile                               = $this->props['padding_mobile'];
		$gutter_width                                 = $this->props['gutter_width'];
		$gutter_width_hover                           = $this->get_hover_value( 'gutter_width' );
		$use_custom_width                             = $this->props['use_custom_width'];
		$custom_width_px                              = $this->props['custom_width_px'];
		$custom_width_px_hover                        = et_pb_hover_options()->get_value( 'custom_width_px', $this->props, $custom_width_px );
		$custom_width_percent                         = $this->props['custom_width_percent'];
		$custom_width_percent_hover                   = et_pb_hover_options()->get_value( 'custom_width_percent', $this->props, $custom_width_percent );
		$width_unit                                   = $this->props['width_unit'];
		$global_module                                = $this->props['global_module'];
		$use_custom_gutter                            = $this->props['use_custom_gutter'];
		$parallax_1                                   = $this->props['parallax_1'];
		$parallax_method_1                            = $this->props['parallax_method_1'];
		$parallax_2                                   = $this->props['parallax_2'];
		$parallax_method_2                            = $this->props['parallax_method_2'];
		$parallax_3                                   = $this->props['parallax_3'];
		$parallax_method_3                            = $this->props['parallax_method_3'];
		$parallax_4                                   = $this->props['parallax_4'];
		$parallax_5                                   = $this->props['parallax_5'];
		$parallax_6                                   = $this->props['parallax_6'];
		$parallax_method_4                            = $this->props['parallax_method_4'];
		$parallax_method_5                            = $this->props['parallax_method_5'];
		$parallax_method_6                            = $this->props['parallax_method_6'];
		$module_id_1                                  = $this->props['module_id_1'];
		$module_id_2                                  = $this->props['module_id_2'];
		$module_id_3                                  = $this->props['module_id_3'];
		$module_id_4                                  = $this->props['module_id_4'];
		$module_id_5                                  = $this->props['module_id_5'];
		$module_id_6                                  = $this->props['module_id_6'];
		$module_class_1                               = $this->props['module_class_1'];
		$module_class_2                               = $this->props['module_class_2'];
		$module_class_3                               = $this->props['module_class_3'];
		$module_class_4                               = $this->props['module_class_4'];
		$module_class_5                               = $this->props['module_class_5'];
		$module_class_6                               = $this->props['module_class_6'];
		$custom_css_before_1                          = $this->props['custom_css_before_1'];
		$custom_css_before_2                          = $this->props['custom_css_before_2'];
		$custom_css_before_3                          = $this->props['custom_css_before_3'];
		$custom_css_before_4                          = $this->props['custom_css_before_4'];
		$custom_css_before_5                          = $this->props['custom_css_before_5'];
		$custom_css_before_6                          = $this->props['custom_css_before_6'];
		$custom_css_main_1                            = $this->props['custom_css_main_1'];
		$custom_css_main_2                            = $this->props['custom_css_main_2'];
		$custom_css_main_3                            = $this->props['custom_css_main_3'];
		$custom_css_main_4                            = $this->props['custom_css_main_4'];
		$custom_css_main_5                            = $this->props['custom_css_main_5'];
		$custom_css_main_6                            = $this->props['custom_css_main_6'];
		$custom_css_after_1                           = $this->props['custom_css_after_1'];
		$custom_css_after_2                           = $this->props['custom_css_after_2'];
		$custom_css_after_3                           = $this->props['custom_css_after_3'];
		$custom_css_after_4                           = $this->props['custom_css_after_4'];
		$custom_css_after_5                           = $this->props['custom_css_after_5'];
		$custom_css_after_6                           = $this->props['custom_css_after_6'];
		$custom_css_before_1_hover                    = $this->get_hover_value( 'custom_css_before_1' );
		$custom_css_before_2_hover                    = $this->get_hover_value( 'custom_css_before_2' );
		$custom_css_before_3_hover                    = $this->get_hover_value( 'custom_css_before_3' );
		$custom_css_before_4_hover                    = $this->get_hover_value( 'custom_css_before_4' );
		$custom_css_before_5_hover                    = $this->get_hover_value( 'custom_css_before_5' );
		$custom_css_before_6_hover                    = $this->get_hover_value( 'custom_css_before_6' );
		$custom_css_main_1_hover                      = $this->get_hover_value( 'custom_css_main_1' );
		$custom_css_main_2_hover                      = $this->get_hover_value( 'custom_css_main_2' );
		$custom_css_main_3_hover                      = $this->get_hover_value( 'custom_css_main_3' );
		$custom_css_main_4_hover                      = $this->get_hover_value( 'custom_css_main_4' );
		$custom_css_main_5_hover                      = $this->get_hover_value( 'custom_css_main_5' );
		$custom_css_main_6_hover                      = $this->get_hover_value( 'custom_css_main_6' );
		$custom_css_after_1_hover                     = $this->get_hover_value( 'custom_css_after_1' );
		$custom_css_after_2_hover                     = $this->get_hover_value( 'custom_css_after_2' );
		$custom_css_after_3_hover                     = $this->get_hover_value( 'custom_css_after_3' );
		$custom_css_after_4_hover                     = $this->get_hover_value( 'custom_css_after_4' );
		$custom_css_after_5_hover                     = $this->get_hover_value( 'custom_css_after_5' );
		$custom_css_after_6_hover                     = $this->get_hover_value( 'custom_css_after_6' );
		$use_background_color_gradient_1              = $this->props['use_background_color_gradient_1'];
		$use_background_color_gradient_2              = $this->props['use_background_color_gradient_2'];
		$use_background_color_gradient_3              = $this->props['use_background_color_gradient_3'];
		$use_background_color_gradient_4              = $this->props['use_background_color_gradient_4'];
		$use_background_color_gradient_5              = $this->props['use_background_color_gradient_5'];
		$use_background_color_gradient_6              = $this->props['use_background_color_gradient_6'];
		$background_color_gradient_type_1             = $this->props['background_color_gradient_type_1'];
		$background_color_gradient_type_2             = $this->props['background_color_gradient_type_2'];
		$background_color_gradient_type_3             = $this->props['background_color_gradient_type_3'];
		$background_color_gradient_type_4             = $this->props['background_color_gradient_type_4'];
		$background_color_gradient_type_5             = $this->props['background_color_gradient_type_5'];
		$background_color_gradient_type_6             = $this->props['background_color_gradient_type_6'];
		$background_color_gradient_direction_1        = $this->props['background_color_gradient_direction_1'];
		$background_color_gradient_direction_2        = $this->props['background_color_gradient_direction_2'];
		$background_color_gradient_direction_3        = $this->props['background_color_gradient_direction_3'];
		$background_color_gradient_direction_4        = $this->props['background_color_gradient_direction_4'];
		$background_color_gradient_direction_5        = $this->props['background_color_gradient_direction_5'];
		$background_color_gradient_direction_6        = $this->props['background_color_gradient_direction_6'];
		$background_color_gradient_direction_radial_1 = $this->props['background_color_gradient_direction_radial_1'];
		$background_color_gradient_direction_radial_2 = $this->props['background_color_gradient_direction_radial_2'];
		$background_color_gradient_direction_radial_3 = $this->props['background_color_gradient_direction_radial_3'];
		$background_color_gradient_direction_radial_4 = $this->props['background_color_gradient_direction_radial_4'];
		$background_color_gradient_direction_radial_5 = $this->props['background_color_gradient_direction_radial_5'];
		$background_color_gradient_direction_radial_6 = $this->props['background_color_gradient_direction_radial_6'];
		$background_color_gradient_start_1            = $this->props['background_color_gradient_start_1'];
		$background_color_gradient_start_2            = $this->props['background_color_gradient_start_2'];
		$background_color_gradient_start_3            = $this->props['background_color_gradient_start_3'];
		$background_color_gradient_start_4            = $this->props['background_color_gradient_start_4'];
		$background_color_gradient_start_5            = $this->props['background_color_gradient_start_5'];
		$background_color_gradient_start_6            = $this->props['background_color_gradient_start_6'];
		$background_color_gradient_end_1              = $this->props['background_color_gradient_end_1'];
		$background_color_gradient_end_2              = $this->props['background_color_gradient_end_2'];
		$background_color_gradient_end_3              = $this->props['background_color_gradient_end_3'];
		$background_color_gradient_end_4              = $this->props['background_color_gradient_end_4'];
		$background_color_gradient_end_5              = $this->props['background_color_gradient_end_5'];
		$background_color_gradient_end_6              = $this->props['background_color_gradient_end_6'];
		$background_color_gradient_start_position_1   = $this->props['background_color_gradient_start_position_1'];
		$background_color_gradient_start_position_2   = $this->props['background_color_gradient_start_position_2'];
		$background_color_gradient_start_position_3   = $this->props['background_color_gradient_start_position_3'];
		$background_color_gradient_start_position_4   = $this->props['background_color_gradient_start_position_4'];
		$background_color_gradient_start_position_5   = $this->props['background_color_gradient_start_position_5'];
		$background_color_gradient_start_position_6   = $this->props['background_color_gradient_start_position_6'];
		$background_color_gradient_end_position_1     = $this->props['background_color_gradient_end_position_1'];
		$background_color_gradient_end_position_2     = $this->props['background_color_gradient_end_position_2'];
		$background_color_gradient_end_position_3     = $this->props['background_color_gradient_end_position_3'];
		$background_color_gradient_end_position_4     = $this->props['background_color_gradient_end_position_4'];
		$background_color_gradient_end_position_5     = $this->props['background_color_gradient_end_position_5'];
		$background_color_gradient_end_position_6     = $this->props['background_color_gradient_end_position_6'];
		$background_color_gradient_overlays_image_1   = $this->props['background_color_gradient_overlays_image_1'];
		$background_color_gradient_overlays_image_2   = $this->props['background_color_gradient_overlays_image_2'];
		$background_color_gradient_overlays_image_3   = $this->props['background_color_gradient_overlays_image_3'];
		$background_color_gradient_overlays_image_4   = $this->props['background_color_gradient_overlays_image_4'];
		$background_color_gradient_overlays_image_5   = $this->props['background_color_gradient_overlays_image_5'];
		$background_color_gradient_overlays_image_6   = $this->props['background_color_gradient_overlays_image_6'];
		$background_video_mp4_1                       = $this->props['background_video_mp4_1'];
		$background_video_mp4_2                       = $this->props['background_video_mp4_2'];
		$background_video_mp4_3                       = $this->props['background_video_mp4_3'];
		$background_video_mp4_4                       = $this->props['background_video_mp4_4'];
		$background_video_mp4_5                       = $this->props['background_video_mp4_5'];
		$background_video_mp4_6                       = $this->props['background_video_mp4_6'];
		$background_video_webm_1                      = $this->props['background_video_webm_1'];
		$background_video_webm_2                      = $this->props['background_video_webm_2'];
		$background_video_webm_3                      = $this->props['background_video_webm_3'];
		$background_video_webm_4                      = $this->props['background_video_webm_4'];
		$background_video_webm_5                      = $this->props['background_video_webm_5'];
		$background_video_webm_6                      = $this->props['background_video_webm_6'];
		$background_video_width_1                     = $this->props['background_video_width_1'];
		$background_video_width_2                     = $this->props['background_video_width_2'];
		$background_video_width_3                     = $this->props['background_video_width_3'];
		$background_video_width_4                     = $this->props['background_video_width_4'];
		$background_video_width_5                     = $this->props['background_video_width_5'];
		$background_video_width_6                     = $this->props['background_video_width_6'];
		$background_video_height_1                    = $this->props['background_video_height_1'];
		$background_video_height_2                    = $this->props['background_video_height_2'];
		$background_video_height_3                    = $this->props['background_video_height_3'];
		$background_video_height_4                    = $this->props['background_video_height_4'];
		$background_video_height_5                    = $this->props['background_video_height_5'];
		$background_video_height_6                    = $this->props['background_video_height_6'];
		$allow_player_pause_1                         = $this->props['allow_player_pause_1'];
		$allow_player_pause_2                         = $this->props['allow_player_pause_2'];
		$allow_player_pause_3                         = $this->props['allow_player_pause_3'];
		$allow_player_pause_4                         = $this->props['allow_player_pause_4'];
		$allow_player_pause_5                         = $this->props['allow_player_pause_5'];
		$allow_player_pause_6                         = $this->props['allow_player_pause_6'];
		$background_video_pause_outside_viewport_1    = $this->props['background_video_pause_outside_viewport_1'];
		$background_video_pause_outside_viewport_2    = $this->props['background_video_pause_outside_viewport_2'];
		$background_video_pause_outside_viewport_3    = $this->props['background_video_pause_outside_viewport_3'];
		$background_video_pause_outside_viewport_4    = $this->props['background_video_pause_outside_viewport_4'];
		$background_video_pause_outside_viewport_5    = $this->props['background_video_pause_outside_viewport_5'];
		$background_video_pause_outside_viewport_6    = $this->props['background_video_pause_outside_viewport_6'];

		$hover = et_pb_hover_options();

		global $et_pb_all_column_settings, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

		$et_pb_all_column_settings = ! isset( $et_pb_all_column_settings ) ?  array() : $et_pb_all_column_settings;

		$et_pb_all_column_settings_backup = $et_pb_all_column_settings;

		$keep_column_padding_mobile = $column_padding_mobile;

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module, $function_name );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$custom_padding_responsive_active = et_pb_get_responsive_status( $custom_padding_last_edited );

		$padding_mobile_values = $custom_padding_responsive_active ? array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		) : array(
			'tablet' => false,
			'phone' => false,
		);

		$et_pb_columns_counter = 0;

		// Column hover backgrounds
		$column_hover_backgrounds = array();

		for ( $i = 0; $i <= 6; $i++ ) {
			$column_hover_backgrounds = array_merge( $column_hover_backgrounds, array(
				"column_{$i}_color_hover"         => $hover->get_value( "background_color_{$i}", $this->props ),
				"column_{$i}_color_hover_enabled" => $hover->is_enabled( "background_color_{$i}", $this->props ),
			) );
		}

		$et_pb_column_backgrounds = array(
			array(
				'color'               => $background_color_1,
				'color_hover'         => $column_hover_backgrounds['column_1_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_1_color_hover_enabled'],
				'image'               => $bg_img_1,
				'image_size'          => $background_size_1,
				'image_position'      => $background_position_1,
				'image_repeat'        => $background_repeat_1,
				'image_blend'         => $background_blend_1,
			),
			array(
				'color'               => $background_color_2,
				'color_hover'         => $column_hover_backgrounds['column_2_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_2_color_hover_enabled'],
				'image'               => $bg_img_2,
				'image_size'          => $background_size_2,
				'image_position'      => $background_position_2,
				'image_repeat'        => $background_repeat_2,
				'image_blend'         => $background_blend_2,
			),
			array(
				'color'               => $background_color_3,
				'color_hover'         => $column_hover_backgrounds['column_3_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_3_color_hover_enabled'],
				'image'               => $bg_img_3,
				'image_size'          => $background_size_3,
				'image_position'      => $background_position_3,
				'image_repeat'        => $background_repeat_3,
				'image_blend'         => $background_blend_3,
			),
			array(
				'color'               => $background_color_4,
				'color_hover'         => $column_hover_backgrounds['column_4_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_4_color_hover_enabled'],
				'image'               => $bg_img_4,
				'image_size'          => $background_size_4,
				'image_position'      => $background_position_4,
				'image_repeat'        => $background_repeat_4,
				'image_blend'         => $background_blend_4,
			),
			array(
				'color'               => $background_color_5,
				'color_hover'         => $column_hover_backgrounds['column_5_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_5_color_hover_enabled'],
				'image'               => $bg_img_5,
				'image_size'          => $background_size_5,
				'image_position'      => $background_position_5,
				'image_repeat'        => $background_repeat_5,
				'image_blend'         => $background_blend_5,
			),
			array(
				'color'               => $background_color_6,
				'color_hover'         => $column_hover_backgrounds['column_6_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_6_color_hover_enabled'],
				'image'               => $bg_img_6,
				'image_size'          => $background_size_6,
				'image_position'      => $background_position_6,
				'image_repeat'        => $background_repeat_6,
				'image_blend'         => $background_blend_6,
			),
		);


		$et_pb_column_backgrounds_gradient = array(
			array(
				'active'           => $use_background_color_gradient_1,
				'type'             => $background_color_gradient_type_1,
				'direction'        => $background_color_gradient_direction_1,
				'radial_direction' => $background_color_gradient_direction_radial_1,
				'color_start'      => $background_color_gradient_start_1,
				'color_end'        => $background_color_gradient_end_1,
				'start_position'   => $background_color_gradient_start_position_1,
				'end_position'     => $background_color_gradient_end_position_1,
				'overlays_image'   => $background_color_gradient_overlays_image_1,
			),
			array(
				'active'           => $use_background_color_gradient_2,
				'type'             => $background_color_gradient_type_2,
				'direction'        => $background_color_gradient_direction_2,
				'radial_direction' => $background_color_gradient_direction_radial_2,
				'color_start'      => $background_color_gradient_start_2,
				'color_end'        => $background_color_gradient_end_2,
				'start_position'   => $background_color_gradient_start_position_2,
				'end_position'     => $background_color_gradient_end_position_2,
				'overlays_image'   => $background_color_gradient_overlays_image_2,
			),
			array(
				'active'           => $use_background_color_gradient_3,
				'type'             => $background_color_gradient_type_3,
				'direction'        => $background_color_gradient_direction_3,
				'radial_direction' => $background_color_gradient_direction_radial_3,
				'color_start'      => $background_color_gradient_start_3,
				'color_end'        => $background_color_gradient_end_3,
				'start_position'   => $background_color_gradient_start_position_3,
				'end_position'     => $background_color_gradient_end_position_3,
				'overlays_image'   => $background_color_gradient_overlays_image_3,
			),
			array(
				'active'           => $use_background_color_gradient_4,
				'type'             => $background_color_gradient_type_4,
				'direction'        => $background_color_gradient_direction_4,
				'radial_direction' => $background_color_gradient_direction_radial_4,
				'color_start'      => $background_color_gradient_start_4,
				'color_end'        => $background_color_gradient_end_4,
				'start_position'   => $background_color_gradient_start_position_4,
				'end_position'     => $background_color_gradient_end_position_4,
				'overlays_image'   => $background_color_gradient_overlays_image_4,
			),
			array(
				'active'           => $use_background_color_gradient_5,
				'type'             => $background_color_gradient_type_5,
				'direction'        => $background_color_gradient_direction_5,
				'radial_direction' => $background_color_gradient_direction_radial_5,
				'color_start'      => $background_color_gradient_start_5,
				'color_end'        => $background_color_gradient_end_5,
				'start_position'   => $background_color_gradient_start_position_5,
				'end_position'     => $background_color_gradient_end_position_5,
				'overlays_image'   => $background_color_gradient_overlays_image_5,
			),
			array(
				'active'           => $use_background_color_gradient_6,
				'type'             => $background_color_gradient_type_6,
				'direction'        => $background_color_gradient_direction_6,
				'radial_direction' => $background_color_gradient_direction_radial_6,
				'color_start'      => $background_color_gradient_start_6,
				'color_end'        => $background_color_gradient_end_6,
				'start_position'   => $background_color_gradient_start_position_6,
				'end_position'     => $background_color_gradient_end_position_6,
				'overlays_image'   => $background_color_gradient_overlays_image_6,
			),
		);

		$et_pb_column_backgrounds_video = array(
			array(
				'background_video_mp4'         => $background_video_mp4_1,
				'background_video_webm'        => $background_video_webm_1,
				'background_video_width'       => $background_video_width_1,
				'background_video_height'      => $background_video_height_1,
				'background_video_allow_pause' => $allow_player_pause_1,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_1,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_2,
				'background_video_webm'        => $background_video_webm_2,
				'background_video_width'       => $background_video_width_2,
				'background_video_height'      => $background_video_height_2,
				'background_video_allow_pause' => $allow_player_pause_2,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_2,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_3,
				'background_video_webm'        => $background_video_webm_3,
				'background_video_width'       => $background_video_width_3,
				'background_video_height'      => $background_video_height_3,
				'background_video_allow_pause' => $allow_player_pause_3,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_3,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_4,
				'background_video_webm'        => $background_video_webm_4,
				'background_video_width'       => $background_video_width_4,
				'background_video_height'      => $background_video_height_4,
				'background_video_allow_pause' => $allow_player_pause_4,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_4,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_5,
				'background_video_webm'        => $background_video_webm_5,
				'background_video_width'       => $background_video_width_5,
				'background_video_height'      => $background_video_height_5,
				'background_video_allow_pause' => $allow_player_pause_5,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_5,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_6,
				'background_video_webm'        => $background_video_webm_6,
				'background_video_width'       => $background_video_width_6,
				'background_video_height'      => $background_video_height_6,
				'background_video_allow_pause' => $allow_player_pause_6,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_6,
			),
		);

		// Column hover paddings
		$column_hover_paddings = array();

		for ( $i = 1; $i <= 6; $i++ ) {
			$column_hover_paddings = array_merge( $column_hover_paddings, array(
				"column_{$i}_padding_hover_enabled" => $hover->is_enabled( "padding_{$i}", $this->props ),
				"column_{$i}_padding_top"           => $hover->get_compose_value( "padding_top_{$i}", "padding_{$i}", $this->props ),
				"column_{$i}_padding_right"         => $hover->get_compose_value( "padding_right_{$i}", "padding_{$i}", $this->props ),
				"column_{$i}_padding_bottom"        => $hover->get_compose_value( "padding_bottom_{$i}", "padding_{$i}", $this->props ),
				"column_{$i}_padding_left"          => $hover->get_compose_value( "padding_left_{$i}", "padding_{$i}", $this->props ),
			) );
		}

		$et_pb_column_paddings = array(
			array(
				'padding-top'           => $padding_top_1,
				'padding-right'         => $padding_right_1,
				'padding-bottom'        => $padding_bottom_1,
				'padding-left'          => $padding_left_1,
				'padding-hover-enabled' => $column_hover_paddings['column_1_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_1_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_1_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_1_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_1_padding_left'],
			),
			array(
				'padding-top'           => $padding_top_2,
				'padding-right'         => $padding_right_2,
				'padding-bottom'        => $padding_bottom_2,
				'padding-left'          => $padding_left_2,
				'padding-hover-enabled' => $column_hover_paddings['column_2_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_2_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_2_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_2_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_2_padding_left'],
			),
			array(
				'padding-top'           => $padding_top_3,
				'padding-right'         => $padding_right_3,
				'padding-bottom'        => $padding_bottom_3,
				'padding-left'          => $padding_left_3,
				'padding-hover-enabled' => $column_hover_paddings['column_3_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_3_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_3_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_3_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_3_padding_left'],
			),
			array(
				'padding-top'           => $padding_top_4,
				'padding-right'         => $padding_right_4,
				'padding-bottom'        => $padding_bottom_4,
				'padding-left'          => $padding_left_4,
				'padding-hover-enabled' => $column_hover_paddings['column_4_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_4_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_4_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_4_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_4_padding_left'],
			),
			array(
				'padding-top'           => $padding_top_5,
				'padding-right'         => $padding_right_5,
				'padding-bottom'        => $padding_bottom_5,
				'padding-left'          => $padding_left_5,
				'padding-hover-enabled' => $column_hover_paddings['column_5_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_5_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_5_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_5_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_5_padding_left'],
			),
			array(
				'padding-top'           => $padding_top_6,
				'padding-right'         => $padding_right_6,
				'padding-bottom'        => $padding_bottom_6,
				'padding-left'          => $padding_left_6,
				'padding-hover-enabled' => $column_hover_paddings['column_6_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_6_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_6_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_6_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_6_padding_left'],
			),
		);

		$et_pb_column_paddings_mobile = array(
			array(
				'tablet' => explode( '|', $padding_1_tablet ),
				'phone'  => explode( '|', $padding_1_phone ),
				'last_edited' => $padding_1_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_2_tablet ),
				'phone'  => explode( '|', $padding_2_phone ),
				'last_edited' => $padding_2_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_3_tablet ),
				'phone'  => explode( '|', $padding_3_phone ),
				'last_edited' => $padding_3_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_4_tablet ),
				'phone'  => explode( '|', $padding_4_phone ),
				'last_edited' => $padding_4_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_5_tablet ),
				'phone'  => explode( '|', $padding_5_phone ),
				'last_edited' => $padding_5_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_6_tablet ),
				'phone'  => explode( '|', $padding_6_phone ),
				'last_edited' => $padding_6_last_edited,
			),
		);

		$et_pb_column_parallax = array(
			array( $parallax_1, $parallax_method_1 ),
			array( $parallax_2, $parallax_method_2 ),
			array( $parallax_3, $parallax_method_3 ),
			array( $parallax_4, $parallax_method_4 ),
			array( $parallax_5, $parallax_method_5 ),
			array( $parallax_6, $parallax_method_6 ),
		);

		$et_pb_column_css = array(
			'css_class'               => array( $module_class_1, $module_class_2, $module_class_3, $module_class_4, $module_class_5, $module_class_6 ),
			'css_id'                  => array( $module_id_1, $module_id_2, $module_id_3, $module_id_4, $module_id_5, $module_id_6 ),
			'custom_css_before'       => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3, $custom_css_before_4, $custom_css_before_5, $custom_css_before_6 ),
			'custom_css_main'         => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3, $custom_css_main_4, $custom_css_main_5, $custom_css_main_6 ),
			'custom_css_after'        => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3, $custom_css_after_4, $custom_css_after_5, $custom_css_after_6 ),
			'custom_css_before_hover' => array( $custom_css_before_1_hover, $custom_css_before_2_hover, $custom_css_before_3_hover, $custom_css_before_4_hover, $custom_css_before_5_hover, $custom_css_before_6_hover ),
			'custom_css_main_hover'   => array( $custom_css_main_1_hover, $custom_css_main_2_hover, $custom_css_main_3_hover, $custom_css_main_4_hover, $custom_css_main_5_hover, $custom_css_main_6_hover ),
			'custom_css_after_hover'  => array( $custom_css_after_1_hover, $custom_css_after_2_hover, $custom_css_after_3_hover, $custom_css_after_4_hover, $custom_css_after_5_hover, $custom_css_after_6_hover ),
		);

		$internal_columns_settings_array = array(
			'keep_column_padding_mobile' => $keep_column_padding_mobile,
			'et_pb_column_backgrounds' => $et_pb_column_backgrounds,
			'et_pb_column_backgrounds_gradient' => $et_pb_column_backgrounds_gradient,
			'et_pb_column_backgrounds_video' => $et_pb_column_backgrounds_video,
			'et_pb_columns_counter' => $et_pb_columns_counter,
			'et_pb_column_paddings' => $et_pb_column_paddings,
			'et_pb_column_paddings_mobile' => $et_pb_column_paddings_mobile,
			'et_pb_column_parallax' => $et_pb_column_parallax,
			'et_pb_column_css' => $et_pb_column_css,
		);


		$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

		$et_pb_all_column_settings[ $current_row_position ] = $internal_columns_settings_array;

		if ( $et_pb_rendering_column_content ) {
			$et_pb_rendering_column_content_row = true;
		}

		if ( 'on' === $make_equal ) {
			$this->add_classname( 'et_pb_equal_columns' );
		}

		$gutter_hover_data = '';

		if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
			$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter width to 1 if 0 entered by user
			$this->add_classname( 'et_pb_gutters' . $gutter_width );

			if ( et_builder_is_hover_enabled( 'gutter_width', $this->props ) ) {
				$this->add_classname( 'et_pb_gutter_hover' );

				$gutter_hover_data = sprintf(
					' data-original_gutter="%1$s" data-hover_gutter="%2$s"',
					esc_attr($gutter_width),
					esc_attr($gutter_width_hover)
				);
			}
		}


		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of Rows support only top and bottom padding, so we need to handle it along with the full padding in the recent version
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right' => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left' => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '%%order_class%%.et_pb_row',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default)
					if ( in_array( $padding_mobile, array( 'on', 'off' ) ) && 'on' !== $padding_mobile && ! $custom_padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				et_pb_generate_responsive_css( $padding_mobile_values_processed, '%%order_class%%.et_pb_row', '', $function_name, ' !important; ' );
			}
		}

		if ( 'on' === $make_fullwidth && 'off' === $use_custom_width ) {
			$this->add_classname( 'et_pb_row_fullwidth' );
		}

		if ( 'on' === $use_custom_width ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%, .et_pb_pagebuilder_layout.single.et_full_width_page #page-container %%order_class%%',
				'declaration' => sprintf(
					'max-width:%1$s !important;
					%2$s',
					'on' === $width_unit ? esc_attr( sprintf( '%1$spx', intval( $custom_width_px ) ) ) : esc_attr( sprintf( '%1$s%%', intval( $custom_width_percent ) ) ),
					'on' !== $width_unit ? esc_attr( sprintf( 'width: %1$s%%;', intval( $custom_width_percent ) ) ) : ''
				),
			) );

			switch ( $width_unit ) {
				case 'on':
					if ( $custom_width_px === $custom_width_px_hover ) {
						break;
					}

					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%:hover',
						'declaration' => sprintf(
							'max-width:%1$s !important;',
							esc_attr( sprintf( '%1$spx', intval( $custom_width_px_hover ) ) )
						),
					) );

					break;
				default:
					if ( $custom_width_percent === $custom_width_percent_hover ) {
						break;
					}

					ET_Builder_Element::set_style( $function_name, array(
						'selector'    => '%%order_class%%:hover',
						'declaration' => sprintf(
							'max-width:%1$s !important;
							%2$s',
							esc_attr( sprintf( '%1$s%%', intval( $custom_width_percent_hover ) ) ),
							esc_attr( sprintf( 'width: %1$s%%;', intval( $custom_width_percent_hover ) ) )
						),
					) );

					break;
			}
		}

		$parallax_image = $this->get_parallax_image_background();
		$background_video = $this->video_background();

		// CSS Filters
		$this->add_classname( $this->generate_css_filters( $function_name ) );

		// Remove automatically added classnames
		$this->remove_classname( 'et_pb_module' );

		// Save module classes into variable BEFORE processing the content with `do_shortcode()`
		// Otherwise order classes messed up with internal rows if exist
		$module_classes = $this->module_classname( $function_name );

		// Inner content shortcode parsing has to be done after all classname addition/removal
		$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );
		$content_dependent_classname = '' === trim( $inner_content ) ? ' et_pb_row_empty' : '';

		// reset the global column settings to make sure they are not affected by internal content
		// This has to be done after inner content's shortcode being parsed
		$et_pb_all_column_settings = $et_pb_all_column_settings_backup;

		// Reset row's column content flag
		if ( $et_pb_rendering_column_content_row ) {
			$et_pb_rendering_column_content_row = false;
		}

		$output = sprintf(
			'<div%4$s class="%2$s%7$s"%8$s>
				%1$s
				%6$s
				%5$s
			</div> <!-- .%3$s -->',
			$inner_content,
			$module_classes,
			esc_html( $function_name ),
			$this->module_id(),
			$background_video,
			$parallax_image,
			$content_dependent_classname,
			et_core_esc_previously( $gutter_hover_data )
		);

		return $output;
	}
}
new ET_Builder_Row;

class ET_Builder_Row_Inner extends ET_Builder_Structure_Element {
	function init() {
		$this->name       = esc_html__( 'Row', 'et_builder' );
		$this->plural     = esc_html__( 'Rows', 'et_builder' );
		$this->slug       = 'et_pb_row_inner';
		$this->vb_support = 'on';

		$this->advanced_fields = array(
			'background'            => array(
				'use_background_color' => true,
				'use_background_image' => true,
				'use_background_color_gradient' => true,
				'use_background_video' => true,
			),
			'margin_padding' => array(
				'use_padding'       => false,
				'css'               => array(
					'main' => '%%order_class%%.et_pb_row_inner',
					'important' => 'all',
				),
				'custom_margin'     => array(
					'priority' => 1,
				),
			),
			'max_width'             => array(
				'options' => array(
					'module_alignment' => array(
						'label' => esc_html__( 'Row Alignment', 'et_builder' ),
					),
				),
			),
			'fonts'                 => false,
			'text'                  => false,
			'button'                => false,
		);

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'background'     => array(
						'title'       => esc_html__( 'Background', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
							'column_4' => esc_html__( 'Column 4', 'et_builder' ),
						),
						'priority' => 80,
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'width'         => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
					'margin_padding' => array(
						'title'       => esc_html__( 'Spacing', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
							'column_4' => esc_html__( 'Column 4', 'et_builder' ),
						),
						'priority'    => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'classes' => array(
						'title'  => esc_html__( 'CSS ID & Classes', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
							'column_4' => esc_html__( 'Column 4', 'et_builder' ),
						),
					),
					'custom_css' => array(
						'title'  => esc_html__( 'Custom CSS', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => esc_html__( 'Column 1', 'et_builder' ),
							'column_2' => esc_html__( 'Column 2', 'et_builder' ),
							'column_3' => esc_html__( 'Column 3', 'et_builder' ),
							'column_4' => esc_html__( 'Column 4', 'et_builder' ),
						),
					),
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'R9ds7bEaHE8' ),
				'name' => esc_html__( 'An introduction to Rows', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'custom_padding' => array(
				'label'           => esc_html__( 'Custom Padding', 'et_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
				'hover'           => 'tabs',
			),
			'custom_padding_tablet' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'custom_padding_phone' => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'padding_mobile' => array(
				'label' => esc_html__( 'Keep Custom Padding on Mobile', 'et_builder' ),
				'type'        => 'skip', // Remaining attribute for backward compatibility
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'use_custom_gutter' => array(
				'label'             => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'affects'           => array(
					'gutter_width',
				),
				'description'       => esc_html__( 'Enable this option to define custom gutter width for this row.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
			),
			'gutter_width' => array(
				'label'            => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'       => 1,
					'max'       => 4,
					'step'      => 1,
					'min_limit' => 1,
					'max_limit' => 4,
				),
				'depends_show_if'  => 'on',
				'description'      => esc_html__( 'Adjust the spacing between each column in this row.', 'et_builder' ),
				'validate_unit'    => false,
				'fixed_range'      => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'default_on_front' => et_get_option( 'gutter_width', 3 ),
				'hover'            => 'tabs',
			),
			'make_equal' => array(
				'label'             => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'width',
			),
			'columns_background' => array(
				'type'            => 'column_settings_background',
				'option_category' => 'configuration',
				'toggle_slug'     => 'background',
				'priority'        => 99,
			),
			'columns_padding' => array(
				'type'            => 'column_settings_padding',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
				'priority'        => 99,
			),
			'column_padding_mobile' => array(
				'label'    => esc_html__( 'Keep Column Padding on Mobile', 'et_builder' ),
				'tab_slug' => 'advanced',
				'type'     => 'skip', // Remaining attribute for backward compatibility
			),
			'columns_css' => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'custom_css',
				'priority'        => 20,
			),
			'custom_padding_last_edited' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'columns_css_fields' => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'priority'        => 20,
			),
		);

		$column_fields = $this->get_column_fields( 4, array(
			'background_color'                           => array(),
			'bg_img'                                     => array(),
			'padding_top_bottom_link'                    => array(),
			'padding_left_right_link'                    => array(),
			'parallax'                                   => array(
				'default_on_front' => 'off',
			),
			'parallax_method'                            => array(
				'default_on_front' => 'on',
			),
			'background_size'                            => array(),
			'background_position'                        => array(),
			'background_repeat'                          => array(),
			'background_blend'                           => array(),
			'use_background_color_gradient'              => array(),
			'background_color_gradient_start'            => array(),
			'background_color_gradient_end'              => array(),
			'background_color_gradient_type'             => array(),
			'background_color_gradient_direction'        => array(),
			'background_color_gradient_direction_radial' => array(),
			'background_color_gradient_start_position'   => array(),
			'background_color_gradient_end_position'     => array(),
			'background_color_gradient_overlays_image'   => array(),
			'background_video_mp4'                       => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'background_video_webm'                      => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'background_video_width'                     => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'background_video_height'                    => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'allow_player_pause'                         => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'background_video_pause_outside_viewport'    => array(
				'computed_affects'   => array(
					'__video_background',
				),
			),
			'__video_background'                         => array(
				'type' => 'computed',
				'computed_callback' => array(
					'ET_Builder_Column',
					'get_column_video_background'
				),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum' => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
			'padding_top'                                => array( 'tab_slug' => 'advanced' ),
			'padding_right'                              => array( 'tab_slug' => 'advanced' ),
			'padding_bottom'                             => array( 'tab_slug' => 'advanced' ),
			'padding_left'                               => array( 'tab_slug' => 'advanced' ),
			'padding_top_bottom_link'                    => array( 'tab_slug' => 'advanced' ),
			'padding_left_right_link'                    => array( 'tab_slug' => 'advanced' ),
			'padding_%column_index%_tablet'              => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
			),
			'padding_%column_index%_phone'               => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
				),
			'padding_%column_index%_last_edited'         => array(
				'has_custom_index_location' => true,
				'tab_slug' => 'advanced',
				),
			'module_id'                                  => array( 'tab_slug' => 'custom_css' ),
			'module_class'                               => array( 'tab_slug' => 'custom_css' ),
			'custom_css_before'                          => array( 'tab_slug' => 'custom_css' ),
			'custom_css_main'                            => array( 'tab_slug' => 'custom_css' ),
			'custom_css_after'                           => array( 'tab_slug' => 'custom_css' ),
		) );

		return array_merge( $fields, $column_fields );
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['custom_width_px'] = array( 'max-width' => '%%order_class%%' );

		for ( $i = 1; $i <= 6; $i ++ ) {
			$selector = "%%order_class%% > .et_pb_column:nth-child({$i})";
			$fields["background_color_{$i}"] = array( 'background-color' => $selector );
			$fields["padding_{$i}"] = array( 'padding' => $selector );
		}

		return $fields;
	}

	function render( $atts, $content = null, $function_name ) {
		$background_color_1                           = $this->props['background_color_1'];
		$background_color_2                           = $this->props['background_color_2'];
		$background_color_3                           = $this->props['background_color_3'];
		$background_color_4                           = $this->props['background_color_4'];
		$bg_img_1                                     = $this->props['bg_img_1'];
		$bg_img_2                                     = $this->props['bg_img_2'];
		$bg_img_3                                     = $this->props['bg_img_3'];
		$bg_img_4                                     = $this->props['bg_img_4'];
		$background_size_1                            = $this->props['background_size_1'];
		$background_size_2                            = $this->props['background_size_2'];
		$background_size_3                            = $this->props['background_size_3'];
		$background_size_4                            = $this->props['background_size_4'];
		$background_position_1                        = $this->props['background_position_1'];
		$background_position_2                        = $this->props['background_position_2'];
		$background_position_3                        = $this->props['background_position_3'];
		$background_position_4                        = $this->props['background_position_4'];
		$background_repeat_1                          = $this->props['background_repeat_1'];
		$background_repeat_2                          = $this->props['background_repeat_2'];
		$background_repeat_3                          = $this->props['background_repeat_3'];
		$background_repeat_4                          = $this->props['background_repeat_4'];
		$background_blend_1                           = $this->props['background_blend_1'];
		$background_blend_2                           = $this->props['background_blend_2'];
		$background_blend_3                           = $this->props['background_blend_3'];
		$background_blend_4                           = $this->props['background_blend_4'];
		$padding_top_1                                = $this->props['padding_top_1'];
		$padding_right_1                              = $this->props['padding_right_1'];
		$padding_bottom_1                             = $this->props['padding_bottom_1'];
		$padding_left_1                               = $this->props['padding_left_1'];
		$padding_top_2                                = $this->props['padding_top_2'];
		$padding_right_2                              = $this->props['padding_right_2'];
		$padding_bottom_2                             = $this->props['padding_bottom_2'];
		$padding_left_2                               = $this->props['padding_left_2'];
		$padding_top_3                                = $this->props['padding_top_3'];
		$padding_top_4                                = $this->props['padding_top_4'];
		$padding_right_3                              = $this->props['padding_right_3'];
		$padding_right_4                              = $this->props['padding_right_4'];
		$padding_bottom_3                             = $this->props['padding_bottom_3'];
		$padding_bottom_4                             = $this->props['padding_bottom_4'];
		$padding_left_3                               = $this->props['padding_left_3'];
		$padding_left_4                               = $this->props['padding_left_4'];
		$padding_1_tablet                             = $this->props['padding_1_tablet'];
		$padding_2_tablet                             = $this->props['padding_2_tablet'];
		$padding_3_tablet                             = $this->props['padding_3_tablet'];
		$padding_4_tablet                             = $this->props['padding_4_tablet'];
		$padding_1_phone                              = $this->props['padding_1_phone'];
		$padding_2_phone                              = $this->props['padding_2_phone'];
		$padding_3_phone                              = $this->props['padding_3_phone'];
		$padding_4_phone                              = $this->props['padding_4_phone'];
		$padding_1_last_edited                        = $this->props['padding_1_last_edited'];
		$padding_2_last_edited                        = $this->props['padding_2_last_edited'];
		$padding_3_last_edited                        = $this->props['padding_3_last_edited'];
		$padding_4_last_edited                        = $this->props['padding_4_last_edited'];
		$gutter_width                                 = $this->props['gutter_width'];
		$gutter_width_hover                           = $this->get_hover_value( 'gutter_width' );
		$make_equal                                   = $this->props['make_equal'];
		$custom_padding                               = $this->props['custom_padding'];
		$padding_mobile                               = $this->props['padding_mobile'];
		$custom_padding_tablet                        = $this->props['custom_padding_tablet'];
		$custom_padding_phone                         = $this->props['custom_padding_phone'];
		$custom_padding_last_edited                   = $this->props['custom_padding_last_edited'];
		$column_padding_mobile                        = $this->props['column_padding_mobile'];
		$global_module                                = $this->props['global_module'];
		$use_custom_gutter                            = $this->props['use_custom_gutter'];
		$parallax_1                                   = $this->props['parallax_1'];
		$parallax_method_1                            = $this->props['parallax_method_1'];
		$parallax_2                                   = $this->props['parallax_2'];
		$parallax_method_2                            = $this->props['parallax_method_2'];
		$parallax_3                                   = $this->props['parallax_3'];
		$parallax_4                                   = $this->props['parallax_4'];
		$parallax_method_3                            = $this->props['parallax_method_3'];
		$parallax_method_4                            = $this->props['parallax_method_4'];
		$module_id_1                                  = $this->props['module_id_1'];
		$module_id_2                                  = $this->props['module_id_2'];
		$module_id_3                                  = $this->props['module_id_3'];
		$module_id_4                                  = $this->props['module_id_4'];
		$module_class_1                               = $this->props['module_class_1'];
		$module_class_2                               = $this->props['module_class_2'];
		$module_class_3                               = $this->props['module_class_3'];
		$module_class_4                               = $this->props['module_class_4'];
		$custom_css_before_1                          = $this->props['custom_css_before_1'];
		$custom_css_before_2                          = $this->props['custom_css_before_2'];
		$custom_css_before_3                          = $this->props['custom_css_before_3'];
		$custom_css_before_4                          = $this->props['custom_css_before_4'];
		$custom_css_main_1                            = $this->props['custom_css_main_1'];
		$custom_css_main_2                            = $this->props['custom_css_main_2'];
		$custom_css_main_3                            = $this->props['custom_css_main_3'];
		$custom_css_main_4                            = $this->props['custom_css_main_4'];
		$custom_css_after_1                           = $this->props['custom_css_after_1'];
		$custom_css_after_2                           = $this->props['custom_css_after_2'];
		$custom_css_after_3                           = $this->props['custom_css_after_3'];
		$custom_css_after_4                           = $this->props['custom_css_after_4'];
		$custom_css_before_1_hover                    = $this->get_hover_value( 'custom_css_before_1');
		$custom_css_before_2_hover                    = $this->get_hover_value( 'custom_css_before_2');
		$custom_css_before_3_hover                    = $this->get_hover_value( 'custom_css_before_3');
		$custom_css_before_4_hover                    = $this->get_hover_value( 'custom_css_before_4');
		$custom_css_main_1_hover                      = $this->get_hover_value( 'custom_css_main_1');
		$custom_css_main_2_hover                      = $this->get_hover_value( 'custom_css_main_2');
		$custom_css_main_3_hover                      = $this->get_hover_value( 'custom_css_main_3');
		$custom_css_main_4_hover                      = $this->get_hover_value( 'custom_css_main_4');
		$custom_css_after_1_hover                     = $this->get_hover_value( 'custom_css_after_1');
		$custom_css_after_2_hover                     = $this->get_hover_value( 'custom_css_after_2');
		$custom_css_after_3_hover                     = $this->get_hover_value( 'custom_css_after_3');
		$custom_css_after_4_hover                     = $this->get_hover_value( 'custom_css_after_4');
		$use_background_color_gradient_1              = $this->props['use_background_color_gradient_1'];
		$use_background_color_gradient_2              = $this->props['use_background_color_gradient_2'];
		$use_background_color_gradient_3              = $this->props['use_background_color_gradient_3'];
		$use_background_color_gradient_4              = $this->props['use_background_color_gradient_4'];
		$background_color_gradient_type_1             = $this->props['background_color_gradient_type_1'];
		$background_color_gradient_type_2             = $this->props['background_color_gradient_type_2'];
		$background_color_gradient_type_3             = $this->props['background_color_gradient_type_3'];
		$background_color_gradient_type_4             = $this->props['background_color_gradient_type_4'];
		$background_color_gradient_direction_1        = $this->props['background_color_gradient_direction_1'];
		$background_color_gradient_direction_2        = $this->props['background_color_gradient_direction_2'];
		$background_color_gradient_direction_3        = $this->props['background_color_gradient_direction_3'];
		$background_color_gradient_direction_4        = $this->props['background_color_gradient_direction_4'];
		$background_color_gradient_direction_radial_1 = $this->props['background_color_gradient_direction_radial_1'];
		$background_color_gradient_direction_radial_2 = $this->props['background_color_gradient_direction_radial_2'];
		$background_color_gradient_direction_radial_3 = $this->props['background_color_gradient_direction_radial_3'];
		$background_color_gradient_direction_radial_4 = $this->props['background_color_gradient_direction_radial_4'];
		$background_color_gradient_start_1            = $this->props['background_color_gradient_start_1'];
		$background_color_gradient_start_2            = $this->props['background_color_gradient_start_2'];
		$background_color_gradient_start_3            = $this->props['background_color_gradient_start_3'];
		$background_color_gradient_start_4            = $this->props['background_color_gradient_start_4'];
		$background_color_gradient_end_1              = $this->props['background_color_gradient_end_1'];
		$background_color_gradient_end_2              = $this->props['background_color_gradient_end_2'];
		$background_color_gradient_end_3              = $this->props['background_color_gradient_end_3'];
		$background_color_gradient_end_4              = $this->props['background_color_gradient_end_4'];
		$background_color_gradient_start_position_1   = $this->props['background_color_gradient_start_position_1'];
		$background_color_gradient_start_position_2   = $this->props['background_color_gradient_start_position_2'];
		$background_color_gradient_start_position_3   = $this->props['background_color_gradient_start_position_3'];
		$background_color_gradient_start_position_4   = $this->props['background_color_gradient_start_position_4'];
		$background_color_gradient_end_position_1     = $this->props['background_color_gradient_end_position_1'];
		$background_color_gradient_end_position_2     = $this->props['background_color_gradient_end_position_2'];
		$background_color_gradient_end_position_3     = $this->props['background_color_gradient_end_position_3'];
		$background_color_gradient_end_position_4     = $this->props['background_color_gradient_end_position_4'];
		$background_color_gradient_overlays_image_1   = $this->props['background_color_gradient_overlays_image_1'];
		$background_color_gradient_overlays_image_2   = $this->props['background_color_gradient_overlays_image_2'];
		$background_color_gradient_overlays_image_3   = $this->props['background_color_gradient_overlays_image_3'];
		$background_color_gradient_overlays_image_4   = $this->props['background_color_gradient_overlays_image_4'];
		$background_video_mp4_1                       = $this->props['background_video_mp4_1'];
		$background_video_mp4_2                       = $this->props['background_video_mp4_2'];
		$background_video_mp4_3                       = $this->props['background_video_mp4_3'];
		$background_video_mp4_4                       = $this->props['background_video_mp4_4'];
		$background_video_webm_1                      = $this->props['background_video_webm_1'];
		$background_video_webm_2                      = $this->props['background_video_webm_2'];
		$background_video_webm_3                      = $this->props['background_video_webm_3'];
		$background_video_webm_4                      = $this->props['background_video_webm_4'];
		$background_video_width_1                     = $this->props['background_video_width_1'];
		$background_video_width_2                     = $this->props['background_video_width_2'];
		$background_video_width_3                     = $this->props['background_video_width_3'];
		$background_video_width_4                     = $this->props['background_video_width_4'];
		$background_video_height_1                    = $this->props['background_video_height_1'];
		$background_video_height_2                    = $this->props['background_video_height_2'];
		$background_video_height_3                    = $this->props['background_video_height_3'];
		$background_video_height_4                    = $this->props['background_video_height_4'];
		$allow_player_pause_1                         = $this->props['allow_player_pause_1'];
		$allow_player_pause_2                         = $this->props['allow_player_pause_2'];
		$allow_player_pause_3                         = $this->props['allow_player_pause_3'];
		$allow_player_pause_4                         = $this->props['allow_player_pause_4'];
		$background_video_pause_outside_viewport_1    = $this->props['background_video_pause_outside_viewport_1'];
		$background_video_pause_outside_viewport_2    = $this->props['background_video_pause_outside_viewport_2'];
		$background_video_pause_outside_viewport_3    = $this->props['background_video_pause_outside_viewport_3'];
		$background_video_pause_outside_viewport_4    = $this->props['background_video_pause_outside_viewport_4'];

		$hover = et_pb_hover_options();

		global $et_pb_all_column_settings_inner, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

		$et_pb_all_column_settings_inner = ! isset( $et_pb_all_column_settings_inner ) ?  array() : $et_pb_all_column_settings_inner;

		$et_pb_all_column_settings_backup = $et_pb_all_column_settings_inner;

		$keep_column_padding_mobile = $column_padding_mobile;

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module, $function_name );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$custom_padding_responsive_active = et_pb_get_responsive_status( $custom_padding_last_edited );

		$padding_mobile_values = $custom_padding_responsive_active ? array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		) : array(
			'tablet' => false,
			'phone' => false,
		);

		// Column hover backgrounds
		$column_hover_backgrounds = array();

		for ( $i = 0; $i <= 3; $i++ ) {
			$column_hover_backgrounds = array_merge( $column_hover_backgrounds, array(
				"column_{$i}_color_hover"         => $hover->get_value( "background_color_{$i}", $this->props ),
				"column_{$i}_color_hover_enabled" => $hover->is_enabled( "background_color_{$i}", $this->props ),
			) );
		}

		$et_pb_columns_inner_counter = 0;
		$et_pb_column_inner_backgrounds = array(
			array(
				'color'               => $background_color_1,
				'color_hover'         => $column_hover_backgrounds['column_1_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_1_color_hover_enabled'],
				'image'               => $bg_img_1,
				'image_size'          => $background_size_1,
				'image_position'      => $background_position_1,
				'image_repeat'        => $background_repeat_1,
				'image_blend'         => $background_blend_1,
			),
			array(
				'color'               => $background_color_2,
				'color_hover'         => $column_hover_backgrounds['column_2_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_2_color_hover_enabled'],
				'image'               => $bg_img_2,
				'image_size'          => $background_size_2,
				'image_position'      => $background_position_2,
				'image_repeat'        => $background_repeat_2,
				'image_blend'         => $background_blend_2,
			),
			array(
				'color'               => $background_color_3,
				'color_hover'         => $column_hover_backgrounds['column_3_color_hover'],
				'color_hover_enabled' => $column_hover_backgrounds['column_3_color_hover_enabled'],
				'image'               => $bg_img_3,
				'image_size'          => $background_size_3,
				'image_position'      => $background_position_3,
				'image_repeat'        => $background_repeat_3,
				'image_blend'         => $background_blend_3,
			),
			array(
				'color'          => $background_color_4,
				'image'          => $bg_img_4,
				'image_size'     => $background_size_4,
				'image_position' => $background_position_4,
				'image_repeat'   => $background_repeat_4,
				'image_blend'    => $background_blend_4,
			),
		);

		$et_pb_column_inner_backgrounds_gradient = array(
			array(
				'active'           => $use_background_color_gradient_1,
				'type'             => $background_color_gradient_type_1,
				'direction'        => $background_color_gradient_direction_1,
				'radial_direction' => $background_color_gradient_direction_radial_1,
				'color_start'      => $background_color_gradient_start_1,
				'color_end'        => $background_color_gradient_end_1,
				'start_position'   => $background_color_gradient_start_position_1,
				'end_position'     => $background_color_gradient_end_position_1,
				'overlays_image'   => $background_color_gradient_overlays_image_1,
			),
			array(
				'active'           => $use_background_color_gradient_2,
				'type'             => $background_color_gradient_type_2,
				'direction'        => $background_color_gradient_direction_2,
				'radial_direction' => $background_color_gradient_direction_radial_2,
				'color_start'      => $background_color_gradient_start_2,
				'color_end'        => $background_color_gradient_end_2,
				'start_position'   => $background_color_gradient_start_position_2,
				'end_position'     => $background_color_gradient_end_position_2,
				'overlays_image'   => $background_color_gradient_overlays_image_2,
			),
			array(
				'active'           => $use_background_color_gradient_3,
				'type'             => $background_color_gradient_type_3,
				'direction'        => $background_color_gradient_direction_3,
				'radial_direction' => $background_color_gradient_direction_radial_3,
				'color_start'      => $background_color_gradient_start_3,
				'color_end'        => $background_color_gradient_end_3,
				'start_position'   => $background_color_gradient_start_position_3,
				'end_position'     => $background_color_gradient_end_position_3,
				'overlays_image'   => $background_color_gradient_overlays_image_3,
			),
			array(
				'active'           => $use_background_color_gradient_4,
				'type'             => $background_color_gradient_type_4,
				'direction'        => $background_color_gradient_direction_4,
				'radial_direction' => $background_color_gradient_direction_radial_4,
				'color_start'      => $background_color_gradient_start_4,
				'color_end'        => $background_color_gradient_end_4,
				'start_position'   => $background_color_gradient_start_position_4,
				'end_position'     => $background_color_gradient_end_position_4,
				'overlays_image'   => $background_color_gradient_overlays_image_4,
			),
		);

		$et_pb_column_inner_backgrounds_video = array(
			array(
				'background_video_mp4'         => $background_video_mp4_1,
				'background_video_webm'        => $background_video_webm_1,
				'background_video_width'       => $background_video_width_1,
				'background_video_height'      => $background_video_height_1,
				'background_video_allow_pause' => $allow_player_pause_1,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_1,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_2,
				'background_video_webm'        => $background_video_webm_2,
				'background_video_width'       => $background_video_width_2,
				'background_video_height'      => $background_video_height_2,
				'background_video_allow_pause' => $allow_player_pause_2,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_2,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_3,
				'background_video_webm'        => $background_video_webm_3,
				'background_video_width'       => $background_video_width_3,
				'background_video_height'      => $background_video_height_3,
				'background_video_allow_pause' => $allow_player_pause_3,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_3,
			),
			array(
				'background_video_mp4'         => $background_video_mp4_4,
				'background_video_webm'        => $background_video_webm_4,
				'background_video_width'       => $background_video_width_4,
				'background_video_height'      => $background_video_height_4,
				'background_video_allow_pause' => $allow_player_pause_4,
				'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_4,
			),
		);

		// Column hover paddings
		$column_hover_paddings = array();

		for ( $i = 0; $i <= 4; $i++ ) {
			$column_hover_paddings = array_merge( $column_hover_paddings, array(
				"column_{$i}_padding_hover_enabled" => $hover->is_enabled( "padding_{$i}", $this->props ),
				"column_{$i}_padding_top"           => $hover->get_compose_value( "padding_top_{$i}", "padding_{$i}", $this->props ),
				"column_{$i}_padding_right"         => $hover->get_compose_value( "padding_right_{$i}", "padding_{$i}", $this->props ),
				"column_{$i}_padding_bottom"        => $hover->get_compose_value( "padding_bottom_{$i}", "padding_{$i}", $this->props ),
				"column_{$i}_padding_left"          => $hover->get_compose_value( "padding_left_{$i}", "padding_{$i}", $this->props ),
			) );
		}

		$et_pb_column_inner_paddings = array(
			array(
				'padding-top'           => $padding_top_1,
				'padding-right'         => $padding_right_1,
				'padding-bottom'        => $padding_bottom_1,
				'padding-left'          => $padding_left_1,
				'padding-hover-enabled' => $column_hover_paddings['column_1_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_1_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_1_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_1_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_1_padding_left'],
			),
			array(
				'padding-top'           => $padding_top_2,
				'padding-right'         => $padding_right_2,
				'padding-bottom'        => $padding_bottom_2,
				'padding-left'          => $padding_left_2,
				'padding-hover-enabled' => $column_hover_paddings['column_2_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_2_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_2_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_2_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_2_padding_left'],
			),
			array(
				'padding-top'           => $padding_top_3,
				'padding-right'         => $padding_right_3,
				'padding-bottom'        => $padding_bottom_3,
				'padding-left'          => $padding_left_3,
				'padding-hover-enabled' => $column_hover_paddings['column_3_padding_hover_enabled'],
				'padding-top-hover'     => $column_hover_paddings['column_3_padding_top'],
				'padding-right-hover'   => $column_hover_paddings['column_3_padding_right'],
				'padding-bottom-hover'  => $column_hover_paddings['column_3_padding_bottom'],
				'padding-left-hover'    => $column_hover_paddings['column_3_padding_left'],
			),
			array(
				'padding-top'    => $padding_top_4,
				'padding-right'  => $padding_right_4,
				'padding-bottom' => $padding_bottom_4,
				'padding-left'   => $padding_left_4,
			),
		);

		$et_pb_column_parallax = array(
			array( $parallax_1, $parallax_method_1 ),
			array( $parallax_2, $parallax_method_2 ),
			array( $parallax_3, $parallax_method_3 ),
			array( $parallax_4, $parallax_method_4 ),
		);

		$et_pb_column_inner_paddings_mobile = array(
			array(
				'tablet' => explode( '|', $padding_1_tablet ),
				'phone'  => explode( '|', $padding_1_phone ),
				'last_edited' => $padding_1_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_2_tablet ),
				'phone'  => explode( '|', $padding_2_phone ),
				'last_edited' => $padding_2_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_3_tablet ),
				'phone'  => explode( '|', $padding_3_phone ),
				'last_edited' => $padding_3_last_edited,
			),
			array(
				'tablet' => explode( '|', $padding_4_tablet ),
				'phone'  => explode( '|', $padding_4_phone ),
				'last_edited' => $padding_4_last_edited,
			),
		);

		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of Rows support only top and bottom padding, so we need to handle it along with the full padding in the recent version
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top' => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right' => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left' => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '.et_pb_column %%order_class%%',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default)
					if ( in_array( $padding_mobile, array( 'on', 'off' ) ) && 'on' !== $padding_mobile && ! $custom_padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				et_pb_generate_responsive_css( $padding_mobile_values_processed, '.et_pb_column %%order_class%%', '', $function_name, ' !important; ' );
			}
		}

		$et_pb_column_inner_css = array(
			'css_class'               => array( $module_class_1, $module_class_2, $module_class_3, $module_class_4 ),
			'css_id'                  => array( $module_id_1, $module_id_2, $module_id_3, $module_id_4 ),
			'custom_css_before'       => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3, $custom_css_before_4 ),
			'custom_css_main'         => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3, $custom_css_main_4 ),
			'custom_css_after'        => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3, $custom_css_after_4 ),
			'custom_css_before_hover' => array( $custom_css_before_1_hover, $custom_css_before_2_hover, $custom_css_before_3_hover, $custom_css_before_4_hover ),
			'custom_css_main_hover'   => array( $custom_css_main_1_hover, $custom_css_main_2_hover, $custom_css_main_3_hover, $custom_css_main_4_hover ),
			'custom_css_after_hover'  => array( $custom_css_after_1_hover, $custom_css_after_2_hover, $custom_css_after_3_hover, $custom_css_after_4_hover ),
		);

		$internal_columns_settings_array = array(
			'keep_column_padding_mobile' => $keep_column_padding_mobile,
			'et_pb_column_inner_backgrounds' => $et_pb_column_inner_backgrounds,
			'et_pb_column_inner_backgrounds_gradient' => $et_pb_column_inner_backgrounds_gradient,
			'et_pb_column_inner_backgrounds_video' => $et_pb_column_inner_backgrounds_video,
			'et_pb_columns_inner_counter' => $et_pb_columns_inner_counter,
			'et_pb_column_inner_paddings' => $et_pb_column_inner_paddings,
			'et_pb_column_inner_paddings_mobile' => $et_pb_column_inner_paddings_mobile,
			'et_pb_column_parallax' => $et_pb_column_parallax,
			'et_pb_column_inner_css' => $et_pb_column_inner_css,
		);

		$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

		$et_pb_all_column_settings_inner[ $current_row_position ] = $internal_columns_settings_array;

		if ( 'on' === $make_equal ) {
			$this->add_classname( 'et_pb_equal_columns' );
		}

		$gutter_hover_data = '';

		if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
			$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter to 1 if 0 entered by user
			$this->add_classname( 'et_pb_gutters' . $gutter_width );

			if ( et_builder_is_hover_enabled( 'gutter_width', $this->props ) ) {
				$this->add_classname( 'et_pb_gutter_hover' );

				$gutter_hover_data = sprintf(
					' data-original_gutter="%1$s" data-hover_gutter="%2$s"',
					esc_attr($gutter_width),
					esc_attr($gutter_width_hover)
				);
			}
		}

		$parallax_image = $this->get_parallax_image_background();
		$background_video = $this->video_background();

		// CSS Filters
		$this->add_classname( $this->generate_css_filters( $function_name ) );

		// Remove automatically added classnames
		$this->remove_classname( 'et_pb_module' );

		// Save module classes into variable BEFORE processing the content with `do_shortcode()`
		// Otherwise order classes messed up with internal rows if exist
		$module_classes = $this->module_classname( $function_name );

		// Inner content shortcode parsing has to be done after all classname addition/removal
		$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );
		$content_dependent_classname = '' === trim( $inner_content ) ? ' et_pb_row_empty' : '';

		// reset the global column settings to make sure they are not affected by internal content
		$et_pb_all_column_settings_inner = $et_pb_all_column_settings_backup;

		$output = sprintf(
			'<div%4$s class="%2$s%7$s"%8$s>
				%1$s
				%5$s
				%6$s
			</div> <!-- .%3$s -->',
			$inner_content,
			$module_classes,
			esc_html( $function_name ),
			$this->module_id(),
			$parallax_image,
			$background_video,
			$content_dependent_classname,
			et_core_esc_previously( $gutter_hover_data )
		);

		return $output;
	}
}
new ET_Builder_Row_Inner;

class ET_Builder_Column extends ET_Builder_Structure_Element {
	function init() {
		$this->name                       = esc_html__( 'Column', 'et_builder' );
		$this->plural                     = esc_html__( 'Columns', 'et_builder' );
		$this->slug                       = 'et_pb_column';
		$this->additional_shortcode_slugs = array( 'et_pb_column_inner' );
		$this->vb_support                 = 'on';
		$this->advanced_fields           = false;

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'R9ds7bEaHE8' ),
				'name' => esc_html__( 'An introduction to the Column module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'type'                        => array(
				'default_on_front' => '4_4',
				'type' => 'skip',
			),
			'specialty_columns'           => array(
				'type' => 'skip',
			),
			'saved_specialty_column_type' => array(
				'type' => 'skip',
			),
		);

		return $fields;
	}

	function render( $atts, $content = null, $function_name ) {
		$type                        = $this->props['type'];
		$specialty_columns           = $this->props['specialty_columns'];
		$saved_specialty_column_type = $this->props['saved_specialty_column_type'];

		global $et_pb_all_column_settings,
			$et_pb_all_column_settings_inner,
			$et_specialty_column_type,
			$et_pb_rendering_column_content,
			$et_pb_rendering_column_content_row,
			$et_pb_column_completion;

		$is_specialty_column = 'et_pb_column_inner' !== $function_name && '' !== $specialty_columns;

		$current_row_position = $et_pb_rendering_column_content_row ? 'internal_row' : 'regular_row';

		if ( 'et_pb_column_inner' !== $function_name ) {
			$et_specialty_column_type = $type;
			$array_index = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_columns_counter", 0 );
			$backgrounds_array = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_backgrounds", array() );
			$background_gradient = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_backgrounds_gradient.[{$array_index}]", '' );
			$background_video = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_backgrounds_video.[{$array_index}]", '' );
			$paddings_array = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_paddings", array() );
			$paddings_mobile_array = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_paddings_mobile", array() );
			$column_css_array = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_css", array() );
			$keep_column_padding_mobile = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.keep_column_padding_mobile", 'on' );
			$column_parallax = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_parallax", '' );
			if ( isset( $et_pb_all_column_settings[ $current_row_position ] ) ) {
				$et_pb_all_column_settings[ $current_row_position ]['et_pb_columns_counter']++;
			}
		} else {
			$array_index = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.et_pb_columns_inner_counter", 0 );
			$backgrounds_array = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.et_pb_column_inner_backgrounds", array() );
			$background_gradient = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.et_pb_column_inner_backgrounds_gradient.[{$array_index}]", '' );
			$background_video = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.et_pb_column_inner_backgrounds_video.[{$array_index}]", '' );
			$paddings_array = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.et_pb_column_inner_paddings", array() );
			$column_css_array = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.et_pb_column_inner_css", array() );
			$paddings_mobile_array = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.et_pb_column_inner_paddings_mobile", array() );
			$keep_column_padding_mobile = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.keep_column_padding_mobile", 'on' );
			$column_parallax = self::$_->array_get( $et_pb_all_column_settings_inner, "{$current_row_position}.et_pb_column_parallax", '' );
			if ( isset( $et_pb_all_column_settings_inner[ $current_row_position ] ) ) {
				$et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_columns_inner_counter']++;
			}
		}


		// Get column type value in array
		$column_type = explode( '_', $type );

		// Just in case for some reason column shortcode has no `type` attribute and causes unexpected $column_type values
		if ( isset( $column_type[0] ) && isset( $column_type[1] ) ) {
			// Get column progress.
			$column_progress = intval( $column_type[0] ) / intval( $column_type[1] );

			if ( 0 === $array_index ) {
				$et_pb_column_completion = $column_progress;
			} else {
				$et_pb_column_completion = $et_pb_column_completion + $column_progress;
			}
		}

		// Last column is when sum of column type value equals to 1
		$is_last_column = 1 === $et_pb_column_completion;

		$background_color = isset( $backgrounds_array[$array_index]['color'] ) ? $backgrounds_array[$array_index]['color'] : '';
		$background_img = isset( $backgrounds_array[$array_index]['image'] ) ? $backgrounds_array[$array_index]['image'] : '';
		$background_size = isset( $backgrounds_array[$array_index]['image_size'] ) ? $backgrounds_array[$array_index]['image_size'] : '';
		$background_position = isset( $backgrounds_array[$array_index]['image_position'] ) ? $backgrounds_array[$array_index]['image_position'] : '';
		$background_repeat = isset( $backgrounds_array[$array_index]['image_repeat'] ) ? $backgrounds_array[$array_index]['image_repeat'] : '';
		$background_blend = isset( $backgrounds_array[$array_index]['image_blend'] ) ? $backgrounds_array[$array_index]['image_blend'] : '';
		$background_gradient_overlays_image = isset( $background_gradient['overlays_image'] ) ? $background_gradient['overlays_image'] : '';

		$padding_values = isset( $paddings_array[$array_index] ) ? $paddings_array[$array_index] : array();
		$padding_mobile_values = isset( $paddings_mobile_array[$array_index] ) ? $paddings_mobile_array[$array_index] : array();
		$padding_last_edited = isset( $padding_mobile_values['last_edited'] ) ? $padding_mobile_values['last_edited'] : 'off|desktop';
		$padding_responsive_active = et_pb_get_responsive_status( $padding_last_edited );
		$parallax_method = isset( $column_parallax[$array_index][0] ) && 'on' === $column_parallax[$array_index][0] ? $column_parallax[$array_index][1] : '';
		$custom_css_class = isset( $column_css_array['css_class'][$array_index] ) ? ' ' . $column_css_array['css_class'][$array_index] : '';
		$custom_css_id = isset( $column_css_array['css_id'][$array_index] ) ? $column_css_array['css_id'][$array_index] : '';
		$custom_css_before = isset( $column_css_array['custom_css_before'][$array_index] ) ? $column_css_array['custom_css_before'][$array_index] : '';
		$custom_css_main = isset( $column_css_array['custom_css_main'][$array_index] ) ? $column_css_array['custom_css_main'][$array_index] : '';
		$custom_css_after = isset( $column_css_array['custom_css_after'][$array_index] ) ? $column_css_array['custom_css_after'][$array_index] : '';

		$custom_css_before_hover = self::$_->array_get( $column_css_array, "custom_css_before_hover.[$array_index]", '' );
		$custom_css_main_hover = self::$_->array_get( $column_css_array, "custom_css_main_hover.[$array_index]", '' );
		$custom_css_after_hover = self::$_->array_get( $column_css_array, "custom_css_after_hover.[$array_index]", '' );

		$background_images = array();

		if ( '' !== $background_gradient && 'on' === $background_gradient['active'] ) {
			$has_background_gradient = true;

			$default_gradient = apply_filters( 'et_pb_default_gradient', array(
				'type'             => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
				'direction'        => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
				'radial_direction' => ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ),
				'color_start'      => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
				'color_end'        => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
				'start_position'   => ET_Global_Settings::get_value( 'all_background_gradient_start_position' ),
				'end_position'     => ET_Global_Settings::get_value( 'all_background_gradient_end_position' ),
			) );

			$background_gradient = wp_parse_args( array_filter( $background_gradient ), $default_gradient );

			$direction               = $background_gradient['type'] === 'linear' ? $background_gradient['direction'] : "circle at {$background_gradient['radial_direction']}";
			$start_gradient_position = et_sanitize_input_unit( $background_gradient['start_position'], false, '%' );
			$end_gradient_position   = et_sanitize_input_unit( $background_gradient['end_position'], false, '%');
			$background_images[]     = "{$background_gradient['type']}-gradient(
				{$direction},
				{$background_gradient['color_start']} ${start_gradient_position},
				{$background_gradient['color_end']} ${end_gradient_position}
			)";
		}

		if ( '' !== $background_img && 'on' !== $parallax_method ) {
			$has_background_image = true;

			$background_images[] = sprintf(
				'url(%s)',
				esc_attr( $background_img )
			);

			if ( '' !== $background_size ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-size:%s;',
						esc_attr( $background_size )
					),
				) );
			}

			if ( '' !== $background_position ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-position:%s;',
						esc_attr( str_replace( '_', ' ', $background_position ) )
					),
				) );
			}

			if ( '' !== $background_repeat ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-repeat:%s;',
						esc_attr( $background_repeat )
					),
				) );
			}

			if ( '' !== $background_blend ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-blend-mode:%s;',
						esc_attr( $background_blend )
					),
				) );
			}
		}

		if ( ! empty( $background_images ) ) {
			if ( 'on' !== $background_gradient_overlays_image ) {
				// The browsers stack the images in the opposite order to what you'd expect.
				$background_images = array_reverse( $background_images );
			}

			$backgorund_images_declaration = sprintf(
				'background-image: %1$s;',
				esc_html( implode( ', ', $background_images ) )
			);

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => esc_attr( $backgorund_images_declaration ),
			) );
		}

		if ( '' !== $background_color && 'rgba(0,0,0,0)' !== $background_color && ! isset( $has_background_gradient, $has_background_image ) ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-color:%s;',
					esc_attr( $background_color )
				),
			) );

			$background_color_hover = self::$_->array_get( $backgrounds_array[$array_index], "color_hover" );
			$background_color_hover_enabled = self::$_->array_get( $backgrounds_array[$array_index], "color_hover_enabled" );

			if ( $background_color_hover && $background_color_hover_enabled ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%%:hover',
					'declaration' => sprintf(
						'background-color:%s;',
						esc_attr( $background_color_hover )
					),
				) );
			}
		} else if ( isset( $has_background_gradient, $has_background_image ) ) {
			// Force background-color: initial
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => 'background-color: initial;'
			) );
		}

		if ( ! empty( $padding_values ) ) {
			$padding_hover_enabled = self::$_->array_get( $padding_values, 'padding-hover-enabled', false );
			unset( $padding_values['padding-hover-enabled'] );

			foreach( $padding_values as $position => $value ) {
				if ( in_array( $position, array('padding-top', 'padding-right', 'padding-bottom', 'padding-left' ) ) && !empty( $value ) ) {
					$element_style = array(
						'selector'    => '%%order_class%%',
						'declaration' => sprintf(
							'%1$s:%2$s;',
							esc_html( $position ),
							esc_html( et_builder_process_range_value( $value ) )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default)
					if ( in_array( $keep_column_padding_mobile, array( 'on', 'off' ) ) && 'on' !== $keep_column_padding_mobile && ! $padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}

				// Add padding hover styles
				if ( $padding_hover_enabled
				     && null != self::$_->array_get( $padding_values, "{$position}-hover" )
				     && '' != self::$_->array_get( $padding_values, "{$position}-hover" )
				) {
					$hover_value = $padding_values["{$position}-hover"];

					$element_style = array(
						'selector'    => '%%order_class%%:hover',
						'declaration' => sprintf(
							'%1$s:%2$s;',
							esc_html( $position ),
							esc_html( et_builder_process_range_value( $hover_value ) )
						),
					);

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( $padding_responsive_active && ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) ) {
			$padding_mobile_values_processed = array();

			foreach( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[$device] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[$device][0] ) ? $padding_mobile_values[$device][0] : '',
					'padding-right'  => isset( $padding_mobile_values[$device][1] ) ? $padding_mobile_values[$device][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[$device][2] ) ? $padding_mobile_values[$device][2] : '',
					'padding-left'   => isset( $padding_mobile_values[$device][3] ) ? $padding_mobile_values[$device][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				$padding_mobile_selector = 'et_pb_column_inner' !== $function_name ? '.et_pb_row > .et_pb_column%%order_class%%' : '.et_pb_row_inner > .et_pb_column%%order_class%%';
				et_pb_generate_responsive_css( $padding_mobile_values_processed, $padding_mobile_selector, '', $function_name );
			}
		}

		if ( '' !== $custom_css_before ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:before',
				'declaration' => trim( $custom_css_before ),
			) );
		}

		if ( '' !== $custom_css_main ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%',
				'declaration' => trim( $custom_css_main ),
			) );
		}

		if ( '' !== $custom_css_after ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:after',
				'declaration' => trim( $custom_css_after ),
			) );
		}

		if ( '' !== $custom_css_before_hover ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:hover:before',
				'declaration' => trim( $custom_css_before_hover ),
			) );
		}

		if ( '' !== $custom_css_main_hover ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:hover',
				'declaration' => trim( $custom_css_main_hover ),
			) );
		}

		if ( '' !== $custom_css_after_hover ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%:hover:after',
				'declaration' => trim( $custom_css_after_hover ),
			) );
		}

		if ( 'et_pb_column_inner' === $function_name ) {
			if ( '1_1' === $type ) {
				$type = '4_4';
			}

			$et_specialty_column_type = '' !== $saved_specialty_column_type ? $saved_specialty_column_type : $et_specialty_column_type;

			switch ( $et_specialty_column_type ) {
				case '1_2':
					if ( '1_2' === $type ) {
						$type = '1_4';
					}
					if ( '1_3' === $type ) {
						$type = '1_6';
					}

					break;
				case '2_3':
					if ( '1_2' === $type ) {
						$type = '1_3';
					}

					if ( '1_4' === $type ) {
						$type = '1_6';
					}

					break;
				case '3_4':
					if ( '1_2' === $type ) {
						$type = '3_8';
					} else if ( '1_3' === $type ) {
						$type = '1_4';
					}

					break;
			}
		}

		$video_background = trim( $this->video_background( $background_video ) );

		// Remove automatically added classname
		$this->remove_classname( 'et_pb_module' );

		$this->add_classname( 'et_pb_column_' . $type, 1 );

		if ( '' !== $custom_css_class ) {
			$this->add_classname( $custom_css_class );
		}

		if ( $is_specialty_column ) {
			$this->add_classname( 'et_pb_specialty_column' );
		}

		// CSS Filters
		$this->add_classname( $this->generate_css_filters( $function_name ) );

		if ( '' !== $parallax_method ) {
			$this->add_classname( 'et_pb_section_parallax' );
		}

		if ( '' !== $video_background ) {
			$this->add_classname( array(
				'et_pb_section_video',
				'et_pb_preload',
			) );
		}

		if ( $is_last_column ) {
			$this->add_classname( 'et-last-child' );
		}

		// Module classname in column has to be contained in variable BEFORE content is being parsed
		// as shortcode because column and column inner use the same ET_Builder_Column's render
		// classname doesn't work in nested situation because each called module doesn't have its own class init
		$module_classname = $this->module_classname( $function_name );

		// Inner content shortcode parsing has to be done after all classname addition/removal
		$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );

		// Inner content dependant class in column shouldn't use add_classname/remove_classname method
		$content_dependent_classname = '' === trim( $inner_content ) ? ' et_pb_column_empty' : '';

		$output = sprintf(
			'<div class="%1$s%6$s"%4$s>
				%5$s
				%3$s
				%2$s
			</div> <!-- .et_pb_column -->',
			$module_classname,
			$inner_content,
			( '' !== $background_img && '' !== $parallax_method
				? sprintf(
					'%3$s<div class="et_parallax_bg%2$s" style="background-image: url(%1$s);"></div>%4$s',
					esc_attr( $background_img ),
					( 'off' === $parallax_method ? ' et_pb_parallax_css' : '' ),
					!et_core_is_fb_enabled() ? '' : '<div class="et_parallax_bg_wrap">',
					!et_core_is_fb_enabled() ? '' : '</div>'
				)
				: ''
			),
			'' !== $custom_css_id ? sprintf( ' id="%1$s"', esc_attr( $custom_css_id ) ) : '', // 5
			$video_background,
			$content_dependent_classname
		);

		return $output;

	}

}
new ET_Builder_Column;
