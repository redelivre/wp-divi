<?php

/**
 * Utility class for manipulating various data formats. Currently includes methods for
 * transforming array data to another format based on key mapping as well as methods for
 * generating XML-RPC method call strings.
 *
 * @since   1.1.0
 *
 * @package ET\Core\Data
 */
class ET_Core_Data_Utils {

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
	 * Gets a value from a nested array using an address string.
	 *
	 * @param array  $array   An array which contains value located at `$address`.
	 * @param string $address The location of the value within `$array` (dot notation).
	 *
	 * @return array {
	 *     Result Array - will be empty if no value was found.
	 *
	 *     @type mixed $value The value found in `$array` at `$address`.
	 * }
	 */
	public function get_array_value_at_address( $array, $address ) {
		$keys   = explode( '.', $address );
		$result = array();
		$value  = $array;

		while ( $key = array_shift( $keys ) ) {
			if ( isset( $value[ $key ] ) ) {
				$value = $value[ $key ];
				continue;
			}
		}

		if ( $value !== $array ) {
			$result['value'] = $value;
		}

		return $result;
	}

	/**
	 * Determine if an array has any `string` keys (thus would be considered an object in JSON)
	 *
	 * @param $array
	 *
	 * @return bool
	 */
	public function is_assoc_array( $array ) {
		return count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
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
	 * Sets a value in a nested array using an address string (dot notation)
	 *
	 * @see http://stackoverflow.com/a/9628276/419887
	 *
	 * @param array  $array The array to modify
	 * @param string $path  The path in the array
	 * @param mixed  $value The value to set
	 */
	public function set_array_value_at_address( &$array, $path, &$value ) {
		$path_parts = explode( '.', $path );
		$current    = &$array;

		foreach ( $path_parts as $key ) {
			if ( ! is_array( $current ) ) {
				$current = array();
			}

			$current = &$current[ $key ];
		}

		$current = $value;
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

			$value = $this->get_array_value_at_address( $from_data, $from_address );

			if ( ! isset( $value['value'] ) ) {
				// Unknown key, skip it.
				continue;
			}

			$value = $value['value'];

			if ( $array_value_required && ! is_array( $value ) ) {
				$value = array( $value );
			}

			$this->set_array_value_at_address( $to_data, $to_address, $value );
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
