<?php

class ET_Builder_Module_Fullwidth_Code extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Fullwidth Code', 'et_builder' );
		$this->plural          = esc_html__( 'Fullwidth Codes', 'et_builder' );
		$this->slug            = 'et_pb_fullwidth_code';
		$this->vb_support      = 'on';
		$this->fullwidth       = true;
		$this->use_raw_content = true;

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'     => array(
				'default' => false,
			),
			'text_shadow' => array(
				// Don't add text-shadow fields since they already are via font-options
				'default' => false,
			),
			'box_shadow'  => array(
				'default' => false,
			),
			'fonts'       => false,
			'button'      => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'dTY6-Cbr00A' ),
				'name' => esc_html__( 'An introduction to the Fullwidth Code module', 'et_builder' ),
			),
		);

		// wptexturize is often incorrectly parsed single and double quotes
		// This disables wptexturize on this module
		add_filter( 'no_texturize_shortcodes', array( $this, 'disable_wptexturize' ) );
	}

	function get_fields() {
		$fields = array(
			'raw_content' => array(
				'label'           => esc_html__( 'Content', 'et_builder' ),
				'type'            => 'codemirror',
				'mode'            => 'html',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the content that will be used within the module.', 'et_builder' ),
				'is_fb_content'   => true,
				'toggle_slug'     => 'main_content',
			),
		);

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$this->add_classname( $this->get_text_orientation_classname() );

		$output = sprintf(
			'<div%2$s class="%3$s">
				%5$s
				%4$s
				<div class="et_pb_code_inner">
					%1$s
				</div>
			</div> <!-- .et_pb_fullwidth_code -->',
			$this->fix_wptexturized_scripts( $this->content ),
			$this->module_id(),
			$this->module_classname( $render_slug ),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Fullwidth_Code;
