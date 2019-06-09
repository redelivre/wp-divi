<?php

class ET_Builder_Module_Social_Media_Follow_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Social Network', 'et_builder' );
		$this->plural                      = esc_html__( 'Social Networks', 'et_builder' );
		$this->slug                        = 'et_pb_social_media_follow_network';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'content';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Network', 'et_builder' ),
					'link'         => esc_html__( 'Link', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon' => esc_html__( 'Icon', 'et_builder' ),
				),
			),
		);

		$this->advanced_setting_title_text = esc_html__( 'New Social Network', 'et_builder' );
		$this->settings_text               = esc_html__( 'Social Network Settings', 'et_builder' );

		$this->custom_css_fields = array(
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

		$this->advanced_fields = array(
			'background'            => array(
				'css' => array(
					'main'      => '%%order_class%% a.icon',
					'important' => 'all',
				),
			),
			'borders'               => array(
				'default' => array(
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
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'main'      => '%%order_class%% a',
						'important' => true,
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'padding' => '.et_pb_social_media_follow li%%order_class%% a',
					'main'    => '%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'fonts'                 => false,
			'text'                  => false,
			'max_width'             => false,
			'height'                => false,
			'button'                => array(
				'button' => array(
					'label'               => esc_html__( 'Follow Button', 'et_builder' ),
					'css'                 => array(
						'main' => ".et_pb_social_media_follow li%%order_class%% .follow_button",
					),
					'hide_icon'           => true,
					'hide_custom_padding' => true,
					'no_rel_attr'         => true,
					'text_size'           => array(
						'default' => '14px',
					),
					'border_width'        => array(
						'default' => '0px',
					),
					'box_shadow'          => array(
						'css' => array(
							'main' => ".et_pb_social_media_follow li%%order_class%% .follow_button",
						),
					),
				),
			),
			'link_options'          => false,
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
			'content' => array(
				'label'       => esc_html__( 'Body', 'et_builder' ),
				'type'        => 'hidden',
				'toggle_slug' => 'main_content',
			),
			'url' => array(
				'label'               => esc_html__( 'Account Link URL', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'basic_option',
				'description'         => esc_html__( 'The URL for this social network link.', 'et_builder' ),
				'depends_show_if_not' => 'skype',
				'depends_on'          => array(
					'social_network'
				),
				'toggle_slug'         => 'link',
				'default_on_front'    => '#',
				'dynamic_content'     => 'url',
			),
			'skype_url' => array(
				'label'           => esc_html__( 'Account Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The Skype account name.', 'et_builder' ),
				'depends_show_if' => 'skype',
				'depends_on'          => array(
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
				'depends_on'          => array(
					'social_network'
				),
				'description'     => esc_html__( 'Here you can choose which action to execute on button click', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'default_on_front' => 'call',
			),
			'icon_color'            => array(
				'label'          => esc_html__( 'Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the social network icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
			),
			'use_icon_font_size'    => array(
				'label'            => esc_html__( 'Use Custom Icon Size', 'et_builder' ),
				'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'icon_font_size',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'option_category'  => 'font_option',
			),
			'icon_font_size'        => array(
				'label'            => esc_html__( 'Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '16px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'depends_show_if'  => 'on',
				'responsive'       => true,
				'hover'            => 'tabs',
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

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['icon_color']     = array( 'color' => '.et_pb_social_media_follow %%order_class%% .icon:before' );
		$fields['icon_font_size'] = array(
			'font-size'   => '.et_pb_social_media_follow %%order_class%% .icon:before',
			'line-height' => '.et_pb_social_media_follow %%order_class%% .icon:before',
			'height'      => '.et_pb_social_media_follow %%order_class%% .icon:before',
			'width'       => '.et_pb_social_media_follow %%order_class%% .icon:before',
			'height'      => '.et_pb_social_media_follow %%order_class%% .icon',
			'width'       => '.et_pb_social_media_follow %%order_class%% .icon',
		);

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

	function render( $attrs, $content = null, $render_slug ) {
		global $et_pb_social_media_follow_link;

		$social_network        = $this->props['social_network'];
		$url                   = $this->props['url'];
		$skype_url             = $this->props['skype_url'];
		$skype_action          = $this->props['skype_action'];
		$custom_padding        = $this->props['custom_padding'];
		$custom_padding_tablet = $this->props['custom_padding_tablet'];
		$custom_padding_phone  = $this->props['custom_padding_phone'];
		$follow_button         = '';
		$is_skype              = false;
		$network_name          = esc_attr( $this->get_network_name( trim( wp_strip_all_tags( $content ) ) ) );
		$use_icon_font_size    = $this->props['use_icon_font_size'];
		$icon_color_hover      = $this->get_hover_value('icon_color');
		$icon_color_values     = et_pb_responsive_options()->get_property_values( $this->props, 'icon_color' );
		$icon_font_size_hover  = $this->get_hover_value( 'icon_font_size' );
		$icon_font_size_values = et_pb_responsive_options()->get_property_values( $this->props, 'icon_font_size' );

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
				$network_name,
				( 'on' === $et_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '' ),
				esc_html__( 'Follow', 'et_builder' )
			);
		}

		if ( '' !== $custom_padding || '' !== $custom_padding_tablet || '' !== $custom_padding_phone ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '.et_pb_social_media_follow li%%order_class%% a',
				'declaration' => 'width: auto; height: auto;',
			) );
		}

		// Icon Color.
		et_pb_responsive_options()->generate_responsive_css( $icon_color_values, '.et_pb_social_media_follow %%order_class%% .icon:before', 'color', $render_slug, '', 'color' );

		if ( et_builder_is_hover_enabled( 'icon_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '.et_pb_social_media_follow %%order_class%%:hover .icon:before',
				'declaration' => sprintf(
					'color: %1$s;',
					esc_html( $icon_color_hover )
				),
			) );
		}

		// Icon Size.
		if ( 'off' !== $use_icon_font_size ) {
			// Proccess for each devices.
			foreach ( $icon_font_size_values as $font_size_key => $font_size_value ) {
				if ( '' === $font_size_value ) {
					continue;
				}

				$media_query = 'general';
				if ( 'tablet' === $font_size_key ) {
					$media_query = ET_Builder_Element::get_media_query( 'max_width_980' );
				} elseif ( 'phone' === $font_size_key ) {
					$media_query = ET_Builder_Element::get_media_query( 'max_width_767' );
				}

				$font_size_value_int    = (int) $font_size_value;
				$font_size_value_unit   = str_replace( $font_size_value_int, '', $font_size_value );
				$font_size_value_double = 0 < $font_size_value_int ? $font_size_value_int * 2 : 0;
				$font_size_value_double = (string) $font_size_value_double . $font_size_value_unit;

				// Icon.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '.et_pb_social_media_follow %%order_class%% .icon:before',
					'declaration' => sprintf(
						'font-size:%1$s; line-height:%2$s; height:%2$s; width:%2$s;',
						esc_html( $font_size_value ),
						esc_html( $font_size_value_double )
					),
					'media_query' => $media_query,
				) );

				// Icon Wrapper.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '.et_pb_social_media_follow %%order_class%% .icon',
					'declaration' => sprintf(
						'height:%1$s; width:%1$s;',
						esc_html( $font_size_value_double )
					),
					'media_query' => $media_query,
				) );
			}

			// Icon hover styles.
			if ( et_builder_is_hover_enabled( 'icon_font_size', $this->props ) && '' !== $icon_font_size_hover ) {
				$icon_font_size_hover_int    = (int) $icon_font_size_hover;
				$icon_font_size_hover_unit   = str_replace( $icon_font_size_hover_int, '', $icon_font_size_hover );
				$icon_font_size_hover_double = 0 < $icon_font_size_hover_int ? $icon_font_size_hover_int * 2 : 0;
				$icon_font_size_hover_double = (string) $icon_font_size_hover_double . $icon_font_size_hover_unit;

				// Icon.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '.et_pb_social_media_follow %%order_class%% .icon:hover:before',
					'declaration' => sprintf(
						'font-size:%1$s; line-height:%2$s; height:%2$s; width:%2$s;',
						esc_html( $icon_font_size_hover ),
						esc_html( $icon_font_size_hover_double )
					),
				) );

				// Icon Wrapper.
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => '.et_pb_social_media_follow %%order_class%% .icon:hover',
					'declaration' => sprintf(
						'height:%1$s; width:%1$s;',
						esc_html( $icon_font_size_hover_double )
					),
				) );
			}
		}

		$social_network             = ET_Builder_Element::add_module_order_class( $social_network, $render_slug );
		$video_background           = $this->video_background();
		$parallax_image_background  = $this->get_parallax_image_background();
		$social_network_link_url    = ! $is_skype ? esc_url( $url ) : $skype_url;
		$social_network_link_target = 'on' === $et_pb_social_media_follow_link['url_new_window'] ? ' target="_blank"' : '';

		// Get custom borders, if any
		$attrs = $this->props;

		// Module classnames
		$this->add_classname( array(
			'et_pb_social_icon',
			'et_pb_social_network_link',
		) );

		if ( '' !== $social_network ) {
			$this->add_classname( sprintf( ' et-social-%s', esc_attr( $social_network ) ) );
		}

		// Remove automatically added classnames
		$this->remove_classname( array(
			$render_slug,
			'et_pb_module',
			'et_pb_section_video',
			'et_pb_preload',
			'et_pb_section_parallax',
		) );

		// Format i18n link title
		$social_network_link_title = sprintf(
			esc_html__( 'Follow on %s', 'et_builder' ),
			$network_name
		);

		// Format i18n link text (visible, but ignored by screen readers)
		$social_network_link_text = esc_html__( 'Follow', 'et_builder' );

		// Prepare CSS classes for the link
		$social_network_link_classes = array( 'icon', 'et_pb_with_border' );
		if ( '' !== $video_background ) {
			array_push( $social_network_link_classes,
				'et_pb_section_video',
				'et_pb_preload',
				$video_background
			);
		}
		if ( '' !== $parallax_image_background ) {
			array_push( $social_network_link_classes,
				'et_pb_section_parallax'
			);
		}
		$social_network_link_classes = implode( ' ', $social_network_link_classes );

		$output = "<li
            class='{$this->module_classname( $render_slug )}'><a
              href='{$social_network_link_url}'
              class='{$social_network_link_classes}'
              title='{$social_network_link_title}'
              {$social_network_link_target}>{$parallax_image_background}<span
                class='et_pb_social_media_follow_network_name'
                aria-hidden='true'
                >{$social_network_link_text}</span></a>{$follow_button}</li>";

		return $output;
	}
}

new ET_Builder_Module_Social_Media_Follow_Item;
