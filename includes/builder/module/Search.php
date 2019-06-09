<?php

class ET_Builder_Module_Search extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Search', 'et_builder' );
		$this->plural     = esc_html__( 'Searches', 'et_builder' );
		$this->slug       = 'et_pb_search';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
					'exceptions'   => esc_html__( 'Exceptions', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
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
		$this->advanced_fields = array(
			'fonts'                 => array(
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
			'margin_padding' => array(
				'css' => array(
					'main'      => "{$this->main_css_element} input.et_pb_s",
					'important' => 'all',
				),
			),
			'background'            => array(
				'css' => array(
					'main' => "{$this->main_css_element} input.et_pb_s",
				),
			),
			'borders'               => array(
				'default' => array(
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
			),
			'text'                  => array(
				'use_background_layout' => true,
				'css'              => array(
					'main' => "{$this->main_css_element} input.et_pb_searchsubmit, {$this->main_css_element} input.et_pb_s",
					'text_shadow' => "{$this->main_css_element} input.et_pb_searchsubmit, {$this->main_css_element} input.et_pb_s",
				),
				'text_orientation' => array(
					'exclude_options' => array( 'justified' ),
				),
				'options' => array(
					'text_orientation'  => array(
						'default'          => 'left',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover' => 'tabs',
					),
				),
			),
			'button'                => false,
			'link_options'          => false,
			'form_field'           => array(
				'form_field' => array(
					'label'          => esc_html__( 'Field', 'et_builder' ),
					'css'            => array(
						'main'        => '%%order_class%% form input.et_pb_s',
						'hover'       => '%%order_class%% form input.et_pb_s:hover',
						'focus'       => '%%order_class%% form input.et_pb_s:focus',
						'focus_hover' => '%%order_class%% form input.et_pb_s:focus:hover',
					),
					'placeholder'    => false,
					'margin_padding' => false,
					'box_shadow'     => false,
					'border_styles'  => false,
					'font_field'     => array(
						'css'         => array(
							'main'        => implode(', ', array(
								'%%order_class%% form input.et_pb_s',
								'%%order_class%% form input.et_pb_s::placeholder',
								'%%order_class%% form input.et_pb_s::-webkit-input-placeholder',
								'%%order_class%% form input.et_pb_s::-ms-input-placeholder',
								'%%order_class%% form input.et_pb_s::-moz-placeholder',
							) ),
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
				),
			),
		);

		$this->custom_css_fields = array(
			'input_field' => array(
				'label'    => esc_html__( 'Input Field', 'et_builder' ),
				'selector' => 'input.et_pb_s',
			),
			'button'      => array(
				'label'    => esc_html__( 'Button', 'et_builder' ),
				'selector' => 'input.et_pb_searchsubmit',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'HNmb20Mdvno' ),
				'name' => esc_html__( 'An introduction to the Search module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
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
				'type'             => 'categories',
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
				'default'         => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turn this on to show the Search button', 'et_builder' ),
			),
			'placeholder' => array(
				'label'           => esc_html__( 'Input Placeholder', 'et_builder' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Type the text you want to use as placeholder for the search field.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'button_color' => array(
				'label'          => esc_html__( 'Button and Border Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'button',
				'hover'          => 'tabs',
				'mobile_options' => true,
			),
			'placeholder_color' => array(
				'label'          => esc_html__( 'Placeholder Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to be used for the placeholder written inside input fields.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'form_field',
				'hover'          => 'tabs',
				'mobile_options' => true,
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['placeholder_color'] = array('color' => array(
			'%%order_class%% form input.et_pb_s::placeholder',
			'%%order_class%% form input.et_pb_s::-webkit-input-placeholder',
			'%%order_class%% form input.et_pb_s::-ms-input-placeholder',
			'%%order_class%% form input.et_pb_s::-moz-placeholder',
		));

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$exclude_categories              = $this->props['include_categories'];
		$exclude_posts                   = $this->props['exclude_posts'];
		$exclude_pages                   = $this->props['exclude_pages'];
		$show_button                     = $this->props['show_button'];
		$placeholder                     = $this->props['placeholder'];
		$input_line_height               = $this->props['form_field_line_height'];
		$button_color_hover              = $this->get_hover_value( 'button_color' );
		$button_color_values             = et_pb_responsive_options()->get_property_values( $this->props, 'button_color' );
		$placeholder_color_hover         = $this->get_hover_value( 'placeholder_color' );
		$placeholder_color_values        = et_pb_responsive_options()->get_property_values( $this->props, 'placeholder_color' );

		$video_background                = $this->video_background();
		$parallax_image_background       = $this->get_parallax_image_background();

		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet        = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone         = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';

		$this->content = et_builder_replace_code_content_entities( $this->content );

		// Button Color.
		et_pb_responsive_options()->generate_responsive_css( $button_color_values, '%%order_class%% input.et_pb_searchsubmit', array( 'background-color', 'border-color' ), $render_slug, ' !important;', 'color' );
		et_pb_responsive_options()->generate_responsive_css( $button_color_values, '%%order_class%% input.et_pb_s', 'border-color', $render_slug, ' !important;', 'color' );

		if ( et_builder_is_hover_enabled( 'button_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% input.et_pb_searchsubmit:hover',
				'declaration' => sprintf(
					'background: %1$s !important;border-color:%1$s !important;',
					esc_html( $button_color_hover )
				),
			) );

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% input.et_pb_s:hover',
				'declaration' => sprintf(
					'border-color:%1$s !important;',
					esc_html( $button_color_hover )
				),
			) );
		}

		// Placeholder Color.
		$placeholder_selectors = array(
			'%%order_class%% form input.et_pb_s::-webkit-input-placeholder',
			'%%order_class%% form input.et_pb_s::-moz-placeholder',
			'%%order_class%% form input.et_pb_s:-ms-input-placeholder',
		);

		et_pb_responsive_options()->generate_responsive_css( $placeholder_color_values, join( ', ', $placeholder_selectors ), 'color', $render_slug, ' !important;', 'color' );

		if ( et_builder_is_hover_enabled( 'placeholder_color', $this->props ) ) {
			$placeholder_selectors = array(
				'%%order_class%% form input.et_pb_s:hover::-webkit-input-placeholder',
				'%%order_class%% form input.et_pb_s:hover::-moz-placeholder',
				'%%order_class%% form input.et_pb_s:hover:-ms-input-placeholder',
			);

			foreach ( $placeholder_selectors as $single_selector ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => $single_selector,
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $placeholder_color_hover )
					),
				) );
			}
		}

		if ( '' !== $input_line_height ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% input.et_pb_s',
				'declaration' => 'height: auto; min-height: 0;',
			) );
		}

		$custom_margin = explode('|', $this->props['custom_margin']);
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

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_search input.et_pb_searchsubmit',
				'declaration' => sprintf(
					'min-height: 0 !important; top: %1$s; right: %2$s; bottom: %3$s;',
					esc_html( $button_top ),
					esc_html( $button_right ),
					esc_html( $button_bottom )
				),
			) );
		}

		// Module classnames
		$this->add_classname( array(
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(true),
		) );

		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}

		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		if ( 'on' !== $show_button ) {
			$this->add_classname( 'et_pb_hide_search_button' );
		}

		if ( ! empty( $custom_margin_units ) && in_array( '%', $custom_margin_units ) ) {
			$this->add_classname( 'et_pb_search_percentage_custom_margin' );
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
			'<div%3$s class="%2$s"%12$s%13$s>
				%11$s
				%10$s
				<form role="search" method="get" class="et_pb_searchform" action="%1$s">
					<div>
						<label class="screen-reader-text" for="s">%8$s</label>
						<input type="text" value="" name="s" class="et_pb_s"%7$s>
						<input type="hidden" name="et_pb_searchform_submit" value="et_search_proccess" />
						%4$s
						%5$s
						%6$s
						<input type="submit" value="%9$s" class="et_pb_searchsubmit">
					</div>
				</form>
			</div> <!-- .et_pb_text -->',
			esc_url( home_url( '/' ) ),
			$this->module_classname( $render_slug ),
			$this->module_id(),
			'' !== $exclude_categories ? sprintf( '<input type="hidden" name="et_pb_search_cat" value="%1$s" />', esc_attr( $exclude_categories ) ) : '',
			'on' !== $exclude_posts ? '<input type="hidden" name="et_pb_include_posts" value="yes" />' : '', // #5
			'on' !== $exclude_pages ? '<input type="hidden" name="et_pb_include_pages" value="yes" />' : '',
			'' !== $placeholder ? sprintf( ' placeholder="%1$s"', esc_attr( $placeholder ) ) : '',
			esc_html__( 'Search for:', 'et_builder' ),
			esc_attr__( 'Search', 'et_builder' ),
			$video_background, // #10
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover )
		);

		return $output;
	}
}

new ET_Builder_Module_Search;
