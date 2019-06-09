<?php

require_once 'Sizing.php';

/**
 * Helper class that provides necessary functions for managing max height option
 *
 * Class ET_Builder_Module_Helper_Min_Height
 */
class ET_Builder_Module_Helper_Min_Height extends ET_Builder_Module_Helper_Sizing {

	public function get_raw_field() {
		return 'min_height';
	}
}

function et_pb_min_height_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Min_Height( $prefix );
}
