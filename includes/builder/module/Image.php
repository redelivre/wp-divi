<?php

class ET_Builder_Module_Image extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Image', 'et_builder' );
		$this->plural     = esc_html__( 'Images', 'et_builder' );
		$this->slug       = 'et_pb_image';
		$this->vb_support = 'on';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Image', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay'    => esc_html__( 'Overlay', 'et_builder' ),
					'alignment'  => esc_html__( 'Alignment', 'et_builder' ),
					'width'      => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ),
				),
			),
			'borders'               => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .et_pb_image_wrap",
							'border_styles' => "%%order_class%% .et_pb_image_wrap",
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'main'    => '%%order_class%% .et_pb_image_wrap',
						'overlay' => 'inset',
					),
				),
			),
			'max_width'             => array(
				'options' => array(
					'width'     => array(
						'depends_show_if' => 'off',
					),
					'max_width' => array(
						'depends_show_if' => 'off',
					),
				),
			),
			'height'                => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_image_wrap img',
				),
			),
			'fonts'                 => false,
			'text'                  => false,
			'button'                => false,
			'link_options'          => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'cYwqxoHnjNA' ),
				'name' => esc_html__( 'An introduction to the Image module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src' => array(
				'label'              => esc_html__( 'Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'hide_metadata'      => true,
				'affects'            => array(
					'alt',
					'title_text',
				),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'dynamic_content'    => 'image',
			),
			'alt' => array(
				'label'           => esc_html__( 'Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'title_text' => array(
				'label'           => esc_html__( 'Image Title Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML Title text.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'show_in_lightbox' => array(
				'label'             => esc_html__( 'Open in Lightbox', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'affects'           => array(
					'url',
					'url_new_window',
					'use_overlay',
				),
				'toggle_slug'       => 'link',
				'description'       => esc_html__( 'Here you can choose whether or not the image should open in Lightbox. Note: if you select to open the image in Lightbox, url options below will be ignored.', 'et_builder' ),
			),
			'url' => array(
				'label'           => esc_html__( 'Image Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'affects'         => array(
					'use_overlay',
				),
				'description'     => esc_html__( 'If you would like your image to be a link, input your destination URL here. No link will be created if this field is left blank.', 'et_builder' ),
				'toggle_slug'     => 'link',
				'dynamic_content' => 'url',
			),
			'url_new_window' => array(
				'label'             => esc_html__( 'Image Link Target', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'depends_show_if'   => 'off',
				'toggle_slug'       => 'link',
				'description'       => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'use_overlay' => array(
				'label'             => esc_html__( 'Image Overlay', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'affects'           => array(
					'overlay_icon_color',
					'hover_overlay_color',
					'hover_icon',
				),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the image', 'et_builder' ),
			),
			'overlay_icon_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay icon', 'et_builder' ),
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'depends_show_if'     => 'on',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
				'description'         => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
			),
			'show_bottom_space' => array(
				'label'             => esc_html__( 'Show Space Below The Image', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'      => esc_html__( 'Yes', 'et_builder' ),
					'off'     => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'margin_padding',
				'description'       => esc_html__( 'Here you can choose whether or not the image should have a space below it.', 'et_builder' ),
				'mobile_options'   => true,
			),
			'align' => array(
				'label'           => esc_html__( 'Image Alignment', 'et_builder' ),
				'type'            => 'text_align',
				'option_category' => 'layout',
				'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'default_on_front' => 'left',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'Here you can choose the image alignment.', 'et_builder' ),
				'options_icon'    => 'module_align',
				'mobile_options'  => true,
			),
			'force_fullwidth' => array(
				'label'             => esc_html__( 'Force Fullwidth', 'et_builder' ),
				'description'       => esc_html__( "When enabled, this will force your image to extend 100% of the width of the column it's in.", 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
				'affects' => array(
					'max_width',
					'width',
				),
			),
		);

		return $fields;
	}

	public function get_alignment( $device = 'desktop' ) {
		$is_desktop = 'desktop' === $device;
		$suffix     = ! $is_desktop ? "_{$device}" : '';
		$alignment  = $is_desktop && isset( $this->props["align"] ) ? $this->props["align"] : '';

		if ( ! $is_desktop && et_pb_responsive_options()->is_responsive_enabled( $this->props, 'align' ) ) {
			$alignment = et_pb_responsive_options()->get_any_value( $this->props, "align{$suffix}" );
		}

		return et_pb_get_alignment( $alignment );
	}

	function render( $attrs, $content = null, $render_slug ) {
		$src                     = $this->props['src'];
		$alt                     = $this->props['alt'];
		$title_text              = $this->props['title_text'];
		$url                     = $this->props['url'];
		$url_new_window          = $this->props['url_new_window'];
		$show_in_lightbox        = $this->props['show_in_lightbox'];
		$align                   = $this->get_alignment();
		$align_tablet            = $this->get_alignment( 'tablet' );
		$align_phone             = $this->get_alignment( 'phone' );
		$force_fullwidth         = $this->props['force_fullwidth'];
		$overlay_icon_color      = $this->props['overlay_icon_color'];
		$hover_overlay_color     = $this->props['hover_overlay_color'];
		$hover_icon              = $this->props['hover_icon'];
		$use_overlay             = $this->props['use_overlay'];
		$animation_style         = $this->props['animation_style'];
		$box_shadow_style        = $this->props['box_shadow_style'];

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$show_bottom_space         = $this->props['show_bottom_space'];
		$show_bottom_space_values  = et_pb_responsive_options()->get_property_values( $this->props, 'show_bottom_space' );
		$show_bottom_space_tablet  = isset( $show_bottom_space_values['tablet'] ) ? $show_bottom_space_values['tablet'] : '';
		$show_bottom_space_phone   = isset( $show_bottom_space_values['phone'] ) ? $show_bottom_space_values['phone'] : '';

		// Handle svg image behaviour
		$src_pathinfo = pathinfo( $src );
		$is_src_svg = isset( $src_pathinfo['extension'] ) ? 'svg' === $src_pathinfo['extension'] : false;

		// overlay can be applied only if image has link or if lightbox enabled
		$is_overlay_applied = 'on' === $use_overlay && ( 'on' === $show_in_lightbox || ( 'off' === $show_in_lightbox && '' !== $url ) ) ? 'on' : 'off';

		if ( 'on' === $force_fullwidth ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%',
				'declaration' => 'width: 100%; max-width: 100% !important;',
			) );

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_image_wrap, %%order_class%% img',
				'declaration' => 'width: 100%;',
			) );
		}

		// Responsive Image Alignment.
		// Set CSS properties and values for the image alignment.
		// 1. Text Align is necessary, just set it from current image alignment value.
		// 2. Margin {Side} is optional. Used to pull the image to right/left side.
		// 3. Margin Left and Right are optional. Used by Center to reset custom margin of point 2.
		$align_values = array(
			'desktop' => array(
				'text-align'      => esc_html( $align ),
				"margin-{$align}" => ! empty( $align ) && 'center' !== $align ? '0' : '',
			),
			'tablet'  => array(
				'text-align'             => esc_html( $align_tablet ),
				'margin-left'            => 'left' !== $align_tablet ? 'auto' : '',
				'margin-right'           => 'left' !== $align_tablet ? 'auto' : '',
				"margin-{$align_tablet}" => ! empty( $align_tablet ) && 'center' !== $align_tablet ? '0' : '',
			),
			'phone'   => array(
				'text-align'            => esc_html( $align_phone ),
				'margin-left'           => 'left' !== $align_phone ? 'auto' : '',
				'margin-right'          => 'left' !== $align_phone ? 'auto' : '',
				"margin-{$align_phone}" => ! empty( $align_phone ) && 'center' !== $align_phone ? '0' : '',
			),
		);

		et_pb_responsive_options()->generate_responsive_css( $align_values, '%%order_class%%', '', $render_slug, '', 'alignment' );

		if ( 'on' === $is_overlay_applied ) {
			if ( '' !== $overlay_icon_color ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .et_overlay:before',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $overlay_icon_color )
					),
				) );
			}

			if ( '' !== $hover_overlay_color ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '%%order_class%% .et_overlay',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $hover_overlay_color )
					),
				) );
			}

			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$overlay_output = sprintf(
				'<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		// Set display block for svg image to avoid disappearing svg image
		if ( $is_src_svg ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_image_wrap',
				'declaration' => 'display: block;',
			) );
		}

		$box_shadow_overlay_wrap_class = 'none' !== $box_shadow_style
			? 'has-box-shadow-overlay'
			: '';

		$box_shadow_overlay_element = 'none' !== $box_shadow_style
			? '<div class="box-shadow-overlay"></div>'
			: '';

		$output = sprintf(
			'<span class="et_pb_image_wrap %5$s">%6$s<img src="%1$s" alt="%2$s"%3$s />%4$s</span>',
			esc_attr( $src ),
			esc_attr( $alt ),
			( '' !== $title_text ? sprintf( ' title="%1$s"', esc_attr( $title_text ) ) : '' ),
			'on' === $is_overlay_applied ? $overlay_output : '',
			$box_shadow_overlay_wrap_class,
			$box_shadow_overlay_element
		);

		if ( 'on' === $show_in_lightbox ) {
			$output = sprintf( '<a href="%1$s" class="et_pb_lightbox_image" title="%3$s">%2$s</a>',
				esc_attr( $src ),
				$output,
				esc_attr( $alt )
			);
		} else if ( '' !== $url ) {
			$output = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				$output,
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		}

		// Module classnames
		if ( ! in_array( $animation_style, array( '', 'none' ) ) ) {
			$this->add_classname( 'et-waypoint' );
		}

		if ( 'on' !== $show_bottom_space ) {
			$this->add_classname( 'et_pb_image_sticky' );
		}

		if ( ! empty( $show_bottom_space_tablet ) ) {
			if ( 'on' === $show_bottom_space_tablet ) {
				$this->add_classname( 'et_pb_image_bottom_space_tablet' );
			} elseif ( 'off' === $show_bottom_space_tablet ) {
				$this->add_classname( 'et_pb_image_sticky_tablet' );
			}
		}

		if ( ! empty( $show_bottom_space_phone ) ) {
			if ( 'on' === $show_bottom_space_phone ) {
				$this->add_classname( 'et_pb_image_bottom_space_phone' );
			} elseif ( 'off' === $show_bottom_space_phone ) {
				$this->add_classname( 'et_pb_image_sticky_phone' );
			}
		}

		if ( 'on' === $is_overlay_applied ) {
			$this->add_classname( 'et_pb_has_overlay' );
		}

		$output = sprintf(
			'<div%3$s class="%2$s">
				%5$s
				%4$s
				%1$s
			</div>',
			$output,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

// This adds the upload label for Image module
// TODO: Remove when BB is removed.
function _et_bb_module_image_add_src_label( $filed ) {
	if ( ! isset( $filed['label'] ) ) {
		$filed['label'] = esc_html__( 'Image URL', 'et_builder' );
	}

	return $filed;
}

add_filter( 'et_builder_module_fields_et_pb_image_field_src', '_et_bb_module_image_add_src_label' );

new ET_Builder_Module_Image;
