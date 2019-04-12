<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Transition Options helper methods
 *
 * Class ET_Builder_Module_Transition_Options
 */
class ET_Builder_Module_Transition_Options {

	private static $instance;

	public static function get() {
		if ( empty( self::$instance ) ) {
			return self::$instance = new ET_Builder_Module_Transition_Options();
		}

		return self::$instance;
	}

	private function get_value( $key, $list, $default = null ) {
		$value = (string) ET_Core_Data_Utils::instance()->array_get( $list, $key );

		return '' === $value ? $default : $value;
	}

	/**
	 * Returns the module transition duration,
	 * In case the setting is empty, a default value is returned
	 *
	 * @param array $props
	 *
	 * @return string
	 */
	public function get_duration( $props ) {
		return $this->get_value( 'hover_transition_duration', $props, '300ms' );
	}

	/**
	 * Returns the module transition speed curve,
	 * In case the setting is empty, a default value is returned
	 *
	 * @param array $props
	 *
	 * @return string
	 */
	public function get_easing( $props ) {
		return $this->get_value( 'hover_transition_speed_curve', $props, 'ease' );
	}

	/**
	 * Returns the module transition transition delay,
	 * In case the setting is empty, a default value is returned
	 *
	 * @param array $props
	 *
	 * @return string
	 */
	public function get_delay( $props ) {
		return $this->get_value( 'hover_transition_delay', $props, '0ms' );
	}

	public function get_style( $property, $props ) {
		$duration = $this->get_duration( $props );
		$easing   = $this->get_easing( $props );
		$delay    = $this->get_delay( $props );

		return "{$property} {$duration} {$easing} {$delay}";
	}
}

function et_pb_transition_options() {
	return ET_Builder_Module_Transition_Options::get();
}
