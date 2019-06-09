<?php

class ET_Builder_Module_Video_Slider_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Video', 'et_builder' );
		$this->plural                      = esc_html__( 'Videos', 'et_builder' );
		$this->slug                        = 'et_pb_video_slider_item';
		$this->vb_support 				   = 'on';
		$this->type                        = 'child';
		$this->custom_css_tab              = false;
		$this->child_title_var             = 'admin_title';
		$this->advanced_setting_title_text = esc_html__( 'New Video', 'et_builder' );
		$this->settings_text               = esc_html__( 'Video Settings', 'et_builder' );

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Video', 'et_builder' ),
					'overlay'      => esc_html__( 'Overlay', 'et_builder' ),
					'admin_label'  => esc_html__( 'Admin Label', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'arrows_color' => esc_html__( 'Arrows Color', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'text'                  => array(
				'use_text_orientation'  => false,
				'use_background_layout' => true,
				'options' => array(
					'background_layout' => array(
						'label'            => esc_html__( 'Slider Arrows Color', 'et_builder' ),
						'option_category'  => 'color_option',
						'toggle_slug'      => 'arrows_color',
						'description'      => esc_html__( 'This setting will make your slider arrows either light or dark in color.', 'et_builder' ),
						'default'          => 'dark',
						'default_on_child' => true,
						'hover'            => 'tabs',
					),
				),
			),
			'box_shadow'            => array(
				'default' => false,
			),
			'borders'               => array(
				'default' => false,
			),
			'text_shadow'           => array(
				'default' => false,
			),
			'background'            => false,
			'fonts'                 => false,
			'max_width'             => false,
			'height'                => false,
			'margin_padding' => false,
			'button'                => false,
			'link_options'          => false,
		);
	}

	function get_fields() {
		$fields = array(
			'admin_title' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the video in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'src' => array(
				'label'              => esc_html__( 'Video MP4/URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'computed_affects' => array(
					'__get_oembed',
					'__oembed_thumbnail',
					'__is_oembed',
				),
			),
			'src_webm' => array(
				'label'              => esc_html__( 'Video Webm', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Video', 'et_builder' ),
				'description'        => esc_html__( 'Upload the .WEBM version of your video here. All uploaded videos should be in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
			),
			'image_src' => array(
				'label'              => esc_html__( 'Image Overlay URL', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'additional_button'  => sprintf(
					'<input type="button" class="button et-pb-video-image-button" value="%1$s" />',
					esc_attr__( 'Generate From Video', 'et_builder' )
				),
				'additional_button_type' => 'generate_image_url_from_video',
				'additional_button_attrs' => array(
					'video_source' => 'src',
				),
				'classes'            => 'et_pb_video_overlay',
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display over your video. You can also generate a still image from your video.', 'et_builder' ),
				'toggle_slug'        => 'overlay',
				'dynamic_content'    => 'image',
			),
			'__oembed_thumbnail' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Video_Slider_Item', 'get_oembed_thumbnail' ),
				'computed_depends_on' => array(
					'src',
					'image_src',
				),
				'computed_minimum' => array(
					'src',
				),
			),
			'__is_oembed' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Video_Slider_Item', 'is_oembed' ),
				'computed_depends_on' => array(
					'src',
				),
				'computed_minimum' => array(
					'src',
				),
			),
			'__get_oembed' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Video_Slider_Item', 'get_oembed' ),
				'computed_depends_on' => array(
					'src',
				),
				'computed_minimum' => array(
					'src',
				),
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['background_layout'] = array( 'color' => '%%order_class%% .et-pb-arrow-prev, %%order_class%% .et-pb-arrow-next' );

		return $fields;
	}

	protected static function resolve_oembed_thumbnail( $src, $post_id ) {
		$dynamic_value = et_builder_parse_dynamic_content( $src );
		if ( $dynamic_value->is_dynamic() && current_user_can( 'edit_post', $post_id ) ) {
			return $dynamic_value->resolve( $post_id );
		}

		return $src;
	}

	static function get_oembed_thumbnail( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$post_id = isset( $current_page['id'] ) ? $current_page['id'] : self::get_current_post_id();
		$defaults = array(
			'image_src' => '',
			'src' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( '' !== $args['image_src'] ) {
			return et_pb_set_video_oembed_thumbnail_resolution(
				self::resolve_oembed_thumbnail( $args['image_src'], $post_id ),
				'high'
			);
		} else {
			if ( false !== et_pb_check_oembed_provider( esc_url( $args['src'] ) ) ) {
				add_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
				// Save thumbnail
				$thumbnail_track_output = wp_oembed_get( esc_url( $args['src'] ) );
				// Set back to normal
				remove_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
				return $thumbnail_track_output;
			} else {
				return '';
			}
		}
	}

	static function is_oembed( $args = array(), $conditional_tags = array(), $current_page = array() ){
		$defaults = array(
			'src'
		);

		$args = wp_parse_args( $args, $defaults );

		return et_pb_check_oembed_provider( esc_url( $args['src'] ) );
	}

 	static function get_oembed( $args = array(), $conditional_tags = array(), $current_page = array() ) {
 		$defaults = array(
 			'src' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// Save thumbnail
		$thumbnail_track_output = wp_oembed_get( esc_url( $args['src'] ) );

		return $thumbnail_track_output;
 	}

	function render( $attrs, $content = null, $render_slug ) {
		$src                             = $this->props['src'];
		$src_webm                        = $this->props['src_webm'];
		$image_src                       = $this->props['image_src'];
		$video_src                       = '';

		// Responsive background layout.
		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet        = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone         = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';

		global $et_pb_slider_image_overlay;

		if ( '' !== $image_src ) {
			$image_overlay_output = et_pb_set_video_oembed_thumbnail_resolution( $image_src, 'high' );
			$thumbnail_track_output = $image_src;
		} else {
			$image_overlay_output = '';
			if ( false !== et_pb_check_oembed_provider( esc_url( $src ) ) ) {
				add_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
				// Save thumbnail
				$thumbnail_track_output = wp_oembed_get( esc_url( $src ) );
				$image_overlay_output = $thumbnail_track_output;
				// Set back to normal
				remove_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
			} else {
				$thumbnail_track_output = '';
			}
		}

		if ( '' !== $src ) {
			if ( false !== et_pb_check_oembed_provider( esc_url( $src ) ) ) {
				$video_src = wp_oembed_get( esc_url( $src ) );
			} else {
				$video_src = sprintf( '
					<video controls>
						%1$s
						%2$s
					</video>',
					( '' !== $src ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $src ) ) : '' ),
					( '' !== $src_webm ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $src_webm ) ) : '' )
				);

				wp_enqueue_style( 'wp-mediaelement' );
				wp_enqueue_script( 'wp-mediaelement' );
			}
		}

		$video_output = sprintf(
			'<div class="et_pb_video_wrap">
				<div class="et_pb_video_box">
					%1$s
				</div>
				%2$s
			</div>',
			( '' !== $video_src ? $video_src : '' ),
			(
				( '' !== $image_overlay_output && $et_pb_slider_image_overlay === 'on' )
					? sprintf(
						'<div class="et_pb_video_overlay" style="background-image: url(%1$s);">
							<div class="et_pb_video_overlay_hover">
								<a href="#" class="et_pb_video_play"></a>
							</div>
						</div>',
						esc_attr( $image_overlay_output )
					)
					: ''
			)
		);

		// Module classnames
		$this->add_classname( array(
			'et_pb_slide',
			"et_pb_bg_layout_{$background_layout}",
		) );

		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}

		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		// Remove automatically added classnames
		$this->remove_classname( array(
			'et_pb_module',
			$render_slug,
		) );

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
			'<div class="%1$s"%3$s%4$s%5$s>
				%2$s
			</div> <!-- .et_pb_slide -->
			',
			$this->module_classname( $render_slug ),
			( '' !== $video_output ? $video_output : '' ),
			( '' !== $thumbnail_track_output ? sprintf( ' data-image="%1$s"', esc_attr( $thumbnail_track_output ) ) : '' ),
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover ) // #5
		);

		return $output;
	}
}

new ET_Builder_Module_Video_Slider_Item;
