<?php

class ET_Builder_Module_Gallery extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Gallery', 'et_builder' );
		$this->slug       = 'et_pb_gallery';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'src',
			'gallery_ids',
			'gallery_orderby',
			'gallery_captions',
			'fullwidth',
			'posts_number',
			'show_title_and_caption',
			'show_pagination',
			'background_layout',
			'auto',
			'auto_speed',
			'admin_label',
			'module_id',
			'module_class',
			'zoom_icon_color',
			'hover_overlay_color',
			'hover_icon',
			'orientation',
			'box_shadow_style_image',
			'box_shadow_horizontal_image',
			'box_shadow_vertical_image',
			'box_shadow_blur_image',
			'box_shadow_spread_image',
			'box_shadow_color_image',
			'box_shadow_position_image',
		);

		$this->fields_defaults = array(
			'fullwidth'              => array( 'off' ),
			'posts_number'           => array( 4, 'add_default_setting' ),
			'show_title_and_caption' => array( 'on' ),
			'show_pagination'        => array( 'on' ),
			'background_layout'      => array( 'light' ),
			'auto'                   => array( 'off' ),
			'auto_speed'             => array( '7000' ),
			'orientation'            => array( 'landscape' ),
		);

		$this->options_toggles = array(
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
		$this->advanced_options = array(
			'fonts' => array(
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
			'background' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'max_width' => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_gallery.et_pb_module',
				),
			),
			'text'      => array(
				'css'   => array(
					'text_shadow' => "{$this->main_css_element}.et_pb_gallery_grid",
				),
			),
			'filters' => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image' => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_gallery_image',
				),
			),
		);

		$this->custom_css_options = array(
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
	}

	function get_fields() {
		$fields = array(
			'src' => array(
				'label'           => esc_html__( 'Gallery Images', 'et_builder' ),
				'renderer'        => 'et_builder_get_gallery_settings',
				'option_category' => 'basic_option',
				'overwrite'       => array(
					'ids'         => 'gallery_ids',
					'orderby'     => 'gallery_orderby',
					'captions'    => 'gallery_captions',
				),
				'toggle_slug'     => 'main_content',
			),
			'gallery_ids' => array(
				'type'  => 'hidden',
				'class' => array( 'et-pb-gallery-ids-field' ),
				'computed_affects'   => array(
					'__gallery',
				),
			),
			'gallery_orderby' => array(
				'label' => esc_html__( 'Gallery Images', 'et_builder' ),
				'type'  => 'hidden',
				'class' => array( 'et-pb-gallery-ids-field' ),
				'computed_affects'   => array(
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
					'box_shadow_style_image',
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
				'description'       => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'elements',
				'computed_affects'  => array(
					'__gallery',
				),
			),
			'background_layout' => array(
				'label'             => esc_html__( 'Text Color', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'color_option',
				'options'           => array(
					'light'  => esc_html__( 'Dark', 'et_builder' ),
					'dark' => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'text',
				'description'       => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
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
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
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

		$fields = array_merge( $fields, ET_Builder_Module_Fields_Factory::get( 'BoxShadow' )->get_fields( array(
			'suffix'          => '_image',
			'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'image',
			'depends_show_if' => 'off',
		) ) );

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
		$text_orientation = isset( $this->shortcode_atts['pagination_text_align'] ) ? $this->shortcode_atts['pagination_text_align'] : '';

		return et_pb_get_alignment( $text_orientation );
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id              = $this->shortcode_atts['module_id'];
		$module_class           = $this->shortcode_atts['module_class'];
		$gallery_ids            = $this->shortcode_atts['gallery_ids'];
		$fullwidth              = $this->shortcode_atts['fullwidth'];
		$show_title_and_caption = $this->shortcode_atts['show_title_and_caption'];
		$background_layout      = $this->shortcode_atts['background_layout'];
		$posts_number           = $this->shortcode_atts['posts_number'];
		$show_pagination        = $this->shortcode_atts['show_pagination'];
		$gallery_orderby        = $this->shortcode_atts['gallery_orderby'];
		$zoom_icon_color        = $this->shortcode_atts['zoom_icon_color'];
		$hover_overlay_color    = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon             = $this->shortcode_atts['hover_icon'];
		$auto                   = $this->shortcode_atts['auto'];
		$auto_speed             = $this->shortcode_atts['auto_speed'];
		$orientation            = $this->shortcode_atts['orientation'];
		$pagination_text_align  = $this->get_pagination_alignment();
		$header_level           = $this->shortcode_atts['title_level'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $zoom_icon_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $zoom_icon_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
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

		$fullwidth_class = 'on' === $fullwidth ? ' et_pb_slider et_pb_gallery_fullwidth' : ' et_pb_gallery_grid';
		$background_class = " et_pb_bg_layout_{$background_layout}";

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$module_class .= 'on' === $auto && 'on' === $fullwidth ? ' et_slider_auto et_slider_speed_' . esc_attr( $auto_speed ) : '';

		$posts_number = 0 === intval( $posts_number ) ? 4 : intval( $posts_number );

		$output = sprintf(
			'<div%1$s class="et_pb_module et_pb_gallery%2$s%3$s%4$s%6$s%7$s%8$s clearfix">
				<div class="et_pb_gallery_items et_post_gallery clearfix" data-per_page="%5$d">',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			esc_attr( $fullwidth_class ),
			esc_attr( $background_class ),
			esc_attr( $posts_number ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$this->get_text_orientation_classname()
		);

		$output .= $video_background;
		$output .= $parallax_image_background;

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_options ) && array_key_exists( 'css', $this->advanced_options['image'] ) ) {
			$generate_css_filters_item = $this->generate_css_filters(
				$function_name,
				'child_',
				self::$data_utils->array_get( $this->advanced_options['image']['css'], 'main', '%%order_class%%' )
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
				esc_attr( $background_class ),
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

	public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );

		if ( isset( $this->shortcode_atts['fullwidth']) && $this->shortcode_atts['fullwidth'] === 'on' ) {
			self::set_style( $function_name, $boxShadow->get_style(
				'.' . self::get_module_order_class( $function_name ),
				$this->shortcode_atts
			) );
			return;
		}

		$selector = sprintf( '.%1$s .et_pb_gallery_image', self::get_module_order_class( $function_name ) );
		self::set_style( $function_name, $boxShadow->get_style(
			$selector,
			$this->shortcode_atts,
			array( 'suffix' => '_image' )
		) );

		parent::process_box_shadow( $function_name );
	}

	protected function _add_additional_border_fields() {
		parent::_add_additional_border_fields();

		$this->advanced_options['border']['css'] = array(
			'main' => array(
				'border_radii'  => "{$this->main_css_element} .et_pb_gallery_item",
				'border_styles' => "{$this->main_css_element} .et_pb_gallery_item",
			),
		);

		$suffix      = 'image';
		$tab_slug    = 'advanced';
		$toggle_slug = 'image';

		$this->_additional_fields_options = array_merge(
			$this->_additional_fields_options,
			ET_Builder_Module_Fields_Factory::get( 'Border' )->get_fields( array(
				'suffix'          => "_{$suffix}",
				'label_prefix'    => esc_html__( 'Image', 'et_builder' ),
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'depends_to'      => array( 'fullwidth' ),
				'depends_show_if' => 'off',
			) )
		);

		$this->advanced_options["border_{$suffix}"]["border_radii_{$suffix}"]  = $this->_additional_fields_options["border_radii_{$suffix}"];
		$this->advanced_options["border_{$suffix}"]["border_styles_{$suffix}"] = $this->_additional_fields_options["border_styles_{$suffix}"];

		$this->advanced_options["border_{$suffix}"]['css'] = array(
			'main' => array(
				'border_radii'  => "{$this->main_css_element} .et_pb_gallery_image",
				'border_styles' => "{$this->main_css_element} .et_pb_gallery_image",
			)
		);
	}

	function process_advanced_border_options( $function_name ) {
		parent::process_advanced_border_options( $function_name );

		$suffix = 'image';
		/**
		 * @var ET_Builder_Module_Field_Border $border_field
		 */
		$border_field = ET_Builder_Module_Fields_Factory::get( 'Border' );

		$css_selector = ! empty( $this->advanced_options["border_{$suffix}"]['css']['main']['border_radii'] ) ? $this->advanced_options["border_{$suffix}"]['css']['main']['border_radii'] : $this->main_css_element;
		self::set_style( $function_name, array(
			'selector'    => $css_selector,
			'declaration' => $border_field->get_radii_style( $this->shortcode_atts, $this->advanced_options, "_{$suffix}" ),
			'priority'    => $this->_style_priority,
		) );

		$css_selector = ! empty( $this->advanced_options["border_{$suffix}"]['css']['main']['border_styles'] ) ? $this->advanced_options["border_{$suffix}"]['css']['main']['border_styles'] : $this->main_css_element;
		self::set_style( $function_name, array(
			'selector'    => $css_selector,
			'declaration' => $border_field->get_borders_style( $this->shortcode_atts, $this->advanced_options, "_{$suffix}" ),
			'priority'    => $this->_style_priority,
		) );
	}


}

new ET_Builder_Module_Gallery;
