<?php

class ET_Builder_Module_Signup extends ET_Builder_Module {

	protected static $_providers;

	public static $enabled_providers;

	public $child_slug = 'et_pb_signup_custom_field';

	public $module_items_config = array(
		'toggle_slug' => 'fields',
		'location'    => 'bottom',
		'show_if'     => array(
			'use_custom_fields' => 'on',
		),
		'show_if_not' => array(
			'function.hasPredefinedFields' => 'off',
			'${provider}_list'             => array( '0|none', '' ),
		),
	);

	function init() {
		$this->name       = esc_html__( 'Email Optin', 'et_builder' );
		$this->plural     = esc_html__( 'Email Optins', 'et_builder' );
		$this->slug       = 'et_pb_signup';
		$this->vb_support = 'on';

		$this->child_item_text = esc_html__( 'Custom Field', 'et_builder' );

		$providers               = self::providers()->names_by_slug();
		$providers['feedburner'] = 'FeedBurner';

		self::$enabled_providers = apply_filters( 'et_builder_module_signup_enabled_providers', $providers );

		ksort( self::$enabled_providers );

		$this->main_css_element = '%%order_class%%.et_pb_subscribe';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content'   => esc_html__( 'Text', 'et_builder' ),
					'provider'       => esc_html__( 'Email Account', 'et_builder' ),
					'fields'         => esc_html__( 'Fields', 'et_builder' ),
					'success_action' => esc_html__( 'Success Action', 'et_builder' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'et_builder' ),
					'fields' => esc_html__( 'Fields', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'privacy' => esc_html__( 'Privacy', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'header'         => array(
					'label'        => esc_html__( 'Title', 'et_builder' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} .et_pb_newsletter_description h2, {$this->main_css_element} .et_pb_newsletter_description h1.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h3.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h4.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h5.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h6.et_pb_module_header",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
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
				'body'           => array(
					'label' => esc_html__( 'Body', 'et_builder' ),
					'css'   => array(
						'main'        => "{$this->main_css_element} .et_pb_newsletter_description, {$this->main_css_element} .et_pb_newsletter_form",
						'line_height' => "{$this->main_css_element} p",
					),
				),
				'result_message' => array(
					'label' => esc_html__( 'Result Message', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .et_pb_newsletter_form .et_pb_newsletter_result h2",
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button'         => array(
				'button' => array(
					'label'      => esc_html__( 'Button', 'et_builder' ),
					'css'        => array(
						'main' => "{$this->main_css_element} .et_pb_newsletter_button.et_pb_button",
						'limited_main' => "{$this->main_css_element} .et_pb_newsletter_button.et_pb_button",
					),
					'box_shadow' => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_newsletter_button',
						),
					),
				),
			),
			'background'     => array(
				'has_background_color_toggle' => true,
				'options'                     => array(
					'use_background_color' => array(
						'default' => 'on',
					),
					'background_color'     => array(
						'depends_show_if' => 'on',
						'default'         => et_builder_accent_color(),
					),
				),
			),
			'borders'        => array(
				'default'      => array(),
				'fields'       => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_newsletter_form p input[type="text"], %%order_class%% .et_pb_newsletter_form p textarea, %%order_class%% .et_pb_newsletter_form p select, %%order_class%% .et_pb_newsletter_form p .input[type="radio"] + label i, %%order_class%% .et_pb_newsletter_form p .input[type="checkbox"] + label i',
							'border_styles' => '%%order_class%% .et_pb_newsletter_form p input[type="text"], %%order_class%% .et_pb_newsletter_form p textarea, %%order_class%% .et_pb_newsletter_form p select, %%order_class%% .et_pb_newsletter_form p .input[type="radio"] + label i, %%order_class%% .et_pb_newsletter_form p .input[type="checkbox"] + label i',
						),
					),
					'label_prefix' => esc_html__( 'Fields', 'et_builder' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'fields',
					'defaults'     => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
					'fields_after' => array(
						'use_focus_border_color' => array(
							'label'           => esc_html__( 'Use Focus Borders', 'et_builder' ),
							'type'            => 'yes_no_button',
							'option_category' => 'color_option',
							'options'         => array(
								'off' => esc_html__( 'No', 'et_builder' ),
								'on'  => esc_html__( 'Yes', 'et_builder' ),
							),
							'affects'         => array(
								'border_radii_fields_focus',
								'border_styles_fields_focus',
							),
							'tab_slug'        => 'advanced',
							'toggle_slug'     => 'fields',
							'default'         => 'off',
						),
					),
				),
				'fields_focus' => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => "%%order_class%% .et_pb_newsletter_form p input:focus",
							'border_styles' => "%%order_class%% .et_pb_newsletter_form p input:focus",
						),
					),
					'label_prefix'    => esc_html__( 'Focus', 'et_builder' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'fields',
					'depends_on'      => array( 'use_focus_border_color' ),
					'depends_show_if' => 'on',
					'defaults'        => array(
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
				'default' => array(),
				'fields'  => array(
					'label'             => esc_html__( 'Fields Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'fields',
					'css'               => array(
						'main' => '%%order_class%% .et_pb_newsletter_form .input',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'max_width'      => array(),
			'text'           => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main' => '%%order_class%% .et_pb_newsletter_description p, %%order_class%% .et_pb_newsletter_description .et_pb_module_header',
					'text_shadow' => '%%order_class%% .et_pb_newsletter_description p, %%order_class%% .et_pb_newsletter_description .et_pb_module_header',
				),
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'left',
					),
					'background_layout' => array(
						'default' => 'dark',
						'hover' => 'tabs',
					),
				),
			),
			'text_shadow'    => array(
				'default' => array(),
				'fields'  => array(
					'label'           => esc_html__( 'Fields', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
				),
			),
			'fields'         => array(
				'css' => array(
					'text_shadow' => "{$this->main_css_element} input, {$this->main_css_element} textarea, {$this->main_css_element} select",
				),
			),
		);

		$this->custom_css_fields = array(
			'newsletter_title' => array(
				'label'    => esc_html__( 'Opt-in Title', 'et_builder' ),
				'selector' => "{$this->main_css_element} .et_pb_newsletter_description h2, {$this->main_css_element} .et_pb_newsletter_description h1.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h3.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h4.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h5.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h6.et_pb_module_header",
			),
			'newsletter_description' => array(
				'label'    => esc_html__( 'Opt-in Description', 'et_builder' ),
				'selector' => '.et_pb_newsletter_description',
			),
			'newsletter_form'        => array(
				'label'    => esc_html__( 'Opt-in Form', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form',
			),
			'newsletter_fields'      => array(
				'label'    => esc_html__( 'Opt-in Form Fields', 'et_builder' ),
				'selector' => '%%order_class%% .et_pb_newsletter_form p input[type="text"], %%order_class%% .et_pb_newsletter_form p textarea, %%order_class%% .et_pb_newsletter_form p select, %%order_class%% .et_pb_newsletter_form p .input[type="radio"] + label i, %%order_class%% .et_pb_newsletter_form p .input[type="checkbox"] + label i',
			),
			'newsletter_button'      => array(
				'label'                    => esc_html__( 'Subscribe Button', 'et_builder' ),
				'selector'                 => '.et_pb_subscribe .et_pb_newsletter_button.et_pb_button',
				'no_space_before_selector' => true,
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'kauQ6xheNiw' ),
				'name' => esc_html__( 'An introduction to the Email Optin module', 'et_builder' ),
			),
		);
	}

	protected static function _get_account_fields( $provider_slug ) {
		et_core_nonce_verified_previously();

		$fields  = self::providers()->account_fields( $provider_slug );
		$is_VB   = ( et_core_is_fb_enabled() && ! et_fb_dynamic_asset_exists( 'definitions' ) ) || ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], array(
			'et_fb_update_builder_assets',
			'et_fb_retrieve_builder_data'
		) ) );
		$show_if = $is_VB ? 'add_new_account' : 'manage|add_new_account';

		$account_name_key = $provider_slug . '_account_name';
		$list_key         = $provider_slug . '_list';
		$description_text = esc_html__( 'Email Provider Account Setup Documentation', 'et_builder' );

		if ( $fields ) {
			$field_ids     = array_keys( $fields );
			$last_field_id = "{$provider_slug}_" . array_pop( $field_ids );
		} else {
			$last_field_id = $account_name_key;
		}

		$buttons = array(
			'option_class' => 'et-pb-option-group--last-field',
			'after'        => array(
				array(
					'type'  => 'button',
					'class' => 'et_pb_email_cancel',
					'text'  => esc_html__( 'Cancel', 'et_builder' ),
				),
				array(
					'type'  => 'button',
					'class' => 'et_pb_email_submit',
					'text'  => esc_html__( 'Submit', 'et_builder' ),
				),
			),
		);

		$account_fields = array(
			$account_name_key => array(
				'name'            => 'account_name',
				'label'           => esc_html__( 'Account Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'A name to associate with the account when displayed in the List select field.', 'et_builder' ),
				'show_if'         => array(
					$list_key => $show_if,
				),
				'class'           => "et_pb_email_{$provider_slug}_account_name",
				'toggle_slug'     => 'provider',
			),
		);

		foreach ( $fields as $field_id => $field_info ) {
			$field_id = "{$provider_slug}_{$field_id}";

			$show_if_conditions = array(
				$list_key => $show_if,
			);

			if ( isset( $field_info['show_if'] ) ) {
				$show_if_conditions = array_merge( $show_if_conditions, $field_info['show_if']);
			}

			$account_fields[ $field_id ] = array(
				'name'            => $field_id,
				'label'           => et_core_esc_previously( $field_info['label'] ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => sprintf( '<a target="_blank" href="https://www.elegantthemes.com/documentation/bloom/accounts#%1$s">%2$s</a>', $provider_slug, $description_text ),
				'show_if'         => $show_if_conditions,
				'class'           => 'et_pb_email_' . $field_id,
				'toggle_slug'     => 'provider',
			);
		}

		$account_fields[ $last_field_id ] = array_merge( $account_fields[ $last_field_id ], $buttons );

		return $account_fields;
	}

	protected static function _get_provider_fields() {
		$fields   = array();
		$lists    = self::get_lists();
		$no_lists = array();

		$no_lists[] = array( 'none' => esc_html__( 'Select a list', 'et_builder' ) );

		$no_lists['manage'] = array(
			'add_new_account' => '',
			'remove_account'  => '',
			'fetch_lists'     => '',
		);

		foreach ( self::$enabled_providers as $provider_slug => $provider_name ) {
			if ( 'feedburner' === $provider_slug ) {
				continue;
			}

			$fields[ $provider_slug . '_list' ] = array(
				'label'           => sprintf( esc_html_x( '%s List', 'MailChimp, Aweber, etc', 'et_builder' ), $provider_name ),
				'type'            => 'select_with_option_groups',
				'option_category' => 'basic_option',
				'options'         => isset( $lists[ $provider_slug ] ) ? $lists[ $provider_slug ] : $no_lists,
				'description'     => esc_html__( 'Choose a list. If you don\'t see any lists, click "Add" to add an account.' ),
				'show_if'         => array(
					'provider' => $provider_slug,
				),
				'default'         => '0|none',
				'default_on_front'=> '',
				'toggle_slug'     => 'provider',
				'after'           => array(
					array(
						'type'  => 'button',
						'class' => 'et_pb_email_add_account',
						'text'  => esc_html__( 'Add', 'et_builder' ),
					),
					array(
						'type'       => 'button',
						'class'      => 'et_pb_email_remove_account',
						'text'       => esc_html__( 'Remove', 'et_builder' ),
						'attributes' => array(
							'data-confirm_text' => esc_attr__( 'Confirm', 'et_builder' ),
						),
					),
					array(
						'type'       => 'button',
						'class'      => 'et_pb_email_force_fetch_lists',
						'text'       => esc_html__( 'Fetch Lists', 'et_builder' ),
						'attributes' => array(
							'data-cancel_text' => esc_attr__( 'Cancel', 'et_builder' ),
						),
					),
				),
				'attributes'      => array(
					'data-confirm_remove_text'     => esc_attr__( 'The following account will be removed:', 'et_builder' ),
					'data-adding_new_account_text' => esc_attr__( 'Use the fields below to add a new account.', 'et_builder' ),
				),
			);

			$account_fields = is_admin() || ( et_core_is_fb_enabled() && ! et_fb_dynamic_asset_exists( 'definitions' ) ) ? self::_get_account_fields( $provider_slug ) : array();
			$fields         = array_merge( $fields, $account_fields );
		}

		return $fields;
	}

	function get_fields() {
		$name_field_only  = array_keys( self::providers()->names_by_slug( 'all', 'name_field_only' ) );
		$no_custom_fields = array_keys( self::providers()->names_by_slug( 'all', 'no_custom_fields' ) );

		return array_merge(
			array(
				'provider'       => array(
					'label'           => esc_html__( 'Service Provider', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'basic_option',
					'options'         => self::$enabled_providers,
					'description'     => esc_html__( 'Choose a service provider.', 'et_builder' ),
					'toggle_slug'     => 'provider',
					'default'         => 'mailchimp',
				),
				'feedburner_uri' => array(
					'label'           => esc_html__( 'Feed Title', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'show_if'         => array(
						'provider' => 'feedburner',
					),
					'description'     => et_get_safe_localization( sprintf( __( 'Enter <a href="%1$s" target="_blank">Feed Title</a>.', 'et_builder' ), esc_url( 'http://feedburner.google.com/fb/a/myfeeds' ) ) ),
					'toggle_slug'     => 'provider',
				),
			),

			self::_get_provider_fields(),

			array(
				'layout'                      => array(
					'label'       => esc_html__( 'Layout', 'et_builder' ),
					'type'        => 'select',
					'options'     => array(
						'left_right' => esc_html__( 'Body On Left, Form On Right', 'et_builder' ),
						'right_left' => esc_html__( 'Body On Right, Form On Left', 'et_builder' ),
						'top_bottom' => esc_html__( 'Body On Top, Form On Bottom', 'et_builder' ),
						'bottom_top' => esc_html__( 'Body On Bottom, Form On Top', 'et_builder' ),
					),
					'default'     => 'left_right',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'layout',
				),
				'ip_address'                  => array(
					'label'           => esc_html__( 'Include IP Address' ),
					'type'            => 'yes_no_button',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'on',
					'option_category' => 'configuration',
					'description'     => esc_html__( "Include the subscriber's ip address in the data sent to your email provider.", 'et_builder' ),
					'toggle_slug'     => 'privacy',
					'tab_slug'        => 'custom_css',
				),
				'name_field'                  => array(
					'label'           => esc_html__( 'Use Single Name Field', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'off',
					'show_if_not'     => array(
						'provider' => array_merge( $name_field_only, array( 'feedburner' ) ),
					),
					'toggle_slug'     => 'fields',
					'description'     => esc_html__( 'Whether or not to use a single Name field in the opt-in form.', 'et_builder' ),
				),
				'name_fullwidth'              => array(
					'label'           => esc_html__( 'Name Fullwidth', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'on',
					'toggle_slug'     => 'layout',
					'tab_slug'        => 'advanced',
					'show_if'         => array(
						'name_field' => 'on',
					),
				),
				'first_name_field'            => array(
					'label'           => esc_html__( 'First Name', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'on',
					'show_if'         => array(
						'name_field' => 'off',
					),
					'show_if_not'     => array(
						'provider' => array_merge( $name_field_only, array( 'feedburner' ) ),
					),
					'toggle_slug'     => 'fields',
					'description'     => esc_html__( 'Whether or not the First Name field should be included in the opt-in form.', 'et_builder' ),
				),
				'first_name_fullwidth'        => array(
					'label'           => esc_html__( 'First Name Fullwidth', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'on',
					'toggle_slug'     => 'layout',
					'tab_slug'        => 'advanced',
					'show_if'         => array(
						'first_name_field' => 'on',
					),
				),
				'last_name_field'             => array(
					'label'           => esc_html__( 'Last Name', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'on',
					'show_if'         => array(
						'name_field' => 'off',
					),
					'show_if_not'     => array(
						'provider' => array_merge( $name_field_only, array( 'feedburner' ) ),
					),
					'toggle_slug'     => 'fields',
					'description'     => esc_html__( 'Whether or not the Last Name field should be included in the opt-in form.', 'et_builder' ),
				),
				'last_name_fullwidth'         => array(
					'label'           => esc_html__( 'Last Name Fullwidth', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'on',
					'toggle_slug'     => 'layout',
					'tab_slug'        => 'advanced',
					'show_if' => array(
						'last_name_field' => 'on',
					),
				),
				'name_field_only'             => array(
					'label'           => esc_html__( 'Name', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'on',
					'show_if'         => array(
						'provider' => $name_field_only,
					),
					'toggle_slug'     => 'fields',
					'description'     => esc_html__( 'Whether or not the Name field should be included in the opt-in form.', 'et_builder' ),
				),
				'email_fullwidth'             => array(
					'label'           => esc_html__( 'Email Fullwidth', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'on',
					'toggle_slug'     => 'layout',
					'tab_slug'        => 'advanced',
				),
				'use_custom_fields'           => array(
					'label'           => esc_html__( 'Use Custom Fields', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'off',
					'allow_dynamic'   => array_keys( self::providers()->names_by_slug( 'all', 'dynamic_custom_fields' ) ),
					'show_if_not'     => array(
						'provider'         => $no_custom_fields,
						'${provider}_list' => array( '0|none', '' ),
					),
					'toggle_slug'     => 'fields',
					'description'     => esc_html__( 'Enable this option to use custom fields in your opt-in form. Learn more <a href="https://www.elegantthemes.com/documentation/divi/modules/adding-custom-fields-to-the-divi-email-optin-module">here</a>', 'et_builder' ),
					'bb_support'      => false,
				),
				'use_custom_fields_notice'   => array(
					'label'           => '',
					'type'            => 'warning',
					'value'           => true,
					'display_if'      => true,
					'message'         => esc_html__( 'You have not defined any custom fields in your email provider account. Once you have defined some fields, click the "Fetch Lists" button in the Email Account toggle above. Learn more <a href="https://www.elegantthemes.com/documentation/divi/modules/adding-custom-fields-to-the-divi-email-optin-module">here</a>', 'et_builder' ),
					'option_category' => 'configuration',
					'show_if'     => array(
						'function.hasPredefinedFields' => 'off',
						'use_custom_fields'            => 'on',
					),
					'show_if_not'     => array(
						'provider'         => $no_custom_fields,
						'${provider}_list' => array( '0|none', '' ),
					),
					'toggle_slug'     => 'fields',
					'bb_support'      => false,
				),
				'success_action'              => array(
					'label'           => esc_html__( 'Action', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'configuration',
					'options'         => array(
						'message'  => esc_html__( 'Display a message.', 'et_builder' ),
						'redirect' => esc_html__( 'Redirect to a custom URL.', 'et_builder' ),
					),
					'default'         => 'message',
					'toggle_slug'     => 'success_action',
					'description'     => esc_html__( 'Choose what happens when a site visitor has been successfully subscribed to your list.', 'et_builder' ),
				),
				'success_message'             => array(
					'label'           => esc_html__( 'Message', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'default'         => esc_html__( 'Success!', 'et_builder' ),
					'show_if'         => array(
						'success_action' => 'message',
					),
					'toggle_slug'     => 'success_action',
					'description'     => esc_html__( 'The message that will be shown to site visitors who subscribe to your list.', 'et_builder' ),
				),
				'success_redirect_url'        => array(
					'label'           => esc_html__( 'Redirect URL', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'show_if'         => array(
						'success_action' => 'redirect',
					),
					'toggle_slug'     => 'success_action',
					'description'     => esc_html__( 'Site visitors who subscribe to your list will be redirected to this URL.', 'et_builder' ),
				),
				'success_redirect_query'      => array(
					'label'           => esc_html__( 'Redirect URL Query', 'et_builder' ),
					'type'            => 'multiple_checkboxes',
					'option_category' => 'configuration',
					'options'         => array(
						'name'       => esc_html__( 'Name', 'et_builder' ),
						'last_name'  => esc_html__( 'Last Name', 'et_builder' ),
						'email'      => esc_html__( 'Email', 'et_builder' ),
						'ip_address' => esc_html__( 'IP Address', 'et_builder' ),
						'css_id'     => esc_html__( 'CSS ID', 'et_builder' ),
					),
					'show_if'         => array(
						'success_action' => 'redirect',
					),
					'toggle_slug'     => 'success_action',
					'description'     => esc_html__( 'Choose what data (if any) to include in the redirect URL as query arguments.', 'et_builder' ),
				),
				'title'                       => array(
					'label'           => esc_html__( 'Title', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'description'     => esc_html__( 'Choose a title of your signup box.', 'et_builder' ),
					'toggle_slug'     => 'main_content',
					'dynamic_content' => 'text',
				),
				'button_text'                 => array(
					'label'            => esc_html__( 'Button Text', 'et_builder' ),
					'type'             => 'text',
					'option_category'  => 'basic_option',
					'description'      => esc_html__( 'Define custom text for the subscribe button.', 'et_builder' ),
					'toggle_slug'      => 'main_content',
					'default_on_front' => esc_html__( 'Subscribe', 'et_builder' ),
					'dynamic_content' => 'text',
				),
				'description'                 => array(
					'label'           => esc_html__( 'Description', 'et_builder' ),
					'type'            => 'tiny_mce',
					'option_category' => 'basic_option',
					'description'     => esc_html__( 'This content will appear below the title.', 'et_builder' ),
					'toggle_slug'     => 'main_content',
					'dynamic_content' => 'text',
				),
				'footer_content'              => array(
					'label'           => esc_html__( 'Footer', 'et_builder' ),
					'type'            => 'tiny_mce',
					'option_category' => 'basic_option',
					'description'     => esc_html__( 'This content will appear below the subscribe button.', 'et_builder' ),
					'toggle_slug'     => 'main_content',
					'dynamic_content' => 'text',
				),
				'form_field_background_color' => array(
					'label'        => esc_html__( 'Form Field Background Color', 'et_builder' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'hover'        => 'tabs',
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'fields',
				),
				'form_field_text_color'       => array(
					'label'        => esc_html__( 'Form Field Text Color', 'et_builder' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'fields',
				),
				'focus_background_color'      => array(
					'label'        => esc_html__( 'Focus Background Color', 'et_builder' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'fields',
				),
				'focus_text_color'            => array(
					'label'        => esc_html__( 'Focus Text Color', 'et_builder' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'fields',
				),
			)
		);
	}

	public function get_transition_fields_css_props() {
		$fields       = parent::get_transition_fields_css_props();
		$fields["form_field_background_color"] = array( 'background-color' => implode(',', array(
			'%%order_class%% .et_pb_newsletter_form p input[type="text"]',
			'%%order_class%% .et_pb_newsletter_form p textarea',
			'%%order_class%% .et_pb_newsletter_form p select',
			'%%order_class%% .et_pb_newsletter_form p .input[type="checkbox"] + label i',
			'%%order_class%% .et_pb_newsletter_form p .input[type="radio"] + label i',
		)) );

		$fields = array_merge( $fields, $this->get_transition_borders_fields_css_props( 'fields' ) );
		$fields = array_merge( $fields, $this->get_transition_borders_fields_css_props( 'fields_focus' ) );
		$fields = array_merge( $fields, $this->get_transition_box_shadow_fields_css_props( 'fields' ) );

		return $fields;
	}

	public static function get_lists() {
		static $migrated = false;

		if ( ! $migrated ) {
			et_builder_email_maybe_migrate_accounts();
			$migrated = true;
		}

		$all_accounts = self::providers()->accounts();
		$lists        = array();

		foreach ( $all_accounts as $provider_slug => $accounts ) {
			if ( ! in_array( $provider_slug, array_keys( self::$enabled_providers ) ) ) {
				continue;
			}

			$lists[ $provider_slug ] = array(
				0 => array( 'none' => esc_html__( 'Select a list', 'et_builder' ) ),
			);

			foreach ( $accounts as $account_name => $account_details ) {
				if ( empty( $account_details['lists'] ) ) {
					continue;
				}

				foreach ( (array) $account_details['lists'] as $list_id => $list_details ) {
					if ( empty( $list_details['name'] ) ) {
						continue;
					}

					$lists[ $provider_slug ][ $account_name ][ $list_id ] = esc_html( $list_details['name'] );
				}
			}

			$lists[ $provider_slug ]['manage'] = array(
				'add_new_account' => '',
				'remove_account'  => '',
				'fetch_lists'     => esc_html__( 'Fetching lists...', 'et_builder' ),
			);
		}

		return $lists;
	}

	public static function get_account_name_for_list_id( $provider_slug, $list_id ) {
		$providers    = ET_Core_API_Email_Providers::instance();
		$all_accounts = $providers->accounts();
		$result       = '';

		if ( ! isset( $all_accounts[ $provider_slug ] ) ) {
			return $result;
		}

		foreach ( $all_accounts[ $provider_slug ] as $account_name => $account_details ) {
			if ( ! empty( $account_details['lists'][ $list_id ] ) ) {
				$result = $account_name;
				break;
			}
		}

		return $result;
	}

	public function get_form_field_html( $field, $single_name_field = false ) {
		$html = '';

		switch ( $field ) {
			case 'name':
				$label          = $single_name_field ? __( 'Name', 'et_builder' ) : __( 'First Name', 'et_builder' );
				$fullwidth_prop = $single_name_field ? 'name_fullwidth' : 'first_name_fullwidth';
				$fullwidth      = 'on' === self::$_->array_get( $this->props, $fullwidth_prop, 'on' );
				$html      = sprintf( '
					<p class="et_pb_newsletter_field%1$s">
						<label class="et_pb_contact_form_label" for="et_pb_signup_firstname" style="display: none;">%2$s</label>
						<input id="et_pb_signup_firstname" class="input" type="text" placeholder="%3$s" name="et_pb_signup_firstname">
					</p>',
					$fullwidth ? ' et_pb_contact_field_last' : ' et_pb_contact_field_half',
					esc_html( $label ),
					esc_attr( $label )
				);
				break;

			case 'last_name':
				$label     = __( 'Last Name', 'et_builder' );
				$fullwidth = 'on' === self::$_->array_get( $this->props, 'last_name_fullwidth', 'on' );
				$html      = sprintf( '
					<p class="et_pb_newsletter_field%1$s">
						<label class="et_pb_contact_form_label" for="et_pb_signup_lastname" style="display: none;">%2$s</label>
						<input id="et_pb_signup_lastname" class="input" type="text" placeholder="%3$s" name="et_pb_signup_lastname">
					</p>',
					$fullwidth ? ' et_pb_contact_field_last' : ' et_pb_contact_field_half',
					esc_html( $label ),
					esc_attr( $label )
				);
				break;

			case 'email':
				$label     = __( 'Email', 'et_builder' );
				$fullwidth = 'on' === self::$_->array_get( $this->props, 'email_fullwidth', 'on' );
				$html      = sprintf( '
					<p class="et_pb_newsletter_field%1$s">
						<label class="et_pb_contact_form_label" for="et_pb_signup_email" style="display: none;">%2$s</label>
						<input id="et_pb_signup_email" class="input" type="text" placeholder="%3$s" name="et_pb_signup_email">
					</p>',
					$fullwidth ? ' et_pb_contact_field_last' : ' et_pb_contact_field_half',
					esc_html( $label ),
					esc_attr( $label )
				);
				break;

			case 'submit_button':
				$button_icon = $this->props['button_icon'] && 'on' === $this->props['custom_button'];
				$button_rel  = $this->props['button_rel'];

				$icon_class = $button_icon ? ' et_pb_custom_button_icon' : '';
				$icon_attr  = $button_icon ? et_pb_process_font_icon( $this->props['button_icon'] ) : '';

				$html = sprintf( '
					<p class="et_pb_newsletter_button_wrap">
						<a class="et_pb_newsletter_button et_pb_button%1$s" href="#"%2$s data-icon="%3$s">
							<span class="et_subscribe_loader"></span>
							<span class="et_pb_newsletter_button_text">%4$s</span>
						</a>
					</p>',
					esc_attr( $icon_class ),
					$this->get_rel_attributes( $button_rel ),
					esc_attr( $icon_attr ),
					esc_html( $this->props['button_text'] )
				);
				break;

			case 'hidden':
				$provider = $this->props['provider'];

				if ( 'feedburner' === $provider ) {
					$html = sprintf( '
						<input type="hidden" value="%1$s" name="uri" />
						<input type="hidden" name="loc" value="%2$s" />',
						esc_attr( $this->props['feedburner_uri'] ),
						esc_attr( get_locale() )
					);
				} else {
					$list       = $this->props[ $provider . '_list' ];
					$ip_address = 'on' === $this->props['ip_address'] ? 'true' : 'false';

					if ( false !== strpos( $list, '|' ) ) {
						list( $account_name, $list ) = explode( '|', $list );
					} else {
						$account_name = self::get_account_name_for_list_id( $provider, $list );
					}

					$html = sprintf( '
						<input type="hidden" value="%1$s" name="et_pb_signup_provider" />
						<input type="hidden" value="%2$s" name="et_pb_signup_list_id" />
						<input type="hidden" value="%3$s" name="et_pb_signup_account_name" />
						<input type="hidden" value="%4$s" name="et_pb_signup_ip_address" />',
						esc_attr( $provider ),
						esc_attr( $list ),
						esc_attr( $account_name ),
						esc_attr( $ip_address )
					);
				}
				break;
		}

		/**
		 * Filters the html output for individual opt-in form fields. The dynamic portion of the filter
		 * name ("$field"), will be one of: 'name', 'last_name', 'email', 'submit_button', 'hidden'.
		 *
		 * @since 3.0.75
		 *
		 * @param string $html              The form field's HTML.
		 * @param bool   $single_name_field Whether or not a single name field is being used.
		 *                                  Only applicable when "$field" is 'name'.
		 */
		return apply_filters( "et_pb_signup_form_field_html_{$field}", $html, $single_name_field );
	}

	public static function providers() {
		if ( null === self::$_providers ) {
			self::$_providers = ET_Core_API_Email_Providers::instance();
		}

		return self::$_providers;
	}

	function render( $attrs, $content = null, $render_slug ) {
		global $et_pb_half_width_counter;

		$et_pb_half_width_counter    = 0;

		$title                           = $this->_esc_attr( 'title' );
		$use_background_color            = $this->props['use_background_color'];
		$provider                        = $this->props['provider'];
		$list                            = ( 'feedburner' !== $provider ) ? $this->props[ $provider . '_list' ] : array();
		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$form_field_background_color     = $this->props['form_field_background_color'];
		$form_field_text_color           = $this->props['form_field_text_color'];
		$focus_background_color          = $this->props['focus_background_color'];
		$focus_text_color                = $this->props['focus_text_color'];
		$success_action                  = $this->props['success_action'];
		$success_message                 = $this->props['success_message'];
		$success_redirect_url            = $this->props['success_redirect_url'];
		$success_redirect_query          = $this->props['success_redirect_query'];
		$header_level                    = $this->props['header_level'];
		$use_focus_border_color          = $this->props['use_focus_border_color'];
		$use_custom_fields               = $this->props['use_custom_fields'];

		if ( 'feedburner' !== $provider ) {
			$_provider   = self::providers()->get( $provider, '', 'builder' );
			$_name_field = $_provider->name_field_only ? 'name_field_only' : 'name_field';

			$name_field       = 'on' === $this->props[ $_name_field ];
			$first_name_field = 'on' === $this->props['first_name_field'] && ! $_provider->name_field_only;
			$last_name_field  = 'on' === $this->props['last_name_field'] && ! $_provider->name_field_only;
		}

		if ( '' !== $focus_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => implode(',', array(
					'%%order_class%% .et_pb_newsletter_form p input.input:focus',
					'%%order_class%% .et_pb_newsletter_form p textarea:focus',
					'%%order_class%% .et_pb_newsletter_form p select:focus',
				)),
				'%%order_class%% .et_pb_newsletter_form p input.input:focus',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $focus_background_color ),
					et_builder_has_limitation( 'force_use_global_important' ) ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $focus_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p .input:focus',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $focus_text_color )
				),
			) );

			// Placeholder
			$focus_placeholders = array(
				'::-webkit-input-placeholder',
				'::-moz-placeholder',
				'::-ms-input-placeholder'
			);

			foreach ( $focus_placeholders as $placeholder ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => implode(',', array(
						'%%order_class%% .et_pb_newsletter_form p .input:focus' . $placeholder,
						'%%order_class%% .et_pb_newsletter_form p textarea:focus' . $placeholder,
					)),
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $focus_text_color )
					),
				) );
			}
		}

		if ( '' !== $form_field_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => implode(',', array(
					'%%order_class%% .et_pb_newsletter_form p input[type="text"]',
					'%%order_class%% .et_pb_newsletter_form p textarea',
					'%%order_class%% .et_pb_newsletter_form p select',
					'%%order_class%% .et_pb_newsletter_form p .input[type="checkbox"] + label i',
					'%%order_class%% .et_pb_newsletter_form p .input[type="radio"] + label i',
				)),
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $form_field_background_color ),
					et_builder_has_limitation( 'force_use_global_important' ) ? ' !important' : ''
				),
			) );
		}

		if ( et_pb_hover_options()->is_enabled( 'form_field_background_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => implode(',', array(
					'%%order_class%% .et_pb_newsletter_form p input[type="text"]:hover',
					'%%order_class%% .et_pb_newsletter_form p textarea:hover',
					'%%order_class%% .et_pb_newsletter_form p select:hover',
					'%%order_class%% .et_pb_newsletter_form p .input[type="checkbox"] + label i:hover',
					'%%order_class%% .et_pb_newsletter_form p .input[type="radio"] + label i:hover',
				)),
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( et_pb_hover_options()->get_value( 'form_field_background_color', $this->props ) ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $form_field_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => implode(',', array(
					'%%order_class%% .et_pb_newsletter_form p input[type="text"]',
					'%%order_class%% .et_pb_newsletter_form p textarea',
					'%%order_class%% .et_pb_newsletter_form p select',
					'%%order_class%% .et_pb_newsletter_form p .input[type="checkbox"] + label i:before',
				)),
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $form_field_text_color )
				),
			) );

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p .input[type="radio"] + label i:before',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $form_field_text_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( $this->props['layout'] ) {
			$this->add_classname( "et_pb_newsletter_layout_{$this->props['layout']}" );
		}

		if ( 'message' === $success_action || empty( $success_redirect_url ) ) {
			$success_redirect_url = $success_redirect_query = '';
		}

		if ( 'redirect' === $success_action && ! empty( $success_redirect_url ) ) {
			$success_redirect_url = et_html_attr( 'data-redirect_url', esc_url( $success_redirect_url ) );

			if ( ! empty( $success_redirect_query ) ) {
				$value_map              = array( 'name', 'last_name', 'email', 'ip_address', 'css_id' );
				$success_redirect_query = $this->process_multiple_checkboxes_field_value( $value_map, $success_redirect_query );
				$success_redirect_query = et_html_attr( 'data-redirect_query', $success_redirect_query );

				if ( false !== strpos( $success_redirect_query, 'ip_address' ) ) {
					$success_redirect_query .= et_html_attr( 'data-ip_address', et_core_get_ip_address() );
				}
			} else {
				$success_redirect_query = '';
			}
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$form          = '';
		$list_selected = ! in_array( $list, array( '', 'none' ) );

		if ( $list_selected && 'feedburner' === $provider ) {
			$form = sprintf( '
				<div class="et_pb_newsletter_form et_pb_feedburner_form">
					<form action="https://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow">
						%1$s
						%2$s
						%3$s
					</form>
				</div>',
				$this->get_form_field_html( 'email' ),
				$this->get_form_field_html( 'submit_button' ),
				$this->get_form_field_html( 'hidden' )
			);
		} else if ( $list_selected ) {
			$name_field_html      = '';
			$last_name_field_html = '';

			if ( $first_name_field || $name_field ) {
				$name_field_html = $this->get_form_field_html( 'name', $name_field );
			}

			if ( $last_name_field && ! $name_field ) {
				$last_name_field_html = $this->get_form_field_html( 'last_name' );
			}

			$footer_content = $this->props['footer_content'];

			$footer_content = preg_replace( '/^[\w]?<\/p>/smi', '', $footer_content );
			$footer_content = preg_replace( '/<p>$/smi', '', $footer_content );

			if ( $footer_content ) {
				$footer_content = sprintf('<div class="et_pb_newsletter_footer">%1$s</div>', et_core_esc_previously( $footer_content ) );
			}

			$form = sprintf( '
				<div class="et_pb_newsletter_form">
					<form method="post"%9$s>
						<div class="et_pb_newsletter_result et_pb_newsletter_error"></div>
						<div class="et_pb_newsletter_result et_pb_newsletter_success">
							<h2>%1$s</h2>
						</div>
						<div class="et_pb_newsletter_fields">
							%2$s
							%3$s
							%4$s
							%5$s
							%6$s
							%7$s
						</div>
						%8$s
					</form>
				</div>',
				esc_html( $success_message ),
				$name_field_html,
				$last_name_field_html,
				$this->get_form_field_html( 'email' ),
				'on' === $use_custom_fields ? $this->content : '',
				$this->get_form_field_html( 'submit_button' ),
				$footer_content,
				$this->get_form_field_html( 'hidden' ),
				'on' === $use_custom_fields ? ' class="et_pb_newsletter_custom_fields"' : ''
			);
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

		// Module classnames
		$this->add_classname( array(
			'et_pb_newsletter',
			'et_pb_subscribe',
			'clearfix',
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(),
		) );

		if ( 'on' !== $use_background_color ) {
			$this->add_classname( 'et_pb_no_bg' );
		}

		if ( 'on' === $use_focus_border_color ) {
			$this->add_classname( 'et_pb_with_focus_border' );
		}

		// Remove automatically added classnames
		$this->remove_classname( array(
			$render_slug,
		) );

		$description = $this->props['description'];

		$description = preg_replace( '/^[\w]?<\/p>/smi', '', $description );
		$description = preg_replace( '/<p>$/smi', '', $description );

		$output = sprintf(
			'<div%6$s class="%4$s"%5$s%9$s%10$s%11$s%12$s>
				%8$s
				%7$s
				<div class="et_pb_newsletter_description">
					%1$s
					%2$s
				</div>
				%3$s
			</div>',
			( '' !== $title ? sprintf( '<%1$s class="et_pb_module_header">%2$s</%1$s>', et_pb_process_header_level( $header_level, 'h2' ), et_core_esc_previously( $title ) ) : '' ),
			$description,
			$form,
			$this->module_classname( $render_slug ),
			'', // #5
			$this->module_id(),
			$video_background,
			$parallax_image_background,
			$success_redirect_url,
			$success_redirect_query, // #10
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover )
		);

		return $output;
	}
}
new ET_Builder_Module_Signup;
