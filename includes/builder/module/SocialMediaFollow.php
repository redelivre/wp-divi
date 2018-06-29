<?php

class ET_Builder_Module_Social_Media_Follow extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Social Media Follow', 'et_builder' );
		$this->slug            = 'et_pb_social_media_follow';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_social_media_follow_network';
		$this->child_item_text = esc_html__( 'Social Network', 'et_builder' );

		$this->main_css_element = 'ul%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'alignment' => esc_html__( 'Alignment', 'et_builder' ),
					'text' => esc_html__( 'Text', 'et_builder' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'before' => array(
				'label'    => esc_html__( 'Before', 'et_builder' ),
				'selector' => 'ul%%order_class%%:before',
			),
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'selector' => 'ul%%order_class%%',
			),
			'after' => array(
				'label'    => esc_html__( 'After', 'et_builder' ),
				'selector' => 'ul%%order_class%%:after',
			),
			'social_follow' => array(
				'label'    => esc_html__( 'Social Follow', 'et_builder' ),
				'selector' => 'li',
			),
			'social_icon' => array(
				'label'    => esc_html__( 'Social Icon', 'et_builder' ),
				'selector' => 'li a.icon',
			),
			'follow_button' => array(
				'label'    => esc_html__( 'Follow Button', 'et_builder' ),
				'selector' => 'li a.follow_button',
			),
		);

		$this->advanced_fields = array(
			'borders'               => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} a.icon",
							'border_styles' => "{$this->main_css_element} a",
						),
					),
					'defaults' => array(
						'border_radii' => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .et_pb_social_icon a',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main' => 'ul%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'text_orientation' => array(
					'exclude_options' => array( 'justified' ),
				),
				'options' => array(
					'text_orientation' => array(
						'label'           => esc_html__( 'Item Alignment', 'et_builder' ),
						'toggle_slug'     => 'alignment',
						'options_icon'    => 'module_align',
					),
					'background_layout' => array(
						'default' => 'light',
					),
				),
			),
			'fonts'                 => false,
			'button'                => false,
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( '8b0BlM_rlHQ' ),
				'name' => esc_html__( 'An introduction to the Social Media Follow module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'url_new_window' => array(
				'label'           => esc_html__( 'Url Opens', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'toggle_slug'     => 'icon',
				'description'     => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
				'default_on_front' => 'on',
			),
			'follow_button' => array(
				'label'           => esc_html__( 'Follow Button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'toggle_slug'     => 'icon',
				'description'     => esc_html__( 'Here you can choose whether or not to include the follow button next to the icon.', 'et_builder' ),
			),
		);
		return $fields;
	}

	function before_render() {
		global $et_pb_social_media_follow_link;

		$url_new_window    = $this->props['url_new_window'];
		$follow_button     = $this->props['follow_button'];

		$et_pb_social_media_follow_link = array(
			'url_new_window' => $url_new_window,
			'follow_button'  => $follow_button,
		);
	}

	function render( $attrs, $content = null, $render_slug ) {
		global $et_pb_social_media_follow_link;

		$background_layout         = $this->props['background_layout'];
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Get custom borders, if any
		$attrs = $this->props;

		// Module classnames
		$this->add_classname( array(
			'clearfix',
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(),
		) );

		if ( 'on' === $et_pb_social_media_follow_link['follow_button'] ) {
			$this->add_classname( 'has_follow_button' );
		}

		$output = sprintf(
			'<ul%3$s class="%2$s">
				%5$s
				%4$s
				%1$s
			</ul> <!-- .et_pb_counters -->',
			$this->content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background
		);

		return $output;
	}
}

new ET_Builder_Module_Social_Media_Follow;
