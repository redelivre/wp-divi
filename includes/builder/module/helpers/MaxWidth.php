<?php

require_once 'Sizing.php';

/**
 * Helper class that provides necessary functions for managing max width option
 *
 * Class ET_Builder_Module_Helper_Max_Width
 */
class ET_Builder_Module_Helper_Max_Width extends ET_Builder_Module_Helper_Sizing {

	public function get_raw_field() {
		return 'max_width';
	}
}

function et_pb_max_width_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Max_Width( $prefix );
}
