<?php

class ET_Builder_Module_Settings_Migration_DividerHeight extends ET_Builder_Module_Settings_Migration {

	public $version = '3.23.4';

	public function get_modules() {
		return array( 'et_pb_divider' );
	}

	public function get_fields() {
		return array(
			'height' => array(
				'affected_fields' => array(
					'height' => $this->get_modules(),
				),
			)
		);
	}

	public function migrate(
		$field_name,
		$current_value,
		$module_slug,
		$saved_value,
		$saved_field_name,
		$attrs,
		$content
	) {
		return et_sanitize_input_unit( $saved_value, false, 'px' );
	}
}

return new ET_Builder_Module_Settings_Migration_DividerHeight();
