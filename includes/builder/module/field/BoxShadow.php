<?php

class ET_Builder_Module_Field_BoxShadow extends ET_Builder_Module_Field_Base {
	private static $classes = array();

	public function get_fields( array $args = array() ) {
		$arguments = shortcode_atts( array(
			'suffix'              => '',
			'label'               => esc_html__( 'Box Shadow', 'et_builder' ),
			'option_category'     => '',
			'tab_slug'            => '',
			'toggle_slug'         => '',
			'sub_toggle_slug'     => null,
			'depends_show_if_not' => null,
			'depends_show_if'     => null,
			'depends_on'          => null,
			'default_on_fronts'   => array(),
			'show_if'             => null,
			'show_if_not'         => null,
		), $args );

		$prefix     = 'box_shadow_';
		$style      = $prefix . 'style' . $arguments['suffix'];
		$horizontal = $prefix . 'horizontal' . $arguments['suffix'];
		$vertical   = $prefix . 'vertical' . $arguments['suffix'];
		$blur       = $prefix . 'blur' . $arguments['suffix'];
		$spread     = $prefix . 'spread' . $arguments['suffix'];
		$position   = $prefix . 'position' . $arguments['suffix'];
		$color      = $prefix . 'color' . $arguments['suffix'];

		$options = array();
		$option  = array(
			'option_category'     => $arguments['option_category'],
			'tab_slug'            => $arguments['tab_slug'],
			'toggle_slug'         => $arguments['toggle_slug'],
			'show_if_not'         => array(
				"{$style}" => 'none',
			),
			'default_on_child'    => true,
		);
		$range   = array_merge(
			$option,
			array(
				'type'           => 'range',
				'range_settings' => array(
					'min'  => - 80,
					'max'  => 80,
					'step' => 1,
				),
				'default'         => 0,
				'validate_unit'   => true,
				'fixed_unit'      => 'px',
				'fixed_range'     => true,
				'hover'           => 'tabs',
			)
		);

		if ( $arguments['sub_toggle_slug'] ) {
			$option['sub_toggle'] = $arguments['sub_toggle_slug'];
		}

		$presets = array();

		foreach ( $this->get_presets() as $id => $preset ) {
			if ( 'none' === $id ) {
				$presets[] = array(
					'value'  => $id,
					'icon'   => $id,
					'fields' => $this->fetch_preset( $preset, $arguments['suffix'] ),
				);
			} else {
				$presets[] = array(
					'value'   => $id,
					'content' => sprintf( '<span class="preset %1$s"></span>', esc_attr( $id ) ),
					'fields'  => $this->fetch_preset( $preset, $arguments['suffix'] ),
				);
			}
		}

		$options[ $style ] = array_merge(
			$option,
			array(
				'label'               => $arguments['label'],
				'type'                => 'select_box_shadow',
				'default'             => 'none',
				'className'           => 'box_shadow',
				'presets'             => $presets,
				'affects'             => array( $horizontal, $vertical, $blur, $spread, $color, $position, ),
				'copy_with'           => array( $horizontal, $vertical, $blur, $spread, $color, $position, ),
				'depends_show_if'     => $arguments['depends_show_if'],
				'depends_show_if_not' => $arguments['depends_show_if_not'],
				'depends_on'          => $arguments['depends_on'],
				'show_if'             => $arguments['show_if'],
				'show_if_not'         => $arguments['show_if_not'],
			)
		);

		// Configure dependency for fields via show_if/show_if_not attribute
		if ( $options[ $style ]['show_if'] === null ) {
			unset( $options[ $style ]['show_if'] );
		}
		if ( $options[ $style ]['show_if_not'] === null ) {
			unset( $options[ $style ]['show_if_not'] );
		}

		// Field dependency via depends_on, depends_show_if, and depends_show_if_not have been deprecated
		// These remain here as backward compatibility for third party modules
		if ( $options[ $style ]['depends_on'] === null ) {
			unset( $options[ $style ]['depends_on'] );
		}
		if ( $options[ $style ]['depends_show_if'] === null ) {
			unset( $options[ $style ]['depends_show_if'] );
		}
		if ( $options[ $style ]['depends_show_if_not'] === null ) {
			unset( $options[ $style ]['depends_show_if_not'] );
		}
		if ( isset( $arguments['default_on_fronts']['style'] ) && false !== $arguments['default_on_fronts']['style'] ) {
			$options[ $style ]['default_on_front'] = $arguments['default_on_fronts']['style'];
		}

		$options[ $horizontal ] = array_merge(
			$range,
			array( 'label' => esc_html__( 'Box Shadow Horizontal Position', 'et_builder' ), )
		);
		$options[ $vertical ]   = array_merge(
			$range,
			array( 'label' => esc_html__( 'Box Shadow Vertical Position', 'et_builder' ), )
		);
		$options[ $blur ]       = array_merge(
			$range,
			array(
				'label'          => esc_html__( 'Box Shadow Blur Strength', 'et_builder' ),
				'range_settings' => array(
					'min'  => 0,
					'max'  => 80,
					'step' => 1,
				),
			)
		);
		$options[ $spread ]     = array_merge(
			$range,
			array( 'label' => esc_html__( 'Box Shadow Spread Strength', 'et_builder' ), )
		);
		$options[ $color ]      = array_merge(
			$option,
			array(
				'label'          => esc_html__( 'Shadow Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'hover'          => 'tabs',
				'default'        => 'rgba(0,0,0,0.3)',
				'field_template' => 'color',
			)
		);

		if ( isset( $arguments['default_on_fronts']['color'] ) && false !== $arguments['default_on_fronts']['color'] ) {
			$options[ $color ]['default_on_front'] = $arguments['default_on_fronts']['color'];
		}

		$options[ $position ]   = array_merge(
			$option,
			array(
				'label'   => esc_html__( 'Box Shadow Position', 'et_builder' ),
				'type'    => 'select',
				'default' => 'outer',
				'options' => array(
					'outer' => esc_html__( 'Outer Shadow', 'et_builder' ),
					'inner' => esc_html__( 'Inner Shadow', 'et_builder' ),
				),
			)
		);

		if ( isset( $arguments['default_on_fronts']['position'] ) && false !== $arguments['default_on_fronts']['position'] ) {
			$options[ $position ]['default_on_front'] = $arguments['default_on_fronts']['position'];
		}

		$list = array(
			'vertical'   => $vertical,
			'horizontal' => $horizontal,
			'blur'       => $blur,
			'spread'     => $spread,
			'position'   => $position,
		);
		foreach ( $list as $id => $field ) {
			$values = array();
			foreach ( array_keys( $this->get_presets() ) as $preset ) {
				$values[ $preset ] = $this->get_preset_field( $preset, $id );
			}
			$options[ $field ]['default'] = array( $style, $values );
		}

		return $options;
	}

	public function get_value( $atts, array $args = array() ) {
		$args      = shortcode_atts( array( 'suffix' => '', 'important' => false, 'hover' => false), $args );
		$suffix    = $args['suffix'];
		$important = $args['important'] ? '!important' : '';
		$hover     = $args['hover'];
		$style     = $this->get_key_value( "style$suffix", $atts );

		if ( empty($style) || 'none' === $style ) {
			return '';
		}


		$preset = $this->get_preset( $style );

		$atts   = array_merge( array(
			"box_shadow_position{$suffix}"   => $preset['position'],
			"box_shadow_horizontal{$suffix}" => $preset['horizontal'],
			"box_shadow_vertical{$suffix}"   => $preset['vertical'],
			"box_shadow_blur{$suffix}"       => $preset['blur'],
			"box_shadow_spread{$suffix}"     => $preset['spread'],
			"box_shadow_color{$suffix}"      => 'rgba(0,0,0,0.3)',
		), array_filter($atts, 'strlen') );

		$position   = $this->get_key_value( "position{$suffix}", $atts ) == 'inner' ? 'inset' : '';
		$horizontal = rtrim( $this->get_key_value( "horizontal{$suffix}", $atts, $hover ), 'px' ) . 'px';
		$vertical   = rtrim( $this->get_key_value( "vertical{$suffix}", $atts, $hover ), 'px' ) . 'px';
		$blur       = rtrim( $this->get_key_value( "blur{$suffix}", $atts, $hover ), 'px' ) . 'px';
		$strength   = rtrim( $this->get_key_value( "spread{$suffix}", $atts, $hover ), 'px' ) . 'px';
		$color      = $this->get_key_value("color{$suffix}", $atts, $hover );
		$value      = sprintf(
			'box-shadow: %1$s %2$s %3$s %4$s %5$s %6$s %7$s;',
			$position,
			$horizontal,
			$vertical,
			$blur,
			$strength,
			$color,
			$important
		);

		// Do not provider hover style if it is the same as normal style
		if ( $hover ) {
			$new_args = $args;
			$new_args['hover'] = false;
			$normal = $this->get_value( $atts, $new_args );

			if ( $normal === $value ) {
				return '';
			}
		}

		return $value;
	}

	public function get_presets() {
		return array(
			'none'    => array(
				"horizontal" => '',
				"vertical"   => '',
				"blur"       => '',
				"spread"     => '',
				"position"   => 'outer',
			),
			'preset1' => array(
				"horizontal" => '0px',
				"vertical"   => '2px',
				"blur"       => '18px',
				"spread"     => '0px',
				"position"   => 'outer',
			),
			'preset2' => array(
				"horizontal" => '6px',
				"vertical"   => '6px',
				"blur"       => '18px',
				"spread"     => '0px',
				"position"   => 'outer',
			),
			'preset3' => array(
				"horizontal" => '0px',
				"vertical"   => '12px',
				"blur"       => '18px',
				"spread"     => '-6px',
				"position"   => 'outer',
			),
			'preset4' => array(
				"horizontal" => '10px',
				"vertical"   => '10px',
				"blur"       => '0px',
				"spread"     => '0px',
				"position"   => 'outer',
			),
			'preset5' => array(
				"horizontal" => '0px',
				"vertical"   => '6px',
				"blur"       => '0px',
				"spread"     => '10px',
				"position"   => 'outer',
			),
			'preset6' => array(
				"horizontal" => '0px',
				"vertical"   => '0px',
				"blur"       => '18px',
				"spread"     => '0px',
				"position"   => 'inner',
			),
			'preset7' => array(
				"horizontal" => '10px',
				"vertical"   => '10px',
				"blur"       => '0px',
				"spread"     => '0px',
				"position"   => 'inner',
			),
		);
	}

	public function get_preset( $name ) {
		$presets = $this->get_presets();

		return isset( $presets[ $name ] )
			? $presets[ $name ]
			: array(
				"horizontal" => 0,
				"vertical"   => 0,
				"blur"       => 0,
				"spread"     => 0,
				"position"   => 'outer',
			);
	}

	public function get_style( $selector, array $atts = array(), array $args = array() ) {
		$value = $this->get_value( $atts, $args );

		return array(
			'selector'    => $selector,
			'declaration' => empty( $value ) ? null : $value,
		);
	}

	public function has_overlay( $atts, $args ) {
		$overlay = ET_Core_Data_Utils::instance()->array_get( $args, 'overlay', false );
		$inset   = $this->is_inset( $this->get_value( $atts, $args ) );

		return ( $inset && 'inset' === $overlay ) || 'always' === 'overlay';
	}

	public function get_overlay_selector( $selector ) {
		$selectors = array_map( 'trim', explode( ',', $selector ) );
		$new_selector   = '';

		foreach ( $selectors as $selector ) {
			$new_selector .= $selector . '>.box-shadow-overlay, ' . $selector . '.et-box-shadow-no-overlay';
		}

		return $new_selector;
	}

	public function get_overlay_style( $function_name,  $selector, $atts, array $args = array() ) {
		$order_class_name = ET_Builder_Element::get_module_order_class( $function_name );

		$reg_selector    = str_replace( '%%order_class%%', ".{$order_class_name}", $selector );
		$reg_selector    = str_replace( '%order_class%', ".{$order_class_name}", $reg_selector );

		// %%parent_class%% only works if child module's slug is `parent_slug` + _item suffix. If child module slug
		// use different slug structure, %%parent_class%% should not be used
		if ( false !== strpos( $reg_selector, '%%parent_class%%' ) ) {
			$parent_class = str_replace( '_item', '', $function_name );
			$reg_selector     = str_replace( '%%parent_class%%', ".{$parent_class}", $reg_selector );
		}

		$selector = $this->get_overlay_selector( $selector );
		$value    = $this->get_value( $atts, $args );

		if ( empty( $value ) ) {
			return array(
				'selector'    => $selector,
				'declaration' => null,
			);
		}

		array_map(
			array( get_class( $this ), 'register_element' ),
			array_map( 'trim', explode( ',', $reg_selector ) )
		);

		return array(
			'selector'    => $selector,
			'declaration' => $value,
		);
	}

	public function is_inset( $style ) {
		return strpos( $style, 'inset' ) !== false;
	}

	public static function register_element( $class ) {
		self::$classes[] = $class;
	}

	public static function get_elements() {
		return self::$classes;
	}

	protected function fetch_preset( array $preset, $suffix ) {
		return array(
			"box_shadow_horizontal{$suffix}" => $preset['horizontal'],
			"box_shadow_vertical{$suffix}"   => $preset['vertical'],
			"box_shadow_blur{$suffix}"       => $preset['blur'],
			"box_shadow_spread{$suffix}"     => $preset['spread'],
			"box_shadow_position{$suffix}"   => $preset['position'],
		);
	}

	protected function get_preset_field( $name, $field ) {
		$preset = $this->get_preset( $name );

		return $preset[ $field ];
	}

	protected function get_key_value( $key, $atts = array(), $hover = false ) {
		$utils = ET_Core_Data_Utils::instance();
		$Hover = et_pb_hover_options();

		return $hover
			? $Hover->get_value( "box_shadow_{$key}", $atts, $utils->array_get( $atts, "box_shadow_{$key}") )
			: $utils->array_get( $atts, "box_shadow_{$key}" );
	}
}

function _action_et_pb_box_shadow_overlay() {
	wp_localize_script(
		apply_filters( 'et_builder_modules_script_handle', 'et-builder-modules-script' ),
		'et_pb_box_shadow_elements',
		ET_Builder_Module_Field_BoxShadow::get_elements()
	);
}

add_action( 'wp_footer', '_action_et_pb_box_shadow_overlay' );

return new ET_Builder_Module_Field_BoxShadow();
