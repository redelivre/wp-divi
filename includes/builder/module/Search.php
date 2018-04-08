<?php

class ET_Builder_Module_Search extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Search', 'et_builder' );
		$this->slug       = 'et_pb_search';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'background_layout',
			'admin_label',
			'module_id',
			'module_class',
			'include_categories',
			'exclude_posts',
			'exclude_pages',
			'button_color',
			'show_button',
			'placeholder',
			'placeholder_color',
		);

		$this->fields_defaults = array(
			'background_layout' => array( 'light' ),
			'text_orientation'  => array( 'left' ),
			'show_button'       => array( 'on' ),
		);

		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
					'exceptions'   => esc_html__( 'Exceptions', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'field' => esc_html__( 'Search Field', 'et_builder' ),
					'text'  => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'width' => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 65,
					),
				),
			),
		);
		$this->advanced_options = array(
			'fonts' => array(
				'input' => array(
					'label'    => esc_html__( 'Input', 'et_builder' ),
					'css'      => array(
						'main'        => "{$this->main_css_element} input.et_pb_s",
						'placeholder' => true,
						'important'   => array( 'line-height', 'text-shadow' ),
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'button' => array(
					'label'          => esc_html__( 'Button', 'et_builder' ),
					'css'            => array(
						'main'      => "{$this->main_css_element} input.et_pb_searchsubmit",
						'important' => array( 'line-height', 'text-shadow' ),
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'hide_text_align' => true,
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'main'      => "{$this->main_css_element} input.et_pb_s",
					'important' => 'all',
				),
			),
			'background' => array(
				'css' => array(
					'main' => "{$this->main_css_element} input.et_pb_s",
				),
			),
			'border' => array(
				'css' => array(
					'main' => array(
						'border_radii' => "{$this->main_css_element}.et_pb_search, {$this->main_css_element} input.et_pb_s",
						'border_styles' => "{$this->main_css_element}.et_pb_search",
					),
				),
				'defaults' => array(
					'border_radii' => 'on|3px|3px|3px|3px',
					'border_styles' => array(
						'width' => '1px',
						'color' => '#dddddd',
						'style' => 'solid',
					),
				),
			),
			'max_width'  => array(),
			'text'       => array(
				'css'              => array(
					'text_shadow' => "{$this->main_css_element} input.et_pb_searchsubmit, {$this->main_css_element} input.et_pb_s",
				),
				'text_orientation' => array(
					'exclude_options' => array( 'justified' ),
				),
			),
			'filters' => array(),
		);

		$this->custom_css_options = array(
			'input_field' => array(
				'label'    => esc_html__( 'Input Field', 'et_builder' ),
				'selector' => 'input.et_pb_s',
			),
			'button'      => array(
				'label'    => esc_html__( 'Button', 'et_builder' ),
				'selector' => 'input.et_pb_searchsubmit',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'light' => esc_html__( 'Dark', 'et_builder' ),
					'dark'  => esc_html__( 'Light', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose the value of your text. If you are working with a dark background, then your text should be set to light. If you are working with a light background, then your text should be dark.', 'et_builder' ),
			),
			'exclude_pages' => array(
				'label'           => esc_html__( 'Exclude Pages', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'description'     => esc_html__( 'Turning this on will exclude Pages from search results', 'et_builder' ),
				'toggle_slug'     => 'exceptions',
			),
			'exclude_posts' => array(
				'label'           => esc_html__( 'Exclude Posts', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'         => array(
					'include_categories',
				),
				'description'     => esc_html__( 'Turning this on will exclude Posts from search results', 'et_builder' ),
				'toggle_slug'     => 'exceptions',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Exclude Categories', 'et_builder' ),
				'renderer'         => 'et_builder_include_categories_option',
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'depends_show_if'  => 'off',
				'description'      => esc_html__( 'Choose which categories you would like to exclude from the search results.', 'et_builder' ),
				'toggle_slug'      => 'exceptions',
			),
			'show_button' => array(
				'label'           => esc_html__( 'Show Button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turn this on to show the Search button', 'et_builder' ),
				'default'         => 'on',
			),
			'placeholder' => array(
				'label'       => esc_html__( 'Placeholder Text', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Type the text you want to use as placeholder for the search field.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'button_color' => array(
				'label'        => esc_html__( 'Button and Border Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'button',
			),
			'placeholder_color' => array(
				'label'        => esc_html__( 'Placeholder Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'field',
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
		);

		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id          = $this->shortcode_atts['module_id'];
		$module_class       = $this->shortcode_atts['module_class'];
		$background_layout  = $this->shortcode_atts['background_layout'];
		$exclude_categories = $this->shortcode_atts['include_categories'];
		$exclude_posts      = $this->shortcode_atts['exclude_posts'];
		$exclude_pages      = $this->shortcode_atts['exclude_pages'];
		$button_color       = $this->shortcode_atts['button_color'];
		$show_button        = $this->shortcode_atts['show_button'];
		$placeholder        = $this->shortcode_atts['placeholder'];
		$placeholder_color  = $this->shortcode_atts['placeholder_color'];
		$input_line_height  = $this->shortcode_atts['input_line_height'];

		$module_class              = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$this->shortcode_content = et_builder_replace_code_content_entities( $this->shortcode_content );

		if ( '' !== $button_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input.et_pb_searchsubmit',
				'declaration' => sprintf(
					'background: %1$s !important;border-color:%1$s !important;',
					esc_html( $button_color )
				),
			) );

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input.et_pb_s',
				'declaration' => sprintf(
					'border-color:%1$s !important;',
					esc_html( $button_color )
				),
			) );
		}

		if ( '' !== $placeholder_color ) {
			$placeholder_selectors = array(
				'%%order_class%% form input.et_pb_s::-webkit-input-placeholder',
				'%%order_class%% form input.et_pb_s::-moz-placeholder',
				'%%order_class%% form input.et_pb_s:-ms-input-placeholder',
			);

			foreach ( $placeholder_selectors as $single_selector ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $single_selector,
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $placeholder_color )
					),
				) );
			}
		}

		if ( '' !== $input_line_height ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input.et_pb_s',
				'declaration' => 'height: auto; min-height: 0;',
			) );
		}

		$custom_margin = explode('|', $this->shortcode_atts['custom_margin']);
		$has_custom_margin = isset( $custom_margin[0], $custom_margin[1], $custom_margin[2],  $custom_margin[3] );
		$custom_margin_units = array();

		if ( $has_custom_margin ) {
			$button_top    = $custom_margin[0];
			$button_bottom = $custom_margin[2];
			$custom_margin_left_unit = et_pb_get_value_unit( $custom_margin[3] );
			$button_right  = ( 0 - floatval( $custom_margin[3] ) ) . $custom_margin_left_unit;

			$custom_margin_units = array(
				et_pb_get_value_unit( $custom_margin[0] ),
				et_pb_get_value_unit( $custom_margin[1] ),
				et_pb_get_value_unit( $custom_margin[2] ),
				$custom_margin_left_unit,
			);

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%%.et_pb_search input.et_pb_searchsubmit',
				'declaration' => sprintf(
					'min-height: 0 !important; top: %1$s; right: %2$s; bottom: %3$s;',
					esc_html( $button_top ),
					esc_html( $button_right ),
					esc_html( $button_bottom )
				),
			) );
		}

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}{$this->get_text_orientation_classname(true)}";
		$class .= 'on' !== $show_button ? ' et_pb_hide_search_button' : '';

		if ( ! empty( $custom_margin_units ) && in_array( '%', $custom_margin_units ) ) {
			$class .= " et_pb_search_percentage_custom_margin";
		}

		$output = sprintf(
			'<div%3$s class="et_pb_search%2$s%4$s%11$s%13$s">
				%14$s
				%12$s
				<form role="search" method="get" class="et_pb_searchform" action="%1$s">
					<div>
						<label class="screen-reader-text" for="s">%9$s</label>
						<input type="text" value="" name="s" class="et_pb_s"%8$s>
						<input type="hidden" name="et_pb_searchform_submit" value="et_search_proccess" />
						%5$s
						%6$s
						%7$s
						<input type="submit" value="%10$s" class="et_pb_searchsubmit">
					</div>
				</form>
			</div> <!-- .et_pb_text -->',
			esc_url( home_url( '/' ) ),
			esc_attr( $class ),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'' !== $exclude_categories ? sprintf( '<input type="hidden" name="et_pb_search_cat" value="%1$s" />', esc_attr( $exclude_categories ) ) : '',
			'on' !== $exclude_posts ? '<input type="hidden" name="et_pb_include_posts" value="yes" />' : '',
			'on' !== $exclude_pages ? '<input type="hidden" name="et_pb_include_pages" value="yes" />' : '',
			'' !== $placeholder ? sprintf( ' placeholder="%1$s"', esc_attr( $placeholder ) ) : '',
			esc_html__( 'Search for:', 'et_builder' ),
			esc_attr__( 'Search', 'et_builder' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Search;
