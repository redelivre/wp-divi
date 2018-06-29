<?php

class ET_Builder_Module_Fullwidth_Slider extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Fullwidth Slider', 'et_builder' );
		$this->slug            = 'et_pb_fullwidth_slider';
		$this->vb_support      = 'on';
		$this->fullwidth       = true;
		$this->child_slug      = 'et_pb_slide';
		$this->child_item_text = esc_html__( 'Slide', 'et_builder' );

		$this->main_css_element = '%%order_class%%.et_pb_slider';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'   => esc_html__( 'Elements', 'et_builder' ),
					'background' => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'    => esc_html__( 'Layout', 'et_builder' ),
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
						'plugin_main' => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title, {$this->main_css_element} .et_pb_slide_description .et_pb_slide_title a",
						'important' => array(
							'color',
							'size',
							'font-size',
							'plugin_all',
						),
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element}.et_pb_module .et_pb_slides .et_pb_slide_content",
						'line_height' => "{$this->main_css_element} p",
						'important' => array( 'size', 'font-size' ),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'custom_style' => true,
					),
				),
			),
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
						'alignment' => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment' => true,
					'box_shadow' => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
				),
			),
			'background'            => array(
				'use_background_color'          => 'fields_only',
				'use_background_color_gradient' => 'fields_only',
				'use_background_image'          => 'fields_only',
				'options'                       => array(
					'parallax_method' => array(
						'default' => 'off',
					),
					'background_position' => array(
					),
					'background_size' => array(
					),
				),
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
						'default'          => 'center',
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
				'id'   => esc_html( 'zfMBE_zX744' ),
				'name' => esc_html__( 'An introduction to the Fullwidth Slider module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'show_arrows' => array(
				'label'           => esc_html__( 'Show Arrows', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'This setting allows you to turn the navigation arrows on or off.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'           => esc_html__( 'Show Controls', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Disabling this option will remove the circle button at the bottom of the slider.', 'et_builder' ),
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
		);

		return $fields;
	}

	function before_render() {
		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_show_mobile, $et_pb_slider_custom_icon, $et_pb_slider_item_num, $et_pb_slider_button_rel;

		$et_pb_slider_item_num = 0;

		$parallax        = $this->props['parallax'];
		$parallax_method = $this->props['parallax_method'];
		$show_content_on_mobile  = $this->props['show_content_on_mobile'];
		$show_cta_on_mobile      = $this->props['show_cta_on_mobile'];
		$button_rel              = $this->props['button_rel'];
		$button_custom           = $this->props['custom_button'];
		$custom_icon             = $this->props['button_icon'];

		$et_pb_slider_has_video = false;

		$et_pb_slider_parallax = $parallax;

		$et_pb_slider_parallax_method = $parallax_method;

		$et_pb_slider_show_mobile = array(
			'show_content_on_mobile'  => $show_content_on_mobile,
			'show_cta_on_mobile'      => $show_cta_on_mobile,
		);

		$et_pb_slider_custom_icon = 'on' === $button_custom ? $custom_icon : '';
		$et_pb_slider_button_rel  = $button_rel;

		// Pass Fullwidth Slider Module settings to Slide Item
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
		);
	}

	function render( $attrs, $content = null, $render_slug ) {
		$show_arrows             = $this->props['show_arrows'];
		$show_pagination         = $this->props['show_pagination'];
		$parallax                = $this->props['parallax'];
		$parallax_method         = $this->props['parallax_method'];
		$auto                    = $this->props['auto'];
		$auto_speed              = $this->props['auto_speed'];
		$auto_ignore_hover       = $this->props['auto_ignore_hover'];
		$show_image_video_mobile = $this->props['show_image_video_mobile'];
		$background_position     = $this->props['background_position'];
		$background_size         = $this->props['background_size'];

		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_show_mobile, $et_pb_slider_custom_icon, $et_pb_slider;

		$content = $this->content;

		if ( '' !== $background_position && 'default' !== $background_position && 'off' === $parallax ) {
			$processed_position = str_replace( '_', ' ', $background_position );

			ET_Builder_Module::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_slide',
				'declaration' => sprintf(
					'background-position: %1$s;',
					esc_html( $processed_position )
				),
			) );
		}

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
		$this->add_classname( 'et_pb_slider' );

		if ( $et_pb_slider_has_video ) {
			$this->add_classname( 'et_pb_preload' );
		}

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

		// Remove automatically added classnames
		$this->remove_classname( 'et_pb_fullwidth_slider' );

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

new ET_Builder_Module_Fullwidth_Slider;
