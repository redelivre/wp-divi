<?php

class ET_Builder_Module_Social_Media_Follow_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Social Network', 'et_builder' );
		$this->slug                        = 'et_pb_social_media_follow_network';
		$this->fb_support                  = true;
		$this->type                        = 'child';
		$this->child_title_var             = 'content_new';

		$this->whitelisted_fields = array(
			'social_network',
			'content_new',
			'url',
			'background_color',
			'skype_url',
			'skype_action',
		);

		$this->fields_defaults = array(
			'url'              => array( '#' ),
			'skype_action'     => array( 'call' ),
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Network', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Social Network', 'et_builder' );
		$this->settings_text               = esc_html__( 'Social Network Settings', 'et_builder' );

		$this->custom_css_options = array(
			'before' => array(
				'label'    => esc_html__( 'Before', 'et_builder' ),
				'selector' => '.et_pb_social_media_follow li%%order_class%%:before',
			),
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
				'selector' => '.et_pb_social_media_follow li%%order_class%%',
			),
			'after' => array(
				'label'    => esc_html__( 'After', 'et_builder' ),
				'selector' => '.et_pb_social_media_follow li%%order_class%%:after',
			),
			'social_icon' => array(
				'label'    => esc_html__( 'Social Icon', 'et_builder' ),
				'selector' => '.et_pb_social_network_link a.icon',
				'no_space_before_selector' => true,
			),
			'follow_button' => array(
				'label'    => esc_html__( 'Follow Button', 'et_builder' ),
				'selector' => '.et_pb_social_network_link a.follow_button',
				'no_space_before_selector' => true,
			),
		);

		$this->advanced_options = array(
			'background' => array(
				'css' => array(
					'main'      => '%%order_class%% a.icon',
					'important' => 'all',
				),
			),
			'border' => array(
				'css'      => array(
					'main' => array(
						'border_radii'  => "%%order_class%%.et_pb_social_icon a.icon",
						'border_styles' => "%%order_class%%.et_pb_social_icon a.icon",
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
			'custom_margin_padding' => array(
				'css' => array(
					'padding' => '.et_pb_social_media_follow li%%order_class%% a',
					'main'    => '%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'filters' => array(),
		);
	}

	function get_fields() {
		$fields = array(
			'social_network' => array(
				'label'           => esc_html__( 'Social Network', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'class'           => 'et-pb-social-network',
				'options' => array(
					''            => esc_html__( 'Select a Network', 'et_builder' ),
					'facebook'    => array(
						'value' => esc_html__( 'Facebook', 'et_builder' ),
						'data'  => array( 'color' => '#3b5998' ),
					),
					'twitter'     => array(
						'value' => esc_html__( 'Twitter', 'et_builder' ),
						'data'  => array( 'color' => '#00aced' ),
					),
					'google-plus' => array(
						'value' => esc_html__( 'Google+', 'et_builder' ),
						'data'  => array( 'color' => '#dd4b39' ),
					),
					'pinterest'   => array(
						'value' => esc_html__( 'Pinterest', 'et_builder' ),
						'data'  => array( 'color' => '#cb2027' ),
					),
					'linkedin'    => array(
						'value' => esc_html__( 'LinkedIn', 'et_builder' ),
						'data'  => array( 'color' => '#007bb6' ),
					),
					'tumblr'      => array(
						'value' => esc_html__( 'tumblr', 'et_builder' ),
						'data'  => array( 'color' => '#32506d' ),
					),
					'instagram'   => array(
						'value' => esc_html__( 'Instagram', 'et_builder' ),
						'data'  => array( 'color' => '#ea2c59' ),
					),
					'skype'       => array(
						'value' => esc_html__( 'skype', 'et_builder' ),
						'data'  => array( 'color' => '#12A5F4' ),
					),
					'flikr'       => array(
						'value' => esc_html__( 'Flickr', 'et_builder' ),
						'data'  => array( 'color' => '#ff0084' ),
					),
					'myspace'     => array(
						'value' => esc_html__( 'MySpace', 'et_builder' ),
						'data'  => array( 'color' => '#3b5998' ),
					),
					'dribbble'    => array(
						'value' => esc_html__( 'dribbble', 'et_builder' ),
						'data'  => array( 'color' => '#ea4c8d' ),
					),
					'youtube'     => array(
						'value' => esc_html__( 'Youtube', 'et_builder' ),
						'data'  => array( 'color' => '#a82400' ),
					),
					'vimeo'       => array(
						'value' => esc_html__( 'Vimeo', 'et_builder' ),
						'data'  => array( 'color' => '#45bbff' ),
					),
					'rss'         => array(
						'value' => esc_html__( 'RSS', 'et_builder' ),
						'data'  => array( 'color' => '#ff8a3c' ),
					),
				),
				'affects'           => array(
					'url',
					'skype_url',
					'skype_action',
				),
				'overwrite_onchange' => array(
					'background_color'
				),
				'description' => esc_html__( 'Choose the social network', 'et_builder' ),
				'toggle_slug' => 'main_content',
			),
			'content_new' => array(
				'label'       => esc_html__( 'Content', 'et_builder' ),
				'type'        => 'hidden',
				'toggle_slug' => 'main_content',
			),
			'url' => array(
				'label'               => esc_html__( 'Account URL', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'description'         => esc_html__( 'The URL for this social network link.', 'et_builder' ),
				'depends_show_if_not' => 'skype',
				'depends_to'          => array(
					'social_network'
				),
				'toggle_slug'         => 'link',
			),
			'skype_url' => array(
				'label'           => esc_html__( 'Account Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The Skype account name.', 'et_builder' ),
				'depends_show_if' => 'skype',
				'depends_to'          => array(
					'social_network'
				),
				'toggle_slug'     => 'main_content',
			),
			'skype_action' => array(
				'label'           => esc_html__( 'Skype Button Action', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'call' => esc_html__( 'Call', 'et_builder' ),
					'chat' => esc_html__( 'Chat', 'et_builder' ),
				),
				'depends_show_if' => 'skype',
				'depends_to'          => array(
					'social_network'
				),
				'description'     => esc_html__( 'Here you can choose which action to execute on button click', 'et_builder' ),
				'toggle_slug'     => 'main_content',
			),
		);

		// Automatically parse social_network's option as value_overwrite
		foreach ( $fields['social_network']['options'] as $value_overwrite_key => $value_overwrite ) {
			if ( is_array( $value_overwrite ) && isset( $value_overwrite['data'] ) && $value_overwrite['data']['color'] ) {
				$fields['social_network']['value_overwrite'][ $value_overwrite_key ] = $value_overwrite['data']['color'];
			}
		}

		return $fields;
	}

	function get_network_name( $network ) {
		$all_fields = $this->get_fields();
		$network_names_mapping = $all_fields['social_network']['options'];

		if ( isset( $network_names_mapping[ $network ] ) && isset( $network_names_mapping[ $network ]['value'] ) ) {
			return $network_names_mapping[ $network ]['value'];
		}

		return $network;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		global $et_pb_social_media_follow_link;

		$social_network        = $this->shortcode_atts['social_network'];
		$url                   = $this->shortcode_atts['url'];
		$skype_url             = $this->shortcode_atts['skype_url'];
		$skype_action          = $this->shortcode_atts['skype_action'];
		$custom_padding        = $this->shortcode_atts['custom_padding'];
		$custom_padding_tablet = $this->shortcode_atts['custom_padding_tablet'];
		$custom_padding_phone  = $this->shortcode_atts['custom_padding_phone'];
		$follow_button  = '';
		$is_skype       = false;

		if ( 'skype' === $social_network ) {
			$skype_url = sprintf(
				'skype:%1$s?%2$s',
				sanitize_text_field( $skype_url ),
				sanitize_text_field( $skype_action )
			);
			$is_skype = true;
		}

		if ( 'on' === $et_pb_social_media_follow_link['follow_button'] ) {
			$follow_button = sprintf(
				'<a href="%1$s" class="follow_button" title="%2$s"%3$s>%4$s</a>',
				! $is_skype ? esc_url( $url ) : $skype_url,
				esc_attr( $this->get_network_name( trim( wp_strip_all_tags( $content ) ) ) ),
				( 'on' === $et_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
				esc_html__( 'Follow', 'et_builder' )
			);
		}

		if ( '' !== $custom_padding || '' !== $custom_padding_tablet || '' !== $custom_padding_phone ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '.et_pb_social_media_follow li%%order_class%% a',
				'declaration' => 'width: auto; height: auto;',
			) );
		}

		$social_network            = ET_Builder_Element::add_module_order_class( $social_network, $function_name );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Get custom borders, if any
		$atts                      = $this->shortcode_atts;

		$output = sprintf(
			'<li class="et_pb_social_icon et_pb_social_network_link%1$s">
				<a href="%2$s" class="icon et_pb_with_border%7$s%8$s" title="%3$s"%5$s>%10$s%9$s<span class="et_pb_social_media_follow_network_name">%4$s</span></a>
				%6$s
			</li>',
			( '' !== $social_network ? sprintf( ' et-social-%s', esc_attr( $social_network ) ) : '' ),
			! $is_skype ? esc_url( $url ) : $skype_url,
			esc_attr( $this->get_network_name( trim( wp_strip_all_tags( $content ) ) ) ),
			sanitize_text_field( $this->get_network_name( $content ) ),
			( 'on' === $et_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
			$follow_button,
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background
		);

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$selector  = sprintf( '.%1$s a', self::get_module_order_class( $function_name ) );
		self::set_style( $function_name, array(
			'selector'    => $selector,
			'declaration' => $boxShadow->get_value( $this->shortcode_atts, array( 'important' => true ) )
		) );
	}
}

new ET_Builder_Module_Social_Media_Follow_Item;
