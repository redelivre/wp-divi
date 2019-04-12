<?php

class ET_Builder_Module_Pricing_Tables_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Pricing Table', 'et_builder' );
		$this->plural                      = esc_html__( 'Pricing Tables', 'et_builder' );
		$this->slug                        = 'et_pb_pricing_table';
		$this->vb_support                  = 'on';
		$this->main_css_element            = '%%order_class%%';
		$this->type                        = 'child';
		$this->child_title_var             = 'title';
		$this->advanced_setting_title_text = esc_html__( 'New Pricing Table', 'et_builder' );
		$this->settings_text               = esc_html__( 'Pricing Table Settings', 'et_builder' );

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'   => esc_html__( 'Layout', 'et_builder' ),
					'bullet'   => esc_html__( 'Bullet', 'et_builder' ),
					'excluded' => esc_html__( 'Excluded Item', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css'                 => array(
						'main' => array(
							'border_radii'  => ".et_pb_pricing .et_pb_pricing_table%%order_class%%",
							'border_styles' => ".et_pb_pricing .et_pb_pricing_table%%order_class%%",
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
						'main' => "{$this->main_css_element} .et_pb_pricing_heading h2, {$this->main_css_element} .et_pb_pricing_heading h1.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h3.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h4.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h5.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h6.et_pb_pricing_title,
						           {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h2, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h1.et_pb_pricing_title, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h3.et_pb_pricing_title, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h4.et_pb_pricing_title, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h5.et_pb_pricing_title, {$this->main_css_element}.et_pb_featured_table .et_pb_pricing_heading h6.et_pb_pricing_title",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_pricing li",
						'limited_main' => "{$this->main_css_element} .et_pb_pricing li, {$this->main_css_element} .et_pb_pricing li span, {$this->main_css_element} .et_pb_pricing li a",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
				'subheader' => array(
					'label'    => esc_html__( 'Subheader', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_pricing_heading .et_pb_best_value",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
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
						'main' => "{$this->main_css_element} .et_pb_et_price .et_pb_sum",
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
				'css' => array(
					'main' => "{$this->main_css_element}.et_pb_pricing_table",
				),
				'settings' => array(
					'color'       => 'alpha',
				),
			),
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css'      => array(
						'main' => ".et_pb_pricing {$this->main_css_element} .et_pb_button",
						'limited_main' => ".et_pb_pricing {$this->main_css_element} .et_pb_pricing_table_button.et_pb_button",
						'alignment' => ".et_pb_pricing {$this->main_css_element} .et_pb_button_wrapper"
					),
					'use_alignment' => true,
					'box_shadow' => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button.et_pb_pricing_table_button',
						),
					),
				),
			),
			'margin_padding' => array(
				'use_margin' => false,
				'css' => array(
					'important'      => 'all', // Need to overwrite pricing table's styling
					'main'           => '.et_pb_pricing %%order_class%% .et_pb_pricing_heading, .et_pb_pricing %%order_class%% .et_pb_pricing_content_top, .et_pb_pricing %%order_class%% .et_pb_pricing_content',

					'padding-right'  => '%%order_class%% .et_pb_button_wrapper',
					'padding-bottom' => '.et_pb_pricing %%order_class%%',
					'padding-left'   => '%%order_class%% .et_pb_button_wrapper',
				),
			),
			'text'                  => array(
				'css' => array(
					'text_orientation' => '%%order_class%%.et_pb_pricing_table, %%order_class%% .et_pb_pricing_content',
					'text_shadow'      => '%%order_class%% .et_pb_pricing_heading, %%order_class%% .et_pb_pricing_content_top, %%order_class%% .et_pb_pricing_content',
				),
			),
			'max_width'             => false,
		);

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
		);
	}

	function get_fields() {
		$fields = array(
			'featured' => array(
				'label'           => esc_html__( 'Make This Table Featured', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'description'     => esc_html__( 'Featuring a table will make it stand out from the rest.', 'et_builder' ),
			),
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a title for the pricing table.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'subtitle' => array(
				'label'           => esc_html__( 'Subtitle', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define a sub title for the table if desired.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'currency' => array(
				'label'           => esc_html__( 'Currency', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired currency symbol here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'per' => array(
				'label'           => esc_html__( 'Per', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If your pricing is subscription based, input the subscription payment cycle here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'sum' => array(
				'label'           => esc_html__( 'Price', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the value of the product here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'button_url' => array(
				'label'           => esc_html__( 'Button Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the destination URL for the signup button.', 'et_builder' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
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
			'button_text' => array(
				'label'           => esc_html__( 'Button Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Adjust the text used from the signup button.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'content' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => sprintf(
					'%1$s<br/> + %2$s<br/> - %3$s',
					esc_html__( 'Input a list of features that are/are not included in the product. Separate items on a new line, and begin with either a + or - symbol: ', 'et_builder' ),
					esc_html__( 'Included option', 'et_builder' ),
					esc_html__( 'Excluded option', 'et_builder' )
				),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'pricing_item_excluded_color' => array(
				'label'             => esc_html__( 'Excluded Item Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'excluded',
				'priority'          => 22,
				'hover'             => 'tabs',
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['pricing_item_excluded_color'] = array( 'color' => '%%order_class%% ul.et_pb_pricing li.et_pb_not_available, %%order_class%% ul.et_pb_pricing li.et_pb_not_available span, %%order_class%% ul.et_pb_pricing li.et_pb_not_available a' );

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		global $et_pb_pricing_tables_num, $et_pb_pricing_tables_icon, $et_pb_pricing_tables_button_rel, $et_pb_pricing_tables_header_level;

		$featured                          = $this->props['featured'];
		$title                             = $this->_esc_attr( 'title' );
		$subtitle                          = $this->_esc_attr( 'subtitle' );
		$currency                          = $this->_esc_attr( 'currency' );
		$per                               = $this->_esc_attr( 'per' );
		$sum                               = $this->_esc_attr( 'sum' );
		$button_url                        = $this->props['button_url'];
		$button_rel                        = $this->props['button_rel'];
		$button_text                       = $this->_esc_attr( 'button_text', 'limited' );
		$url_new_window                    = $this->props['url_new_window'];
		$button_custom                     = $this->props['custom_button'];
		$custom_icon                       = $this->props['button_icon'];
		$header_level                      = $this->props['header_level'];
		$pricing_item_excluded_color       = $this->props['pricing_item_excluded_color'];
		$pricing_item_excluded_color_hover = $this->get_hover_value( 'pricing_item_excluded_color' );

		// Overwrite button rel with pricin tables' button_rel if needed
		if ( in_array( $button_rel, array( '', 'off|off|off|off|off' ) ) && '' !== $et_pb_pricing_tables_button_rel ) {
			$button_rel = $et_pb_pricing_tables_button_rel;
		}

		$et_pb_pricing_tables_num++;

		$custom_table_icon = 'on' === $button_custom && '' !== $custom_icon ? $custom_icon : $et_pb_pricing_tables_icon;

		if ( '' !== $pricing_item_excluded_color ) {
			$pricing_item_excluded_color_selector = et_builder_has_limitation( 'use_additional_limiting_styles' ) ? '%%order_class%% ul.et_pb_pricing li.et_pb_not_available, %%order_class%% ul.et_pb_pricing li.et_pb_not_available span, %%order_class%% ul.et_pb_pricing li.et_pb_not_available a' : '%%order_class%% ul.et_pb_pricing li.et_pb_not_available';

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $pricing_item_excluded_color_selector,
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $pricing_item_excluded_color )
				),
			) );
		}

		if ( et_builder_is_hover_enabled( 'pricing_item_excluded_color', $this->props ) ) {
			$pricing_item_excluded_color_selector = et_is_builder_plugin_active() ? '%%order_class%% ul.et_pb_pricing:hover li.et_pb_not_available, %%order_class%% ul.et_pb_pricing:hover li.et_pb_not_available span, %%order_class%% ul.et_pb_pricing:hover li.et_pb_not_available a' : '%%order_class%% ul.et_pb_pricing:hover li.et_pb_not_available';
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $pricing_item_excluded_color_selector,
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $pricing_item_excluded_color_hover )
				),
			) );
		}

		$button_url = trim( $button_url );

		$button = $this->render_button( array(
			'button_classname'    => array( 'et_pb_pricing_table_button' ),
			'button_custom'       => '' !== $custom_table_icon ? 'on' : 'off',
			'button_rel'          => $button_rel,
			'button_text'         => $button_text,
			'button_text_escaped' => true,
			'button_url'          => $button_url,
			'custom_icon'         => $custom_table_icon,
			'url_new_window'      => $url_new_window,
			'display_button'      => ( '' !== $button_url && '' !== $button_text ),
		) );

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// inherit header level from parent settings
		$header_level = '' === $header_level && '' !== $et_pb_pricing_tables_header_level ? $et_pb_pricing_tables_header_level : $header_level;

		// Module classnames
		if ( 'off' !== $featured ) {
			$this->add_classname( 'et_pb_featured_table' );
		}

		// Remove automatically added classnames
		$this->remove_classname( array(
			'et_pb_module',
		) );

		$output = sprintf(
			'<div class="%1$s">
				%10$s
				%9$s
				<div class="et_pb_pricing_heading">
					%2$s
					%3$s
				</div> <!-- .et_pb_pricing_heading -->
				<div class="et_pb_pricing_content_top">
					<span class="et_pb_et_price">%6$s%7$s%8$s</span>
				</div> <!-- .et_pb_pricing_content_top -->
				<div class="et_pb_pricing_content">
					<ul class="et_pb_pricing">
						%4$s
					</ul>
				</div> <!-- .et_pb_pricing_content -->
				%5$s
			</div>',
			$this->module_classname( $render_slug ),
			( '' !== $title ? sprintf( '<%2$s class="et_pb_pricing_title">%1$s</%2$s>', et_core_esc_previously( $title ), et_pb_process_header_level( $header_level, 'h2' ) ) : '' ),
			( '' !== $subtitle ? sprintf( '<span class="et_pb_best_value">%1$s</span>', et_core_esc_previously( $subtitle ) ) : '' ),
			do_shortcode( et_pb_fix_shortcodes( et_pb_extract_items( $content ) ) ),
			$button,
			( '' !== $currency ? sprintf( '<span class="et_pb_dollar_sign">%1$s</span>', et_core_esc_previously( $currency ) ) : '' ),
			( '' !== $sum ? sprintf( '<span class="et_pb_sum">%1$s</span>', et_core_esc_previously( $sum ) ) : '' ),
			( '' !== $per ? sprintf( '<span class="et_pb_frequency">/%1$s</span>', et_core_esc_previously( $per ) ) : '' ),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Pricing_Tables_Item;
