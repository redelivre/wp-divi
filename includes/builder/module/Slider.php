<?php

class ET_Builder_Module_Slider extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Slider', 'et_builder' );
		$this->plural          = esc_html__( 'Sliders', 'et_builder' );
		$this->slug            = 'et_pb_slider';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_slide';
		$this->child_item_text = esc_html__( 'Slide', 'et_builder' );

		$this->main_css_element = '%%order_class%%.et_pb_slider';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'    => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'navigation' => esc_html__( 'Navigation', 'et_builder' ),
					'image'      => esc_html__( 'Image', 'et_builder' ),
					'layout'     => esc_html__( 'Layout', 'et_builder' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'header' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title",
						'limited_main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title, {$this->main_css_element} .et_pb_slide_description .et_pb_slide_title a",
						'font_size_tablet' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_description .et_pb_slide_title",
						'font_size_phone'  => "{$this->main_css_element} .et_pb_slides .et_pb_slide_description .et_pb_slide_title",
						'important' => array( 'size', 'font-size', 'plugin_all' ),
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element}",
						'main' => "{$this->main_css_element} .et_pb_slide_content",
						'line_height_tablet' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'line_height_phone' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'font_size_tablet' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'font_size_phone' => "{$this->main_css_element} .et_pb_slides .et_pb_slide_content",
						'important' => array( 'size', 'font-size' ),
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
					),
				),
			),
			'borders'               => array(
				'default' => array(),
				'image'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_slide_image',
							'border_styles' => '%%order_class%% .et_pb_slide_image',
						)
					),
					'label_prefix'    => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'depends_show_if' => 'off',
					'defaults'        => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main' => '%%order_class%% .et_pb_slide_image',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
						'limited_main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
						'alignment' => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment' => true,
					'box_shadow' => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
			'background'            => array(
				'use_background_color'          => 'fields_only',
				'use_background_color_gradient' => 'fields_only',
				'use_background_image'          => 'fields_only',
				'options' => array(
					'parallax_method' => array(
						'default' => 'off',
					),
				)
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'padding'   => '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'                  => array(
				'css'   => array(
					'text_orientation' => '%%order_class%% .et_pb_slide .et_pb_slide_description',
					'text_shadow'      => '%%order_class%% .et_pb_slide .et_pb_slide_description',
				),
				'options' => array(
					'text_orientation'  => array(
						'default'      => 'center',
					),
				),
			),
			'height' => array(
				'css' => array(
					'main' => '%%order_class%%, %%order_class%% .et_pb_slide',
				)
			),
			'image'                 => array(
				'css' => array(
					'main' => array(
						'%%order_class%% .et_pb_slide_image',
						'%%order_class%% .et_pb_section_video_bg',
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description .et_pb_slide_title',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector' => '.et_pb_slider .et_pb_slide .et_pb_slide_description a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_controllers' => array(
				'label'    => esc_html__( 'Slide Controllers', 'et_builder' ),
				'selector' => '.et-pb-controllers',
			),
			'slide_active_controller' => array(
				'label'    => esc_html__( 'Slide Active Controller', 'et_builder' ),
				'selector' => '.et-pb-controllers .et-pb-active-control',
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
			'slide_arrows' => array(
				'label'    => esc_html__( 'Slide Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( '-YeoR2xSLOY' ),
				'name' => esc_html__( 'An introduction to the Slider module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_arrows'         => array(
				'label'           => esc_html__( 'Show Arrows', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'This setting will turn on and off the navigation arrows.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Controls', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'This setting will turn on and off the circle buttons at the bottom of the slider.', 'et_builder' ),
			),
			'show_content_on_mobile' => array(
				'label'           => esc_html__( 'Show Content On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'show_cta_on_mobile' => array(
				'label'           => esc_html__( 'Show CTA On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'show_image_video_mobile' => array(
				'label'           => esc_html__( 'Show Image / Video On Mobile', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'use_bg_overlay'         => array(
				'label'            => esc_html__( 'Use Background Overlay', 'et_builder' ),
				'description'      => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'yes', 'et_builder' ),
				),
				'default_on_front' => '',
				'affects'          => array(
					'bg_overlay_color',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'option_category'  => 'configuration',
			),
			'bg_overlay_color'       => array(
				'label'           => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'description'     => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'option_category' => 'configuration',
				'mobile_options'  => true,
			),
			'use_text_overlay'       => array(
				'label'            => esc_html__( 'Use Text Overlay', 'et_builder' ),
				'description'      => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'yes', 'et_builder' ),
				),
				'default_on_front' => '',
				'affects'          => array(
					'text_overlay_color',
					'text_border_radius',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'option_category'  => 'configuration',
			),
			'text_overlay_color'     => array(
				'label'           => esc_html__( 'Text Overlay Color', 'et_builder' ),
				'description'     => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'option_category' => 'configuration',
				'mobile_options'  => true,
			),
			'text_border_radius'     => array(
				'label'            => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
				'description'      => esc_html__( 'Increasing the border radius will increase the roundness of the overlay corners. Setting this value to 0 will result in squared corners.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '3',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'mobile_options'   => true,
			),
			'arrows_custom_color'    => array(
				'label'          => esc_html__( 'Arrow Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the slider arrows that are used to navigate through each slide.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'navigation',
				'mobile_options' => true,
			),
			'dot_nav_custom_color'   => array(
				'label'          => esc_html__( 'Dot Navigation Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the dot navigation that appears at the bottom of the slider to designate which slide is active.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'navigation',
				'mobile_options' => true,
			),
		);

		return $fields;
	}

	function before_render() {
		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_show_mobile, $et_pb_slider_custom_icon, $et_pb_slider_custom_icon_tablet, $et_pb_slider_custom_icon_phone, $et_pb_slider_item_num, $et_pb_slider_button_rel;

		$et_pb_slider_item_num = 0;

		$parallax                = $this->props['parallax'];
		$parallax_method         = $this->props['parallax_method'];
		$show_content_on_mobile  = $this->props['show_content_on_mobile'];
		$show_cta_on_mobile      = $this->props['show_cta_on_mobile'];
		$button_rel              = $this->props['button_rel'];
		$button_custom           = $this->props['custom_button'];

		$custom_icon_values      = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon             = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet      = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone       = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$et_pb_slider_has_video = false;

		$et_pb_slider_parallax = $parallax;

		$et_pb_slider_parallax_method = $parallax_method;

		$et_pb_slider_show_mobile = array(
			'show_content_on_mobile'  => $show_content_on_mobile,
			'show_cta_on_mobile'      => $show_cta_on_mobile,
		);

		$et_pb_slider_custom_icon        = 'on' === $button_custom ? $custom_icon : '';
		$et_pb_slider_custom_icon_tablet = 'on' === $button_custom ? $custom_icon_tablet : '';
		$et_pb_slider_custom_icon_phone  = 'on' === $button_custom ? $custom_icon_phone : '';

		$et_pb_slider_button_rel  = $button_rel;

		// BG Overlay Color.
		$bg_overlay_color        = $this->props['bg_overlay_color'];
		$bg_overlay_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'bg_overlay_color' );
		$bg_overlay_color_tablet = isset( $bg_overlay_color_values['tablet'] ) ? $bg_overlay_color_values['tablet'] : '';
		$bg_overlay_color_phone  = isset( $bg_overlay_color_values['phone'] ) ? $bg_overlay_color_values['phone'] : '';

		// Text Overlay Color.
		$text_overlay_color        = $this->props['text_overlay_color'];
		$text_overlay_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'text_overlay_color' );
		$text_overlay_color_tablet = isset( $text_overlay_color_values['tablet'] ) ? $text_overlay_color_values['tablet'] : '';
		$text_overlay_color_phone  = isset( $text_overlay_color_values['phone'] ) ? $text_overlay_color_values['phone'] : '';

		// Text Border Radius.
		$text_border_radius        = $this->props['text_border_radius'];
		$text_border_radius_values = et_pb_responsive_options()->get_property_values( $this->props, 'text_border_radius' );
		$text_border_radius_tablet = isset( $text_border_radius_values['tablet'] ) ? $text_border_radius_values['tablet'] : '';
		$text_border_radius_phone  = isset( $text_border_radius_values['phone'] ) ? $text_border_radius_values['phone'] : '';

		// Arrows Color.
		$arrows_custom_color        = $this->props['arrows_custom_color'];
		$arrows_custom_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'arrows_custom_color' );
		$arrows_custom_color_tablet = isset( $arrows_custom_color_values['tablet'] ) ? $arrows_custom_color_values['tablet'] : '';
		$arrows_custom_color_phone  = isset( $arrows_custom_color_values['phone'] ) ? $arrows_custom_color_values['phone'] : '';

		// Dot Nav Custom Color.
		$dot_nav_custom_color        = $this->props['dot_nav_custom_color'];
		$dot_nav_custom_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'dot_nav_custom_color' );
		$dot_nav_custom_color_tablet = isset( $dot_nav_custom_color_values['tablet'] ) ? $dot_nav_custom_color_values['tablet'] : '';
		$dot_nav_custom_color_phone  = isset( $dot_nav_custom_color_values['phone'] ) ? $dot_nav_custom_color_values['phone'] : '';

		// Pass Slider Module setting to Slide Item
		global $et_pb_slider;

		$et_pb_slider = array(
			'background_color'                           => $this->props['background_color'],
			'use_background_color_gradient'              => $this->props['use_background_color_gradient'],
			'background_color_gradient_type'             => $this->props['background_color_gradient_type'],
			'background_color_gradient_direction'        => $this->props['background_color_gradient_direction'],
			'background_color_gradient_direction_radial' => $this->props['background_color_gradient_direction_radial'],
			'background_color_gradient_start'            => $this->props['background_color_gradient_start'],
			'background_color_gradient_end'              => $this->props['background_color_gradient_end'],
			'background_color_gradient_start_position'   => $this->props['background_color_gradient_start_position'],
			'background_color_gradient_end_position'     => $this->props['background_color_gradient_end_position'],
			'background_color_gradient_overlays_image'   => $this->props['background_color_gradient_overlays_image'],
			'background_image'                           => $this->props['background_image'],
			'background_size'                            => $this->props['background_size'],
			'background_position'                        => $this->props['background_position'],
			'background_repeat'                          => $this->props['background_repeat'],
			'background_blend'                           => $this->props['background_blend'],
			'parallax'                                   => $this->props['parallax'],
			'parallax_method'                            => $this->props['parallax_method'],
			'background_video_mp4'                       => $this->props['background_video_mp4'],
			'background_video_webm'                      => $this->props['background_video_webm'],
			'background_video_width'                     => $this->props['background_video_width'],
			'background_video_height'                    => $this->props['background_video_height'],
			'header_level'                               => $this->props['header_level'],
			'use_bg_overlay'                             => $this->props['use_bg_overlay'],
			'bg_overlay_color'                           => $bg_overlay_color,
			'bg_overlay_color_slider_last_edited'        => $this->props['bg_overlay_color_last_edited'],
			'bg_overlay_color_tablet'                    => $bg_overlay_color_tablet,
			'bg_overlay_color_phone'                     => $bg_overlay_color_phone,
			'use_text_overlay'                           => $this->props['use_text_overlay'],
			'text_overlay_color'                         => $text_overlay_color,
			'text_overlay_color_slider_last_edited'      => $this->props['text_overlay_color_last_edited'],
			'text_overlay_color_tablet'                  => $text_overlay_color_tablet,
			'text_overlay_color_phone'                   => $text_overlay_color_phone,
			'text_border_radius'                         => $text_border_radius,
			'text_border_radius_slider_last_edited'      => $this->props['text_border_radius_last_edited'],
			'text_border_radius_tablet'                  => $text_border_radius_tablet,
			'text_border_radius_phone'                   => $text_border_radius_phone,
			'arrows_custom_color'                        => $arrows_custom_color,
			'arrows_custom_color_slider_last_edited'     => $this->props['arrows_custom_color_last_edited'],
			'arrows_custom_color_tablet'                 => $arrows_custom_color_tablet,
			'arrows_custom_color_phone'                  => $arrows_custom_color_phone,
			'dot_nav_custom_color'                       => $dot_nav_custom_color,
			'dot_nav_custom_color_slider_last_edited'    => $this->props['dot_nav_custom_color_last_edited'],
			'dot_nav_custom_color_tablet'                => $dot_nav_custom_color_tablet,
			'dot_nav_custom_color_phone'                 => $dot_nav_custom_color_phone,
		);

		// Hover Options attribute doesn't have field definition and rendered on the fly, thus the use of array_get()
		$background_hover_enabled_key = et_pb_hover_options()->get_hover_enabled_field( 'background' );
		$background_color_hover_key   = et_pb_hover_options()->get_hover_field( 'background_color' );

		$et_pb_slider[ $background_hover_enabled_key ] = self::$_->array_get( $this->props, $background_hover_enabled_key, '' );
		$et_pb_slider[ $background_color_hover_key ]   = self::$_->array_get( $this->props, $background_color_hover_key, '' );
	}

	function render( $attrs, $content = null, $render_slug ) {
		$show_arrows             = $this->props['show_arrows'];
		$show_pagination         = $this->props['show_pagination'];
		$parallax                = $this->props['parallax'];
		$parallax_method         = $this->props['parallax_method'];
		$auto                    = $this->props['auto'];
		$auto_speed              = $this->props['auto_speed'];
		$auto_ignore_hover       = $this->props['auto_ignore_hover'];
		$body_font_size          = $this->props['body_font_size'];
		$show_content_on_mobile  = $this->props['show_content_on_mobile'];
		$show_cta_on_mobile      = $this->props['show_cta_on_mobile'];
		$show_image_video_mobile = $this->props['show_image_video_mobile'];
		$background_position     = $this->props['background_position'];
		$background_size         = $this->props['background_size'];

		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_show_mobile, $et_pb_slider_custom_icon, $et_pb_slider_custom_icon_tablet, $et_pb_slider_custom_icon_phone, $et_pb_slider;

		$content = $this->content;

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		if ( '' !== $background_position && 'default' !== $background_position  && 'off' === $parallax ) {
			$processed_position = str_replace( '_', ' ', $background_position );

			ET_Builder_Module::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_slide',
				'declaration' => sprintf(
					'background-position: %1$s;',
					esc_html( $processed_position )
				),
			) );
		}

		// Handle slider's previous background size default value ("default") as well
		if ( '' !== $background_size && 'default' !== $background_size && 'off' === $parallax ) {
			ET_Builder_Module::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_slide',
				'declaration' => sprintf(
					'-moz-background-size: %1$s;
					-webkit-background-size: %1$s;
					background-size: %1$s;',
					esc_html( $background_size )
				),
			) );
		}

		$fullwidth = 'et_pb_fullwidth_slider' === $render_slug ? 'on' : 'off';

		// Module classnames
		if ( 'off' === $fullwidth ) {
			$this->add_classname( 'et_pb_slider_fullwidth_off' );
		}

		if ( 'off' === $show_arrows ) {
			$this->add_classname( 'et_pb_slider_no_arrows' );
		}

		if ( 'off' === $show_pagination ) {
			$this->add_classname( 'et_pb_slider_no_pagination' );
		}

		if ( 'on' === $parallax ) {
			$this->add_classname( 'et_pb_slider_parallax' );
		}

		if ( 'on' === $auto ) {
			$this->add_classname( array(
				'et_slider_auto',
				"et_slider_speed_{$auto_speed}",
			) );
		}

		if ( 'on' === $auto_ignore_hover ) {
			$this->add_classname( 'et_slider_auto_ignore_hover' );
		}

		if ( 'on' === $show_image_video_mobile ) {
			$this->add_classname( 'et_pb_slider_show_image' );
		}

		$output = sprintf(
			'<div%3$s class="%1$s">
				<div class="et_pb_slides">
					%2$s
				</div> <!-- .et_pb_slides -->
				%4$s
			</div> <!-- .et_pb_slider -->
			',
			$this->module_classname( $render_slug ),
			$content,
			$this->module_id(),
			$this->inner_shadow_back_compatibility( $render_slug )
		);

		// Reset passed slider item value
		$et_pb_slider = array();

		return $output;
	}

	private function inner_shadow_back_compatibility( $functions_name ) {
		$utils = ET_Core_Data_Utils::instance();
		$atts  = $this->props;
		$style = '';

		if (
			version_compare( $utils->array_get( $atts, '_builder_version', '3.0.93' ), '3.0.99', 'lt' )
		) {
			$class = self::get_module_order_class( $functions_name );
			$style = sprintf(
				'<style>%1$s</style>',
				sprintf(
					'.%1$s.et_pb_slider .et_pb_slide {'
					. '-webkit-box-shadow: none; '
					. '-moz-box-shadow: none; '
					. 'box-shadow: none; '
					.'}',
					esc_attr( $class )
				)
			);

			if ( 'off' !== $utils->array_get( $atts, 'show_inner_shadow' ) ) {
				$style .= sprintf(
					'<style>%1$s</style>',
					sprintf(
						'.%1$s > .box-shadow-overlay { '
						. '-webkit-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. '-moz-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. 'box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. '}',
						esc_attr( $class )
					)
				);
			}
		}

		return $style;
	}
}

new ET_Builder_Module_Slider;
