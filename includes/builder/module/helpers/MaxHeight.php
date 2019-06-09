<?php

require_once 'Sizing.php';

/**
 * Helper class that provides necessary functions for managing max height option
 *
 * Class ET_Builder_Module_Helper_Max_Height
 */
class ET_Builder_Module_Helper_Max_Height extends ET_Builder_Module_Helper_Sizing {

	public function get_raw_field() {
		return 'max_height';
	}
}

function et_pb_max_height_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Max_Height( $prefix );
}
