<?php

class ET_Builder_Module_Pricing_Tables extends ET_Builder_Module {
	function init() {
		$this->name                 = esc_html__( 'Pricing Tables', 'et_builder' );
		$this->slug                 = 'et_pb_pricing_tables';
		$this->vb_support           = 'on';
		$this->main_css_element     = '%%order_class%%';
		$this->child_slug           = 'et_pb_pricing_table';
		$this->child_item_text      = esc_html__( 'Pricing Table', 'et_builder' );

		$this->additional_shortcode = 'et_pb_pricing_item';
		$this->custom_css_fields = array(
			'pricing_heading' => array(
				'label'    => esc_html__( 'Pricing Heading', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading',
			),
			'pricing_title' => array(
				'label'    => esc_html__( 'Pricing Title', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading h2',
			),
			'pricing_subtitle' => array(
				'label'    => esc_html__( 'Pricing Subtitle', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading .et_pb_best_value',
			),
			'pricing_top' => array(
				'label'    => esc_html__( 'Pricing Top', 'et_builder' ),
				'selector' => '.et_pb_pricing_content_top',
			),
			'price' => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => '.et_pb_et_price',
			),
			'currency' => array(
				'label'    => esc_html__( 'Currency', 'et_builder' ),
				'selector' => '.et_pb_dollar_sign',
			),
			'frequency' => array(
				'label'    => esc_html__( 'Frequency', 'et_builder' ),
				'selector' => '.et_pb_frequency',
			),
			'pricing_content' => array(
				'label'    => esc_html__( 'Pricing Content', 'et_builder' ),
				'selector' => '.et_pb_pricing_content',
			),
			'pricing_item' => array(
				'label'    => esc_html__( 'Pricing Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li',
			),
			'pricing_item_excluded' => array(
				'label'    => esc_html__( 'Excluded Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li.et_pb_not_available',
			),
			'pricing_button' => array(
				'label'    => esc_html__( 'Pricing Button', 'et_builder' ),
				'selector' => '.et_pb_pricing_table_button',
			),
			'featured_table' => array(
				'label'    => esc_html__( 'Featured Table', 'et_builder' ),
				'selector' => '.et_pb_featured_table',
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'    => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
					'bullet' => esc_html__( 'Bullet', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					// @TODO
					'additional_elements' => array(
						array(
							"{$this->main_css_element} .et_pb_pricing_content_top" => array( 'bottom' ),
						),
					),
					'css'                 => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_pricing_table",
							'border_styles' => "{$this->main_css_element} .et_pb_pricing_table",
						),
					),
					'defaults' => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#bebebe',
							'style' => 'solid',
						),
					),
				),
			),
			'fonts'                 => array(
				'header' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_pricing_heading h2, {$this->main_css_element} .et_pb_pricing_heading h1.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h3.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h4.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h5.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h6.et_pb_pricing_title",
						'important' => 'all',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_pricing li",
						'plugin_main' => "{$this->main_css_element} .et_pb_pricing li, {$this->main_css_element} .et_pb_pricing li span, {$this->main_css_element} .et_pb_pricing li a",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'font_size' => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'subheader' => array(
					'label'    => esc_html__( 'Subheader', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_best_value",
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'currency_frequency' => array(
					'label'    => esc_html__( 'Currency &amp; Frequency', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_dollar_sign, {$this->main_css_element} .et_pb_frequency",
					),
					'hide_text_align' => true,
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_sum",
						'text_align' => "{$this->main_css_element} .et_pb_pricing_content_top",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
			'background'            => array(
				'css'  => array(
					'main' => "{$this->main_css_element} .et_pb_pricing_table",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_pricing_table_button.et_pb_button",
						'alignment'   => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment' => true,
					'box_shadow' => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important'      => 'all', // needed to overwrite last module margin-bottom styling
					'main'           => '%%order_class%% .et_pb_pricing_heading, %%order_class%% .et_pb_pricing_content_top, %%order_class%% .et_pb_pricing_content',
					'padding-right'  => '%%order_class%% .et_pb_button_wrapper',
					'padding-bottom' => '%%order_class%% .et_pb_pricing_table',
					'padding-left'   => '%%order_class%% .et_pb_button_wrapper',
				),
			),
			'text'                  => array(
				'css' => array(
					'text_orientation' => '%%order_class%% .et_pb_pricing_table, %%order_class%% .et_pb_pricing_content',
					'text_shadow'      => '%%order_class%% .et_pb_pricing_heading, %%order_class%% .et_pb_pricing_content_top, %%order_class%% .et_pb_pricing_content',
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'BVzu4WnjgYI' ),
				'name' => esc_html__( 'An introduction to the Pricing Tables module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'featured_table_background_color' => array(
				'label'             => esc_html__( 'Featured Table Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'layout',
				'priority'          => 23,
			),
			'featured_table_header_background_color' => array(
				'label'             => esc_html__( 'Featured Table Header Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'header',
				'priority'          => 21,
			),
			'featured_table_header_text_color' => array(
				'label'             => esc_html__( 'Featured Table Header Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'header',
				'priority'          => 20,
			),
			'header_background_color' => array(
				'label'             => esc_html__( 'Table Header Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'header',
			),
			'featured_table_subheader_text_color' => array(
				'label'             => esc_html__( 'Featured Table Subheader Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'subheader',
				'priority'          => 20,
			),
			'featured_table_price_color' => array(
				'label'             => esc_html__( 'Featured Table Price Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'price',
				'priority'          => 20,
			),
			'featured_table_text_color' => array(
				'label'             => esc_html__( 'Featured Table Body Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'body',
				'priority'          => 22,
			),
			'show_bullet' => array(
				'label'           => esc_html__( 'Show Bullet', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'affects'          => array(
					'bullet_color',
				),
			),
			'bullet_color' => array(
				'label'             => esc_html__( 'Bullet Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'bullet',
				'depends_show_if'   => 'on',
			),
			'featured_table_bullet_color' => array(
				'label'             => esc_html__( 'Featured Table Bullet Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'bullet',
				'priority'          => 22,
			),
			'show_featured_drop_shadow' => array(
				'label'           => esc_html__( 'Show Featured Table Drop Shadow', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'priority'        => 24,
			),
			'center_list_items' => array(
				'label'           => esc_html__( 'Center List Items', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
		);
		return $fields;
	}

	function before_render() {
		global $et_pb_pricing_tables_num, $et_pb_pricing_tables_icon, $et_pb_pricing_tab, $et_pb_pricing_tables_button_rel, $et_pb_pricing_tables_header_level;

		$button_custom = $this->props['custom_button'];
		$custom_icon   = $this->props['button_icon'];

		$et_pb_pricing_tables_num = 0;

		$et_pb_pricing_tables_icon = 'on' === $button_custom ? $custom_icon : '';

		$et_pb_pricing_tables_button_rel = $this->props['button_rel'];
		$et_pb_pricing_tables_header_level = 'h2' === $this->props['header_level'] ? '' : $this->props['header_level'];
	}

	function render( $attrs, $content = null, $render_slug ) {
		$featured_table_background_color        = $this->props['featured_table_background_color'];
		$featured_table_text_color              = $this->props['featured_table_text_color'];
		$header_background_color                = $this->props['header_background_color'];
		$featured_table_header_background_color = $this->props['featured_table_header_background_color'];
		$featured_table_header_text_color       = $this->props['featured_table_header_text_color'];
		$featured_table_subheader_text_color    = $this->props['featured_table_subheader_text_color'];
		$featured_table_price_color             = $this->props['featured_table_price_color'];
		$bullet_color                           = $this->props['bullet_color'];
		$featured_table_bullet_color            = $this->props['featured_table_bullet_color'];
		$show_featured_drop_shadow              = $this->props['show_featured_drop_shadow'];
		$center_list_items                      = $this->props['center_list_items'];
		$show_bullet                            = $this->props['show_bullet'];
		$featured_table                         = $this->get_featured_table( $content );

		global $et_pb_pricing_tables_num, $et_pb_pricing_tables_icon;

		if ( 'on' !== $show_featured_drop_shadow ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_featured_table',
				'declaration' => '-moz-box-shadow: none; -webkit-box-shadow: none; box-shadow: none;',
			) );
		}

		if ( 'off' === $show_bullet ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_pricing li span:before',
				'declaration' => 'display: none;',
			) );
		}

		if ( '' !== $featured_table_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_featured_table',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $featured_table_background_color )
				),
			) );
		}

		if ( '' !== $header_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_pricing_heading',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $header_background_color )
				),
			) );
		}

		if ( '' !== $featured_table_header_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_pricing_heading',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $featured_table_header_background_color )
				),
			) );
		}

		if ( '' !== $featured_table_header_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_pricing_heading h2, %%order_class%% .et_pb_featured_table .et_pb_pricing_heading .et_pb_pricing_title',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $featured_table_header_text_color )
				),
			) );
		}

		if ( '' !== $featured_table_subheader_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_best_value',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $featured_table_subheader_text_color )
				),
			) );
		}

		if ( '' !== $featured_table_price_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_sum',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $featured_table_price_color )
				),
			) );
		}

		if ( '' !== $featured_table_text_color ) {
			$featured_table_text_color_selector = et_is_builder_plugin_active() ? '%%order_class%% .et_pb_featured_table .et_pb_pricing_content li, %%order_class%% .et_pb_featured_table .et_pb_pricing_content li span, %%order_class%% .et_pb_featured_table .et_pb_pricing_content li a' : '%%order_class%% .et_pb_featured_table .et_pb_pricing_content li';
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $featured_table_text_color_selector,
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $featured_table_text_color )
				),
			) );
		}

		if ( '' !== $bullet_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_pricing li span:before',
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $bullet_color )
				),
			) );
		}

		if ( '' !== $featured_table_bullet_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_featured_table .et_pb_pricing li span:before',
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $featured_table_bullet_color )
				),
			) );
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$content                   = $this->content;

		// Module classnames
		$this->add_classname( array(
			'et_pb_pricing',
			'clearfix',
			"et_pb_pricing_{$et_pb_pricing_tables_num}",
			$featured_table,
		) );

		if ( 'on' === $center_list_items ) {
			$this->add_classname( 'et_pb_centered_pricing_items' );
		}

		// Remove automatically added classnames
		$this->remove_classname( array(
			$render_slug
		) );

		$output = sprintf(
			'<div%3$s class="%2$s">
				%5$s
				%4$s
				<div class="et_pb_pricing_table_wrap">
					%1$s
				</div>
			</div>',
			$content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background
		);

		$output .= $this->keep_box_shadow_compatibility( $attrs, $content, $render_slug );

		return $output;
	}

	function additional_render( $atts, $content = null, $function_name ) {
		$attributes = shortcode_atts( array(
			'available' => 'on',
		), $atts );

		$output = sprintf( '<li%2$s><span>%1$s</span></li>',
			$content,
			( 'on' !== $attributes['available'] ? ' class="et_pb_not_available"' : '' )
		);
		return $output;
	}

	private function get_featured_table( $content ) {
		//Extract `et_pb_pricing_table` shortcode attributes
		preg_match_all( '/\[et_pb_pricing_table(\s+[^\]]*)\]/', $content, $matches );

		if ( ! isset( $matches[1] ) || 0 === count( $matches[1] ) ) {
			return '';
		}

		$list = array();

		foreach ( $matches[1] as $match ) {
			//Check if the shortcode has the `feature` attribute on
			//TODO: Find a better way to do that
			$list[] = (bool) preg_match( '/[\s]featured=[\'|"]on[\'|"]/', $match );
		}

		//We need to know only the first 4 tables status,
		//because in a row are maximum 4 tables
		$count = count( $list ) > 4 ? 4 : count( $list );

		for ( $i = 0; $i < $count; $i ++ ) {
			if ( true === $list[ $i ] ) {
				switch ( $i ) {
					case 0 :
						return '';
					case 1 :
						return 'et_pb_second_featured';
					case 2 :
						return 'et_pb_third_featured';
					case 3 :
						return 'et_pb_fourth_featured';
				}
			}
		}

		return 'et_pb_no_featured_in_first_row';
	}

	private function keep_box_shadow_compatibility( $atts, $content, $function_name ) {
		/**
		 * @var ET_Builder_Module_Field_BoxShadow $box_shadow
		 */
		$box_shadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$utils      = ET_Core_Data_Utils::instance();

		if (
			! is_admin()
			&&
			version_compare( $utils->array_get( $atts, '_builder_version', '3.0.93' ), '3.0.97', 'lt' )
			&&
			$box_shadow->is_inset( $box_shadow->get_value( $atts ) )
		) {
			$class = '.' . self::get_module_order_class($function_name);
			$overlay_shadow = $box_shadow->get_style( $class, $atts);

			return sprintf(
				'<style type="text/css">%1$s %2$s %3$s</style>',
				'.et_pb_pricing > .box-shadow-overlay { z-index: 11; }',
				sprintf( '%1$s { box-shadow: none; }', esc_attr( $class ) ),
				sprintf( '%1$s { %2$s }', esc_attr( $overlay_shadow['selector'] ), esc_attr( $overlay_shadow['declaration'] ) )
			);
		}

		return '';
	}
}

new ET_Builder_Module_Pricing_Tables;
