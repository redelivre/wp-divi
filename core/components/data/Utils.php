<?php

/**
 * Utility class for manipulating various data formats. Includes methods for
 * transforming array data to another format based on key mapping, methods for
 * generating XML-RPC method call strings, methods for working with arrays, and more.
 *
 * @since   3.0.62
 *
 * @package ET\Core\Data
 */
class ET_Core_Data_Utils {

	private static $_instance;

	private $_sort_by;

	/**
	 * Generate an XML-RPC array.
	 *
	 * @param array $values
	 *
	 * @return string
	 */
	private function _create_xmlrpc_array( $values ) {
		$output = '';

		foreach ( $values as $value ) {
			$output .= $this->_create_xmlrpc_value( $value );
		}

		return "<array><data>{$output}</data></array>";
	}

	/**
	 * Generate an XML-RPC struct.
	 *
	 * @param array $members
	 *
	 * @return string
	 */
	private function _create_xmlrpc_struct( $members ) {
		$output = '';

		foreach ( $members as $name => $value ) {
			$output .= sprintf( '<member><name>%1$s</name>%2$s</member>', $name, $this->_create_xmlrpc_value( $value ) );
		}

		return "<struct>{$output}</struct>";
	}

	/**
	 * Generate an XML-RPC value.
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	private function _create_xmlrpc_value( $value ) {
		$output = '';

		if ( is_string( $value ) ) {
			$output = "<string>{$value}</string>";
		} else if ( is_bool( $value ) ) {
			$value  = (int) $value;
			$output = "<boolean>{$value}</boolean>";
		} else if ( is_int( $value ) ) {
			$output = "<int>{$value}</int>";
		} else if ( is_array( $value ) && $this->is_assoc_array( $value ) ) {
			$output = $this->_create_xmlrpc_struct( $value );
		} else if ( is_array( $value ) ) {
			$output = $this->_create_xmlrpc_array( $value );
		}

		return "<value>{$output}</value>";
	}

	/**
	 * Convert a SimpleXMLElement to a native PHP data type.
	 *
	 * @param SimpleXMLElement $value
	 *
	 * @return mixed
	 */
	private function _parse_value( $value ) {
		switch ( true ) {
			case is_string( $value ):
				$result = $value;
				break;
			case count( $value->struct ) > 0:
				$result = new stdClass();

				foreach ( $value->struct->member as $member ) {
					$name          = (string) $member->name;
					$member_value  = $this->_parse_value( $member->value );
					$result->$name = $member_value;
				}

				break;
			case count( $value->array ) > 0:
				$result = array();

				foreach ( $value->array->data->value as $array_value ) {
					$result[] = $this->_parse_value( $array_value );
				}

				break;
			case count( $value->i4 ) > 0:
				$result = (int) $value->i4;
				break;
			case count( $value->int ) > 0:
				$result = (int) $value->int;
				break;
			case count( $value->boolean ) > 0:
				$result = (boolean) $value->boolean;
				break;
			case count( $value->double ) > 0:
				$result = (double) $value->double;
				break;
			default:
				$result = (string) $value;
		}

		return $result;
	}

	private function _remove_empty_directories( $path ) {
		if ( ! is_dir( $path ) ) {
			return false;
		}

		$empty              = true;
		$directory_contents = glob( untrailingslashit( $path ) . '/*' );

		foreach ( (array) $directory_contents as $item ) {
			if ( ! $this->_remove_empty_directories( $item ) ) {
				$empty = false;
			}
		}

		return $empty ? @rmdir( $path ) : false;
	}

	public function _array_sort_by_callback( $a, $b ) {
		$sort_by = $this->_sort_by;

		if ( is_array( $a ) ) {
			return strcmp( $a[ $sort_by ], $b[ $sort_by ] );
		} else if ( is_object( $a ) ) {
			return strcmp( $a->$sort_by, $b->$sort_by );
		}

		return 0;
	}

	/**
	 * Returns `true` if all values in `$array` are not empty, `false` otherwise.
	 * If `$condition` is provided then values are checked against it instead of `empty()`.
	 *
	 * @param array $array
	 * @param bool  $condition Compare values to this instead of `empty()`. Optional.
	 *
	 * @return bool
	 */
	public function all( array $array, $condition = null ) {
		if ( null === $condition ) {
			foreach( $array as $key => $value ) {
				if ( empty( $value ) ) {
					return false;
				}
			}
		} else {
			foreach( $array as $key => $value ) {
				if ( $value !== $condition ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Flattens a multi-dimensional array.
	 *
	 * @since 3.0.99
	 *
	 * @param array $array An array to flatten.
	 *
	 * @return array
	 */
	function array_flatten( array $array ) {
		$iterator = new RecursiveIteratorIterator( new RecursiveArrayIterator( $array ) );
		$use_keys = false;

		return iterator_to_array( $iterator, $use_keys );
	}

	/**
	 * Gets a value from a nested array using an address string.
	 *
	 * @param array  $array   An array which contains value located at `$address`.
	 * @param string $address The location of the value within `$array` (dot notation).
	 * @param mixed  $default Value to return if not found. Default is an empty string.
	 *
	 * @return mixed The value, if found, otherwise $default.
	 */
	public function array_get( $array, $address, $default = '' ) {
		$keys   = explode( '.', $address );
		$value  = $array;

		while ( $key = array_shift( $keys ) ) {
			if ( '[' === $key[0] && is_numeric( substr( $key, 1, -1 ) ) ) {
				$key = (int) $key;
			}

			if ( ! isset( $value[ $key ] ) ) {
				return $default;
			}

			$value = $value[ $key ];
		}

		return $value;
	}

	/**
	 * Sets a value in a nested array using an address string (dot notation)
	 *
	 * @see http://stackoverflow.com/a/9628276/419887
	 *
	 * @param array        $array The array to modify
	 * @param string|array $path  The path in the array
	 * @param mixed        $value The value to set
	 */
	public function array_set( &$array, $path, &$value ) {
		$path_parts = is_array( $path ) ? $path : explode( '.', $path );
		$current    = &$array;

		foreach ( $path_parts as $key ) {
			if ( ! is_array( $current ) ) {
				$current = array();
			}

			if ( '[' === $key[0] && is_numeric( substr( $key, 1, - 1 ) ) ) {
				$key = (int) $key;
			}

			$current = &$current[ $key ];
		}

		$current = $value;
	}

	public function array_sort_by( $array, $key_or_prop ) {
		if ( ! is_string( $key_or_prop ) && ! is_int( $key_or_prop ) ) {
			return $array;
		}

		$this->_sort_by = $key_or_prop;

		if ( $this->is_assoc_array( $array ) ) {
			uasort( $array, array( $this, '_array_sort_by_callback' ) );
		} else {
			usort( $array, array( $this, '_array_sort_by_callback' ) );
		}

		return $array;
	}

	public function ensure_directory_exists( $path ) {
		return file_exists( $path ) ? true : @mkdir( $path, 0755, true );
	}

	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new ET_Core_Data_Utils();
		}

		return self::$_instance;
	}

	/**
	 * Determine if an array has any `string` keys (thus would be considered an object in JSON)
	 *
	 * @param $array
	 *
	 * @return bool
	 */
	public function is_assoc_array( $array ) {
		return is_array( $array ) && count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
	}

	/**
	 * Determine if value is an XML-RPC error.
	 *
	 * @param SimpleXMLElement $value
	 *
	 * @return bool
	 */
	public function is_xmlrpc_error( $value ) {
		return is_object( $value ) && isset( $value->faultCode );
	}

	/**
	 * Replaces any Windows style directory separators in $path with Linux style separators.
	 * Windows actually supports both styles, even mixed together. However, its better not
	 * to mix them (especially when doing string comparisons on paths).
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public function normalize_path( $path ) {
		return $path ? str_replace( '\\', '/', $path ) : '';
	}

	/**
	 * Generate post data for a XML-RPC method call
	 *
	 * @param string $method_name
	 * @param array  $params
	 *
	 * @return string
	 */
	public function prepare_xmlrpc_method_call( $method_name, $params = array() ) {
		$output = '';

		foreach ( $params as $param ) {
			$value = $this->_create_xmlrpc_value( $param );
			$output .= "<param>{$value}</param>";
		}

		return
			"<?xml version='1.0' encoding='UTF-8'?>
			<methodCall>
				<methodName>{$method_name}</methodName>
				<params>
					{$output}
				</params>
			</methodCall>";
	}

	/**
	 * Process an XML-RPC response string.
	 *
	 * @param $response
	 *
	 * @return mixed
	 */
	public function process_xmlrpc_response( $response, $skip_processing = false ) {
		$response = simplexml_load_string( $response );
		$result   = array();

		if ( $skip_processing ) {
			return $response;
		}

		if ( count( $response->fault ) > 0 ) {
			// An error was returned
			return $this->_parse_value( $response->fault->value );
		}

		$single = count( $response->params->param ) === 1;

		foreach ( $response->params->param as $param ) {
			$value = $this->_parse_value( $param->value );

			if ( $single ) {
				return $value;
			} else {
				$result[] = $value;
			}
		}

		return $result;
	}

	/**
	 * Removes empty directories recursively starting at and (possibly) including `$path`. `$path` must be
	 * an absolute path located under {@see WP_CONTENT_DIR}. Current user must have 'manage_options'
	 * capability. If the path or permissions check fails, no directories will be removed.
	 *
	 * @param string $path Absolute path to parent directory.
	 */
	function remove_empty_directories( $path ) {
		$path = realpath( $path );

		if ( empty( $path ) ) {
			// $path doesn't exist
			return;
		}

		$path        = $this->normalize_path( $path );
		$content_dir = $this->normalize_path( WP_CONTENT_DIR );

		if ( 0 !== strpos( $path, $content_dir ) || $content_dir === $path ) {
			return;
		}

		$capability = 0 === strpos( $path, "{$content_dir}/cache/et" ) ? 'edit_posts' : 'manage_options';

		if ( ! wp_doing_cron() && ! et_core_security_check_passed( $capability ) ) {
			return;
		}

		$this->_remove_empty_directories( $path );
	}

	/**
	 * Whether or not a value includes another value.
	 *
	 * @param string $haystack The value to look in.
	 * @param string $needle   The value to look for.
	 *
	 * @return bool
	 */
	function includes( $haystack, $needle ) {
		if ( is_string( $haystack ) ) {
			return false !== strpos( $haystack, $needle );
		}

		if ( is_object( $haystack ) ) {
			return property_exists( $haystack, $needle );
		}

		if ( is_array( $haystack ) ) {
			return in_array( $needle, $haystack );
		}

		return false;
	}

	/**
	 * Transforms an assoc array to/from internal/external data formats.
	 *
	 * @param string $data_format       The format to which the data should be transformed.
	 * @param array  $from_data         The data to transform.
	 * @param array  $data_keys_mapping An array mapping internal data keys to external data keys.
	 * @param array  $exclude_keys      Keys that should be excluded from the result. Optional.
	 *
	 * @return array
	 */
	public function transform_data_to( $data_format, $from_data, $data_keys_mapping, $exclude_keys = array() ) {
		$want_our_data_format = 'our_data' === $data_format;
		$to_data              = array();

		foreach ( $data_keys_mapping as $our_data_address => $their_data_address ) {
			$from_address = $want_our_data_format ? $their_data_address : $our_data_address;
			$to_address   = $want_our_data_format ? $our_data_address : $their_data_address;

			$array_value_required = 0 === strpos( $to_address, '@_' );
			$to_address           = $array_value_required ? str_replace( '@_', '', $to_address ) : $to_address;

			if ( ! empty( $exclude_keys ) && array_key_exists( $to_address, $exclude_keys ) ) {
				continue;
			}

			$value = $this->array_get( $from_data, $from_address, null );

			if ( null === $value ) {
				// Unknown key, skip it.
				continue;
			}

			if ( $array_value_required && ! is_array( $value ) ) {
				$value = array( $value );
			}

			$this->array_set( $to_data, $to_address, $value );
		}

		return $to_data;
	}

	/**
	 * Converts xml data to array. Useful in cases where the xml doesn't adhere to XML-RPC spec.
	 *
	 * @param string|\SimpleXMLElement $xml_data
	 *
	 * @return array
	 */
	function xml_to_array( $xml_data ) {
		if ( is_string( $xml_data ) ) {
			$xml_data = simplexml_load_string( $xml_data );
		}

		$json = json_encode( $xml_data );
		return json_decode( $json, true );
	}

}


function et_core_data_utils_minify_css( $string = '' ) {
	$comments = <<< EOS
(?sx)
	# don't change anything inside of quotes
	( "(?:[^"\\\]++|\\\.)*+" | '(?:[^'\\\]++|\\\.)*+' )
|
	# comments
	/\* (?> .*? \*/ )
EOS;

	$everything_else = <<< EOS
(?six)
	# don't change anything inside of quotes
	( "(?:[^"\\\]++|\\\.)*+" | '(?:[^'\\\]++|\\\.)*+' )
|
	# spaces before and after ; and }
	\s*+ ; \s*+ ( } ) \s*+
|
	# all spaces around meta chars/operators (excluding + and -)
	\s*+ ( [*$~^|]?+= | [{};,>~] | !important\b ) \s*+
|
	# all spaces around + and - (in selectors only!)
	\s*([+-])\s*(?=[^}]*{)
|
	# spaces right of ( [ :
	( [[(:] ) \s++
|
	# spaces left of ) ]
	\s++ ( [])] )
|
	# spaces left (and right) of : (but not in selectors)!
	\s+(:)(?![^\}]*\{)
|
	# spaces at beginning/end of string
	^ \s++ | \s++ \z
|
	# double spaces to single
	(\s)\s+
EOS;

	$search_patterns  = array( "%{$comments}%", "%{$everything_else}%" );
	$replace_patterns = array( '$1', '$1$2$3$4$5$6$7' );

	return preg_replace( $search_patterns, $replace_patterns, $string );
}
