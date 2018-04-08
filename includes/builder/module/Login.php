<?php

class ET_Builder_Module_Login extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Login', 'et_builder' );
		$this->slug       = 'et_pb_login';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'title',
			'current_page_redirect',
			'use_background_color',
			'background_color',
			'background_layout',
			'content_new',
			'admin_label',
			'module_id',
			'module_class',
			'form_field_background_color',
			'form_field_text_color',
			'focus_background_color',
			'focus_text_color',
			'box_shadow_style_fields',
			'box_shadow_horizontal_fields',
			'box_shadow_vertical_fields',
			'box_shadow_blur_fields',
			'box_shadow_spread_fields',
			'box_shadow_color_fields',
			'box_shadow_position_fields',
		);

		$this->fields_defaults = array(
			'current_page_redirect'  => array( 'off' ),
			'use_background_color'   => array( 'on' ),
			'background_color'       => array( et_builder_accent_color(), 'add_default_setting' ),
			'background_layout'      => array( 'dark' ),
			'text_orientation'       => array( 'left' ),
			'use_focus_border_color' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_login';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'redirect'     => esc_html__( 'Redirect', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
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
			'fonts' => array(
				'header' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h2, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'    => esc_html__( 'Body', 'et_builder' ),
					'css'      => array(
						'line_height' => "{$this->main_css_element} p",
						'font'        => "{$this->main_css_element}, {$this->main_css_element} .et_pb_newsletter_description_content, {$this->main_css_element} p, {$this->main_css_element} span",
						'text_shadow' => "{$this->main_css_element}, {$this->main_css_element} .et_pb_newsletter_description_content, {$this->main_css_element} p, {$this->main_css_element} span",
					),
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button' => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element} .et_pb_newsletter_button.et_pb_button",
					),
					'no_rel_attr' => true,
				),
			),
			'background' => array(
				'use_background_color' => false,
			),
			'max_width' => array(),
			'text'      => array(),
			'fields'    => array(
				'css' => array(
					'text_shadow' => "{$this->main_css_element} input",
				),
			),
			'filters' => array(),
		);
		$this->custom_css_options = array(
			'newsletter_title' => array(
				'label'    => esc_html__( 'Login Title', 'et_builder' ),
				'selector' => "{$this->main_css_element} h2, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
			),
			'newsletter_description' => array(
				'label'    => esc_html__( 'Login Description', 'et_builder' ),
				'selector' => '.et_pb_newsletter_description',
			),
			'newsletter_form' => array(
				'label'    => esc_html__( 'Login Form', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form',
			),
			'newsletter_fields' => array(
				'label'    => esc_html__( 'Login Fields', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form input',
			),
			'newsletter_button' => array(
				'label'    => esc_html__( 'Login Button', 'et_builder' ),
				'selector' => '.et_pb_login .et_pb_login_form .et_pb_newsletter_button.et_pb_button',
				'no_space_before_selector' => true,
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose a title of your login box.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
			'current_page_redirect' => array(
				'label'           => esc_html__( 'Redirect To The Current Page', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'toggle_slug'     => 'redirect',
				'description'     => esc_html__( 'Here you can choose whether the user should be redirected to the current page.', 'et_builder' ),
			),
			'background_color' => array(
				'label'             => esc_html__( 'Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'description'       => esc_html__( 'Define a custom background color for your module, or leave blank to use the default color.', 'et_builder' ),
				'depends_default'   => true,
				'toggle_slug'       => 'background',
			),
			'use_background_color' => array(
				'label'           => esc_html__( 'Use Background Color', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'color_option',
				'options'         => array(
					'on'          => esc_html__( 'Yes', 'et_builder' ),
					'off'         => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					'background_color',
				),
				'toggle_slug'     => 'background',
				'description'     => esc_html__( 'Here you can choose whether background color setting below should be used or not.', 'et_builder' ),
			),
			'background_layout' => array(
				'label'           => esc_html__( 'Text Color', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'color_option',
				'options'      	  => array(
					'dark'  => esc_html__( 'Light', 'et_builder' ),
					'light' => esc_html__( 'Dark', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'Here you can choose whether your text should be light or dark. If you are working with a dark background, then your text should be light. If your background is light, then your text should be set to dark.', 'et_builder' ),
			),
			'content_new' => array(
				'label'             => esc_html__( 'Content', 'et_builder' ),
				'type'              => 'tiny_mce',
				'option_category'   => 'basic_option',
				'description'       => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'       => 'main_content',
			),
			'form_field_background_color' => array(
				'label'             => esc_html__( 'Form Field Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'form_field_text_color' => array(
				'label'             => esc_html__( 'Form Field Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'focus_background_color' => array(
				'label'             => esc_html__( 'Focus Background Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
			),
			'focus_text_color' => array(
				'label'             => esc_html__( 'Focus Text Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'fields',
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

		$fields = array_merge(
			$fields,
			ET_Builder_Module_Fields_Factory::get( 'BoxShadow' )->get_fields( array(
				'suffix'          => '_fields',
				'label'           => esc_html__( 'Fields Box Shadow', 'et_builder' ),
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'fields',
			) )
		);

		return $fields;
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
		$background_layout           = $this->shortcode_atts['background_layout'];
		$use_background_color        = $this->shortcode_atts['use_background_color'];
		$current_page_redirect       = $this->shortcode_atts['current_page_redirect'];
		$form_field_background_color = $this->shortcode_atts['form_field_background_color'];
		$form_field_text_color       = $this->shortcode_atts['form_field_text_color'];
		$focus_background_color      = $this->shortcode_atts['focus_background_color'];
		$focus_text_color            = $this->shortcode_atts['focus_text_color'];
		$button_custom               = $this->shortcode_atts['custom_button'];
		$custom_icon                 = $this->shortcode_atts['button_icon'];
		$header_level                = $this->shortcode_atts['header_level'];
		$content                     = $this->shortcode_content;
		$use_focus_border_color      = $this->shortcode_atts['use_focus_border_color'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( '' !== $focus_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input:focus',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $focus_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $focus_text_color ) {
			$placeholder_selectors = array(
				'%%order_class%% .et_pb_newsletter_form p input:focus::-webkit-input-placeholder',
				'%%order_class%% .et_pb_newsletter_form p input:focus::-moz-placeholder',
				'%%order_class%% .et_pb_newsletter_form p input:focus:-ms-input-placeholder',
			);

			foreach ( $placeholder_selectors as $single_selector ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $single_selector,
					'declaration' => sprintf(
						'color: %1$s;',
						esc_html( $focus_text_color )
					),
				) );
			}

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_pb_newsletter_form p input:focus',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $focus_text_color )
				),
			) );
		}

		if ( '' !== $form_field_background_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input[type="text"], %%order_class%% textarea, %%order_class%% .input',
				'declaration' => sprintf(
					'background-color: %1$s%2$s;',
					esc_html( $form_field_background_color ),
					et_is_builder_plugin_active() ? ' !important' : ''
				),
			) );
		}

		if ( '' !== $form_field_text_color ) {
			$placeholder_selectors = array(
				'%%order_class%% .input::-webkit-input-placeholder',
				'%%order_class%% .input::-moz-placeholder',
				'%%order_class%% .input:-ms-input-placeholder',
			);

			foreach ( $placeholder_selectors as $single_selector ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $single_selector,
					'declaration' => sprintf(
						'color: %1$s;',
						esc_html( $form_field_text_color )
					),
				) );
			}

			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% input[type="text"], %%order_class%% textarea, %%order_class%% .input',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $form_field_text_color )
				),
			) );
		}

		$redirect_url = 'on' === $current_page_redirect
			? ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
			: '';

		if ( is_user_logged_in() && ! is_customize_preview() && ! is_et_pb_preview() ) {
			$current_user = wp_get_current_user();

			$content .= sprintf( '<br/>%1$s <a href="%2$s">%3$s</a>',
				sprintf( esc_html__( 'Logged in as %1$s', 'et_builder' ), esc_html( $current_user->display_name ) ),
				esc_url( wp_logout_url( $redirect_url ) ),
				esc_html__( 'Log out', 'et_builder' )
			);
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$class = " et_pb_module et_pb_bg_layout_{$background_layout}{$this->get_text_orientation_classname()}";

		$form = '';

		if ( ! is_user_logged_in() || is_customize_preview() || is_et_pb_preview() ) {
			$username = esc_html__( 'Username', 'et_builder' );
			$password = esc_html__( 'Password', 'et_builder' );

			$form = sprintf( '
				<div class="et_pb_newsletter_form et_pb_login_form">
					<form action="%7$s" method="post">
						<p>
							<label class="et_pb_contact_form_label" for="user_login_%12$s" style="display: none;">%3$s</label>
							<input id="user_login_%12$s" placeholder="%4$s" class="input" type="text" value="" name="log" />
						</p>
						<p>
							<label class="et_pb_contact_form_label" for="user_pass_%12$s" style="display: none;">%5$s</label>
							<input id="user_pass_%12$s" placeholder="%6$s" class="input" type="password" value="" name="pwd" />
						</p>
						<p class="et_pb_forgot_password"><a href="%2$s">%1$s</a></p>
						<p>
							<button type="submit" class="et_pb_newsletter_button et_pb_button%11$s"%10$s>%8$s</button>
							%9$s
						</p>
					</form>
				</div>',
				esc_html__( 'Forgot your password?', 'et_builder' ),
				esc_url( wp_lostpassword_url() ),
				esc_html( $username ),
				esc_attr( $username ),
				esc_html( $password ),
				esc_attr( $password ),
				esc_url( site_url( 'wp-login.php', 'login_post' ) ),
				esc_html__( 'Login', 'et_builder' ),
				( 'on' === $current_page_redirect
					? sprintf( '<input type="hidden" name="redirect_to" value="%1$s" />',  $redirect_url )
					: ''
				),
				'' !== $custom_icon && 'on' === $button_custom ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon ) )
				) : '',
				'' !== $custom_icon && 'on' === $button_custom ? ' et_pb_custom_button_icon' : '',
				// Prevent an accidental "duplicate ID" error if there's more than one instance of this module
				( '' !== $module_id ? esc_attr( $module_id ) : uniqid() )
			);
		}

		$output = sprintf(
			'<div%6$s class="et_pb_newsletter et_pb_login clearfix%4$s%7$s%8$s%9$s%11$s%12$s"%5$s>
				%13$s
				%10$s
				<div class="et_pb_newsletter_description">
					%1$s
					%2$s
				</div>
				%3$s
			</div>',
			( '' !== $title ? sprintf( '<%1$s class="et_pb_module_header">%2$s</%1$s>', et_pb_process_header_level( $header_level, 'h2' ), esc_html( $title ) ) : '' ),
			( '' !== $content ? '<div class="et_pb_newsletter_description_content">' . $content . '</div>' : '' ),
			$form,
			esc_attr( $class ),
			( 'on' === $use_background_color
				? sprintf( ' style="background-color: %1$s;"', esc_attr( $background_color ) )
				: ''
			),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			is_customize_preview() || is_et_pb_preview() ? ' et_pb_in_customizer' : '',
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			( '' !== $parallax_image_background ? ' et_pb_section_parallax' : '' ), // #11
			( 'on' === $use_focus_border_color ? ' et_pb_with_focus_border' : '' ), // #12
			$parallax_image_background // #13
		);

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$selector  = sprintf( '.%1$s .et_pb_button', self::get_module_order_class( $function_name ) );

		if ( isset( $this->shortcode_atts['custom_button'] ) && 'on' === $this->shortcode_atts['custom_button'] ) {
			self::set_style( $function_name, array(
				'selector'    => $selector,
				'declaration' => $boxShadow->get_value( $this->shortcode_atts, array( 'suffix' => '_button' ) )
			) );
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

		foreach ( $suffixes as $suffix ) {
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

new ET_Builder_Module_Login;
