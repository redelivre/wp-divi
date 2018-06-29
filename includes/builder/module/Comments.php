<?php

class ET_Builder_Module_Comments extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Comments', 'et_builder' );
		$this->slug       = 'et_pb_comments';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements'   => esc_html__( 'Elements', 'et_builder' ),
					'background' => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url']",
							'border_styles' => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url']",
						),
						'important' => 'all',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'fonts'                 => array(
				'header' => array(
					'label'          => esc_html__( 'Title', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} h1.page_title, {$this->main_css_element} h2.page_title, {$this->main_css_element} h3.page_title, {$this->main_css_element} h4.page_title, {$this->main_css_element} h5.page_title, {$this->main_css_element} h6.page_title",
					),
					'header_level' => array(
						'default' => 'h1',
					),
				),
				'body' => array(
					'label'          => esc_html__( 'Comment', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .comment-content p",
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
				'form_field' => array(
					'label'          => esc_html__( 'Field', 'et_builder' ),
					'css'            => array(
						'main'      => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url'], {$this->main_css_element} #commentform label",
						'important' => 'all',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '18px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'meta' => array(
					'label'          => esc_html__( 'Meta', 'et_builder' ),
					'css'            => array(
						'main'      => "{$this->main_css_element} .comment_postinfo span",
						'important' => 'all',
						'text_align' => "{$this->main_css_element} .comment_postinfo",
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
			'button'                => array(
				'button' => array(
					'label' => esc_html__( 'Button', 'et_builder' ),
					'css' => array(
						'plugin_main' => "{$this->main_css_element}.et_pb_comments_module .et_pb_button",
						'alignment' => "{$this->main_css_element} .form-submit",
					),
					'no_rel_attr' => true,
					'use_alignment' => true,
					'box_shadow'    => array(
						'css' => array(
							'main' => "{$this->main_css_element} .et_pb_button",
						),
					),
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'css' => array(
					'text_shadow' => '%%order_class%% p, %%order_class%% .comment_postinfo, %%order_class%% .page_title, %%order_class%% .comment-reply-title',
				),
				'options' => array(
					'background_layout' => array(
						'default_on_front' => 'light',
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'main_header' => array(
				'label'    => esc_html__( 'Comments Count', 'et_builder' ),
				'selector' => 'h1#comments',
			),
			'comment_body' => array(
				'label'    => esc_html__( 'Comment Body', 'et_builder' ),
				'selector' => '.comment-body',
			),
			'comment_meta' => array(
				'label'    => esc_html__( 'Comment Meta', 'et_builder' ),
				'selector' => '.comment_postinfo',
			),
			'comment_content' => array(
				'label'    => esc_html__( 'Comment Content', 'et_builder' ),
				'selector' => '.comment_area .comment-content',
			),
			'comment_avatar' => array(
				'label'    => esc_html__( 'Comment Avatar', 'et_builder' ),
				'selector' => '.comment_avatar',
			),
			'reply_button' => array(
				'label'    => esc_html__( 'Reply Button', 'et_builder' ),
				'selector' => '.comment-reply-link.et_pb_button',
			),
			'new_title' => array(
				'label'    => esc_html__( 'New Comment Title', 'et_builder' ),
				'selector' => 'h3#reply-title',
			),
			'message_field' => array(
				'label'    => esc_html__( 'Message Field', 'et_builder' ),
				'selector' => '.comment-form-comment textarea#comment',
			),
			'name_field' => array(
				'label'    => esc_html__( 'Name Field', 'et_builder' ),
				'selector' => '.comment-form-author input',
			),
			'email_field' => array(
				'label'    => esc_html__( 'Email Field', 'et_builder' ),
				'selector' => '.comment-form-email input',
			),
			'website_field' => array(
				'label'    => esc_html__( 'Website Field', 'et_builder' ),
				'selector' => '.comment-form-url input',
			),
			'submit_button' => array(
				'label'    => esc_html__( 'Submit Button', 'et_builder' ),
				'selector' => '.form-submit .et_pb_button#et_pb_submit',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'k6vskmOxM4U' ),
				'name' => esc_html__( 'An introduction to the Comments module', 'et_builder' ),
			),
		);
	}

	function get_fields() {

		$fields = array(
			'show_avatar' => array(
				'label'            => esc_html__( 'Show author avatar', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
			),
			'show_reply' => array(
				'label'            => esc_html__( 'Show reply button', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
			),
			'show_count' => array(
				'label'            => esc_html__( 'Show comments count', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
			),
			'form_background_color' => array(
				'label'        => esc_html__( 'Field Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'toggle_slug'  => 'form_field',
				'tab_slug'     => 'advanced',
			),
		);

		return $fields;
	}

	/**
	 * Get comments markup for comments module
	 *
	 * @return string of comment section markup
	 */
	static function get_comments( $header_level ) {
		global $et_pb_comments_print, $et_comments_header_level;

		// Globally flag that comment module is being printed
		$et_pb_comments_print = true;

		// set custom header level for comments form
		$et_comments_header_level = $header_level;

		// remove filters to make sure comments module rendered correctly if the below filters were applied earlier.
		remove_filter( 'get_comments_number', '__return_zero' );
		remove_filter( 'comments_open', '__return_false' );
		remove_filter( 'comments_array', '__return_empty_array' );

		// Custom action before calling comments_template.
		do_action( 'et_fb_before_comments_template' );

		ob_start();
		comments_template( '', true );
		$comments_content = ob_get_contents();
		ob_end_clean();

		// Custom action after calling comments_template.
		do_action( 'et_fb_after_comments_template' );

		// Globally flag that comment module has been printed
		$et_pb_comments_print = false;
		$et_comments_header_level = '';

		return $comments_content;
	}

	function et_pb_comments_template() {
		return realpath( dirname(__FILE__) . '/..' ) . '/comments_template.php';
	}

	function et_pb_comments_submit_button( $submit_button ) {
		return sprintf(
			'<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			esc_attr( 'submit' ),
			esc_attr( 'et_pb_submit' ),
			esc_attr( 'submit' ),
			esc_html__( 'Submit Comment', 'et_builder' )
		);
	}

	function et_pb_modify_comments_request( $params ) {
		// modify the request parameters the way it doesn't change the result just to make request with unique parameters
		$params->query_vars['type__not_in'] = 'et_pb_comments_random_type_' . $this->et_pb_unique_comments_module_class;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$button_custom         = $this->props['custom_button'];
		$custom_icon           = $this->props['button_icon'];
		$form_background_color = $this->props['form_background_color'];
		$show_avatar           = $this->props['show_avatar'];
		$show_reply            = $this->props['show_reply'];
		$show_count            = $this->props['show_count'];
		$background_layout     = $this->props['background_layout'];
		$header_level          = $this->props['header_level'];
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$this->et_pb_unique_comments_module_class = ET_Builder_Element::get_module_order_class( $render_slug ); // use this variable to make the comments request unique for each module instance

		if ( '' !== $form_background_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%% #commentform textarea, %%order_class%% #commentform input[type="text"], %%order_class%% #commentform input[type="email"], %%order_class%% #commentform input[type="url"]',
				'declaration' => sprintf(
					'background-color: %1$s;',
					esc_html( $form_background_color )
				),
			) );
		}

		// Modify the comments request to make sure it's unique.
		// Otherwise WP generates SQL error and doesn't allow multiple comments sections on single page
		add_action( 'pre_get_comments', array( $this, 'et_pb_modify_comments_request' ), 1 );

		// include custom comments_template to display the comment section with Divi style
		add_filter( 'comments_template', array( $this, 'et_pb_comments_template' ) );

		// Modify submit button to be advanced button style ready
		add_filter( 'comment_form_submit_button', array( $this, 'et_pb_comments_submit_button' ) );

		$comments_content = self::get_comments( et_pb_process_header_level( $header_level, 'h1' ) );

		// remove all the actions and filters to not break the default comments section from theme
		remove_filter( 'comments_template', array( $this, 'et_pb_comments_template' ) );
		remove_action( 'pre_get_comments', array( $this, 'et_pb_modify_comments_request' ), 1 );

		$comments_custom_icon = 'on' === $button_custom ? $custom_icon : '';

		// Module classname
		$this->add_classname( array(
			'et_pb_comments_module',
			$this->get_text_orientation_classname(),
			"et_pb_bg_layout_{$background_layout}",
		) );

		if ( 'off' === $show_avatar ) {
			$this->add_classname( 'et_pb_no_avatar' );
		}

		if ( 'off' === $show_reply ) {
			$this->add_classname( 'et_pb_no_reply_button' );
		}

		if ( 'off' === $show_count ) {
			$this->add_classname( 'et_pb_no_comments_count' );
		}

		// Removed automatically added classname
		$this->remove_classname( $render_slug );

		$output = sprintf(
			'<div%3$s class="%2$s"%4$s>
				%5$s
				%6$s
				%1$s
			</div>',
			$comments_content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			'' !== $comments_custom_icon ? sprintf( ' data-icon="%1$s"', esc_attr( et_pb_process_font_icon( $comments_custom_icon ) ) ) : '',
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Comments;
