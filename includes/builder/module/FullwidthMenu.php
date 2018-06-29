<?php

class ET_Builder_Module_Fullwidth_Menu extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Menu', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_menu';
		$this->vb_support = 'on';
		$this->fullwidth  = true;

		$this->main_css_element = '%%order_class%%.et_pb_fullwidth_menu';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'background'   => esc_html__( 'Background', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'   => esc_html__( 'Layout', 'et_builder' ),
					'links'    => esc_html__( 'Links', 'et_builder' ),
					'dropdown' => esc_html__( 'Dropdown Menu', 'et_builder' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'      => array(
				'menu' => array(
					'label'    => esc_html__( 'Menu', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} ul li a",
						'plugin_main' => "{$this->main_css_element} ul li a, {$this->main_css_element} ul li",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
						'range_settings' => array(
							'min'  => '12',
							'max'  => '24',
							'step' => '1',
						),
					),
					'letter_spacing' => array(
						'default' => '0px',
						'range_settings' => array(
							'min'  => '0',
							'max'  => '8',
							'step' => '1',
						),
					),
					'hide_text_align' => true,
				),
			),
			'background' => array(
				'options' => array(
					'background_color'  => array(
						'default'          => '#ffffff',
					),
				),
			),
			'box_shadow' => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%%, %%order_class%% .sub-menu',
						'custom_style' => true,
					),
				),
			),
			'text'       => array(
				'use_background_layout' => true,
				'toggle_slug' => 'links',
				'options' => array(
					'text_orientation'  => array(
						'default_on_front' => 'left',
					),
					'background_layout' => array(
						'default_on_front' => 'light',
					),
				),
			),
			'button'     => false,
		);

		$this->custom_css_fields = array(
			'menu_link' => array(
				'label'    => esc_html__( 'Menu Link', 'et_builder' ),
				'selector' => '.fullwidth-menu-nav li a',
			),
			'active_menu_link' => array(
				'label'    => esc_html__( 'Active Menu Link', 'et_builder' ),
				'selector' => '.fullwidth-menu-nav li.current-menu-item a',
			),
			'dropdown_container' => array(
				'label'    => esc_html__( 'Dropdown Menu Container', 'et_builder' ),
				'selector' => '.fullwidth-menu-nav li ul.sub-menu',
			),
			'dropdown_links' => array(
				'label'    => esc_html__( 'Dropdown Menu Links', 'et_builder' ),
				'selector' => '.fullwidth-menu-nav li ul.sub-menu a',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'Q2heZC2GbNg' ),
				'name' => esc_html__( 'An introduction to the Fullwidth Menu module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'menu_id' => array(
				'label'           => esc_html__( 'Menu', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => et_builder_get_nav_menus_options(),
				'description'     => sprintf(
					'<p class="description">%2$s. <a href="%1$s" target="_blank">%3$s</a>.</p>',
					esc_url( admin_url( 'nav-menus.php' ) ),
					esc_html__( 'Select a menu that should be used in the module', 'et_builder' ),
					esc_html__( 'Click here to create new menu', 'et_builder' )
				),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__menu',
				),
			),
			'submenu_direction' => array(
				'label'           => esc_html__( 'Sub-Menus Open', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'downwards' => esc_html__( 'Downwards', 'et_builder' ),
					'upwards'   => esc_html__( 'Upwards', 'et_builder' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Here you can choose the direction that your sub-menus will open. You can choose to have them open downwards or upwards.', 'et_builder' ),
				'computed_affects' => array(
					'__menu',
				),
			),
			'fullwidth_menu' => array(
				'label'           => esc_html__( 'Make Menu Links Fullwidth', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'active_link_color' => array(
				'label'        => esc_html__( 'Active Link Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'links',
			),
			'dropdown_menu_bg_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'dropdown',
			),
			'dropdown_menu_line_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Line Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'dropdown',
			),
			'dropdown_menu_text_color' => array(
				'label'        => esc_html__( 'Dropdown Menu Text Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'links',
			),
			'mobile_menu_bg_color' => array(
				'label'        => esc_html__( 'Mobile Menu Background Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'dropdown',
			),
			'mobile_menu_text_color' => array(
				'label'        => esc_html__( 'Mobile Menu Text Color', 'et_builder' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'links',
			),
			'__menu' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Fullwidth_Menu', 'get_fullwidth_menu' ),
				'computed_depends_on' => array(
					'menu_id',
					'submenu_direction',
				),
			),
		);

		return $fields;
	}

	/**
	 * Add the class with page ID to menu item so it can be easily found by ID in Frontend Builder
	 *
	 * @return menu item object
	 */
	static function modify_fullwidth_menu_item( $menu_item ) {
		if ( esc_url( home_url( '/' ) ) === $menu_item->url ) {
			$fw_menu_custom_class = 'et_pb_menu_page_id-home';
		} else {
			$fw_menu_custom_class = 'et_pb_menu_page_id-' . $menu_item->object_id;
		}

		$menu_item->classes[] = $fw_menu_custom_class;
		return $menu_item;
	}

	/**
	 * Get fullwidth menu markup for fullwidth menu module
	 *
	 * @return string of fullwidth menu markup
	 */
	static function get_fullwidth_menu( $args = array() ) {
		$defaults = array(
			'submenu_direction' => '',
			'menu_id'           => '',
		);

		// modify the menu item to include the required data
		add_filter( 'wp_setup_nav_menu_item', array( 'ET_Builder_Module_Fullwidth_Menu', 'modify_fullwidth_menu_item' ) );

		$args = wp_parse_args( $args, $defaults );

		$menu = '<nav class="fullwidth-menu-nav">';

		$menuClass = 'fullwidth-menu nav';

		if ( ! et_is_builder_plugin_active() && 'on' == et_get_option( 'divi_disable_toptier' ) ) {
			$menuClass .= ' et_disable_top_tier';
		}
		$menuClass .= ( '' !== $args['submenu_direction'] ? sprintf( ' %s', esc_attr( $args['submenu_direction'] ) ) : '' );

		$primaryNav = '';

		$menu_args = array(
			'theme_location' => 'primary-menu',
			'container'      => '',
			'fallback_cb'    => '',
			'menu_class'     => $menuClass,
			'menu_id'        => '',
			'echo'           => false,
		);

		if ( '' !== $args['menu_id'] ) {
			$menu_args['menu'] = (int) $args['menu_id'];
		}

		$primaryNav = wp_nav_menu( apply_filters( 'et_fullwidth_menu_args', $menu_args ) );

		if ( '' == $primaryNav ) {
			$menu .= sprintf(
				'<ul class="%1$s">
					%2$s',
				esc_attr( $menuClass ),
				( ! et_is_builder_plugin_active() && 'on' === et_get_option( 'divi_home_link' )
					? sprintf( '<li%1$s><a href="%2$s">%3$s</a></li>',
						( is_home() ? ' class="current_page_item"' : '' ),
						esc_url( home_url( '/' ) ),
						esc_html__( 'Home', 'et_builder' )
					)
					: ''
				)
			);

			ob_start();

			// @todo: check if Fullwidth Menu module works fine with no menu selected in settings
			if ( et_is_builder_plugin_active() ) {
				wp_page_menu();
			} else {
				show_page_menu( $menuClass, false, false );
				show_categories_menu( $menuClass, false );
			}

			$menu .= ob_get_contents();

			$menu .= '</ul>';

			ob_end_clean();
		} else {
			$menu .= $primaryNav;
		}

		$menu .= '</nav>';

		remove_filter( 'wp_setup_nav_menu_item', array( 'ET_Builder_Module_Fullwidth_Menu', 'modify_fullwidth_menu_item' ) );

		return $menu;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$background_color  = $this->props['background_color'];
		$background_layout = $this->props['background_layout'];
		$menu_id           = $this->props['menu_id'];
		$submenu_direction = $this->props['submenu_direction'];
		$active_link_color        = $this->props['active_link_color'];
		$dropdown_menu_bg_color   = $this->props['dropdown_menu_bg_color'];
		$dropdown_menu_line_color = $this->props['dropdown_menu_line_color'];
		$dropdown_menu_text_color = $this->props['dropdown_menu_text_color'];
		$dropdown_menu_animation  = $this->props['dropdown_menu_animation'];
		$mobile_menu_bg_color     = $this->props['mobile_menu_bg_color'];
		$mobile_menu_text_color   = $this->props['mobile_menu_text_color'];

		$style = '';

		if ( '' !== $background_color ) {
			$style .= sprintf( ' style="background-color: %s;"',
				esc_attr( $background_color )
			);
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$menu = self::get_fullwidth_menu( array(
			'menu_id'           => $menu_id,
			'submenu_direction' => $submenu_direction,
		) );

		if ( '' !== $active_link_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu ul li.current-menu-item a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $active_link_color )
				),
			) );
		}

		if ( '' !== $background_color || '' !== $dropdown_menu_bg_color ) {
			$et_menu_bg_color = '' !== $dropdown_menu_bg_color ? $dropdown_menu_bg_color : $background_color;

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .nav li ul',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $et_menu_bg_color )
				),
			) );
		}

		if ( '' !== $dropdown_menu_line_color ) {

			$dropdown_menu_line_color_selector = 'upwards' === $submenu_direction ? '%%order_class%%.et_pb_fullwidth_menu .fullwidth-menu-nav > ul.upwards li ul' : '%%order_class%%.et_pb_fullwidth_menu .nav li ul';

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => $dropdown_menu_line_color_selector,
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $dropdown_menu_line_color )
				),
			) );

			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .et_mobile_menu',
				'declaration' => sprintf(
					'border-color: %1$s;',
					esc_html( $dropdown_menu_line_color )
				),
			) );
		}

		if ( '' !== $dropdown_menu_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .nav li ul a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $dropdown_menu_text_color )
				),
			) );
		}

		if ( '' !== $mobile_menu_bg_color || '' !== $background_color ) {
			$et_menu_bg_color_mobile = '' !== $mobile_menu_bg_color ? $mobile_menu_bg_color : $background_color;
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .et_mobile_menu, %%order_class%%.et_pb_fullwidth_menu .et_mobile_menu ul',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $et_menu_bg_color_mobile )
				),
			) );
		}

		if ( '' !== $mobile_menu_text_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%.et_pb_fullwidth_menu .et_mobile_menu a',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $mobile_menu_text_color )
				),
			) );
		}

		// Module classnames
		$this->add_classname( array(
			"et_pb_bg_layout_{$background_layout}",
			$this->get_text_orientation_classname(),
			"et_dropdown_animation_{$dropdown_menu_animation}",
		) );

		if ( $this->props['fullwidth_menu'] === 'on' ) {
			$this->add_classname( 'et_pb_fullwidth_menu_fullwidth' );
		}

		$output = sprintf(
			'<div%4$s class="%3$s"%2$s>
				%6$s
				%5$s
				<div class="et_pb_row clearfix">
					%1$s
					<div class="et_mobile_nav_menu">
						<a href="#" class="mobile_nav closed%7$s">
							<span class="mobile_menu_bar"></span>
						</a>
					</div>
				</div>
			</div>',
			$menu,
			$style,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background,
			'upwards' === $submenu_direction ? ' et_pb_mobile_menu_upwards' : ''
		);

		return $output;
	}
}

new ET_Builder_Module_Fullwidth_Menu;
