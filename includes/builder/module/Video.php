<?php

class ET_Builder_Module_Video extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Video', 'et_builder' );
		$this->plural     = esc_html__( 'Videos', 'et_builder' );
		$this->slug       = 'et_pb_video';
		$this->vb_support = 'on';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Video', 'et_builder' ),
					'overlay'      => esc_html__( 'Overlay', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'play_icon' => esc_html__( 'Play Icon', 'et_builder' ),
					'overlay'   => esc_html__( 'Overlay', 'et_builder' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'video_icon' => array(
				'label'    => esc_html__( 'Video Icon', 'et_builder' ),
				'selector' => '.et_pb_video_play',
			),
		);

		$this->advanced_fields = array(
			'background'            => array(
				'options' => array(
					'background_color' => array(
						'depends_on'      => array(
							'custom_padding',
						),
						'depends_on_responsive' => array(
							'custom_padding',
						),
						'depends_show_if_not' => array(
							'',
							'|||',
						),
						'is_toggleable' => true,
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
				'custom_padding' => array(
					'responsive_affects' => array(
						'background_color',
					),
				),
			),
			'fonts'                 => false,
			'text'                  => false,
			'button'                => false,
			'link_options'          => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( '3jXN8CBz0TU' ),
				'name' => esc_html__( 'An introduction to the Video module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Video MP4 File Or Youtube URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects' => array(
					'__video',
				),
			),
			'src_webm' => array(
				'label'              => esc_html__( 'Video WEBM File', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload the .WEBM version of your video here. All uploaded videos should be in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects' => array(
					'__video',
				),
			),
			'image_src' => array(
				'label'              => esc_html__( 'Overlay Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'additional_button'  => sprintf(
					'<input type="button" class="button et-pb-video-image-button" value="%1$s" />',
					esc_attr__( 'Generate Image From Video', 'et_builder' )
				),
				'additional_button_type' => 'generate_image_url_from_video',
				'additional_button_attrs' => array(
					'video_source' => 'src',
				),
				'classes'            => 'et_pb_video_overlay',
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display over your video. You can also generate a still image from your video.', 'et_builder' ),
				'toggle_slug'        => 'overlay',
				'computed_affects' => array(
					'__video_cover_src',
				),
				'dynamic_content'   => 'image',
			),
			'play_icon_color' => array(
				'label'             => esc_html__( 'Play Icon Color', 'et_builder' ),
				'description'       => esc_html__( 'Here you can define a custom color for the play icon.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'play_icon',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'__video' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Video', 'get_video' ),
				'computed_depends_on' => array(
					'src',
					'src_webm',
				),
				'computed_minimum' => array(
					'src',
					'src_webm',
				),
			),
			'__video_cover_src' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Video', 'get_video_cover_src' ),
				'computed_depends_on' => array(
					'image_src',
				),
				'computed_minimum' => array(
					'image_src',
				),
			),
			'use_icon_font_size'      => array(
				'label'            => esc_html__( 'Use Custom Icon Size', 'et_builder' ),
				'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'icon_font_size',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'play_icon',
				'option_category'  => 'font_option',
			),
			'icon_font_size'          => array(
				'label'            => esc_html__( 'Play Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'play_icon',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '96px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'depends_show_if'  => 'on',
				'responsive'       => true,
				'hover'            => 'tabs',
			),
			'thumbnail_overlay_color' => array(
				'label'            => esc_html__( 'Overlay Background Color', 'et_builder' ),
				'description'      => esc_html__( 'Pick a color to use for the overlay that appears behind the play icon when hovering over the video.', 'et_builder' ),
				'type'             => 'color-alpha',
				'custom_color'     => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'default_on_front' => 'rgba(0,0,0,.6)',
				'mobile_options'   => true,
			),

		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['play_icon_color'] = array( 'color' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play' );
		$fields['icon_font_size']  = array(
			'font-size'   => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
			'line-height' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
			'margin-top'  => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
			'margin-left' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play',
		);

		return $fields;
	}

	static function get_video( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'src'      => '',
			'src_webm' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$video_src = '';

		if ( false !== et_pb_check_oembed_provider( esc_url( $args['src'] ) ) ) {
			$video_src = wp_oembed_get( esc_url( $args['src'] ) );
		} else {
			$video_src = sprintf( '
				<video controls>
					%1$s
					%2$s
				</video>',
				( '' !== $args['src'] ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $args['src'] ) ) : '' ),
				( '' !== $args['src_webm'] ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $args['src_webm'] ) ) : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		return $video_src;
	}

	static function get_video_cover_src( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$post_id = isset( $current_page['id'] ) ? $current_page['id'] : self::get_current_post_id();
		$defaults = array(
			'image_src' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( isset( $args['image_src'] ) ) {
			$dynamic_value = et_builder_parse_dynamic_content( stripslashes( $args['image_src'] ) );
			if ( $dynamic_value->is_dynamic() && current_user_can( 'edit_post', $post_id ) ) {
				$args['image_src'] = $dynamic_value->resolve( $post_id );
			}
		}

		$image_output = '';

		if ( '' !== $args['image_src'] ) {
			$image_output = et_pb_set_video_oembed_thumbnail_resolution( $args['image_src'], 'high' );
		}

		return $image_output;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$src                      = $this->props['src'];
		$src_webm                 = $this->props['src_webm'];
		$image_src                = $this->props['image_src'];
		$use_icon_font_size       = $this->props['use_icon_font_size'];
		$play_icon_color_values   = et_pb_responsive_options()->get_property_values( $this->props, 'play_icon_color' );
		$play_icon_color_hover    = $this->get_hover_value( 'play_icon_color' );
		$icon_font_size_values    = et_pb_responsive_options()->get_property_values( $this->props, 'icon_font_size' );
		$icon_font_size_hover     = $this->get_hover_value( 'icon_font_size' );
		$thumbnail_overlay_colors = et_pb_responsive_options()->get_property_values( $this->props, 'thumbnail_overlay_color' );

		$video_src       = self::get_video( array(
			'src'      => $src,
			'src_webm' => $src_webm,
		) );

		$image_output = self::get_video_cover_src( array(
			'image_src' => $image_src,
		) );

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Play Icon color.
		et_pb_responsive_options()->generate_responsive_css( $play_icon_color_values, '%%order_class%% .et_pb_video_overlay .et_pb_video_play', 'color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'play_icon_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector' => '%%order_class%% .et_pb_video_overlay .et_pb_video_play:hover',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $play_icon_color_hover )
				),
			) );
		}

		// Icon Size.
		$icon_selector = '%%order_class%% .et_pb_video_overlay .et_pb_video_play';
		if ( 'off' !== $use_icon_font_size ) {
			// Proccess for each devices.
			foreach ( $icon_font_size_values as $font_size_key => $font_size_value ) {
				if ( '' === $font_size_value ) {
					continue;
				}

				$media_query = 'general';
				if ( 'tablet' === $font_size_key ) {
					$media_query = ET_Builder_Element::get_media_query( 'max_width_980' );
				} elseif ( 'phone' === $font_size_key ) {
					$media_query = ET_Builder_Element::get_media_query( 'max_width_767' );
				}

				$font_size_value_int  = (int) $font_size_value;
				$font_size_value_unit = str_replace( $font_size_value_int, '', $font_size_value );
				$font_size_value_half = 0 < $font_size_value_int ? $font_size_value_int / 2 : 0;
				$font_size_value_half = (string) $font_size_value_half . $font_size_value_unit;
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => $icon_selector,
					'declaration' => sprintf(
						'font-size:%1$s; line-height:%1$s; margin-top:-%2$s; margin-left:-%2$s;',
						esc_html( $font_size_value ),
						esc_html( $font_size_value_half )
					),
					'media_query' => $media_query,
				) );
			}

			// Icon hover styles.
			if ( et_builder_is_hover_enabled( 'icon_font_size', $this->props ) && '' !== $icon_font_size_hover ) {
				$icon_font_size_hover_int  = (int) $icon_font_size_hover;
				$icon_font_size_hover_unit = str_replace( $icon_font_size_hover_int, '', $icon_font_size_hover );
				$icon_font_size_hover_half = 0 < $icon_font_size_hover_int ? $icon_font_size_hover_int / 2 : 0;
				$icon_font_size_hover_half = (string) $icon_font_size_hover_half . $icon_font_size_hover_unit;
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => $this->add_hover_to_selectors( $icon_selector ),
					'declaration' => sprintf(
						'font-size:%1$s; line-height:%1$s; margin-top:-%2$s; margin-left:-%2$s;',
						esc_html( $icon_font_size_hover ),
						esc_html( $icon_font_size_hover_half )
					),
				) );
			}
		}

		// Thumbnail Overlay Color.
		et_pb_responsive_options()->generate_responsive_css( $thumbnail_overlay_colors, '%%order_class%% .et_pb_video_overlay_hover:hover', 'background-color', $render_slug, '', 'color' );

		$output = sprintf(
			'<div%2$s class="%3$s">
				%6$s
				%5$s
				<div class="et_pb_video_box">
					%1$s
				</div>
				%4$s
			</div>',
			( '' !== $video_src ? $video_src : '' ),
			$this->module_id(),
			$this->module_classname( $render_slug ),
			( '' !== $image_output
				? sprintf(
					'<div class="et_pb_video_overlay" style="background-image: url(%1$s);">
						<div class="et_pb_video_overlay_hover">
							<a href="#" class="et_pb_video_play"></a>
						</div>
					</div>',
					esc_attr( $image_output )
				)
				: ''
			),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Video;
