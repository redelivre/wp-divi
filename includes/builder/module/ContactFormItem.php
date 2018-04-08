<?php

class ET_Builder_Module_Contact_Form_Item extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Field', 'et_builder' );
		$this->slug            = 'et_pb_contact_field';
		$this->fb_support      = true;
		$this->type            = 'child';
		$this->child_title_var = 'field_id';

		$this->whitelisted_fields = array(
			'field_title',
			'field_type',
			'field_id',
			'required_mark',
			'fullwidth_field',
			'input_border_radius',
			'field_background_color',
			'checkbox_checked',
			'checkbox_options',
			'radio_options',
			'select_options',
			'conditional_logic',
			'conditional_logic_relation',
			'conditional_logic_rules',
			'min_length',
			'max_length',
			'allowed_symbols',
		);

		$this->fields_defaults = array(
			'field_title'                => array( esc_html__( 'New Field', 'et_builder' ) ),
			'field_type'                 => array( 'input' ),
			'field_id'                   => array( '' ),
			'fullwidth_field'            => array( 'off' ),
			'required_mark'              => array( 'on' ),
			'checkbox_checked'           => array( 'off' ),
			'conditional_logic'          => array( 'off' ),
			'conditional_logic_relation' => array( 'off' ),
			'min_length'                 => array( '0' ),
			'max_length'                 => array( '0' ),
			'allowed_symbols'            => array( 'all' ),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Field', 'et_builder' );
		$this->settings_text               = esc_html__( 'Field Settings', 'et_builder' );
		$this->main_css_element = '.et_pb_contact_form_container %%order_class%%.et_pb_contact_field';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'      => esc_html__( 'Text', 'et_builder' ),
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

		$this->advanced_options = array(
			'border' => array(
				'css'          => array(
					'main'      => array(
						'border_radii'  => sprintf( '%1$s .input, %1$s .input[type="checkbox"] + label i, %1$s .input[type="radio"] + label i', $this->main_css_element ),
						'border_styles' => sprintf( '%1$s .input, %1$s .input[type="checkbox"] + label i, %1$s .input[type="radio"] + label i', $this->main_css_element ),
					),
					'important' => 'plugin_only',
				),
				'label_prefix' => esc_html__( 'Input', 'et_builder' ),
			),
			'fonts' => array(
				'form_field'   => array(
					'label'    => esc_html__( 'Field', 'et_builder' ),
					'css'      => array(
						'main' => array(
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
			'background' => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'padding' => 'p%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'max_width' => array(),
			'text'      => array(
				'css' => array(
					'text_orientation' => '%%order_class%% input, %%order_class%% textarea, %%order_class%% label',
				),
			),
			'filters' => array(
				'css' => array(
					'main' => array(
						'%%order_class%% input',
						'%%order_class%% textarea',
						'%%order_class%% label',
					),
				),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'field_id' => array(
				'label'       => esc_html__( 'Field ID', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Define the unique ID of this field. You should use only English characters without special characters and spaces.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'field_title' => array(
				'label'       => esc_html__( 'Title', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'field_type' => array(
				'label'       => esc_html__( 'Type', 'et_builder' ),
				'type'        => 'select',
				'default'     => 'input',
				'option_category' => 'basic_option',
				'options'         => array(
					'input'    => esc_html__( 'Input Field', 'et_builder' ),
					'email'    => esc_html__( 'Email Field', 'et_builder' ),
					'text'     => esc_html__( 'Textarea', 'et_builder' ),
					'checkbox' => esc_html__( 'Checkboxes', 'et_builder' ),
					'radio'    => esc_html__( 'Radio Buttons', 'et_builder' ),
					'select'   => esc_html__( 'Select Dropdown', 'et_builder' ),
				),
				'description' => esc_html__( 'Choose the type of field', 'et_builder' ),
				'affects'     => array(
					'checkbox_options',
					'radio_options',
					'select_options',
					'min_length',
					'max_length',
					'allowed_symbols',
				),
				'toggle_slug' => 'field_options',
			),
			'checkbox_checked' => array(
				'label'           => esc_html__( 'Checked By Default', 'et_builder' ),
				'type'            => 'hidden',
				'option_category' => 'layout',
				'default'         => 'off',
				'depends_show_if' => 'checkbox',
				'toggle_slug'     => 'field_options',
			),
			'checkbox_options' => array(
				'label'           => esc_html__( 'Options', 'et_builder' ),
				'type'            => 'options_list',
				'checkbox'        => true,
				'option_category' => 'basic_option',
				'depends_show_if' => 'checkbox',
				'toggle_slug'     => 'field_options',
			),
			'radio_options' => array(
				'label'           => esc_html__( 'Options', 'et_builder' ),
				'type'            => 'options_list',
				'radio'           => true,
				'option_category' => 'basic_option',
				'depends_show_if' => 'radio',
				'toggle_slug'     => 'field_options',
			),
			'select_options' => array(
				'label'           => esc_html__( 'Options', 'et_builder' ),
				'type'            => 'options_list',
				'option_category' => 'basic_option',
				'depends_show_if' => 'select',
				'toggle_slug'     => 'field_options',
			),
			'min_length'   => array(
				'label'          => esc_html__( 'Minimum Length', 'et_builder' ),
				'description'    => esc_html__( 'Leave at 0 to remove restriction', 'et_builder' ),
				'type'           => 'range',
				'default'        => '0',
				'fixed_unit'     => null,
				'range_settings' => array(
					'min'  => '0',
					'max'  => '255',
					'step' => '1',
				),
				'option_category' => 'basic_option',
				'depends_show_if' => 'input',
				'toggle_slug'     => 'field_options',
			),
			'max_length'   => array(
				'label'          => esc_html__( 'Maximum Length', 'et_builder' ),
				'description'    => esc_html__( 'Leave at 0 to remove restriction', 'et_builder' ),
				'type'           => 'range',
				'default'        => '0',
				'fixed_unit'     => null,
				'range_settings' => array(
					'min'  => '0',
					'max'  => '255',
					'step' => '1',
				),
				'option_category' => 'basic_option',
				'depends_show_if' => 'input',
				'toggle_slug'     => 'field_options',
			),
			'allowed_symbols' => array(
				'label'       => esc_html__( 'Allowed Symbols', 'et_builder' ),
				'type'        => 'select',
				'default'     => 'all',
				'options'     => array(
					'all'          => esc_html__( 'All', 'et_builder' ),
					'letters'      => esc_html__( 'Letters Only (A-Z)', 'et_builder' ),
					'numbers'      => esc_html__( 'Numbers Only (0-9)', 'et_builder' ),
					'alphanumeric' => esc_html__( 'Alphanumeric Only (A-Z, 0-9)', 'et_builder' ),
				),
				'option_category' => 'basic_option',
				'depends_show_if' => 'input',
				'toggle_slug'     => 'field_options',
			),
			'required_mark' => array(
				'label'           => esc_html__( 'Required Field', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'default'         => 'on',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'description'     => esc_html__( 'Define whether the field should be required or optional', 'et_builder' ),
				'toggle_slug'     => 'field_options',
			),
			'fullwidth_field' => array(
				'label'           => esc_html__( 'Make Fullwidth', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'description'     => esc_html__( 'If enabled, the field will take 100% of the width of the content area, otherwise it will take 50%', 'et_builder' ),
			),
			'conditional_logic' => array(
				'label'           => esc_html__( 'Enable', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'default'         => 'off',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'         => array(
					'conditional_logic_rules',
					'conditional_logic_relation',
				),
				'description' => et_get_safe_localization( __( "Enabling conditional logic makes this field only visible when any or all of the rules below are fulfilled<br><strong>Note:</strong> Only fields with an unique and non-empty field ID can be used", 'et_builder' ) ),
				'toggle_slug' => 'conditional_logic',
			),
			'conditional_logic_relation' => array(
				'label'             => esc_html__( 'Relation', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'All', 'et_builder' ),
					'off' => esc_html__( 'Any', 'et_builder' ),
				),
				'default'           => 'off',
				'button_options'    => array(
					'button_type' => 'equal',
				),
				'depends_show_if' => 'on',
				'description' => esc_html__( 'Choose whether any or all of the rules should be fulfilled', 'et_builder' ),
				'toggle_slug' => 'conditional_logic',
			),
			'conditional_logic_rules' => array(
				'label'           => esc_html__( 'Rules', 'et_builder' ),
				'type'            => 'conditional_logic',
				'option_category' => 'layout',
				'depends_show_if' => 'on',
				'toggle_slug'     => 'conditional_logic',
			),
			'field_background_color' => array(
				'label'             => esc_html__( 'Field Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'toggle_slug'       => 'form_field',
				'tab_slug'          => 'advanced',
			),
		);

		return $fields;
	}

	// Don't add text-shadow fields since they already are via font-options
	protected function _add_additional_text_shadow_fields() {}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$field_title                = $this->shortcode_atts['field_title'];
		$field_type                 = $this->shortcode_atts['field_type'];
		$field_id                   = $this->shortcode_atts['field_id'];
		$required_mark              = $this->shortcode_atts['required_mark'];
		$fullwidth_field            = $this->shortcode_atts['fullwidth_field'];
		$form_field_text_color      = $this->shortcode_atts['form_field_text_color'];
		$field_background_color     = $this->shortcode_atts['field_background_color'];
		$checkbox_checked           = $this->shortcode_atts['checkbox_checked'];
		$checkbox_options           = $this->shortcode_atts['checkbox_options'];
		$radio_options              = $this->shortcode_atts['radio_options'];
		$select_options             = $this->shortcode_atts['select_options'];
		$min_length                 = $this->shortcode_atts['min_length'];
		$max_length                 = $this->shortcode_atts['max_length'];
		$conditional_logic          = $this->shortcode_atts['conditional_logic'];
		$conditional_logic_relation = $this->shortcode_atts['conditional_logic_relation'];
		$conditional_logic_rules    = $this->shortcode_atts['conditional_logic_rules'];
		$allowed_symbols            = $this->shortcode_atts['allowed_symbols'];

		global $et_pb_contact_form_num;

		// do not output the fields with empty ID
		if ( '' === $field_id ) {
			return;
		}

		$field_id = strtolower( $field_id );

		$current_module_num = '' === $et_pb_contact_form_num ? 0 : intval( $et_pb_contact_form_num ) + 1;

		$module_class              = ET_Builder_Element::add_module_order_class( '', $function_name );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$shortcode_callback_num    = $this->shortcode_callback_num();

		$this->half_width_counter = ! isset( $this->half_width_counter ) ? 0 : $this->half_width_counter;

		// count fields to add the et_pb_contact_field_last properly
		if ( 'off' === $fullwidth_field ) {
			$this->half_width_counter++;
		} else {
			$this->half_width_counter = 0;
		}

		$input_field = '';

		if ( '' !== $form_field_text_color ) {
			if ( 'checkbox' === $field_type ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .input + label, %%order_class%% .input + label i:before',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $form_field_text_color )
					),
				) );
			}

			if ( 'radio' === $field_type ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .input + label',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $form_field_text_color )
					),
				) );

				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .input + label i:before',
					'declaration' => sprintf(
						'background-color: %1$s !important;',
						esc_html( $form_field_text_color )
					),
				) );
			}
		}

		if ( '' !== $field_background_color ) {
			$input_selector = '%%order_class%% .input';

			if ( in_array( $field_type, array( 'checkbox', 'radio' ) ) ) {
				$input_selector = '%%order_class%% .input + label i';
			}

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => $input_selector,
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $field_background_color )
				),
			) );
		}

		$pattern         = '';
		$title           = '';
		$min_length      = intval( $min_length );
		$max_length      = intval( $max_length );
		$max_length_attr = '';
		$symbols_pattern = '.';
		$length_pattern  = '*';

		if ( in_array( $allowed_symbols, array( 'letters', 'numbers', 'alphanumeric' ) ) ) {
			switch ( $allowed_symbols ) {
				case 'letters':
					$symbols_pattern = '[A-Z|a-z]';
					$title           = __( 'Only letters allowed.', 'et_builder' );
					break;
				case 'numbers':
					$symbols_pattern = '[0-9]';
					$title           = __( 'Only numbers allowed.', 'et_builder' );
					break;
				case 'alphanumeric':
					$symbols_pattern = '[A-Z|a-z|0-9]';
					$title           = __( 'Only letters and numbers allowed.', 'et_builder' );
					break;
			}
		}

		if ( 0 !== $min_length && 0 !== $max_length ) {
			$max_length = max( $min_length, $max_length );
			$min_length = min( $min_length, $max_length );

			if ( $max_length > 0 ) {
				$max_length_attr = sprintf(
					' maxlength="%1$d"',
					$max_length
				);
			}
		}

		if ( 0 !== $min_length || 0 !== $max_length ) {
			$length_pattern = '{';

			if ( 0 !== $min_length ) {
				$length_pattern .= $min_length;
				$title   .= sprintf( __( 'Minimum length: %1$d characters. ', 'et_builder' ), $min_length );
			}

			if ( 0 === $max_length ) {
				$length_pattern .= ',';
			}

			if ( 0 === $min_length ) {
				$length_pattern .= '0';
			}

			if ( 0 !== $max_length ) {
				$length_pattern .= ",{$max_length}";
				$title   .= sprintf( __( 'Maximum length: %1$d characters.', 'et_builder' ), $max_length );
			}


			$length_pattern .= '}';
		}

		if ( '.' !== $symbols_pattern || '*' !== $length_pattern ) {
			$pattern = sprintf(
				' pattern="%1$s%2$s"',
				esc_attr( $symbols_pattern ),
				esc_attr( $length_pattern )
			);
		}

		if ( '' !== $title ) {
			$title = sprintf(
				' title="%1$s"',
				esc_attr( $title )
			);
		}

		$conditional_logic_attr = '';

		if ( 'on' === $conditional_logic && ! empty( $conditional_logic_rules ) ) {
			$option_search           = array( '&#91;', '&#93;' );
			$option_replace          = array( '[', ']' );
			$conditional_logic_rules = str_replace( $option_search, $option_replace, $conditional_logic_rules );
			$condition_rows          = json_decode( $conditional_logic_rules );
			$ruleset                 = array();

			foreach ( $condition_rows as $condition_row ) {
				$condition_value = isset( $condition_row->value ) ? $condition_row->value : '';
				$condition_value = trim( $condition_value );

				$ruleset[] = array(
					$condition_row->field,
					$condition_row->condition,
					$condition_value,
				);
			}

			if ( ! empty( $ruleset ) ) {
				$json     = json_encode( $ruleset );
				$relation = $conditional_logic_relation === 'off' ? 'any' : 'all';

				$conditional_logic_attr = sprintf(
					' data-conditional-logic="%1$s" data-conditional-relation="%2$s"',
					esc_attr( $json ),
					$relation
				);
			}
		}

		switch( $field_type ) {
			case 'text':
				$input_field = sprintf(
					'<textarea name="et_pb_contact_%3$s_%2$s" id="et_pb_contact_%3$s_%2$s" class="et_pb_contact_message input" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s" placeholder="%5$s">%1$s</textarea>',
					( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_html( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
					esc_attr( $current_module_num ),
					esc_attr( $field_id ),
					esc_attr( $field_type ),
					esc_attr( $field_title ),
					'off' === $required_mark ? 'not_required' : 'required'
				);
				break;
			case 'input' :
			case 'email' :
				if ( 'email' === $field_type ) {
					$pattern = '';
				}

				$input_field = sprintf(
					'<input type="text" id="et_pb_contact_%3$s_%2$s" class="input" value="%1$s" name="et_pb_contact_%3$s_%2$s" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s" placeholder="%5$s"%7$s%8$s%9$s>',
					( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_attr( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
					esc_attr( $current_module_num ),
					esc_attr( $field_id ),
					esc_attr( $field_type ),
					esc_attr( $field_title ),
					'off' === $required_mark ? 'not_required' : 'required',
					$pattern,
					$title,
					$max_length_attr
				);
				break;
			case 'checkbox' :
				$input_field = '';

				if ( ! $checkbox_options ) {
					$is_checked       = ! empty( $checkbox_checked ) && 'on' === $checkbox_checked;
					$checkbox_options = sprintf(
						'[{"value":"%1$s","checked":%2$s}]',
						esc_attr( $field_title ),
						$is_checked ? 1 : 0
					);
					$field_title = '';
				}

				$option_search    = array( '&#91;', '&#93;' );
				$option_replace   = array( '[', ']' );
				$checkbox_options = str_replace( $option_search, $option_replace, $checkbox_options );
				$checkbox_options = json_decode( $checkbox_options );

				foreach ( $checkbox_options as $index => $option ) {
					$is_checked   = 1 === $option->checked ? true : false;
					$option_value = wp_strip_all_tags( $option->value );

					$input_field .= sprintf(
						'<span class="et_pb_contact_field_checkbox">
							<input type="checkbox" id="et_pb_contact_%1$s_%5$s_%3$s" class="input" value="%2$s"%4$s>
							<label for="et_pb_contact_%1$s_%5$s_%3$s"><i></i>%2$s</label>
						</span>',
						esc_attr( $field_id ),
						esc_attr( $option_value ),
						esc_attr( $index ),
						$is_checked ? ' checked="checked"' : '',
						esc_attr( $shortcode_callback_num )
					);
				}

				$input_field = sprintf(
					'<input class="et_pb_checkbox_handle" type="hidden" name="et_pb_contact_%1$s_%4$s" data-required_mark="%3$s" data-field_type="%2$s" data-original_id="%1$s">
					<span class="et_pb_contact_field_options_wrapper">
						<span class="et_pb_contact_field_options_title">%5$s</span>
						<span class="et_pb_contact_field_options_list">%6$s</span>
					</span>',
					esc_attr( $field_id ),
					esc_attr( $field_type ),
					'off' === $required_mark ? 'not_required' : 'required',
					esc_attr( $current_module_num ),
					esc_html( $field_title ),
					$input_field
				);

				break;
			case 'radio' :
				$input_field = '';

				if ( $radio_options ) {
					$option_search  = array( '&#91;', '&#93;' );
					$option_replace = array( '[', ']' );
					$radio_options  = str_replace( $option_search, $option_replace, $radio_options );
					$radio_options  = json_decode( $radio_options );

					foreach ( $radio_options as $index => $option ) {
						$is_checked = 1 === $option->checked ? true : false;

						$input_field .= sprintf(
							'<span class="et_pb_contact_field_radio">
								<input type="radio" id="et_pb_contact_%3$s_%2$s_%10$s_%7$s" class="input" value="%8$s" name="et_pb_contact_%3$s_%2$s" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s" %9$s>
								<label for="et_pb_contact_%3$s_%2$s_%10$s_%7$s"><i></i>%8$s</label>
							</span>',
							( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_attr( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
							esc_attr( $current_module_num ),
							esc_attr( $field_id ),
							esc_attr( $field_type ),
							esc_attr( $field_title ), // #5
							'off' === $required_mark ? 'not_required' : 'required',
							esc_attr( $index ),
							wp_strip_all_tags( $option->value ),
							checked( $is_checked, true, false ),
							esc_attr( $shortcode_callback_num ) // #10
						);
					}
				} else {
					$input_field .= esc_html__( 'No options added.', 'et_builder' );
				}

				$input_field = sprintf(
					'<span class="et_pb_contact_field_options_wrapper">
						<span class="et_pb_contact_field_options_title">%1$s</span>
						<span class="et_pb_contact_field_options_list">%2$s</span>
					</span>',
					esc_html( $field_title ),
					$input_field
				);

				break;
			case 'select' :
				$options = sprintf(
					'<option value="">-- %1$s --</option>',
					esc_attr( $field_title )
				);

				if ( $select_options ) {
					$option_search  = array( '&#91;', '&#93;' );
					$option_replace = array( '[', ']' );
					$select_options = str_replace( $option_search, $option_replace, $select_options );
					$select_options = json_decode( $select_options );

					foreach ( $select_options as $option ) {
						$options .= sprintf(
							'<option value="%1$s">%2$s</option>',
							esc_attr( wp_strip_all_tags( $option->value ) ),
							wp_strip_all_tags( $option->value )
						);
					}
				}

				$input_field = sprintf(
					'<select id="et_pb_contact_%3$s_%2$s" class="et_pb_contact_select input" name="et_pb_contact_%3$s_%2$s" data-required_mark="%6$s" data-field_type="%4$s" data-original_id="%3$s">
						%7$s
					</select>',
					( isset( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ? esc_attr( sanitize_text_field( $_POST['et_pb_contact_' . $field_id . '_' . $current_module_num] ) ) : '' ),
					esc_attr( $current_module_num ),
					esc_attr( $field_id ),
					esc_attr( $field_type ),
					esc_attr( $field_title ),
					'off' === $required_mark ? 'not_required' : 'required',
					$options
				);
				break;
		}

		$output = sprintf(
			'<p class="et_pb_contact_field%5$s%6$s%7$s%10$s%12$s%14$s"%8$s data-id="%3$s" data-type="%9$s">
				%13$s
				%11$s
				<label for="et_pb_contact_%3$s_%2$s" class="et_pb_contact_form_label">%1$s</label>
				%4$s
			</p>',
			esc_html( $field_title ),
			esc_attr( $current_module_num ),
			esc_attr( $field_id ),
			$input_field,
			esc_attr( $module_class ),
			'off' === $fullwidth_field ? ' et_pb_contact_field_half' : '',
			0 === $this->half_width_counter % 2 ? ' et_pb_contact_field_last' : '',
			$conditional_logic_attr,
			$field_type,
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background,
			$this->get_text_orientation_classname()
		);

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );

		$selectors = array(
			'%%order_class%% input',
			'%%order_class%% select',
			'%%order_class%% textarea',
			'%%order_class%% .et_pb_contact_field_options_list label > i',
		);
		self::set_style( $function_name, array(
				'selector'    => implode( ', ', $selectors ),
				'declaration' => $boxShadow->get_value( $this->shortcode_atts, array( 'important' => true ) )
			)
		);
	}
}

new ET_Builder_Module_Contact_Form_Item;
