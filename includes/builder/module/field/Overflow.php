<?php

class ET_Builder_Module_Field_Overflow extends ET_Builder_Module_Field_Base {

	public function get_defaults() {
		return array(
			'prefix'         => '',
			'tab_slug'       => 'custom_css',
			'toggle_slug'    => 'visibility',
			'hover'          => 'tabs',
			'mobile_options' => true,
			'default'        => ET_Builder_Module_Helper_Overflow::OVERFLOW_DEFAULT,
		);
	}

	public function get_fields( array $args = array() ) {
		$settings = array_merge( $this->get_defaults(), $args );

		return array_merge(
			$this->get_field( 'x', $settings ),
			$this->get_field( 'y', $settings )
		);
	}

	protected function get_field( $axis, $args ) {
		$overflow         = et_pb_overflow();
		$OVERFLOW_DEFAULT = ET_Builder_Module_Helper_Overflow::OVERFLOW_DEFAULT;
		$OVERFLOW_VISIBLE = ET_Builder_Module_Helper_Overflow::OVERFLOW_VISIBLE;
		$OVERFLOW_SCROLL  = ET_Builder_Module_Helper_Overflow::OVERFLOW_SCROLL;
		$OVERFLOW_HIDDEN  = ET_Builder_Module_Helper_Overflow::OVERFLOW_HIDDEN;
		$OVERFLOW_AUTO    = ET_Builder_Module_Helper_Overflow::OVERFLOW_AUTO;

		switch ( $axis ) {
			case 'x' :
				$field = $overflow->get_field_x( $args['prefix'] );
				$label = __( 'Horizontal Overflow', 'et_builder' );
				break;
			default :
				$field = $overflow->get_field_y( $args['prefix'] );
				$label = __( 'Vertical Overflow', 'et_builder' );
				break;
		}

		$settings = array(
			'label'          => $label,
			'type'           => 'select',
			'id'             => $field,
			'hover'          => $args['hover'],
			'mobile_options' => $args['mobile_options'],
			'default'        => $args['default'],
			'tab_slug'       => $args['tab_slug'],
			'toggle_slug'    => $args['toggle_slug'],
			'options'        => array(
				$OVERFLOW_DEFAULT => __( 'Default', 'et_builder' ),
				$OVERFLOW_VISIBLE => __( 'Visible', 'et_builder' ),
				$OVERFLOW_SCROLL  => __( 'Scroll', 'et_builder' ),
				$OVERFLOW_HIDDEN  => __( 'Hidden', 'et_builder' ),
				$OVERFLOW_AUTO    => __( 'Auto', 'et_builder' ),
			),
			'description'    => sprintf(
				__( 'Here you can control element overflow on the %s axis. If set to scroll, content that overflows static widths or heights will trigger a browser scrollbar. If set to hidden, content overflow will be clipped.', 'et_builder' ),
				strtoupper( $axis )
			),
		);

		$options = array( $field => $settings );

		return $options;
	}
}

return new ET_Builder_Module_Field_Overflow();
