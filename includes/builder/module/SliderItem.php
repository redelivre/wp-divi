<?php

class ET_Builder_Module_Slider_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Slide', 'et_builder' );
		$this->plural                      = esc_html__( 'Slides', 'et_builder' );
		$this->slug                        = 'et_pb_slide';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'admin_title';
		$this->child_title_fallback_var    = 'heading';
		$this->advanced_setting_title_text = esc_html__( 'New Slide', 'et_builder' );
		$this->settings_text               = esc_html__( 'Slide Settings', 'et_builder' );
		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'image_video'  => esc_html__( 'Image & Video', 'et_builder' ),
					'player_pause' => esc_html__( 'Player Pause', 'et_builder' ),
					'admin_label' => array(
						'title'    => esc_html__( 'Admin Label', 'et_builder' ),
						'priority' => 99,
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'navigation' => esc_html__( 'Navigation', 'et_builder' ),
					'alignment'  => esc_html__( 'Alignment', 'et_builder' ),
					'image' => array(
						'title' => esc_html__( 'Image', 'et_builder' ),
					),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'header' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title",
						'limited_main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title, .et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_title a",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '0.1',
						),
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main'        => ".et_pb_slider.et_pb_module {$this->main_css_element}.et_pb_slide .et_pb_slide_description .et_pb_slide_content",
						'line_height' => "{$this->main_css_element} p",
						'important'   => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '0.1',
						),
					),
				),
			),
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css'      => array(
						'main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_more_button.et_pb_button",
						'limited_main' => ".et_pb_slider {$this->main_css_element}.et_pb_slide .et_pb_more_button.et_pb_button",
						'alignment' => ".et_pb_slider {$this->main_css_element} .et_pb_slide_description .et_pb_button_wrapper",
					),
					'use_alignment' => true,
					'box_shadow' => array(
						'css' => array(
							'main'      => '%%order_class%% .et_pb_button',
							'important' => true,
						),
					),
				),
			),
			'background'            => array(
				'css' => array(
					'main' => ".et_pb_slider %%order_class%%",
				),
				'options' => array(
					'background_color' => array(
						'default'          => et_builder_accent_color(),
						'default_on_child' => true,
					),
				),
			),
			'borders'               => array(
				'default' => false,
			),
			'margin_padding' => array(
				'use_margin' => false,
				'css' => array(
					'padding'   => '.et_pb_slider %%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off %%order_class%% .et_pb_slide_description',
					'important' => array( 'custom_padding' ), // Important is needed to overwrite parent and column-specific padding specificity
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'css' => array(
					'main'             => implode( ', ', array(
						'%%order_class%% .et_pb_slide_description .et_pb_slide_title',
						'%%order_class%% .et_pb_slide_description .et_pb_slide_title a',
						'%%order_class%% .et_pb_slide_description .et_pb_slide_content',
						'%%order_class%% .et_pb_slide_description .et_pb_slide_content .post-meta',
						'%%order_class%% .et_pb_slide_description .et_pb_slide_content .post-meta a',
						'%%order_class%% .et_pb_slide_description .et_pb_slide_content .et_pb_button',
					) ),
					'text_orientation' => '.et_pb_slides %%order_class%%.et_pb_slide .et_pb_slide_description',
					'text_shadow'      => '.et_pb_slides %%order_class%%.et_pb_slide .et_pb_slide_description',
				),
				'options'              => array(
					'background_layout' => array(
						'default'          => 'dark',
						'default_on_child' => true,
						'hover'            => 'tabs'
					),
				),
			),
			'box_shadow'            => array(
				'default' => false,
			),
			'filters'               => array(
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'                 => array(
				'css' => array(
					'main' => array(
						'%%order_class%% .et_pb_slide_image',
						'%%order_class%% .et_pb_section_video_bg',
					),
				),
			),
			'max_width'             => false,
		);

		$this->custom_css_fields = array(
			'slide_title' => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description h2',
			),
			'slide_container' => array(
				'label'    => esc_html__( 'Slide Description Container', 'et_builder' ),
				'selector' => '.et_pb_container',
			),
			'slide_description' => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_button' => array(
				'label'    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector' => '.et_pb_slide .et_pb_container a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_image' => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
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
			'heading' => array(
				'label'           => esc_html__( 'Heading', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the title text for your slide.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the text for the slide button', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'button_link' => array(
				'label'            => esc_html__( 'Button Link URL', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Input a destination URL for the slide button.', 'et_builder' ),
				'toggle_slug'      => 'link_options',
				'default_on_front' => '#',
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
				'toggle_slug'      => 'link_options',
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
				'default_on_front' => 'off',
			),
			'image' => array(
				'label'              => esc_html__( 'Slide Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'configuration',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Slide Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Slide Image', 'et_builder' ),
				'affects'            => array(
					'image_alt',
				),
				'description'        => esc_html__( 'If defined, this slide image will appear to the left of your slide text. Upload an image, or leave blank for a text-only slide.', 'et_builder' ),
				'toggle_slug'        => 'image_video',
				'dynamic_content'    => 'image',
			),
			'use_bg_overlay'      => array(
				'label'           => esc_html__( 'Use Background Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'yes', 'et_builder' ),
				),
				'affects'           => array(
					'bg_overlay_color',
				),
				'default_on_front' => '',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'et_builder' ),
			),
			'bg_overlay_color' => array(
				'label'             => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'description'       => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'et_builder' ),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'use_text_overlay'      => array(
				'label'           => esc_html__( 'Use Text Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'yes', 'et_builder' ),
				),
				'default_on_front' => '',
				'affects'           => array(
					'text_overlay_color',
					'text_border_radius',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'et_builder' ),
			),
			'text_overlay_color' => array(
				'label'             => esc_html__( 'Text Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'et_builder' ),
			),
			'alignment' => array(
				'label'           => esc_html__( 'Slide Image Vertical Alignment', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'center' => esc_html__( 'Center', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom', 'et_builder' ),
				),
				'default_on_front' => 'center',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'This setting determines the vertical alignment of your slide image. Your image can either be vertically centered, or aligned to the bottom of your slide.', 'et_builder' ),
			),
			'video_url' => array(
				'label'           => esc_html__( 'Slide Video', 'et_builder' ),
				'type'            => 'upload',
				'data_type'       => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If defined, this video will appear to the left of your slide text. Enter youtube or vimeo page url, or leave blank for a text-only slide.', 'et_builder' ),
				'toggle_slug'     => 'image_video',
				'computed_affects' => array(
					'__video_embed',
				),
			),
			'image_alt' => array(
				'label'           => esc_html__( 'Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'image',
				),
				'description'     => esc_html__( 'If you have a slide image defined, input your HTML ALT text for the image here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'allow_player_pause' => array(
				'label'           => esc_html__( 'Pause Video When Another Video Plays', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => '',
				'toggle_slug'     => 'player_pause',
				'description'     => esc_html__( 'Allow video to be paused by other players when they begin playing' ,'et_builder' ),
			),
			'content' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your main slide text content here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'arrows_custom_color' => array(
				'label'        => esc_html__( 'Arrows Custom Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'dot_nav_custom_color' => array(
				'label'        => esc_html__( 'Dot Nav Custom Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'navigation',
			),
			'admin_title' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the slide in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'text_border_radius' => array(
				'label'           => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'default'         => '3',
				'default_unit'    => 'px',
				'default_on_front' => '',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
			),
			'__video_embed' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Slider_Item', 'get_video_embed' ),
				'computed_depends_on' => array(
					'video_url',
				),
				'computed_minimum' => array(
					'video_url',
				),
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();
		$fields['background_layout'] = array(
			'background-color' => '%%order_class%% .et_pb_slide_overlay_container, %%order_class%% .et_pb_text_overlay_wrapper',
			'color' => self::$_->array_get( $this->advanced_fields, 'text.css.main', '%%order_class%%' ),
		);

		return $fields;
	}

	static function get_video_embed( $args = array(), $conditonal_args = array(), $current_page = array() ) {
		global $wp_embed;

		$video_url = esc_url( $args['video_url'] );

		$autoembed      = $wp_embed->autoembed( $video_url );
		$is_local_video = has_shortcode( $autoembed, 'video' );
		$video_embed    = '';

		if ( $is_local_video ) {
			$video_embed = wp_video_shortcode( array( 'src' => $video_url ) );
		} else {
			$video_embed = wp_oembed_get( $video_url );

			$video_embed = preg_replace( '/<embed /','<embed wmode="transparent" ', $video_embed );

			$video_embed = preg_replace( '/<\/object>/','<param name="wmode" value="transparent" /></object>', $video_embed );
		}

		return $video_embed;
	}

	function maybe_inherit_values() {
		// Inheriting slider attribute
		global $et_pb_slider;

		// Check if current slide item version is made before Divi v3.2 (UI Improvement release). v3.2 changed default
		// background color for slide item for usability and inheritance mechanism requires custom treatment on FE and VB
		$is_prior_v32 = version_compare( self::$_->array_get( $this->props, '_builder_version', '3.0.47' ), '3.2', '<' );

		// Attribute inheritance should be done on front-end / published page only.
		// Don't run attribute inheritance in VB and Backend to avoid attribute inheritance accidentally being saved on VB / BB
		if ( ! empty( $et_pb_slider ) && ! is_admin() && ! et_fb_is_enabled() ) {
			foreach ( $et_pb_slider as $slider_attr => $slider_attr_value ) {
				// Get default value
				$default = isset( $this->fields_unprocessed[ $slider_attr ][ 'default' ] ) ? $this->fields_unprocessed[ $slider_attr ][ 'default' ] : '';

				// Slide item isn't empty nor default
				if ( ! in_array( self::$_->array_get( $this->props, $slider_attr, '' ), array( '', $default ) ) ) {
					continue;
				}

				// Slider value is equal to empty or slide item's default
				if ( in_array( $slider_attr_value, array( '', $default ) ) ) {
					continue;
				}

				// Overwrite slider item's empty / default value
				$this->props[ $slider_attr ] = $slider_attr_value;
			}
		}

		// In VB, inheritance is done in VB side. However in migrating changing default that is affected by inheritance, the value
		// needs to be modified before being set to avoid sudden color change when _builder_version is bumped when settings modal
		// is opened. This making prior saved value changed but it is the safest option considering old Divi doesn't trim background_color
		if ( ! empty( $et_pb_slider ) && ( is_admin() || et_core_is_fb_enabled() ) && $is_prior_v32 ) {
			$slider_background_color           = self::$_->array_get( $et_pb_slider, 'background_color', '' );
			$is_slide_background_color_empty   = in_array( $this->props['background_color'], array( '', '#ffffff', et_builder_accent_color() ) );
			$is_slider_background_color_filled = '' !== $slider_background_color;

			if ( $is_slide_background_color_empty && $is_slider_background_color_filled ) {
				$this->props['background_color'] = '';
			}
		}
	}

	function render( $attrs, $content = null, $render_slug ) {
		$alignment                       = $this->props['alignment'];
		// Allowing full html for backwards compatibility.
		$heading                         = $this->_esc_attr( 'heading', 'full' );
		$button_text                     = $this->_esc_attr( 'button_text', 'limited' );
		$button_link                     = $this->props['button_link'];
		$url_new_window                  = $this->props['url_new_window'];
		$image                           = $this->props['image'];
		$image_alt                       = $this->props['image_alt'];
		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$video_url                       = $this->props['video_url'];
		$dot_nav_custom_color            = $this->props['dot_nav_custom_color'];
		$arrows_custom_color             = $this->props['arrows_custom_color'];
		$custom_icon                     = $this->props['button_icon'];
		$button_custom                   = $this->props['custom_button'];
		$button_rel                      = $this->props['button_rel'];
		$use_bg_overlay                  = $this->props['use_bg_overlay'];
		$bg_overlay_color                = $this->props['bg_overlay_color'];
		$use_text_overlay                = $this->props['use_text_overlay'];
		$text_overlay_color              = $this->props['text_overlay_color'];
		$text_border_radius              = $this->props['text_border_radius'];
		$header_level                    = $this->props['header_level'];
		$video_background                = $this->video_background();
		$parallax_image_background       = $this->get_parallax_image_background();
		$background_color                = $this->props['background_color'];

		global $et_pb_slider_has_video, $et_pb_slider_parallax, $et_pb_slider_parallax_method, $et_pb_slider_show_mobile, $et_pb_slider_custom_icon, $et_pb_slider_item_num, $et_pb_slider_button_rel;

		$et_pb_slider_item_num++;

		$hide_on_mobile_class = self::HIDE_ON_MOBILE;

		$is_text_overlay_applied = 'on' === $use_text_overlay;

		$custom_slide_icon = 'on' === $button_custom && '' !== $custom_icon ? $custom_icon : $et_pb_slider_custom_icon;

		if ( '' !== $heading ) {
			if ( '#' !== $button_link ) {
				$heading = sprintf( '<a href="%1$s">%2$s</a>',
					esc_url( $button_link ),
					et_core_esc_previously( $heading )
				);
			}

			$heading = sprintf(
				'<%1$s class="et_pb_slide_title">%2$s</%1$s>',
				et_pb_process_header_level( $header_level, 'h2' ),
				et_core_esc_previously( $heading )
			);
		}

		// Overwrite button rel with pricin tables' button_rel if needed
		if ( in_array( $button_rel, array( '', 'off|off|off|off|off' ) ) && '' !== $et_pb_slider_button_rel ) {
			$button_rel = $et_pb_slider_button_rel;
		}

		// render button
		$button_classname = array( 'et_pb_more_button' );

		if ( 'on' !== $et_pb_slider_show_mobile['show_cta_on_mobile'] ) {
			$button_classname[] = $hide_on_mobile_class;
		}

		$button = $this->render_button( array(
			'button_classname'    => $button_classname,
			'button_custom'       => '' !== $custom_slide_icon ? 'on' : 'off',
			'button_rel'          => $button_rel,
			'button_text'         => $button_text,
			'button_text_escaped' => $button_text,
			'button_url'          => $button_link,
			'url_new_window'      => $url_new_window,
			'custom_icon'         => $custom_slide_icon,
			'display_button'      => true,
		) );

		$class = '';

		if ( 'on' === $use_bg_overlay && '' !== $bg_overlay_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_slide .et_pb_slide_overlay_container',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $bg_overlay_color )
				),
			) );
		}

		if ( ! empty( $background_color ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_color )
				),
			) );
		}

		if ( $is_text_overlay_applied && '' !== $text_overlay_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_slide .et_pb_text_overlay_wrapper',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $text_overlay_color )
				),
			) );
		}

		if ( '' !== $text_border_radius ) {
			$border_radius_value = et_builder_process_range_value( $text_border_radius );
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_slider_with_text_overlay .et_pb_text_overlay_wrapper',
				'declaration' => sprintf(
					'border-radius: %1$s;',
					esc_html( $border_radius_value )
				),
			) );
		}

		$image = '' !== $image
			? sprintf( '<div class="et_pb_slide_image"><img src="%1$s" alt="%2$s" /></div>',
				esc_url( $image ),
				esc_attr( $image_alt )
			)
			: '';

		if ( '' !== $video_url ) {
			$video_embed = self::get_video_embed(array(
				'video_url' => $video_url,
			));

			$image = sprintf( '<div class="et_pb_slide_video">%1$s</div>',
				$video_embed
			);
		}

		$data_dot_nav_custom_color = '' !== $dot_nav_custom_color
			? sprintf( ' data-dots_color="%1$s"', esc_attr( $dot_nav_custom_color ) )
			: '';

		$data_arrows_custom_color = '' !== $arrows_custom_color
			? sprintf( ' data-arrows_color="%1$s"', esc_attr( $arrows_custom_color ) )
			: '';

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$this->add_classname( $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
			) );
		}

		// Module classnames
		$this->add_classname( array(
			"et_pb_bg_layout_{$background_layout}",
		) );

		if ( '' !== $image ) {
			$this->add_classname( 'et_pb_slide_with_image' );
		}

		if ( '' !== $video_url ) {
			$this->add_classname( 'et_pb_slide_with_video' );
		}

		if ( 'bottom' !== $alignment ) {
			$this->add_classname( "et_pb_media_alignment_{$alignment}" );
		}

		if ( 'on' === $use_bg_overlay ) {
			$this->add_classname( 'et_pb_slider_with_overlay' );
		}

		if ( 'on' === $use_text_overlay ) {
			$this->add_classname( 'et_pb_slider_with_text_overlay' );
		}

		if ( 1 === $et_pb_slider_item_num ) {
			$this->add_classname( 'et-pb-active-slide' );
		}

		// Remove automatically added classnames
		$this->remove_classname( array(
			'et_pb_module',
		) );

		$slide_content = sprintf(
			'%1$s
				<div class="et_pb_slide_content%3$s">%2$s</div>',
			et_core_esc_previously( $heading ),
			$this->content,
			( 'on' !== $et_pb_slider_show_mobile['show_content_on_mobile'] ? esc_attr( " {$hide_on_mobile_class}" ) : '' )
		);

		//apply text overlay wrapper
		if ( $is_text_overlay_applied ) {
			$slide_content = sprintf(
				'<div class="et_pb_text_overlay_wrapper">
					%1$s
				</div>',
				$slide_content
			);
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

		$output = sprintf(
			'<div class="%4$s"%7$s%8$s%10$s%11$s>
				%6$s
				%9$s
				<div class="et_pb_container clearfix">
					<div class="et_pb_slider_container_inner">
						%3$s
						<div class="et_pb_slide_description">
							%1$s
							%2$s
						</div> <!-- .et_pb_slide_description -->
					</div>
				</div> <!-- .et_pb_container -->
				%5$s
			</div> <!-- .et_pb_slide -->
			',
			$slide_content,
			$button,
			$image,
			$this->module_classname( $render_slug ), // #5
			$video_background,
			$parallax_image_background,
			$data_dot_nav_custom_color,
			$data_arrows_custom_color,
			'on' === $use_bg_overlay ? '<div class="et_pb_slide_overlay_container"></div>' : '', // #10
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover )
		);

		return $output;
	}
}

new ET_Builder_Module_Slider_Item;
