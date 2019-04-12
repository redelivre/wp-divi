<?php

class ET_Builder_Module_Gallery extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Gallery', 'et_builder' );
		$this->plural     = esc_html__( 'Galleries', 'et_builder' );
		$this->slug       = 'et_pb_gallery';
		$this->vb_support = 'on';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Images', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'  => esc_html__( 'Layout', 'et_builder' ),
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'image' => array(
						'title' => esc_html__( 'Image', 'et_builder' ),
					),
					'text'    => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
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

		$this->main_css_element = '%%order_class%%.et_pb_gallery';
		$this->advanced_fields = array(
			'fonts'                 => array(
				'title'   => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_gallery_title",
					),
					'header_level' => array(
						'default' => 'h3',
					),
				),
				'caption' => array(
					'label'    => esc_html__( 'Caption', 'et_builder' ),
					'use_all_caps' => true,
					'css'      => array(
						'main' => "{$this->main_css_element} .mfp-title, {$this->main_css_element} .et_pb_gallery_caption",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'depends_show_if'   => 'off',
				),
				'pagination' => array(
					'label' => esc_html__( 'Pagination', 'et_builder' ),
					'css' => array(
						'main'       => "{$this->main_css_element} .et_pb_gallery_pagination a",
						'text_align' => "{$this->main_css_element} .et_pb_gallery_pagination ul",
					),
					'text_align' => array(
						'options' => et_builder_get_text_orientation_options( array( 'justified' ), array() ),
					),
				),
			),
			'borders'               => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_gallery_item",
							'border_styles' => "{$this->main_css_element} .et_pb_gallery_item",
						),
					),
				),
				'image' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_gallery_image",
							'border_styles' => "{$this->main_css_element} .et_pb_gallery_image",
						)
					),
					'label_prefix'    => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'depends_on'      => array( 'fullwidth' ),
					'depends_show_if' => 'off',
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'show_if' => array(
						'fullwidth' => 'on',
					),
				),
				'image'   => array(
					'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'show_if'         => array(
						'fullwidth' => 'off',
					),
					'css' => array(
						'main'         => '%%order_class%% .et_pb_gallery_image',
						'overlay' => 'inset',
					),
					'default_on_fronts'  => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'max_width'             => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_gallery.et_pb_module',
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'css'   => array(
					'main' => implode(', ', array(
						"{$this->main_css_element} .et_pb_gallery_title",
						"{$this->main_css_element} .mfp-title",
						"{$this->main_css_element} .et_pb_gallery_caption",
						"{$this->main_css_element} .et_pb_gallery_pagination a",
					)),
					'text_shadow' => "{$this->main_css_element}.et_pb_gallery_grid",
				),
				'options' => array(
					'background_layout' => array(
						'default' => 'light',
						'hover' => 'tabs',
					),
				),
			),
			'filters'               => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'                 => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_gallery_image',
				),
			),
			'button' => false,
		);

		$this->custom_css_fields = array(
			'gallery_item' => array(
				'label'       => esc_html__( 'Gallery Item', 'et_builder' ),
				'selector'    => '.et_pb_gallery_item',
			),
			'overlay' => array(
				'label'       => esc_html__( 'Overlay', 'et_builder' ),
				'selector'    => '.et_overlay',
			),
			'overlay_icon' => array(
				'label'       => esc_html__( 'Overlay Icon', 'et_builder' ),
				'selector'    => '.et_overlay:before',
			),
			'gallery_item_title' => array(
				'label'       => esc_html__( 'Gallery Item Title', 'et_builder' ),
				'selector'    => '.et_pb_gallery_title',
			),
			'gallery_item_caption' => array(
				'label'       => esc_html__( 'Gallery Item Caption', 'et_builder' ),
				'selector'    => '.et_pb_gallery_caption',
			),
			'gallery_pagination' => array(
				'label'       => esc_html__( 'Gallery Pagination', 'et_builder' ),
				'selector'    => '.et_pb_gallery_pagination',
			),
			'gallery_pagination_active' => array(
				'label'       => esc_html__( 'Pagination Active Page', 'et_builder' ),
				'selector'    => '.et_pb_gallery_pagination a.active',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'BRjX-pNHk-s' ),
				'name' => esc_html__( 'An introduction to the Gallery module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'gallery_ids' => array(
				'label'            => esc_html__( 'Gallery Images', 'et_builder' ),
				'type'             => 'upload-gallery',
				'computed_affects' => array(
					'__gallery',
				),
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
			),
			'gallery_orderby' => array(
				'label'   => esc_html__( 'Order By', 'et_builder' ),
				'type'    => $this->is_loading_bb_data() ? 'hidden' : 'select',
				'options' => array(
					''     => esc_html__( 'Default', 'et_builder' ),
					'rand' => esc_html__( 'Random', 'et_builder' ),
				),
				'default' => 'off',
				'class'   => array( 'et-pb-gallery-ids-field' ),
				'computed_affects' => array(
					'__gallery',
				),
				'toggle_slug' => 'main_content',
			),
			'gallery_captions' => array(
				'type'  => 'hidden',
				'class' => array( 'et-pb-gallery-captions-field' ),
				'computed_affects'   => array(
					'__gallery',
				),
			),
			'fullwidth' => array(
				'label'             => esc_html__( 'Layout', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Grid', 'et_builder' ),
					'on'  => esc_html__( 'Slider', 'et_builder' ),
				),
				'default_on_front'  => 'off',
				'description'       => esc_html__( 'Toggle between the various blog layout types.', 'et_builder' ),
				'affects'           => array(
					'zoom_icon_color',
					'caption_font',
					'caption_text_color',
					'caption_line_height',
					'caption_font_size',
					'caption_all_caps',
					'caption_letter_spacing',
					'hover_overlay_color',
					'auto',
					'posts_number',
					'show_title_and_caption',
					'show_pagination',
					'orientation',
					'border_radii_image',
					'border_styles_image',
				),
				'computed_affects'   => array(
					'__gallery',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'layout',
			),
			'posts_number' => array(
				'default'           => 4,
				'label'             => esc_html__( 'Images Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Define the number of images that should be displayed per page.', 'et_builder' ),
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'main_content',
			),
			'orientation'            => array(
				'label'              => esc_html__( 'Thumbnail Orientation', 'et_builder' ),
				'type'               => 'select',
				'options_category'   => 'configuration',
				'options'            => array(
					'landscape' => esc_html__( 'Landscape', 'et_builder' ),
					'portrait'  => esc_html__( 'Portrait', 'et_builder' ),
				),
				'default_on_front'            => 'landscape',
				'description'        => sprintf(
					'%1$s<br><small><em><strong>%2$s:</strong> %3$s <a href="//wordpress.org/plugins/force-regenerate-thumbnails" target="_blank">%4$s</a>.</em></small>',
					esc_html__( 'Choose the orientation of the gallery thumbnails.', 'et_builder' ),
					esc_html__( 'Note', 'et_builder' ),
					esc_html__( 'If this option appears to have no effect, you might need to', 'et_builder' ),
					esc_html__( 'regenerate your thumbnails', 'et_builder' )
				),
				'depends_show_if'    => 'off',
				'computed_affects'   => array(
					'__gallery',
				),
				'tab_slug'           => 'advanced',
				'toggle_slug'        => 'layout',
			),
			'show_title_and_caption' => array(
				'label'              => esc_html__( 'Show Title and Caption', 'et_builder' ),
				'type'               => 'yes_no_button',
				'option_category'    => 'configuration',
				'options'            => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front'   => 'on',
				'description'        => esc_html__( 'Whether or not to show the title and caption for images (if available).', 'et_builder' ),
				'depends_show_if'    => 'off',
				'toggle_slug'        => 'elements',
			),
			'show_pagination' => array(
				'label'             => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front'  => 'on',
				'description'       => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'elements',
				'computed_affects'  => array(
					'__gallery',
				),
			),
			'zoom_icon_color' => array(
				'label'             => esc_html__( 'Zoom Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'__gallery' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Gallery', 'get_gallery' ),
				'computed_depends_on' => array(
					'gallery_ids',
					'gallery_orderby',
					'gallery_captions',
					'fullwidth',
					'orientation',
					'show_pagination',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get attachment data for gallery module
	 *
	 * @param array $args {
	 *     Gallery Options
	 *
	 *     @type array  $gallery_ids     Attachment Ids of images to be included in gallery.
	 *     @type string $gallery_orderby `orderby` arg for query. Optional.
	 *     @type string $fullwidth       on|off to determine grid / slider layout
	 *     @type string $orientation     Orientation of thumbnails (landscape|portrait).
	 * }
	 * @param array $conditional_tags
	 * @param array $current_page
	 *
	 * @return array Attachments data
	 */
	static function get_gallery( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$attachments = array();

		$defaults = array(
			'gallery_ids'      => array(),
			'gallery_orderby'  => '',
			'gallery_captions' => array(),
			'fullwidth'        => 'off',
			'orientation'      => 'landscape',
		);

		$args = wp_parse_args( $args, $defaults );

		$attachments_args = array(
			'include'        => $args['gallery_ids'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'post__in',
		);

		if ( 'rand' === $args['gallery_orderby'] ) {
			$attachments_args['orderby'] = 'rand';
		}

		if ( 'on' === $args['fullwidth'] ) {
			$width  = 1080;
			$height = 9999;
		} else {
			$width  =  400;
			$height = ( 'landscape' === $args['orientation'] ) ? 284 : 516;
		}

		$width  = (int) apply_filters( 'et_pb_gallery_image_width', $width );
		$height = (int) apply_filters( 'et_pb_gallery_image_height', $height );

		$_attachments = get_posts( $attachments_args );

		foreach ( $_attachments as $key => $val ) {
			$attachments[$key] = $_attachments[$key];
			$attachments[$key]->image_src_full  = wp_get_attachment_image_src( $val->ID, 'full' );
			$attachments[$key]->image_src_thumb = wp_get_attachment_image_src( $val->ID, array( $width, $height ) );
		}

		return $attachments;
	}

	public function get_pagination_alignment() {
		$text_orientation = isset( $this->props['pagination_text_align'] ) ? $this->props['pagination_text_align'] : '';

		return et_pb_get_alignment( $text_orientation );
	}

	function render( $attrs, $content = null, $render_slug ) {
		$gallery_ids                     = $this->props['gallery_ids'];
		$fullwidth                       = $this->props['fullwidth'];
		$show_title_and_caption          = $this->props['show_title_and_caption'];
		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$posts_number                    = $this->props['posts_number'];
		$show_pagination                 = $this->props['show_pagination'];
		$gallery_orderby                 = $this->props['gallery_orderby'];
		$zoom_icon_color                 = $this->props['zoom_icon_color'];
		$hover_overlay_color             = $this->props['hover_overlay_color'];
		$hover_icon                      = $this->props['hover_icon'];
		$auto                            = $this->props['auto'];
		$auto_speed                      = $this->props['auto_speed'];
		$orientation                     = $this->props['orientation'];
		$pagination_text_align           = $this->get_pagination_alignment();
		$header_level                    = $this->props['title_level'];

		if ( '' !== $zoom_icon_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_overlay:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $zoom_icon_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_overlay',
				'declaration' => sprintf(
					'background-color: %1$s;
					border-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		// Get gallery item data
		$attachments = self::get_gallery( array(
			'gallery_ids'     => $gallery_ids,
			'gallery_orderby' => $gallery_orderby,
			'fullwidth'       => $fullwidth,
			'orientation'     => $orientation,
		) );

		if ( empty( $attachments ) ) {
			return '';
		}

		wp_enqueue_script( 'hashchange' );

		$background_class          = "et_pb_bg_layout_{$background_layout}";
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$posts_number              = 0 === intval( $posts_number ) ? 4 : intval( $posts_number );

		// Module classnames
		$this->add_classname( array(
			$background_class,
			$this->get_text_orientation_classname(),
		) );

		if ( 'on' === $fullwidth ) {
			$this->add_classname( array(
				'et_pb_slider',
				'et_pb_gallery_fullwidth',
			) );
		} else {
			$this->add_classname( 'et_pb_gallery_grid' );
		}

		if ( 'on' === $auto && 'on' === $fullwidth ) {
			$this->add_classname( array(
				'et_slider_auto',
				"et_slider_speed_{$auto_speed}",
				'clearfix',
			) );
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
			'<div%1$s class="%2$s"%4$s%5$s>
				<div class="et_pb_gallery_items et_post_gallery clearfix" data-per_page="%3$d">',
			$this->module_id(),
			$this->module_classname( $render_slug ),
			esc_attr( $posts_number ),
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover ) // #5
		);

		$output .= $video_background;
		$output .= $parallax_image_background;

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$generate_css_filters_item = $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
			);
		}

		foreach ( $attachments as $id => $attachment ) {
			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$image_output = sprintf(
				'<a href="%1$s" title="%2$s">
					<img src="%3$s" alt="%2$s" />
					<span class="et_overlay%4$s"%5$s></span>
				</a>',
				esc_url( $attachment->image_src_full[0] ),
				esc_attr( $attachment->post_title ),
				esc_url( $attachment->image_src_thumb[0] ),
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);

			$output .= sprintf(
				'<div class="et_pb_gallery_item%2$s%1$s%3$s">',
				esc_attr( ' ' . $background_class ),
				( 'on' !== $fullwidth ? ' et_pb_grid_item' : '' ),
				$generate_css_filters_item
			);
			$output .= "
				<div class='et_pb_gallery_image {$orientation}'>
					$image_output
				</div>";

			if ( 'on' !== $fullwidth && 'on' === $show_title_and_caption ) {
				if ( trim( $attachment->post_title ) ) {
					$output .= sprintf( '<%2$s class="et_pb_gallery_title">%1$s</%2$s>', wptexturize( $attachment->post_title ), et_pb_process_header_level( $header_level, 'h3' ) );
				}
				if ( trim( $attachment->post_excerpt ) ) {
					$output .= "
						<p class='et_pb_gallery_caption'>
						" . wptexturize( $attachment->post_excerpt ) . "
						</p>";
				}
			}
			$output .= "</div>";
		}

		$output .= "</div><!-- .et_pb_gallery_items -->";

		if ( 'on' !== $fullwidth && 'on' === $show_pagination ) {
			$output .= sprintf(
				'<div class="et_pb_gallery_pagination%1$s"></div>',
				$pagination_text_align === 'justify' ? ' et_pb_gallery_pagination_justify' : ''
			);
		}

		$output .= "</div><!-- .et_pb_gallery -->";

		return $output;
	}
}

new ET_Builder_Module_Gallery;
