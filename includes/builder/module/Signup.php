<?php

class ET_Builder_Module_Signup extends ET_Builder_Module {

	private static $_providers;

	public static $enabled_providers;

	function init() {
		$this->name       = esc_html__( 'Email Optin', 'et_builder' );
		$this->slug       = 'et_pb_signup';
		$this->fb_support = true;

		$providers               = self::providers()->names_by_slug();
		$providers['feedburner'] = 'FeedBurner';

		self::$enabled_providers = apply_filters( 'et_builder_module_signup_enabled_providers', $providers );

		ksort( self::$enabled_providers );

		$this->whitelisted_fields = $this->_get_whitelisted_fields();

		$this->fields_defaults = array(
			'provider'               => array( 'mailchimp' ),
			'button_text'            => array( esc_html__( 'Subscribe', 'et_builder' ) ),
			'use_background_color'   => array( 'on' ),
			'background_color'       => array( et_builder_accent_color(), 'add_default_setting' ),
			'background_layout'      => array( 'dark' ),
			'text_orientation'       => array( 'left' ),
			'use_focus_border_color' => array( 'off' ),
			'first_name_field'       => array( 'on' ),
			'last_name_field'        => array( 'on' ),
			'name_field'             => array( 'off' ),
			'name_field_only'        => array( 'on' ),
			'success_action'         => array( 'message' ),
			'success_message'        => array( esc_html__( 'Success!', 'et_builder' ) ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_subscribe';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'   => esc_html__( 'Text', 'et_builder' ),
					'background'     => esc_html__( 'Background', 'et_builder' ),
					'provider'       => esc_html__( 'Email Account', 'et_builder' ),
					'fields'         => esc_html__( 'Fields', 'et_builder' ),
					'success_action' => esc_html__( 'Success Action', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'fields' => esc_html__( 'Fields', 'et_builder' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_options = array(
			'fonts'                 => array(
				'header'         => array(
					'label' => esc_html__( 'Title', 'et_builder' ),
					'css'   => array(
						'main'      => "{$this->main_css_element} .et_pb_newsletter_description h2, {$this->main_css_element} .et_pb_newsletter_description h1.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h3.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h4.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h5.et_pb_module_header, {$this->main_css_element} .et_pb_newsletter_description h6.et_pb_module_header",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'           => array(
					'label' => esc_html__( 'Body', 'et_builder' ),
					'css'   => array(
						'main'        => "{$this->main_css_element} .et_pb_newsletter_description, {$this->main_css_element} .et_pb_newsletter_form",
						'line_height' => "{$this->main_css_element} p",
						'text_shadow' => "{$this->main_css_element} .et_pb_newsletter_description",
					),
				),
				'result_message' => array(
					'label' => esc_html__( 'Result Message', 'et_builder' ),
					'css'   => array(
						'main' => "{$this->main_css_element} .et_pb_newsletter_form .et_pb_newsletter_result h2",
					),
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css'   => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_newsletter_button.et_pb_button",
					),
				),
			),
			'background'            => array(
				'use_background_color' => false,
			),
			'max_width'             => array(),
			'text'                  => array(
				'css' => array(
					'text_shadow' => '%%order_class%% .et_pb_newsletter_description',
				),
			),
			'fields'                => array(
				'css' => array(
					'text_shadow' => "{$this->main_css_element} input",
				),
			),
			'filters' => array(),
		);

		$this->custom_css_options = array(
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
				'selector' => '.et_pb_newsletter_form input',
			),
			'newsletter_button'      => array(
				'label'                    => esc_html__( 'Subscribe Button', 'et_builder' ),
				'selector'                 => '.et_pb_subscribe .et_pb_newsletter_button.et_pb_button',
				'no_space_before_selector' => true,
			),
		);
	}

	protected static function _get_account_fields( $provider_slug ) {
		$fields  = self::providers()->account_fields( $provider_slug );
		$is_VB   = isset( $_REQUEST['action'] ) && 'et_fb_retrieve_builder_data' === $_REQUEST['action'];
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

			$account_fields[ $field_id ] = array(
				'name'            => $field_id,
				'label'           => et_esc_previously( $field_info['label'] ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => sprintf( '<a target="_blank" href="https://www.elegantthemes.com/documentation/bloom/accounts#%1$s">%2$s</a>', $provider_slug, $description_text ),
				'show_if'         => array(
					$list_key => $show_if,
				),
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

			$account_fields = is_admin() ? self::_get_account_fields( $provider_slug ) : array();
			$fields         = array_merge( $fields, $account_fields );
		}

		return $fields;
	}

	protected function _get_whitelisted_fields() {
		static $fields = array();

		if ( ! empty( $fields ) ) {
			return $fields;
		}

		$fields = array(
			'admin_label',
			'background_color',
			'background_layout',
			'button_text',
			'content_new',
			'first_name_field',
			'focus_background_color',
			'focus_text_color',
			'form_field_background_color',
			'form_field_text_color',
			'last_name_field',
			'module_class',
			'module_id',
			'name_field',
			'name_field_only',
			'provider',
			'success_action',
			'success_message',
			'success_redirect_url',
			'success_redirect_query',
			'title',
			'use_background_color',
			'box_shadow_style_fields',
			'box_shadow_horizontal_fields',
			'box_shadow_vertical_fields',
			'box_shadow_blur_fields',
			'box_shadow_spread_fields',
			'box_shadow_color_fields',
			'box_shadow_position_fields',
		);

		foreach ( self::$enabled_providers as $provider_slug => $provider_name ) {
			$suffix   = 'feedburner' === $provider_slug ? '_uri' : '_list';
			$fields[] = $provider_slug . $suffix;
		}

		return $fields;
	}

	function get_fields() {
		$name_field_only = array_keys( self::providers()->names_by_slug( 'all', 'name_field_only' ) );

		return array_merge(
			array(
				'provider'       => array(
					'label'           => esc_html__( 'Service Provider', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'basic_option',
					'options'         => self::$enabled_providers,
					'default'         => 'mailchimp',
					'description'     => esc_html__( 'Choose a service provider.', 'et_builder' ),
					'toggle_slug'     => 'provider',
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
					'label'             => esc_html__( 'Message', 'et_builder' ),
					'type'              => 'text',
					'option_category'   => 'configuration',
					'default'           => esc_html__( 'Success!', 'et_builder' ),
					'shortcode_default' => esc_html__( 'Success!', 'et_builder' ),
					'show_if'           => array(
						'success_action' => 'message',
					),
					'toggle_slug'       => 'success_action',
					'description'       => esc_html__( 'The message that will be shown to site visitors who subscribe to your list.', 'et_builder' ),
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
						'name'       => esc_html__( 'Name' ),
						'last_name'  => esc_html__( 'Last Name' ),
						'email'      => esc_html__( 'Email' ),
						'ip_address' => esc_html__( 'IP Address' ),
						'css_id'     => esc_html__( 'CSS ID' ),
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
				),
				'button_text'                 => array(
					'label'           => esc_html__( 'Button Text', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'description'     => esc_html__( 'Define custom text for the subscribe button.', 'et_builder' ),
					'toggle_slug'     => 'main_content',
				),
				'background_color'            => array(
					'label'           => esc_html__( 'Background Color', 'et_builder' ),
					'type'            => 'color-alpha',
					'description'     => esc_html__( 'Define a custom background color for your module, or leave blank to use the default color.', 'et_builder' ),
					'depends_default' => true,
					'toggle_slug'     => 'background',
				),
				'use_background_color'        => array(
					'label'           => esc_html__( 'Use Background Color', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'affects'         => array(
						'background_color',
					),
					'toggle_slug'     => 'background',
					'description'     => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'et_builder' ),
				),
				'background_layout'           => array(
					'label'           => esc_html__( 'Text Color', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'configuration',
					'options'         => array(
						'dark'  => esc_html__( 'Light', 'et_builder' ),
						'light' => esc_html__( 'Dark', 'et_builder' ),
					),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'text',
					'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
				),
				'content_new'                 => array(
					'label'           => esc_html__( 'Content', 'et_builder' ),
					'type'            => 'tiny_mce',
					'option_category' => 'basic_option',
					'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
					'toggle_slug'     => 'main_content',
				),
				'form_field_background_color' => array(
					'label'        => esc_html__( 'Form Field Background Color', 'et_builder' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
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
				'disabled_on'                 => array(
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
				'admin_label'                 => array(
					'label'       => esc_html__( 'Admin Label', 'et_builder' ),
					'type'        => 'text',
					'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
					'toggle_slug' => 'admin_label',
				),
				'module_id'                   => array(
					'label'           => esc_html__( 'CSS ID', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'module_class'                => array(
					'label'           => esc_html__( 'CSS Class', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'option_class'    => 'et_pb_custom_css_regular',
				),
			),

			ET_Builder_Module_Fields_Factory::get( 'BoxShadow' )->get_fields( array(
				'suffix'              => '_fields',
				'label'               => esc_html__( 'Fields Box Shadow', 'et_builder' ),
				'option_category'     => 'layout',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'fields',
			) )
		);
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
					if ( ! empty( $list_details['name'] ) ) {
						$lists[ $provider_slug ][ $account_name ][ $list_id ] = esc_html( $list_details['name'] );
					}
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
				$label = $single_name_field ? __( 'Name', 'et_builder' ) : __( 'First Name', 'et_builder' );
				$html  = sprintf( '
					<p>
						<label class="et_pb_contact_form_label" for="et_pb_signup_firstname" style="display: none;">%1$s</label>
						<input id="et_pb_signup_firstname" class="input" type="text" placeholder="%2$s" name="et_pb_signup_firstname">
					</p>',
					esc_html( $label ),
					esc_attr( $label )
				);
				break;

			case 'last_name':
				$label = __( 'Last Name', 'et_builder' );
				$html  = sprintf( '
					<p>
						<label class="et_pb_contact_form_label" for="et_pb_signup_lastname" style="display: none;">%1$s</label>
						<input id="et_pb_signup_lastname" class="input" type="text" placeholder="%2$s" name="et_pb_signup_lastname">
					</p>',
					esc_html( $label ),
					esc_attr( $label )
				);
				break;

			case 'email':
				$label = __( 'Email', 'et_builder' );
				$html  = sprintf( '
					<p>
						<label class="et_pb_contact_form_label" for="et_pb_signup_email" style="display: none;">%1$s</label>
						<input id="et_pb_signup_email" class="input" type="text" placeholder="%2$s" name="et_pb_signup_email">
					</p>',
					esc_html( $label ),
					esc_attr( $label )
				);
				break;

			case 'submit_button':
				$button_icon = $this->shortcode_atts['button_icon'] && 'on' === $this->shortcode_atts['custom_button'];
				$button_rel  = $this->shortcode_atts['button_rel'];

				$icon_class = $button_icon ? ' et_pb_custom_button_icon' : '';
				$icon_attr  = $button_icon ? et_pb_process_font_icon( $this->shortcode_atts['button_icon'] ) : '';

				$html = sprintf( '
					<p>
						<a class="et_pb_newsletter_button et_pb_button%1$s" href="#"%2$s data-icon="%3$s">
							<span class="et_subscribe_loader"></span>
							<span class="et_pb_newsletter_button_text">%4$s</span>
						</a>
					</p>',
					esc_attr( $icon_class ),
					$this->get_rel_attributes( $button_rel ),
					esc_attr( $icon_attr ),
					esc_html( $this->shortcode_atts['button_text'] )
				);
				break;

			case 'hidden':
				$provider = $this->shortcode_atts['provider'];

				if ( 'feedburner' === $provider ) {
					$html = sprintf( '
						<input type="hidden" value="%1$s" name="uri" />
						<input type="hidden" name="loc" value="%2$s" />',
						esc_url( $this->shortcode_atts['feedburner_uri'] ),
						esc_attr( get_locale() )
					);
				} else {
					$list = $this->shortcode_atts[ $provider . '_list' ];

					if ( false !== strpos( $list, '|' ) ) {
						list( $account_name, $list ) = explode( '|', $list );
					} else {
						$account_name = self::get_account_name_for_list_id( $provider, $list );
					}

					$html = sprintf( '
						<input type="hidden" value="%1$s" name="et_pb_signup_provider" />
						<input type="hidden" value="%2$s" name="et_pb_signup_list_id" />
						<input type="hidden" value="%3$s" name="et_pb_signup_account_name" />',
						esc_attr( $provider ),
						esc_attr( $list ),
						esc_attr( $account_name )
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

	/**
	 * Add additional Text Shadow fields to this module
	 *
	 * @return array
	 */
	protected function _add_additional_text_shadow_fields() {
		// Add to Text (done in the parent)
		parent::_add_additional_text_shadow_fields();

		// Add to Fields
		$this->_additional_fields_options = array_merge(
			$this->_additional_fields_options,
			$this->text_shadow->get_fields(array(
				'label'           => esc_html__( 'Fields', 'et_builder' ),
				'prefix'          => 'fields',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'fields',
			))
		);
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id                   = $this->shortcode_atts['module_id'];
		$module_class                = $this->shortcode_atts['module_class'];
		$title                       = $this->shortcode_atts['title'];
		$background_color            = $this->shortcode_atts['background_color'];
		$use_background_color        = $this->shortcode_atts['use_background_color'];
		$provider                    = $this->shortcode_atts['provider'];
		$feedburner_uri              = $this->shortcode_atts['feedburner_uri'];
		$list                        = $this->shortcode_atts[ $provider . '_list' ];
		$background_layout           = $this->shortcode_atts['background_layout'];
		$form_field_background_color = $this->shortcode_atts['form_field_background_color'];
		$form_field_text_color       = $this->shortcode_atts['form_field_text_color'];
		$focus_background_color      = $this->shortcode_atts['focus_background_color'];
		$focus_text_color            = $this->shortcode_atts['focus_text_color'];
		$success_action              = $this->shortcode_atts['success_action'];
		$success_message             = $this->shortcode_atts['success_message'];
		$success_redirect_url        = $this->shortcode_atts['success_redirect_url'];
		$success_redirect_query      = $this->shortcode_atts['success_redirect_query'];
		$header_level                = $this->shortcode_atts['header_level'];
		$use_focus_border_color      = $this->shortcode_atts['use_focus_border_color'];

		$_provider   = self::providers()->get( $provider, '', 'builder' );
		$_name_field = $_provider->name_field_only ? 'name_field_only' : 'name_field';

		$name_field       = 'on' === $this->shortcode_atts[ $_name_field ];
		$first_name_field = 'on' === $this->shortcode_atts['first_name_field'] && ! $_provider->name_field_only;
		$last_name_field  = 'on' === $this->shortcode_atts['last_name_field'] && ! $_provider->name_field_only;

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $focus_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input.input:focus',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $focus_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $focus_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input.input:focus',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $focus_text_color )
				),
			) );
		}

		if ( '' !== $form_field_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input[type="text"], %%order_class%% textarea',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $form_field_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $form_field_text_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input[type="text"], %%order_class%% textarea',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $form_field_text_color )
				),
			) );
		}

		if ( 'message' === $success_action || empty( $success_redirect_url ) ) {
			$success_redirect_url = $success_redirect_query = '';
		}

		if ( 'redirect' === $success_action && ! empty( $success_redirect_url ) ) {
			$success_redirect_url = et_html5_data_attr( 'redirect_url', esc_url( $success_redirect_url ) );

			if ( ! empty( $success_redirect_query ) ) {
				$value_map              = array( 'name', 'last_name', 'email', 'ip_address', 'css_id' );
				$success_redirect_query = $this->process_multiple_checkboxes_field_value( $value_map, $success_redirect_query );
				$success_redirect_query = et_html5_data_attr( 'redirect_query', $success_redirect_query );

				if ( false !== strpos( $success_redirect_query, 'ip_address' ) ) {
					$success_redirect_query .= et_html5_data_attr( 'ip_address', et_core_get_ip_address() );
				}
			} else {
				$success_redirect_query = '';
			}
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class         = " et_pb_module et_pb_bg_layout_{$background_layout}{$this->get_text_orientation_classname()}";
		$form          = '';
		$list_selected = ! in_array( $list, array( '', 'none' ) );

		if ( $list_selected && 'feedburner' === $provider ) {
			$form = sprintf( '
				<div class="et_pb_newsletter_form et_pb_feedburner_form">
					<form action="https://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open(\'https://feedburner.google.com/fb/a/mailverify?uri=%4$s\', \'popupwindow\', \'scrollbars=yes,width=550,height=520\'); return true">
						%1$s
						%2$s
						%3$s
					</form>
				</div>',
				$this->get_form_field_html( 'email' ),
				$this->get_form_field_html( 'submit_button' ),
				$this->get_form_field_html( 'hidden' ),
				esc_url( $feedburner_uri )
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

			$form = sprintf( '
				<div class="et_pb_newsletter_form">
					<div class="et_pb_newsletter_result et_pb_newsletter_error"></div>
					<div class="et_pb_newsletter_result et_pb_newsletter_success">
						<h2>%1$s</h2>
					</div>
					%2$s
					%3$s
					%4$s
					%5$s
					%6$s
				</div>',
				esc_html( $success_message ),
				$name_field_html,
				$last_name_field_html,
				$this->get_form_field_html( 'email' ),
				$this->get_form_field_html( 'submit_button' ),
				$this->get_form_field_html( 'hidden' )
			);
		}

		$output = sprintf(
			'<div%6$s class="et_pb_newsletter et_pb_subscribe clearfix%4$s%7$s%8$s%9$s%11$s%12$s"%5$s%14$s%15$s>
				%13$s
				%10$s
				<div class="et_pb_newsletter_description">
					%1$s
					%2$s
				</div>
				%3$s
			</div>',
			( '' !== $title ? sprintf( '<%1$s class="et_pb_module_header">%2$s</%1$s>', et_pb_process_header_level( $header_level, 'h2' ), esc_html( $title ) ) : '' ),
			$this->shortcode_content,
			$form,
			esc_attr( $class ),
			( 'on' === $use_background_color
				? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
				: ''
			),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ), // #6
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			( 'on' !== $use_background_color ? ' et_pb_no_bg' : '' ),
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background, // #10
			( '' !== $parallax_image_background ? ' et_pb_section_parallax' : '' ), // #11
			( 'on' === $use_focus_border_color ? ' et_pb_with_focus_border' : '' ), // #12
			$parallax_image_background, // #13
			$success_redirect_url, // #14
			$success_redirect_query // #15
		);

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );

		if (
			isset( $this->shortcode_atts['custom_button'] )
			&&
			$this->shortcode_atts['custom_button'] === 'on'
		) {
			self::set_style( $function_name, array(
					'selector'    => '%%order_class%% .et_pb_newsletter_button',
					'declaration' => $boxShadow->get_value( $this->shortcode_atts, array( 'suffix' => '_button' ) )
				)
			);
		}

		self::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form .input',
				'declaration' => $boxShadow->get_value( $this->shortcode_atts, array( 'suffix' => '_fields' ) )
			)
		);

		parent::process_box_shadow( $function_name );
	}

	protected function _add_additional_border_fields() {
		parent::_add_additional_border_fields();

		$suffix = 'fields';
		$tab_slug = 'advanced';
		$toggle_slug = 'fields';

		$this->_additional_fields_options = array_merge(
			$this->_additional_fields_options,
			ET_Builder_Module_Fields_Factory::get( 'Border' )->get_fields( array(
				'suffix'          => "_{$suffix}",
				'label_prefix'    => esc_html__( 'Fields', 'et_builder' ),
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'defaults'        => array(
					'border_radii'  => 'on|3px|3px|3px|3px',
					'border_styles' => array(
						'width' => '0px',
						'color' => '#333333',
						'style' => 'solid',
					),
				),
			) )
		);

		$this->advanced_options["border_{$suffix}"]["border_radii_{$suffix}"] = $this->_additional_fields_options["border_radii_{$suffix}"];
		$this->advanced_options["border_{$suffix}"]["border_styles_{$suffix}"] = $this->_additional_fields_options["border_styles_{$suffix}"];

		$this->advanced_options["border_{$suffix}"]['css'] = array(
			'main' => array(
				'border_radii' => "%%order_class%% .et_pb_newsletter_form p input",
				'border_styles' => "%%order_class%% .et_pb_newsletter_form p input",
			)
		);

		$this->_additional_fields_options = array_merge(
			$this->_additional_fields_options,
			array('use_focus_border_color' => array(
				'label'           => esc_html__( 'Use Focus Borders', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'     => array(
					'border_radii_fields_focus',
					'border_styles_fields_focus',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'fields',
			)
			)
		);

		$suffix = 'fields_focus';
		$tab_slug = 'advanced';
		$toggle_slug = 'fields';

		$this->_additional_fields_options = array_merge(
			$this->_additional_fields_options,
			ET_Builder_Module_Fields_Factory::get( 'Border' )->get_fields( array(
				'suffix'          => "_{$suffix}",
				'label_prefix'    => esc_html__( 'Focus', 'et_builder' ),
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'depends_to'      => array( 'use_focus_border_color' ),
				'depends_show_if' => 'on',
				'defaults'        => array(
					'border_radii'  => 'on|3px|3px|3px|3px',
					'border_styles' => array(
						'width' => '0px',
						'color' => '#333333',
						'style' => 'solid',
					),
				),
			) )
		);

		$this->advanced_options["border_{$suffix}"]["border_radii_{$suffix}"] = $this->_additional_fields_options["border_radii_{$suffix}"];
		$this->advanced_options["border_{$suffix}"]["border_styles_{$suffix}"] = $this->_additional_fields_options["border_styles_{$suffix}"];

		$this->advanced_options["border_{$suffix}"]['css'] = array(
			'main' => array(
				'border_radii' => "%%order_class%% .et_pb_newsletter_form p input:focus",
				'border_styles' => "%%order_class%% .et_pb_newsletter_form p input:focus",
			)
		);
	}

	function process_advanced_border_options( $function_name ) {
		parent::process_advanced_border_options( $function_name );

		$suffixes = array( 'fields' );

		$use_focus_border_color = $this->shortcode_atts['use_focus_border_color'] === 'on' ? true : false;
		if ( $use_focus_border_color ) {
			$suffixes[] = 'fields_focus';
		}

		foreach ($suffixes as $suffix) {
			/**
			 * @var ET_Builder_Module_Field_Border $border_field
			 */
			$border_field = ET_Builder_Module_Fields_Factory::get( 'Border' );

			$css_selector = ! empty( $this->advanced_options["border_{$suffix}"]['css']['main']['border_radii'] ) ? $this->advanced_options["border_{$suffix}"]['css']['main']['border_radii'] : $this->main_css_element;
			self::set_style( $function_name, array(
				'selector'    => $css_selector,
				'declaration' => $border_field->get_radii_style( $this->shortcode_atts, $this->advanced_options, "_{$suffix}" ),
				'priority'    => $this->_style_priority,
			) );

			$css_selector = ! empty( $this->advanced_options["border_{$suffix}"]['css']['main']['border_styles'] ) ? $this->advanced_options["border_{$suffix}"]['css']['main']['border_styles'] : $this->main_css_element;
			self::set_style( $function_name, array(
				'selector'    => $css_selector,
				'declaration' => $border_field->get_borders_style( $this->shortcode_atts, $this->advanced_options, "_{$suffix}" ),
				'priority'    => $this->_style_priority,
			) );
		}
	}
}
new ET_Builder_Module_Signup;
