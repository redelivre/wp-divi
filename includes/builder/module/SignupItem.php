<?php

class ET_Builder_Module_Signup_Item extends ET_Builder_Module {

	public $child_title_var  = 'field_title';
	public $bb_support       = false;
	public $main_css_element = '.et_pb_newsletter_form %%order_class%%';
	public $no_render        = true;
	public $slug             = 'et_pb_signup_custom_field';
	public $type             = 'child';
	public $vb_support       = 'on';

	public function init() {
		$this->name                        = esc_html__( 'Custom Field', 'et_builder' );
		$this->plural                      = esc_html__( 'Custom Fields', 'et_builder' );
		$this->advanced_setting_title_text = $this->name;
		$this->settings_text               = esc_html__( 'Custom Field Settings', 'et_builder' );
	}

	public function get_advanced_fields_config() {
		return array(
			'background'     => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'borders'        => array(
				'default' => array(
					'css'          => array(
						'main'      => array(
							'border_radii'  => sprintf( '%1$s .input, %1$s .input[type="checkbox"] + label i, %1$s .input[type="radio"] + label i', $this->main_css_element ),
							'border_styles' => sprintf( '%1$s .input, %1$s .input[type="checkbox"] + label i, %1$s .input[type="radio"] + label i', $this->main_css_element ),
						),
						'important' => 'plugin_only',
					),
					'label_prefix' => esc_html__( 'Input', 'et_builder' ),
					'defaults'     => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main'      => implode( ', ', array(
							'%%order_class%% input',
							'%%order_class%% select',
							'%%order_class%% textarea',
							'%%order_class%% .et_pb_contact_field_options_list label > i',
						) ),
						'important' => true,
					),
				),
			),
			'filters'        => array(
				'css' => array(
					'main' => array(
						'%%order_class%% input',
						'%%order_class%% textarea',
						'%%order_class%% label',
					),
				),
			),
			'fonts'          => array(
				'form_field' => array(
					'label' => esc_html__( 'Field', 'et_builder' ),
					'css'   => array(
						'main'      => array(
							"%%order_class%%.et_pb_contact_field .et_pb_contact_field_options_title",
							"{$this->main_css_element} .input",
							"{$this->main_css_element} .input::-webkit-input-placeholder",
							"{$this->main_css_element} .input::-moz-placeholder",
							"{$this->main_css_element} .input:-ms-input-placeholder",
							"{$this->main_css_element} .input[type=checkbox] + label",
							"{$this->main_css_element} .input[type=radio] + label",
						),
						'important' => 'plugin_only',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'padding'   => 'p%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'           => array(
				'css' => array(
					'text_orientation' => '%%order_class%% input, %%order_class%% textarea, %%order_class%% label',
				),
			),
			'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options
				'default' => false,
			),
		);
	}

	public function get_fields() {
		return ET_Core_API_Email_Fields::get_definitions( 'builder' );
	}

	public function get_settings_modal_toggles() {
		return array(
			'general'  => array(
				'toggles' => array(
					'main_content'      => esc_html__( 'Field', 'et_builder' ),
					'field_options'     => esc_html__( 'Field Options', 'et_builder' ),
					'conditional_logic' => esc_html__( 'Conditional Logic', 'et_builder' ),
					'background'        => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
				),
			),
		);
	}
}

new ET_Builder_Module_Signup_Item;
