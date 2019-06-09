<?php

require_once 'Sizing.php';

/**
 * Helper class that provides necessary functions for managing alignment option
 *
 * Class ET_Builder_Module_Helper_Alignment
 */
class ET_Builder_Module_Helper_Alignment extends ET_Builder_Module_Helper_Sizing {

	public function get_raw_field() {
		return 'module_alignment';
	}
}

function et_pb_alignment_options( $prefix = '' ) {
	return new ET_Builder_Module_Helper_Alignment( $prefix );
}
