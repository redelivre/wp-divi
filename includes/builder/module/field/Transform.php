<?php

class ET_Builder_Module_Field_Transform extends ET_Builder_Module_Field_Base {

	private $processing_props = array();

	public $defaults = array(
		'scale'     => '100%',
		'translate' => '0px',
		'rotate'    => '0deg',
		'skew'      => '0deg',
		'origin'    => '50%',
	);

	public function get_fields( array $args = array() ) {
		$settings = wp_parse_args( array(
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'transform',
			'depends_on'      => null,
			'depends_show_if' => null,
			'defaults'        => $this->defaults,
		), $args );

		$additional_options = array();
		$defaults           = $settings['defaults'];

		$tabs = array(
			'scale'     => array(
				'icon'     => 'resize',
				'controls' => array(
					'transform_scale' => array(
						'type'           => 'transform',
						'label'          => esc_html__( 'Transform Scale', 'et_builder' ),
						'default'        => "${defaults['scale']}|${defaults['scale']}",
						'default_unit'   => '%',
						'range_settings' => array(
							'min'  => -100,
							'max'  => 300,
							'step' => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
					),
				),
			),
			'translate' => array(
				'icon'     => 'move',
				'controls' => array(
					'transform_translate' => array(
						'type'           => 'transform',
						'label'          => esc_html__( 'Transform Translate', 'et_builder' ),
						'default'        => "${defaults['translate']}|${defaults['translate']}",
						'default_unit'   => 'px',
						'range_settings' => array(
							'min'  => -300,
							'max'  => 300,
							'step' => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
					),
				),
			),
			'rotate'    => array(
				'icon'     => 'rotate',
				'controls' => array(
					'transform_rotate' => array(
						'type'           => 'transform',
						'label'          => esc_html__( 'Transform Rotate', 'et_builder' ),
						'default'        => "${defaults['rotate']}|${defaults['rotate']}|${defaults['rotate']}",
						'default_unit'   => 'deg',
						'range_settings' => array(
							'min'  => 0,
							'max'  => 360,
							'step' => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
					),
				),
			),
			'skew'      => array(
				'icon'     => 'skew',
				'controls' => array(
					'transform_skew' => array(
						'type'           => 'transform',
						'label'          => esc_html__( 'Transform Skew', 'et_builder' ),
						'default'        => "${defaults['skew']}|${defaults['skew']}",
						'default_unit'   => 'deg',
						'range_settings' => array(
							'min'       => -180,
							'max'       => 180,
							'min_limit' => -180,
							'max_limit' => 180,
							'step'      => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
					),
				),
			),
			'origin'    => array(
				'icon'     => 'transform-origin',
				'controls' => array(
					'transform_origin' => array(
						'type'           => 'transform',
						'label'          => esc_html__( 'Transform Origin', 'et_builder' ),
						'default'        => "${defaults['origin']}|${defaults['origin']}",
						'default_unit'   => '%',
						'range_settings' => array(
							'min'  => -50,
							'max'  => 150,
							'step' => 1,
						),
						'context'        => 'transform_styles',
						'mobile_options' => true,
					),
				),
			),
		);

		$additional_options['transform_styles'] = array(
			'label'               => esc_html__( 'Transform', 'et_builder' ),
			'tab_slug'            => $settings['tab_slug'],
			'toggle_slug'         => $settings['toggle_slug'],
			'type'                => 'composite',
			'attr_suffix'         => '',
			'composite_type'      => 'transforms',
			'hover'               => 'tabs',
			'mobile_options'      => true,
			'responsive'          => true,
			'bb_support'          => false,
			'description'         => esc_html__( 'Using the transform controls, you can performance visual adjustments to any element using a combination of Scale, Translation, Rotation and Skew settings. This allows you to create advanced design effects without the need of a separate graphic design program.',
				'et_builder' ),
			'composite_structure' => $tabs,
		);

		//Register responsive options
		$skip = array(
			'type'        => 'skip',
			'tab_slug'    => $settings['tab_slug'],
			'toggle_slug' => $settings['toggle_slug'],
		);
		foreach ( $additional_options['transform_styles']['composite_structure'] as $tab_name => $tab ) {
			foreach ( $tab['controls'] as $field_name => $field_options ) {
				$additional_options['transform_styles']['composite_structure'][ $tab_name ]['controls']["${field_name}_tablet"]      = $skip;
				$additional_options['transform_styles']['composite_structure'][ $tab_name ]['controls']["${field_name}_phone"]       = $skip;
				$additional_options['transform_styles']['composite_structure'][ $tab_name ]['controls']["${field_name}_last_edited"] = $skip;
			}
		}

		$additional_options['transform_styles_last_edited'] = $skip;

		return $additional_options;
	}

	//Processing functions

	public function percent_to_unit( $percent = 0 ) {
		if ( strpos( $percent, '%' ) === false ) {
			return $percent;
		}
		$value = (float) trim( str_replace( '%', '', $percent ) );

		return $value / 100;
	}

	public function set_props( $props ) {
		$this->processing_props = $props;
	}

	public function get_setting( $value, $default ) {
		if ( ! empty( $this->processing_props[ $value ] ) ) {
			return $this->processing_props[ $value ];
		} else {
			return $default;
		}
	}

	public function get_option( $typeAxis, $type = 'desktop' ) {
		$setting     = "transform_$typeAxis[0]";
		$interpreter = array( 'x' => 0, 'y' => 1, 'z' => 2 );
		$index       = $interpreter[ $typeAxis[1] ];

		$defaultValue = false;
		$optionValue  = $this->get_setting( $setting, false );

		if ( 'hover' === $type ) {
			$defaultValue = $this->get_setting( $setting, false );
			$optionValue  = $this->get_setting( $setting . '__hover', false );
		} elseif ( 'tablet' === $type ) {
			$defaultValue = $this->get_setting( $setting, false );
			$optionValue  = $this->get_setting( $setting . '_tablet', false );
		} elseif ( 'phone' === $type ) {
			$defaultValue = $this->get_setting( $setting . '_tablet', false );
			$optionValue  = $this->get_setting( $setting . '_phone', false );
			if ( $defaultValue == false ) {
				$defaultValue = $this->get_setting( $setting, false );
			}
		}

		if ( false === $optionValue ) {
			if ( false !== $defaultValue ) {
				$optionValue = $defaultValue;
			}
		}

		if ( false === $optionValue ) {
			return '';
		}

		$valueArray = explode( '|', $optionValue );
		$value = $valueArray[ $index ];

		if ( 'scale' === $typeAxis[0] ) {
			return $this->percent_to_unit( $value );
		}

		return $value;

	}

	public function get_declaration( $important, $type ) {
		if ( empty( $this->processing_props ) ) {
			wp_die( new WP_Error( '666', 'Run set_props first' ) );
		}

		$allTransforms  = array(
			'scale_x',
			'scale_y',
			'translate_x',
			'translate_y',
			'rotate_x',
			'rotate_y',
			'rotate_z',
			'skew_x',
			'skew_y',
			'origin_x',
			'origin_y',
		);
		$declaration    = '';
		$transformArray = array();
		$originArray    = array();
		foreach ( $allTransforms as $option ) {
			$typeAxis = explode( '_', $option );
			$clean    = $typeAxis[0] . strtoupper( $typeAxis[1] );
			$value    = esc_attr( $this->get_option( $typeAxis, $type ) );
			if ( ! empty( $value ) ) {
				if ( 'origin' === $typeAxis[0] ) {
					if ( 'origin_y' === $option && empty( $originArray ) ) {
						//default value of origin_x
						array_push( $originArray, '50%' );
					}
					array_push( $originArray, $value );
				} else {
					array_push( $transformArray, "$clean($value)" );
				}
			}
		}
		if ( ! empty( $transformArray ) ) {
			if ( $important ) {
				array_push( $transformArray, '!important' );
			}
			$declaration .= 'transform:' . implode( ' ', $transformArray ) . ';';
		}
		if ( ! empty( $originArray ) ) {
			if ( $important ) {
				array_push( $originArray, '!important' );
			}
			$declaration .= 'transform-origin:' . implode( ' ', $originArray ) . ';';
		}

		return $declaration;
	}
}

return new ET_Builder_Module_Field_Transform();
