<?php

class ET_Builder_Module_Tabs_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Tab', 'et_builder' );
		$this->slug                        = 'et_pb_tab';
		$this->fb_support                  = true;
		$this->type                        = 'child';
		$this->child_title_var             = 'title';

		$this->whitelisted_fields = array(
			'title',
			'content_new',
		);

		$this->advanced_setting_title_text = esc_html__( 'New Tab', 'et_builder' );
		$this->settings_text               = esc_html__( 'Tab Settings', 'et_builder' );
		$this->main_css_element = '%%order_class%%';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'tab' => array(
					'label'    => esc_html__( 'Tab', 'et_builder' ),
					'css'      => array(
						'main'      => ".et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element}, .et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element} a",
						'color'     => ".et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element} a",
						'important' => 'all',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'hide_text_align' => true,
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'main' => ".et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab",
						'line_height' => ".et_pb_tabs {$this->main_css_element}.et_pb_tab p",
						'plugin_main' => ".et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab, .et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab p",
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
			'background' => array(
				'css' => array(
					'main' => ".et_pb_tabs {$this->main_css_element}.et_pb_tab",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'custom_margin_padding' => array(
				'use_margin'  => false,
				'css'         => array(
					'padding' => '.et_pb_tabs .et_pb_tab%%order_class%%',
				),
			),
			'filters' => array(),
		);

		$this->custom_css_options = array(
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'selector' => ".et_pb_tabs div{$this->main_css_element}.et_pb_tab",
			)
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'       => esc_html__( 'Title', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'The title will be used within the tab button for this tab.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'content_new' => array(
				'label'       => esc_html__( 'Content', 'et_builder' ),
				'type'        => 'tiny_mce',
				'description' => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_tab_titles;
		global $et_pb_tab_classes;

		$title = $this->shortcode_atts['title'];

		$module_class = ET_Builder_Element::add_module_order_class( '', $function_name );

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$i = 0;

		$et_pb_tab_titles[]  = '' !== $title ? $title : esc_html__( 'Tab', 'et_builder' );
		$et_pb_tab_classes[] = $module_class;

		$output = sprintf(
			'<div class="et_pb_tab clearfix%2$s%3$s%4$s%6$s%8$s">
				%7$s
				%5$s
				<div class="et_pb_tab_content">
					%1$s
				</div><!-- .et_pb_tab_content" -->
			</div> <!-- .et_pb_tab -->',
			$this->shortcode_content,
			( 1 === count( $et_pb_tab_titles ) ? ' et_pb_active_content' : '' ),
			esc_attr( $module_class ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background,
			$this->get_text_orientation_classname()
		);

		return $output;
	}

	public function _add_additional_shadow_fields() {

	}

	protected function _add_additional_border_fields() {
		return false;
	}

	function process_advanced_border_options( $function_name ) {
		return false;
	}


}

new ET_Builder_Module_Tabs_Item;
