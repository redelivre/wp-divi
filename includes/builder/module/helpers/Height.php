<?php

require_once 'Sizing.php';

/**
 * Helper class that provides necessary functions for managing height option
 *
 * Class ET_Builder_Module_Helper_Height
 */
class ET_Builder_Module_Helper_Height extends ET_Builder_Module_Helper_Sizing {

	public function get_raw_field() {
		return 'height';
	}
}

function et_pb_height_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Height( $prefix );
}
