<?php

class ET_Builder_Module_Testimonial extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Testimonial', 'et_builder' );
		$this->plural     = esc_html__( 'Testimonials', 'et_builder' );
		$this->slug       = 'et_pb_testimonial';
		$this->vb_support = 'on';
		$this->main_css_element = '%%order_class%%.et_pb_testimonial';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'image'        => esc_html__( 'Image', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon'       => esc_html__( 'Quote Icon', 'et_builder' ),
					'text'       => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 51,
					),
					'image' => array(
						'title' => esc_html__( 'Image', 'et_builder' ),
						'priority' => 49,
					),
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 100,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'body' => array(
					'label'            => esc_html__( 'Body', 'et_builder' ),
					'css'              => array(
						'main' => "{$this->main_css_element} *",
					),
					'hide_text_shadow' => true,
					'block_elements'   => array(
						'tabbed_subtoggles' => true,
					),
				),
				'author'   => array(
					'label'          => esc_html__( 'Author', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_testimonial_author",
					),
					'font'           => array(
						'default' => '|700|||||||',
					),
					'line_height'    => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.5' ) ) . 'em',
					),
					'font_size'      => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'position' => array(
					'label'           => esc_html__( 'Position', 'et_builder' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .et_pb_testimonial_position, {$this->main_css_element} .et_pb_testimonial_separator",
					),
					'hide_text_align' => true,
					'line_height'    => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.5' ) ) . 'em',
					),
					'font_size'      => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'company'  => array(
					'label'           => esc_html__( 'Company', 'et_builder' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .et_pb_testimonial_company",
					),
					'hide_text_align' => true,
					'line_height'    => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.5' ) ) . 'em',
					),
					'font_size'      => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'background'            => array(
				'has_background_color_toggle' => true,
				'use_background_color' => true,
				'options' => array(
					'use_background_color' => array(
						'default'          => 'on',
					),
					'background_color' => array(
						'depends_show_if'  => 'on',
						'default'          => '#f5f5f5',
					),
				),
				'settings'             => array(
					'color' => 'alpha',
				),
			),
			'borders'               => array(
				'default' => array(),
				'portrait' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .et_pb_testimonial_portrait, %%order_class%% .et_pb_testimonial_portrait:before",
							'border_styles' => "%%order_class%% .et_pb_testimonial_portrait",
						),
					),
					'label_prefix' => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
					'defaults'        => array(
						'border_radii'  => 'on|90px|90px|90px|90px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(),
				'image'   => array(
					'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'css'             => array(
						'main'         => '%%order_class%% .et_pb_testimonial_portrait:before',
					),
					'default_on_fronts'  => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'options' => array(
					'text_orientation'  => array(
						'default'      => 'left',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
				'css' => array(
					'main' => implode(', ', array(
						'%%order_class%% .et_pb_testimonial_description p',
						'%%order_class%% .et_pb_testimonial_description a',
						'%%order_class%% .et_pb_testimonial_description .et_pb_testimonial_author',
					))
				)
			),
			'filters'               => array(
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'                 => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_testimonial_portrait',
				),
			),
			'button'                => false,
		);

		$this->custom_css_fields = array(
			'testimonial_portrait' => array(
				'label'    => esc_html__( 'Testimonial Portrait', 'et_builder' ),
				'selector' => '.et_pb_testimonial_portrait',
			),
			'testimonial_description' => array(
				'label'    => esc_html__( 'Testimonial Description', 'et_builder' ),
				'selector' => '.et_pb_testimonial_description',
			),
			'testimonial_author' => array(
				'label'    => esc_html__( 'Testimonial Author', 'et_builder' ),
				'selector' => '.et_pb_testimonial_author',
			),
			'testimonial_meta' => array(
				'label'    => esc_html__( 'Testimonial Meta', 'et_builder' ),
				'selector' => '.et_pb_testimonial_meta',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'FkQuawiGWUw' ),
				'name' => esc_html__( 'An introduction to the Testimonial module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'author' => array(
				'label'           => esc_html__( 'Author', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the testimonial author.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'job_title' => array(
				'label'           => esc_html__( 'Job Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the job title.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'company_name' => array(
				'label'           => esc_html__( 'Company', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the company.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'url' => array(
				'label'           => esc_html__( 'Company Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the website of the author or leave blank for no link.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Company Link Target', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'link_options',
				'description'     => esc_html__( 'Choose whether or not the URL should open in a new window.', 'et_builder' ),
				'default_on_front' => 'off',
			),
			'portrait_url' => array(
				'label'              => esc_html__( 'Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
			),
			'quote_icon' => array(
				'label'           => esc_html__( 'Show Quote Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'description'     => esc_html__( 'Choose whether or not the quote icon should be visible.', 'et_builder' ),
				'toggle_slug'     => 'elements',
			),
			'content' => array(
				'label'           => esc_html__( 'Body', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'quote_icon_color' => array(
				'label'             => esc_html__( 'Quote Icon Color', 'et_builder' ),
				'description'       => esc_html__( 'Here you can define a custom color for the quote icon.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'quote_icon_background_color' => array(
				'label'            => esc_html__( 'Quote Icon Background Color', 'et_builder' ),
				'description'      => esc_html__( 'Pick a color to use for the circular background area behind the quote icon.', 'et_builder' ),
				'type'             => 'color-alpha',
				'custom_color'     => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'default'          => '#f5f5f5',
				'default_on_front' => '',
				'hover'            => 'tabs',
				'mobile_options'   => true,
			),
			'portrait_width' => array(
				'label'           => esc_html__( 'Image Width', 'et_builder' ),
				'description'     => esc_html__( "Adjust the width of the person's portrait photo within the testimonial.", 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image',
				'default_unit'    => 'px',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
				'mobile_options'  => true,
			),
			'portrait_height' => array(
				'label'           => esc_html__( 'Image Height', 'et_builder' ),
				'description'     => esc_html__( "Adjust the height of the person's portrait photo within the testimonial.", 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image',
				'allowed_units'   => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
				'mobile_options'  => true,
			),
			'use_icon_font_size' => array(
				'label'            => esc_html__( 'Use Custom Quote Icon Size', 'et_builder' ),
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
				'toggle_slug'      => 'icon',
				'option_category'  => 'font_option',
			),
			'icon_font_size'     => array(
				'label'            => esc_html__( 'Quote Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'default'          => '32px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
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
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['quote_icon_color'] = array( 'color' => '%%order_class%%.et_pb_testimonial:before' );
		$fields['quote_icon_background_color'] = array( 'background-color' => '%%order_class%%.et_pb_testimonial:before' );
		$fields['icon_font_size']              = array(
			'font-size'     => '%%order_class%%:before',
			'border-radius' => '%%order_class%%:before',
			'height'        => '%%order_class%% .et-fb-quick-access-item-testimonial-icon',
			'width'         => '%%order_class%% .et-fb-quick-access-item-testimonial-icon',
			'top'           => '%%order_class%%:before, %%order_class%% .et-fb-quick-access-item-testimonial-icon',
			'margin-left'   => '%%order_class%%:before, %%order_class%% .et-fb-quick-access-item-testimonial-icon',
		);

		return $fields;
	}

	public function get_transition_image_fields_css_props() {
		$fields = parent::get_transition_image_fields_css_props();
		$fields = array_merge( $this->get_transition_borders_fields_css_props( 'portrait' ), $fields );

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		// Allowing full html for backwards compatibility.
		$author                            = $this->_esc_attr( 'author', 'full' );
		$job_title                         = $this->_esc_attr( 'job_title' );
		$portrait_url                      = $this->props['portrait_url'];
		// Allowing full html for backwards compatibility.
		$company_name                      = $this->_esc_attr( 'company_name', 'full' );
		$url                               = $this->props['url'];
		$quote_icon                        = $this->props['quote_icon'];
		$url_new_window                    = $this->props['url_new_window'];
		$use_background_color              = $this->props['use_background_color'];
		$background_color                  = $this->props['background_color'];
		$background_color_hover            = $this->get_hover_value( 'background_color' );
		$use_icon_font_size                = $this->props['use_icon_font_size'];
		$quote_icon_color_hover            = $this->get_hover_value('quote_icon_color');
		$quote_icon_color_values           = et_pb_responsive_options()->get_property_values( $this->props, 'quote_icon_color' );
		$quote_icon_background_color_hover = $this->get_hover_value('quote_icon_background_color');
		$quote_icon_background_colors      = et_pb_responsive_options()->get_property_values( $this->props, 'quote_icon_background_color' );
		$portrait_width_values             = et_pb_responsive_options()->get_property_values( $this->props, 'portrait_width' );
		$portrait_height_values            = et_pb_responsive_options()->get_property_values( $this->props, 'portrait_height' );
		$icon_font_size_hover              = $this->get_hover_value( 'icon_font_size' );
		$icon_font_size_values             = et_pb_responsive_options()->get_property_values( $this->props, 'icon_font_size' );

		$background_layout                 = $this->props['background_layout'];
		$background_layout_hover           = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled   = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values          = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet          = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone           = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';

		// Potrait Width.
		et_pb_responsive_options()->generate_responsive_css( $portrait_width_values, '%%order_class%% .et_pb_testimonial_portrait', 'width', $render_slug, ' !important;' );

		// Potrait Height.
		et_pb_responsive_options()->generate_responsive_css( $portrait_height_values, '%%order_class%% .et_pb_testimonial_portrait', 'height', $render_slug, ' !important;' );

		// Quote Icon Color.
		et_pb_responsive_options()->generate_responsive_css( $quote_icon_color_values, '%%order_class%%.et_pb_testimonial:before', 'color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'quote_icon_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_testimonial:hover:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $quote_icon_color_hover )
				),
			) );
		}

		// Quote Icon Background Color.
		et_pb_responsive_options()->generate_responsive_css( $quote_icon_background_colors, '%%order_class%%.et_pb_testimonial:before', 'background-color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'quote_icon_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_testimonial:hover:before',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $quote_icon_background_color_hover )
				),
			) );
		}

		// Icon Size.
		$icon_selector = '%%order_class%%:before';
		if ( 'off' !== $quote_icon && 'off' !== $use_icon_font_size ) {
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
						'font-size:%1$s; border-radius:%1$s; top:-%2$s; margin-left:-%2$s;',
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
					'selector'    => $this->add_hover_to_order_class( $icon_selector ),
					'declaration' => sprintf(
						'font-size:%1$s; border-radius:%1$s; top:-%2$s; margin-left:-%2$s;',
						esc_html( $icon_font_size_hover ),
						esc_html( $icon_font_size_hover_half )
					),
				) );
			}
		}

		$portrait_image = '';

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		if ( '' !== $portrait_url ) {
			$portrait_image = sprintf(
				'<div class="et_pb_testimonial_portrait" style="background-image: url(%1$s);">
				</div>',
				esc_attr( $portrait_url )
			);
		}

		$author = ! empty( $author ) ? $author : '';
		$company_name = ! empty( $company_name ) ? $company_name : '';

		if ( '' !== $url && ( '' !== $company_name || '' !== $author ) ) {
			// NOT allowing full html for backwards compatibility in this case.
			$author       = $this->_esc_attr( 'author' );
			$company_name = $this->_esc_attr( 'company_name' );
			$link_output  = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				( '' !== $company_name ? et_core_esc_previously( $company_name ) : et_core_esc_previously( $author ) ),
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);

			if ( '' !== $company_name ) {
				$company_name = $link_output;
			} else {
				$author = $link_output;
			}
		}

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
			'clearfix',
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(),
		) );

		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}

		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		if ( 'off' === $quote_icon ) {
			$this->add_classname( 'et_pb_icon_off' );
		}

		if ( '' === $portrait_image ) {
			$this->add_classname( 'et_pb_testimonial_no_image' );
		}

		if ( 'off' === $use_background_color ) {
			$this->add_classname( 'et_pb_testimonial_no_bg' );
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

		if ( 'on' === $use_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_testimonial',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $background_color )
				),
			) );

			if ( et_builder_is_hover_enabled( 'background_color', $this->props ) ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%%.et_pb_testimonial' ),
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $background_color_hover )
					),
				) );
			}
		}

		$output = sprintf(
			'<div%3$s class="%4$s"%10$s%11$s>
				%9$s
				%8$s
				%7$s
				<div class="et_pb_testimonial_description">
					<div class="et_pb_testimonial_description_inner">
					%1$s
					<span class="et_pb_testimonial_author">%2$s</span>
					<p class="et_pb_testimonial_meta">%5$s%6$s</p>
					</div> <!-- .et_pb_testimonial_description_inner -->
				</div> <!-- .et_pb_testimonial_description -->
			</div> <!-- .et_pb_testimonial -->',
			$this->content,
			et_core_esc_previously( $author ),
			$this->module_id(),
			$this->module_classname( $render_slug ),
			( '' !== $job_title // #5
				? sprintf( '<span class="et_pb_testimonial_position">%1$s%2$s</span>',
					et_core_esc_previously( $job_title ),
					( '' !== $company_name ? ', ' : '' )
				)
				: ''
			),
			( '' !== $company_name
				? sprintf( '<span class="et_pb_testimonial_company">%1$s</span>',
					et_core_esc_previously( $company_name )
				)
				: ''
			),
			( '' !== $portrait_image ? $portrait_image : '' ),
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ), // #10
			et_core_esc_previously( $data_background_layout_hover )
		);

		return $output;
	}
}

new ET_Builder_Module_Testimonial;
