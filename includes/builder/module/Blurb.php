<?php

class ET_Builder_Module_Blurb extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Blurb', 'et_builder' );
		$this->plural           = esc_html__( 'Blurbs', 'et_builder' );
		$this->slug             = 'et_pb_blurb';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%%.et_pb_blurb';
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'image'        => esc_html__( 'Image & Icon', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon_settings' => esc_html__( 'Image & Icon', 'et_builder' ),
					'text'          => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'width'         => array(
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
			'fonts'                 => array(
				'header' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h4, {$this->main_css_element} h4 a, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h1.et_pb_module_header a, {$this->main_css_element} h2.et_pb_module_header, {$this->main_css_element} h2.et_pb_module_header a, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header a, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header a, {$this->main_css_element} h6.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header a",
					),
					'header_level' => array(
						'default' => 'h4',
					),
				),
				'body'   => array(
					'label'          => esc_html__( 'Body', 'et_builder' ),
					'css'            => array(
						'line_height' => "{$this->main_css_element} p",
						'text_align'  => "{$this->main_css_element} .et_pb_blurb_description",
						'text_shadow' => "{$this->main_css_element} .et_pb_blurb_description",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'css'               => array(
							'main' => "{$this->main_css_element} .et_pb_blurb_description",
						),
					),
				),
			),
			'background'            => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'               => array(
				'default' => array(),
				'image'   => array(
					'css'             => array(
						'main' => array(
							'border_radii' => "%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap",
							'border_styles' => "%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap",
						)
					),
					'label_prefix'    => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_settings',
					'depends_on'      => array( 'use_icon' ),
					'depends_show_if' => 'off',
				),
			),
			'box_shadow'            => array(
				'default' => array(),
				'image'   => array(
					'label'               => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'     => 'layout',
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon_settings',
					'show_if'             => array(
						'use_icon' => 'off',
					),
					'css'                 => array(
						'main' => '%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap',
						'show_if_not' => array(
							'use_icon' => 'on',
						),
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
			'max_width'             => array(
				'css' => array(
					'main' => $this->main_css_element,
					'module_alignment' => '%%order_class%%.et_pb_blurb.et_pb_module',
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'css'              => array(
					'text_shadow' => "{$this->main_css_element} .et_pb_blurb_container",
				),
				'options' => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover' => 'tabs',
					),
					'text_orientation' => array(
						'default_on_front' => 'left',
					),
				),
			),
			'filters'               => array(
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'icon_settings',
					'depends_show_if' => 'off',
					'css'                 => array(
						'main' => '%%order_class%% .et_pb_main_blurb_image',
					),
				),
			),
			'icon_settings'         => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_main_blurb_image',
				),
			),
			'button'                => false,
		);

		$this->custom_css_fields = array(
			'blurb_image' => array(
				'label'    => esc_html__( 'Blurb Image', 'et_builder' ),
				'selector' => '.et_pb_main_blurb_image',
			),
			'blurb_title' => array(
				'label'    => esc_html__( 'Blurb Title', 'et_builder' ),
				'selector' => '.et_pb_module_header',
			),
			'blurb_content' => array(
				'label'    => esc_html__( 'Blurb Content', 'et_builder' ),
				'selector' => '.et_pb_blurb_content',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'XW7HR86lp8U' ),
				'name' => esc_html__( 'An introduction to the Blurb module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$et_accent_color = et_builder_accent_color();

		$image_icon_placement = array(
			'top' => esc_html__( 'Top', 'et_builder' ),
		);

		if ( ! is_rtl() ) {
			$image_icon_placement['left'] = esc_html__( 'Left', 'et_builder' );
		} else {
			$image_icon_placement['right'] = esc_html__( 'Right', 'et_builder' );
		}

		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title of your blurb will appear in bold below your blurb image.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'url' => array(
				'label'           => esc_html__( 'Title Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to make your blurb a link, input your destination URL here.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'url_new_window' => array(
				'label'           => esc_html__( 'Title Link Target', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'link_options',
				'description'     => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
				'default_on_front'=> 'off',
			),
			'use_icon' => array(
				'label'           => esc_html__( 'Use Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'toggle_slug'     => 'image',
				'affects'         => array(
					'border_radii_image',
					'border_styles_image',
					'font_icon',
					'image_max_width',
					'use_icon_font_size',
					'use_circle',
					'icon_color',
					'image',
					'alt',
					'child_filter_hue_rotate',
					'child_filter_saturate',
					'child_filter_brightness',
					'child_filter_contrast',
					'child_filter_invert',
					'child_filter_sepia',
					'child_filter_opacity',
					'child_filter_blur',
					'child_mix_blend_mode',
				),
				'description' => esc_html__( 'Here you can choose whether icon set below should be used.', 'et_builder' ),
				'default_on_front'=> 'off',
			),
			'font_icon' => array(
				'label'               => esc_html__( 'Icon', 'et_builder' ),
				'type'                => 'select_icon',
				'option_category'     => 'basic_option',
				'class'               => array( 'et-pb-font-icon' ),
				'toggle_slug'         => 'image',
				'description'         => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'depends_show_if'     => 'on',
			),
			'icon_color' => array(
				'default'           => $et_accent_color,
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Here you can define a custom color for your icon.', 'et_builder' ),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon_settings',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'use_circle' => array(
				'label'           => esc_html__( 'Circle Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'use_circle_border',
					'circle_color',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'description'      => esc_html__( 'Here you can choose whether icon set above should display within a circle.', 'et_builder' ),
				'depends_show_if'  => 'on',
				'default_on_front'=> 'off',
			),
			'circle_color' => array(
				'default'         => $et_accent_color,
				'label'           => esc_html__( 'Circle Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for the icon circle.', 'et_builder' ),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'use_circle_border' => array(
				'label'           => esc_html__( 'Show Circle Border', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'circle_border_color',
				),
				'description' => esc_html__( 'Here you can choose whether if the icon circle border should display.', 'et_builder' ),
				'depends_show_if'   => 'on',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon_settings',
				'default_on_front'  => 'off',
			),
			'circle_border_color' => array(
				'default'         => $et_accent_color,
				'label'           => esc_html__( 'Circle Border Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for the icon circle border.', 'et_builder' ),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'image' => array(
				'label'              => esc_html__( 'Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'depends_show_if'    => 'off',
				'description'        => esc_html__( 'Upload an image to display at the top of your blurb.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
			),
			'alt' => array(
				'label'           => esc_html__( 'Image Alt Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'et_builder' ),
				'depends_show_if' => 'off',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'icon_placement' => array(
				'label'             => esc_html__( 'Image/Icon Placement', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => $image_icon_placement,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon_settings',
				'description'       => esc_html__( 'Here you can choose where to place the icon.', 'et_builder' ),
				'default_on_front'  => 'top',
				'mobile_options'    => true,
			),
			'content' => array(
				'label'             => esc_html__( 'Body', 'et_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
				'dynamic_content'   => 'text',
			),
			'image_max_width' => array(
				'label'           => esc_html__( 'Image Width', 'et_builder' ),
				'description'     => esc_html__( 'Adjust the width of the image within the blurb.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
				'depends_show_if' => 'off',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'         => '100%',
				'default_unit'    => '%',
				'default_on_front'=> '',
				'allow_empty'     => true,
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'responsive'      => true,
			),
			'content_max_width' => array(
				'label'           => esc_html__( 'Content Width', 'et_builder' ),
				'description'     => esc_html__( 'Adjust the width of the content within the blurb.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'mobile_options'  => true,
				'validate_unit'   => true,
				'default'         => '550px',
				'default_unit'    => 'px',
				'default_on_front'=> '',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'allow_empty'     => true,
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '1100',
					'step' => '1',
				),
				'responsive'      => true,
			),
			'use_icon_font_size' => array(
				'label'           => esc_html__( 'Use Icon Font Size', 'et_builder' ),
				'description'     => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'font_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'     => array(
					'icon_font_size',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'default_on_front' => 'off',
			),
			'icon_font_size' => array(
				'label'           => esc_html__( 'Icon Font Size', 'et_builder' ),
				'description'     => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'font_option',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'default'         => '96px',
				'default_unit'    => 'px',
				'default_on_front'=> '',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings' => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'  => true,
				'depends_show_if' => 'on',
				'responsive'      => true,
				'hover'           => 'tabs',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();
		$fields['icon_color'] = array(
			'color' => '%%order_class%% .et-pb-icon',
		);

		$fields['circle_color'] = array(
			'background-color' => '%%order_class%% .et-pb-icon',
		);

		$fields['circle_border_color'] = array(
			'border-color' => '%%order_class%% .et-pb-icon',
		);

		$fields['icon_font_size'] = array(
			'font-size' => '%%order_class%% .et-pb-icon',
		);

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$title                           = $this->_esc_attr( 'title' );
		$url                             = $this->props['url'];
		$image                           = $this->props['image'];
		$url_new_window                  = $this->props['url_new_window'];
		$alt                             = $this->_esc_attr( 'alt' );
		$font_icon                       = $this->props['font_icon'];
		$use_icon                        = $this->props['use_icon'];
		$use_circle                      = $this->props['use_circle'];
		$use_circle_border               = $this->props['use_circle_border'];
		$use_icon_font_size              = $this->props['use_icon_font_size'];
		$icon_font_size                  = $this->props['icon_font_size'];
		$icon_font_size_hover            = $this->get_hover_value( 'icon_font_size' );
		$icon_font_size_tablet           = $this->props['icon_font_size_tablet'];
		$icon_font_size_phone            = $this->props['icon_font_size_phone'];
		$header_level                    = $this->props['header_level'];
		$icon_font_size_last_edited      = $this->props['icon_font_size_last_edited'];
		$image_max_width                 = $this->props['image_max_width'];
		$image_max_width_tablet          = $this->props['image_max_width_tablet'];
		$image_max_width_phone           = $this->props['image_max_width_phone'];
		$image_max_width_last_edited     = $this->props['image_max_width_last_edited'];
		$content_max_width               = $this->props['content_max_width'];
		$content_max_width_tablet        = $this->props['content_max_width_tablet'];
		$content_max_width_phone         = $this->props['content_max_width_phone'];
		$content_max_width_last_edited   = $this->props['content_max_width_last_edited'];

		$icon_color                      = $this->props['icon_color'];
		$icon_color_hover                = $this->get_hover_value( 'icon_color' );
		$icon_color_values               = et_pb_responsive_options()->get_property_values( $this->props, 'icon_color' );
		$icon_color_tablet               = isset( $icon_color_values['tablet'] ) ? $icon_color_values['tablet'] : '';
		$icon_color_phone                = isset( $icon_color_values['phone'] ) ? $icon_color_values['phone'] : '';

		$circle_color                    = $this->props['circle_color'];
		$circle_color_hover              = $this->get_hover_value( 'circle_color' );
		$circle_color_values             = et_pb_responsive_options()->get_property_values( $this->props, 'circle_color' );
		$circle_color_tablet             = isset( $circle_color_values['tablet'] ) ? $circle_color_values['tablet'] : '';
		$circle_color_phone              = isset( $circle_color_values['phone'] ) ? $circle_color_values['phone'] : '';

		$circle_border_color             = $this->props['circle_border_color'];
		$circle_border_color_hover       = $this->get_hover_value( 'circle_border_color' );
		$circle_border_color_values      = et_pb_responsive_options()->get_property_values( $this->props, 'circle_border_color' );
		$circle_border_color_tablet      = isset( $circle_border_color_values['tablet'] ) ? $circle_border_color_values['tablet'] : '';
		$circle_border_color_phone       = isset( $circle_border_color_values['phone'] ) ? $circle_border_color_values['phone'] : '';

		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet        = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone         = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';

		$icon_placement                  = $this->props['icon_placement'];
		$icon_placement_values           = et_pb_responsive_options()->get_property_values( $this->props, 'icon_placement' );
		$icon_placement_tablet           = isset( $icon_placement_values['tablet'] ) ? $icon_placement_values['tablet'] : '';
		$icon_placement_phone            = isset( $icon_placement_values['phone'] ) ? $icon_placement_values['phone'] : '';

		$animation                       = $this->props['animation'];
		$animation_values                = et_pb_responsive_options()->get_property_values( $this->props, 'animation' );
		$animation_tablet                = isset( $animation_values['tablet'] ) ? $animation_values['tablet'] : '';
		$animation_phone                 = isset( $animation_values['phone'] ) ? $animation_values['phone'] : '';

		$image_pathinfo = pathinfo( $image );
		$is_image_svg   = isset( $image_pathinfo['extension'] ) ? 'svg' === $image_pathinfo['extension'] : false;

		$icon_selector = '%%order_class%% .et-pb-icon';

		if ( 'off' !== $use_icon_font_size ) {
			$font_size_responsive_active = et_pb_get_responsive_status( $icon_font_size_last_edited );

			$font_size_values = array(
				'desktop' => $icon_font_size,
				'tablet'  => $font_size_responsive_active ? $icon_font_size_tablet : '',
				'phone'   => $font_size_responsive_active ? $icon_font_size_phone : '',
			);

			et_pb_generate_responsive_css( $font_size_values, $icon_selector, 'font-size', $render_slug );

			if ( et_builder_is_hover_enabled( 'icon_font_size', $this->props ) ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => $this->add_hover_to_order_class( $icon_selector ),
					'declaration' => sprintf(
						'font-size: %1$s;',
						esc_html( $icon_font_size_hover )
					),
				) );
			}
		}

		if ( '' !== $image_max_width_tablet || '' !== $image_max_width_phone || '' !== $image_max_width || $is_image_svg ) {
			$is_size_px = false;

			// If size is given in px, we want to override parent width
			if (
				false !== strpos( $image_max_width, 'px' ) ||
				false !== strpos( $image_max_width_tablet, 'px' ) ||
				false !== strpos( $image_max_width_phone, 'px' )
			) {
				$is_size_px = true;
			}
			// SVG image overwrite. SVG image needs its value to be explicit
			if ( '' === $image_max_width && $is_image_svg ) {
				$image_max_width = '100%';
			}

			// Image max width selector.
			$image_max_width_selectors       = array();
			$image_max_width_reset_selectors = array();
			$image_max_width_reset_values    = array();

			$image_max_width_selector = $icon_placement === 'top' && $is_image_svg ? '%%order_class%% .et_pb_main_blurb_image' : '%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap';

			foreach ( array( 'tablet', 'phone' ) as $device ) {
				$device_icon_placement = 'tablet' === $device ? $icon_placement_tablet : $icon_placement_phone;
				if ( empty( $device_icon_placement ) ) {
					continue;
				}

				$image_max_width_selectors[ $device ] = 'top' === $device_icon_placement && $is_image_svg ? '%%order_class%% .et_pb_main_blurb_image' : '%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap';

				$prev_icon_placement = 'tablet' === $device ? $icon_placement : $icon_placement_tablet;
				if ( empty( $prev_icon_placement ) || $prev_icon_placement === $device_icon_placement || ! $is_image_svg ) {
					continue;
				}

				// Image/icon placement setting is related to image width setting. In some cases,
				// user uses different image/icon placement settings for each devices. We need to
				// reset previous device image width styles to make it works with current style.
				$image_max_width_reset_selectors[ $device ] = '%%order_class%% .et_pb_main_blurb_image';
				$image_max_width_reset_values[ $device ]    = array( 'width' => '32px' );

				if ( 'top' === $device_icon_placement ) {
					$image_max_width_reset_selectors[ $device ] = '%%order_class%% .et_pb_main_blurb_image .et_pb_image_wrap';
					$image_max_width_reset_values[ $device ]    = array( 'width' => 'auto' );
				}
			}

			// Add image max width desktop selector if user sets different image/icon placement setting.
			if ( ! empty( $image_max_width_selectors ) ) {
				$image_max_width_selectors['desktop'] = $image_max_width_selector;
			}

			$image_max_width_property = ( $is_image_svg || $is_size_px ) ? 'width' : 'max-width';

			$image_max_width_responsive_active = et_pb_get_responsive_status( $image_max_width_last_edited );

			$image_max_width_values = array(
				'desktop' => $image_max_width,
				'tablet'  => $image_max_width_responsive_active ? $image_max_width_tablet : '',
				'phone'   => $image_max_width_responsive_active ? $image_max_width_phone : '',
			);

			$main_image_max_width_selector = $image_max_width_selector;

			// Overwrite image max width if there are image max width selectors for different devices.
			if ( ! empty( $image_max_width_selectors ) ) {
				$main_image_max_width_selector = $image_max_width_selectors;

				if ( ! empty( $image_max_width_selectors['tablet'] ) && empty( $image_max_width_values['tablet'] ) ) {
					$image_max_width_values['tablet'] = $image_max_width_responsive_active ? esc_attr( et_pb_responsive_options()->get_any_value( $this->props, 'image_max_width_tablet', '100%', true ) ) : esc_attr( $image_max_width );
				}

				if ( ! empty( $image_max_width_selectors['phone'] ) && empty( $image_max_width_values['phone'] ) ) {
					$image_max_width_values['phone'] = $image_max_width_responsive_active ? esc_attr( et_pb_responsive_options()->get_any_value( $this->props, 'image_max_width_phone', '100%', true ) ) : esc_attr( $image_max_width );
				}
			}

			et_pb_responsive_options()->generate_responsive_css( $image_max_width_values, $main_image_max_width_selector, $image_max_width_property, $render_slug );

			// Reset custom image max width styles.
			if ( ! empty( $image_max_width_selectors ) && ! empty( $image_max_width_reset_selectors ) ) {
				et_pb_responsive_options()->generate_responsive_css( $image_max_width_reset_values, $image_max_width_reset_selectors, $image_max_width_property, $render_slug, '', 'input' );
			}
		}

		if ( '' !== $content_max_width_tablet || '' !== $content_max_width_phone || '' !== $content_max_width ) {
			$content_max_width_responsive_active = et_pb_get_responsive_status( $content_max_width_last_edited );

			$content_max_width_values = array(
				'desktop' => $content_max_width,
				'tablet'  => $content_max_width_responsive_active ? $content_max_width_tablet : '',
				'phone'   => $content_max_width_responsive_active ? $content_max_width_phone : '',
			);

			et_pb_generate_responsive_css( $content_max_width_values, '%%order_class%% .et_pb_blurb_content', 'max-width', $render_slug );
		}

		if ( is_rtl() && 'left' === $icon_placement ) {
			$icon_placement = 'right';
		}

		if ( is_rtl() && 'left' === $icon_placement_tablet ) {
			$icon_placement_tablet = 'right';
		}

		if ( is_rtl() && 'left' === $icon_placement_phone ) {
			$icon_placement_phone = 'right';
		}

		if ( '' !== $title && '' !== $url ) {
			$title = sprintf( '<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				et_core_esc_previously( $title ),
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		} else {
			// Allowing full html for backwards compatibility.
			$title = $this->_esc_attr( 'title', 'full' );
		}

		if ( '' !== $title ) {
			$title = sprintf(
				'<%1$s class="et_pb_module_header">%2$s</%1$s>',
				et_pb_process_header_level( $header_level, 'h4' ),
				et_core_esc_previously( $title )
			);
		}

		// Added for backward compatibility
		if ( empty( $animation ) ) {
			$animation = 'top';
		}

		if ( 'off' === $use_icon ) {
			$image = ( '' !== trim( $image ) ) ? sprintf(
				'<img src="%1$s" alt="%2$s" class="et-waypoint%3$s%4$s%5$s" />',
				esc_attr( $image ),
				et_core_esc_previously( $alt ),
				esc_attr( " et_pb_animation_{$animation}" ),
				! empty( $animation_tablet ) ? esc_attr( " et_pb_animation_{$animation_tablet}_tablet" ) : '',
				! empty( $animation_phone ) ? esc_attr( " et_pb_animation_{$animation_phone}_phone" ) : ''
			) : '';
		} else {
			$icon_style        = sprintf( 'color: %1$s;', esc_attr( $icon_color ) );
			$icon_tablet_style = '' !== $icon_color_tablet ? sprintf( 'color: %1$s;', esc_attr( $icon_color_tablet ) ) : '';
			$icon_phone_style  = '' !== $icon_color_phone ? sprintf( 'color: %1$s;', esc_attr( $icon_color_phone ) ) : '';
			$icon_style_hover  = '';

			if ( et_builder_is_hover_enabled( 'icon_color', $this->props ) ) {
				$icon_style_hover = sprintf( 'color: %1$s;', esc_attr( $icon_color_hover ) );
			}

			if ( 'on' === $use_circle ) {
				$icon_style .= sprintf( ' background-color: %1$s;', esc_attr( $circle_color ) );
				$icon_tablet_style .= '' !== $circle_color_tablet ? sprintf( ' background-color: %1$s;', esc_attr( $circle_color_tablet ) ) : '';
				$icon_phone_style  .= '' !== $circle_color_phone ? sprintf( ' background-color: %1$s;', esc_attr( $circle_color_phone ) ) : '';

				if ( et_builder_is_hover_enabled( 'circle_color', $this->props ) ) {
					$icon_style_hover .= sprintf( ' background-color: %1$s;', esc_attr( $circle_color_hover ) );
				}

				if ( 'on' === $use_circle_border ) {
					$icon_style .= sprintf( ' border-color: %1$s;', esc_attr( $circle_border_color ) );
					$icon_tablet_style .= '' !== $circle_border_color_tablet ? sprintf( ' border-color: %1$s;', esc_attr( $circle_border_color_tablet ) ) : '';
					$icon_phone_style  .= '' !== $circle_border_color_phone ? sprintf( ' border-color: %1$s;', esc_attr( $circle_border_color_phone ) ) : '';

					if ( et_builder_is_hover_enabled( 'circle_border_color', $this->props ) ) {
						$icon_style_hover .= sprintf( ' border-color: %1$s;', esc_attr( $circle_border_color_hover ) );
					}
				}
			}

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $icon_selector,
				'declaration' => $icon_style,
			) );

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $icon_selector,
				'declaration' => $icon_tablet_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			) );

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $icon_selector,
				'declaration' => $icon_phone_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			) );

			if ( '' !== $icon_style_hover ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => $this->add_hover_to_order_class( $icon_selector ),
					'declaration' => $icon_style_hover,
				) );
			}

			$image = ( '' !== $font_icon ) ? sprintf(
				'<span class="et-pb-icon et-waypoint%2$s%3$s%4$s%6$s%7$s">%1$s</span>',
				esc_attr( et_pb_process_font_icon( $font_icon ) ),
				esc_attr( " et_pb_animation_{$animation}" ),
				( 'on' === $use_circle ? ' et-pb-icon-circle' : '' ),
				( 'on' === $use_circle && 'on' === $use_circle_border ? ' et-pb-icon-circle-border' : '' ),
				$icon_style,
				! empty( $animation_tablet ) ? esc_attr( " et_pb_animation_{$animation_tablet}_tablet" ) : '',
				! empty( $animation_phone ) ? esc_attr( " et_pb_animation_{$animation_phone}_phone" ) : ''
			) : '';
		}

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		$generate_css_image_filters = '';
		if ( $image && array_key_exists( 'icon_settings', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['icon_settings'] ) ) {
			$generate_css_image_filters = $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['icon_settings']['css'], 'main', '%%order_class%%' )
			);
		}

		$image = $image ? sprintf( '<span class="et_pb_image_wrap">%1$s</span>', $image ) : '';
		$image = $image ? sprintf(
			'<div class="et_pb_main_blurb_image%2$s">%1$s</div>',
			( '' !== $url
				? sprintf(
					'<a href="%1$s"%3$s>%2$s</a>',
					esc_attr( $url ),
					$image,
					( 'on' === $url_new_window ? ' target="_blank"' : '' )
				)
				: $image
			),
			esc_attr( $generate_css_image_filters )
		) : '';

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Module classnames
		$this->add_classname( array(
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(),
			sprintf( ' et_pb_blurb_position_%1$s', esc_attr( $icon_placement ) ),
		) );

		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}

		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		if ( ! empty( $icon_placement_tablet ) ) {
			$this->add_classname( "et_pb_blurb_position_{$icon_placement_tablet}_tablet" );
		}

		if ( ! empty( $icon_placement_phone ) ) {
			$this->add_classname( "et_pb_blurb_position_{$icon_placement_phone}_phone" );
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
			'<div%5$s class="%4$s"%8$s%9$s>
				%7$s
				%6$s
				<div class="et_pb_blurb_content">
					%2$s
					<div class="et_pb_blurb_container">
						%3$s
						<div class="et_pb_blurb_description">
							%1$s
						</div><!-- .et_pb_blurb_description -->
					</div>
				</div> <!-- .et_pb_blurb_content -->
			</div> <!-- .et_pb_blurb -->',
			$this->content,
			et_core_esc_previously( $image ),
			et_core_esc_previously( $title ),
			$this->module_classname( $render_slug ),
			$this->module_id(), // #5
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover )
		);

		return $output;
	}
}

new ET_Builder_Module_Blurb;
