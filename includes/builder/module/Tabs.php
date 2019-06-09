<?php

class ET_Builder_Module_Tabs extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Tabs', 'et_builder' );
		$this->plural          = esc_html__( 'Tabs', 'et_builder' );
		$this->slug            = 'et_pb_tabs';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_tab';
		$this->child_item_text = esc_html__( 'Tab', 'et_builder' );
		$this->main_css_element = '%%order_class%%.et_pb_tabs';

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => $this->main_css_element,
							'border_styles' => $this->main_css_element,
						),
					),
					'defaults' => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#d9d9d9',
							'style' => 'solid',
						),
					),
				),
			),
			'fonts'                 => array(
				'body' => array(
					'label'          => esc_html__( 'Body', 'et_builder' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_all_tabs .et_pb_tab",
						'limited_main' => "{$this->main_css_element} .et_pb_all_tabs .et_pb_tab, {$this->main_css_element} .et_pb_all_tabs .et_pb_tab p",
						'line_height'  => "{$this->main_css_element} .et_pb_tab p",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
					),
				),
				'tab'  => array(
					'label'            => esc_html__( 'Tab', 'et_builder' ),
					'css'              => array(
						'main'        => "{$this->main_css_element} .et_pb_tabs_controls li, {$this->main_css_element} .et_pb_tabs_controls li a",
						'color'       => "{$this->main_css_element} .et_pb_tabs_controls li a",
						'hover'       => "{$this->main_css_element} .et_pb_tabs_controls li:hover, {$this->main_css_element} .et_pb_tabs_controls li:hover a",
						'color_hover' => "{$this->main_css_element} .et_pb_tabs_controls li:hover a",
					),
					'hide_text_align'  => true,
					'options_priority' => array(
						'tab_text_color' => 9,
					),
				),
			),
			'background'            => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_all_tabs",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'padding' => '%%order_class%% .et_pb_tab',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'                  => false,
			'button'                => false,
		);

		$this->custom_css_fields = array(
			'tabs_controls' => array(
				'label'    => esc_html__( 'Tabs Controls', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls',
			),
			'tab' => array(
				'label'    => esc_html__( 'Tab', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls li',
			),
			'active_tab' => array(
				'label'    => esc_html__( 'Active Tab', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls li.et_pb_tab_active',
			),
			'tabs_content' => array(
				'label'    => esc_html__( 'Tabs Content', 'et_builder' ),
				'selector' => '.et_pb_tab',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'xk2Ite-oFhg' ),
				'name' => esc_html__( 'An introduction to the Tabs module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'active_tab_background_color' => array(
				'label'             => esc_html__( 'Active Tab Background Color', 'et_builder' ),
				'description'       => esc_html__( 'Pick a color to be used for active tab backgrounds. You can assign a unique color to active tabs to differentiate them from inactive tabs.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'tab',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'inactive_tab_background_color' => array(
				'label'             => esc_html__( 'Inactive Tab Background Color', 'et_builder' ),
				'description'       => esc_html__( 'Pick a color to be used for inactive tab backgrounds. You can assign a unique color to inactive tabs to differentiate them from active tabs.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'tab',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'active_tab_text_color'         => array(
				'label'          => esc_html__( 'Active Tab Text Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for tab text within active tabs. You can assign a unique color to active tabs to differentiate them from inactive tabs.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'tab',
				'hover'          => 'tabs',
				'mobile_options' => true,
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['inactive_tab_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_tabs_controls li' );
		$fields['active_tab_background_color']   = array( 'background-color' => '%%order_class%% .et_pb_tabs_controls li' );
		$fields['active_tab_text_color']         = array( 'color' => '%%order_class%% .et_pb_tabs_controls li a' );

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$active_tab_background_color_hover    = $this->get_hover_value( 'active_tab_background_color' );
		$active_tab_background_color_values   = et_pb_responsive_options()->get_property_values( $this->props, 'active_tab_background_color' );
		$inactive_tab_background_color_hover  = $this->get_hover_value( 'inactive_tab_background_color' );
		$inactive_tab_background_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'inactive_tab_background_color' );
		$active_tab_text_color_hover          = $this->get_hover_value( 'active_tab_text_color' );
		$active_tab_text_color_values         = et_pb_responsive_options()->get_property_values( $this->props, 'active_tab_text_color' );

		$all_tabs_content = $this->content;

		global $et_pb_tab_titles;
		global $et_pb_tab_classes;

		// Inactive Tab Background Color.
		et_pb_responsive_options()->generate_responsive_css( $inactive_tab_background_color_values, '%%order_class%% .et_pb_tabs_controls li', 'background-color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'inactive_tab_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_tabs_controls li:hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $inactive_tab_background_color_hover )
				),
			) );
		}

		// Active Tab Background Color.
		et_pb_responsive_options()->generate_responsive_css( $active_tab_background_color_values, '%%order_class%% .et_pb_tabs_controls li.et_pb_tab_active', 'background-color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'active_tab_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_tabs_controls li.et_pb_tab_active:hover',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $active_tab_background_color_hover )
				),
			) );
		}

		// Active Text Color
		et_pb_responsive_options()->generate_responsive_css( $active_tab_text_color_values, '%%order_class%% .et_pb_tabs_controls li.et_pb_tab_active a', 'color', $render_slug, ' !important;', 'color' );

		if ( et_builder_is_hover_enabled( 'active_tab_text_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_tabs_controls li.et_pb_tab_active:hover a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $active_tab_text_color_hover )
				),
			) );
		}

		$tabs = '';

		$i = 0;
		if ( ! empty( $et_pb_tab_titles ) ) {
			foreach ( $et_pb_tab_titles as $tab_title ){
				++$i;
				$tabs .= sprintf( '<li class="%3$s%1$s"><a href="#">%2$s</a></li>',
					( 1 === $i ? ' et_pb_tab_active' : '' ),
					esc_html( $tab_title ),
					esc_attr( ltrim( $et_pb_tab_classes[ $i-1 ] ) )
				);
			}
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$et_pb_tab_titles = $et_pb_tab_classes = array();

		// Module classnames
		$this->add_classname( array(
			$this->get_text_orientation_classname(),
		) );

		$output = sprintf(
			'<div%3$s class="%4$s">
				%6$s
				%5$s
				<ul class="et_pb_tabs_controls clearfix">
					%1$s
				</ul>
				<div class="et_pb_all_tabs">
					%2$s
				</div> <!-- .et_pb_all_tabs -->
			</div> <!-- .et_pb_tabs -->',
			$tabs,
			$all_tabs_content,
			$this->module_id(),
			$this->module_classname( $render_slug ),
			$video_background,
			$parallax_image_background
 		);

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$style     = $boxShadow->get_value( $this->props );

		if ( $boxShadow->is_inset( $style ) ) {
			$this->advanced_fields['box_shadow'] = array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .et-pb-active-slide',
					),
				),
			);


		}

		parent::process_box_shadow( $function_name );
	}
}

new ET_Builder_Module_Tabs;
