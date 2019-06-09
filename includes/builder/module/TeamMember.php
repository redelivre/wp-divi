<?php

class ET_Builder_Module_Team_Member extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Person', 'et_builder' );
		$this->plural     = esc_html__( 'Persons', 'et_builder' );
		$this->slug       = 'et_pb_team_member';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_team_member';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'image'        => esc_html__( 'Image', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon'  => esc_html__( 'Icon', 'et_builder' ),
					'image' => esc_html__( 'Image', 'et_builder' ),
					'text'  => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Image Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'header' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h4, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h2.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h4',
					),
				),
				'body'     => array(
					'label'          => esc_html__( 'Body', 'et_builder' ),
					'css'            => array(
						'main'  => "{$this->main_css_element}",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
					),
				),
				'position' => array(
					'label'          => esc_html__( 'Position', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_member_position",
					),
					'line_height'    => array(
						'default' => '1.7em',
					),
					'font_size'      => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'background'            => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'               => array(
				'default' => array(),
				'image' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_team_member_image",
							'border_styles' => "{$this->main_css_element} .et_pb_team_member_image",
						),
					),
					'label_prefix' => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'            => array(
				'default' => array(),
				'image'   => array(
					'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'css'          => array(
						'main'         => '%%order_class%% .et_pb_team_member_image',
						'custom_style' => true,
					),
					'default_on_fronts'  => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'max_width'             => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_team_member.et_pb_module',
				),
			),
			'text'                  => array(
				'use_background_layout' => true,
				'options' => array(
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
				'css' => array(
					'main' => implode(', ', array(
						'%%order_class%% .et_pb_module_header',
						'%%order_class%% .et_pb_member_position',
						'%%order_class%% .et_pb_team_member_description p',
					))
				)
			),
			'filters'               => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'                 => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_team_member_image',
				),
			),
			'button'                => false,
		);

		$this->custom_css_fields = array(
			'member_image' => array(
				'label'    => esc_html__( 'Member Image', 'et_builder' ),
				'selector' => '.et_pb_team_member_image',
			),
			'member_description' => array(
				'label'    => esc_html__( 'Member Description', 'et_builder' ),
				'selector' => '.et_pb_team_member_description',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => '.et_pb_team_member_description h4',
			),
			'member_position' => array(
				'label'    => esc_html__( 'Member Position', 'et_builder' ),
				'selector' => '.et_pb_member_position',
			),
			'member_social_links' => array(
				'label'    => esc_html__( 'Member Social Links', 'et_builder' ),
				'selector' => '.et_pb_member_social_links',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'rrKmaQ0n7Hw' ),
				'name' => esc_html__( 'An introduction to the Person module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'name' => array(
				'label'           => esc_html__( 'Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the person', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'position' => array(
				'label'           => esc_html__( 'Position', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( "Input the person's position.", 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'image_url' => array(
				'label'              => esc_html__( 'Image', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
			),
			'facebook_url' => array(
				'label'           => esc_html__( 'Facebook Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input Facebook Profile Url.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'url',
			),
			'twitter_url' => array(
				'label'           => esc_html__( 'Twitter Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input Twitter Profile Url', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'url',
			),
			'google_url' => array(
				'label'           => esc_html__( 'Google+ Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input Google+ Profile Url', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'url',
			),
			'linkedin_url' => array(
				'label'           => esc_html__( 'LinkedIn Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input LinkedIn Profile Url', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'url',
			),
			'content' => array(
				'label'           => esc_html__( 'Body', 'et_builder' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'icon_color' => array(
				'label'             => esc_html__( 'Icon Color', 'et_builder' ),
				'description'       => esc_html__( 'Here you can define a custom color for the icon.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'icon',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'use_icon_font_size' => array(
				'label'            => esc_html__( 'Use Icon Font Size', 'et_builder' ),
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
			'icon_font_size'     => array(
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

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['icon_color']     = array( 'color' => '%%order_class%% .et_pb_member_social_links a' );
		$fields['icon_font_size'] = array( 'font-size' => '%%order_class%% .et_pb_member_social_links a' );

		return $fields;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$name                            = $this->_esc_attr( 'name' );
		$position                        = $this->_esc_attr( 'position' );
		$image_url                       = $this->props['image_url'];
		$animation                       = $this->props['animation'];
		$facebook_url                    = $this->props['facebook_url'];
		$twitter_url                     = $this->props['twitter_url'];
		$google_url                      = $this->props['google_url'];
		$linkedin_url                    = $this->props['linkedin_url'];
		$header_level                    = $this->props['header_level'];
		$hover                           = et_pb_hover_options();
		$use_icon_font_size              = $this->props['use_icon_font_size'];
		$icon_color_values               = et_pb_responsive_options()->get_property_values( $this->props, 'icon_color' );
		$icon_font_size_values           = et_pb_responsive_options()->get_property_values( $this->props, 'icon_font_size' );
		$icon_font_size_hover            = $this->get_hover_value( 'icon_font_size' );

		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet        = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone         = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';


		$image = $social_links = '';

		// Icon Color.
		et_pb_responsive_options()->generate_responsive_css( $icon_color_values, '%%order_class%% .et_pb_member_social_links a', 'color', $render_slug, ' !important;', 'color' );

		if ( $hover->is_enabled( 'icon_color', $this->props ) && $hover->get_value( 'icon_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug,
				array(
					'selector'    => '%%order_class%% .et_pb_member_social_links a:hover',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $hover->get_value( 'icon_color', $this->props ) )
					),
				) );
		}

		if ( '' !== $facebook_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_facebook_icon"><span>%2$s</span></a></li>',
				esc_url( $facebook_url ),
				esc_html__( 'Facebook', 'et_builder' )
			);
		}

		if ( '' !== $twitter_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_twitter_icon"><span>%2$s</span></a></li>',
				esc_url( $twitter_url ),
				esc_html__( 'Twitter', 'et_builder' )
			);
		}

		if ( '' !== $google_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_google_icon"><span>%2$s</span></a></li>',
				esc_url( $google_url ),
				esc_html__( 'Google+', 'et_builder' )
			);
		}

		if ( '' !== $linkedin_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_linkedin_icon"><span>%2$s</span></a></li>',
				esc_url( $linkedin_url ),
				esc_html__( 'LinkedIn', 'et_builder' )
			);
		}

		if ( '' !== $social_links ) {
			$social_links = sprintf( '<ul class="et_pb_member_social_links">%1$s</ul>', $social_links );
		}

		// Icon Size.
		$icon_selector = '%%order_class%% .et_pb_member_social_links .et_pb_font_icon';
		if ( 'off' !== $use_icon_font_size ) {
			et_pb_responsive_options()->generate_responsive_css( $icon_font_size_values, $icon_selector, 'font-size', $render_slug );

			// Icon hover styles.
			if ( et_builder_is_hover_enabled( 'icon_font_size', $this->props ) ) {
				ET_Builder_Element::set_style( $render_slug, array(
					'selector'    => $this->add_hover_to_selectors( $icon_selector ),
					'declaration' => sprintf(
						'font-size: %1$s;',
						esc_html( $icon_font_size_hover )
					),
				) );
			}
		}

		// Added for backward compatibility
		if ( empty( $animation ) ) {
			$animation = 'top';
		}

		if ( '' !== $image_url ) {
			// Images: Add CSS Filters and Mix Blend Mode rules (if set)
			$generate_css_filters_image = '';
			if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
				$generate_css_filters_image = $this->generate_css_filters(
					$render_slug,
					'child_',
					self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
				);
			}

			$image_pathinfo = pathinfo( $image_url );
			$is_image_svg   = isset( $image_pathinfo['extension'] ) ? 'svg' === $image_pathinfo['extension'] : false;

			$image = sprintf(
				'<div class="et_pb_team_member_image et-waypoint%3$s%4$s%5$s">
					<img src="%1$s" alt="%2$s" />
				</div>',
				esc_attr( $image_url ),
				esc_attr( $name ),
				esc_attr( " et_pb_animation_{$animation}" ),
				$generate_css_filters_image,
				$is_image_svg ? esc_attr( " et-svg" ) : ''
			);
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Module classnames
		$this->add_classname( array(
			"et_pb_bg_layout_{$background_layout}",
			'clearfix',
			$this->get_text_orientation_classname()
		) );

		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}

		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		if ( '' === $image ) {
			$this->add_classname( 'et_pb_team_member_no_image' );
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

		$output = sprintf(
			'<div%3$s class="%4$s"%10$s%11$s>
				%9$s
				%8$s
				%2$s
				<div class="et_pb_team_member_description">
					%5$s
					%6$s
					%1$s
					%7$s
				</div> <!-- .et_pb_team_member_description -->
			</div> <!-- .et_pb_team_member -->',
			$this->content,
			( '' !== $image ? $image : '' ),
			$this->module_id(),
			$this->module_classname( $render_slug ),
			( '' !== $name ? sprintf( '<%1$s class="et_pb_module_header">%2$s</%1$s>', et_pb_process_header_level( $header_level, 'h4' ), et_core_esc_previously( $name ) ) : '' ), // #5
			( '' !== $position ? sprintf( '<p class="et_pb_member_position">%1$s</p>', et_core_esc_previously( $position ) ) : '' ),
			$social_links,
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ), // #10
			et_core_esc_previously( $data_background_layout_hover )
		);

		return $output;
	}
}

new ET_Builder_Module_Team_Member;
