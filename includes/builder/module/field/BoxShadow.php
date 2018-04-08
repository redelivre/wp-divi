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
			'depends_to'          => null,
			'depends_default'     => null
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
			'depends_to'          => array( $style ),
			'depends_show_if_not' => 'none',
			'default_on_child'    => true,
		);
		$range   = array_merge(
			$option,
			array(
				'type'            => 'range',
				'range_settings'  => array(
					'min'  => - 80,
					'max'  => 80,
					'step' => 1,
				),
				'default'         => 0,
				'validate_unit'   => true,
				'fixed_unit'      => 'px',
				'fixed_range'     => true,
			)
		);

		if ( $arguments['sub_toggle_slug'] ) {
			$option['sub_toggle'] = $arguments['sub_toggle_slug'];
		}

		$presets = array();

		foreach ( $this->get_presets() as $id => $preset ) {
			if ( 'none' === $id ) {
				$presets[] = array(
					'value'   => $id,
					'icon'    => $id,
					'fields'  => $this->fetch_preset( $preset, $arguments['suffix'] ),
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
				'depends_show_if'     => $arguments['depends_show_if'],
				'depends_show_if_not' => $arguments['depends_show_if_not'],
				'depends_to'          => $arguments['depends_to'],
				'depends_default'     => $arguments['depends_default'],
			)
		);
		if ( $options[ $style ]['depends_to'] === null ) {
			unset( $options[ $style ]['depends_to'] );
		}
		if ( $options[ $style ]['depends_show_if'] === null ) {
			unset( $options[ $style ]['depends_show_if'] );
		}
		if ( $options[ $style ]['depends_show_if_not'] === null ) {
			unset( $options[ $style ]['depends_show_if_not'] );
		}
		if ( $options[ $style ]['depends_default'] === null ) {
			unset( $options[ $style ]['depends_default'] );
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
				'default'        => 'rgba(0,0,0,0.3)',
				'field_template' => 'color',
			)
		);
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

	public function get_value( array $atts, array $args = array() ) {
		$args      = shortcode_atts( array( 'suffix' => '', 'important' => false, ), $args );
		$suffix    = $args['suffix'];
		$important = $args['important'] ? '!important' : '';

		if (
			! isset( $atts["box_shadow_style{$suffix}"] )
			||
			empty( $atts["box_shadow_style{$suffix}"] )
			||
			$atts["box_shadow_style{$suffix}"] === 'none'
		) {
			return '';
		}

		$preset = $this->get_preset( $atts["box_shadow_style{$suffix}"] );
		$atts   = $this->merge_pairs( array(
			"box_shadow_position{$suffix}"   => $preset['position'],
			"box_shadow_horizontal{$suffix}" => $preset['horizontal'],
			"box_shadow_vertical{$suffix}"   => $preset['vertical'],
			"box_shadow_blur{$suffix}"       => $preset['blur'],
			"box_shadow_spread{$suffix}"     => $preset['spread'],
			"box_shadow_color{$suffix}"      => 'rgba(0,0,0,0.3)',
		), $atts );

		$position   = $atts["box_shadow_position{$suffix}"] == 'inner' ? 'inset' : '';
		$horizontal = rtrim( $atts["box_shadow_horizontal{$suffix}"], 'px' ) . 'px';
		$vertical   = rtrim( $atts["box_shadow_vertical{$suffix}"], 'px' ) . 'px';
		$blur       = rtrim( $atts["box_shadow_blur{$suffix}"], 'px' ) . 'px';
		$strength   = rtrim( $atts["box_shadow_spread{$suffix}"], 'px' ) . 'px';
		$color     = $atts["box_shadow_color{$suffix}"];

		return sprintf(
			'box-shadow: %1$s %2$s %3$s %4$s %5$s %6$s %7$s;',
			$position,
			$horizontal,
			$vertical,
			$blur,
			$strength,
			$color,
			$important
		);
	}

	public function get_presets() {
		return array(
			'none' => array(
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
		$selectors      = array_map( 'trim', explode( ',', $selector ) );
		$always_overlay = isset( $args['always_overlay'] ) && $args['always_overlay'] === true;
		$value          = $this->get_value( $atts, $args );

		if ( empty( $value ) ) {
			return array(
				'selector'    => implode( ', ', $selectors ),
				'declaration' => null,
			);
		}

		if ( $this->is_inset( $value ) || $always_overlay ) {
			foreach ( $selectors as &$selector ) {
				self::register_element( $selector );
				$selector = $selector . '>.box-shadow-overlay, ' . $selector . '.et-box-shadow-no-overlay';
			}
		}

		return array(
			'selector'    => implode( ', ', $selectors ),
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

	protected function merge_pairs( array $arr1, array $arr2 ) {
		$out = array();
		foreach ( $arr1 as $name => $default ) {
			if ( isset( $arr2[ $name ] ) && $arr2[ $name ] !== null && $arr2[ $name ] !== '' ) {
				$out[ $name ] = $arr2[ $name ];
			} else {
				$out[ $name ] = $default;
			}
		}

		return $out;
	}

	protected function get_preset_field( $name, $field ) {
		$preset = $this->get_preset( $name );

		return $preset[ $field ];
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
