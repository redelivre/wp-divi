<?php


class ET_Core_Logger {

	/**
	 * Writes a message to the WP Debug and PHP Error logs.
	 *
	 * @param mixed $message
	 */
	private static function _write_log( $message ) {
		$before_message = ' ';

		if ( function_exists( 'd' ) ) {
			// https://wordpress.org/plugins/kint-debugger/
			d( $message );
			return;
		}

		if ( ! is_scalar( $message ) ) {
			$message        = print_r( $message, true );
			$before_message = "\n";
		}

		$backtrace = debug_backtrace( 1 );
		$caller    = $backtrace[3];

		$file = isset( $backtrace[3]['file'] ) ? basename( $backtrace[3]['file'] ) : '<unknown file>';
		$line = isset( $backtrace[3]['line'] ) ? $backtrace[3]['line'] : '<unknown line>';

		$message = "{$file}:{$line} -> {$caller['function']}():{$before_message}{$message}";

		error_log( $message );
	}

	/**
	 * Writes message to the logs if {@link WP_DEBUG} is `true`, otherwise does nothing.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $message
	 */
	public static function debug( $message ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			self::_write_log( $message );
		}
	}

	public static function disable_php_notices() {
		$error_reporting = error_reporting();
		$notices_enabled = $error_reporting & E_NOTICE;

		if ( $notices_enabled ) {
			error_reporting( $error_reporting & ~E_NOTICE );
		}
	}

	/**
	 * Writes an error message to the logs regardless of whether or not debug mode is enabled.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $message
	 */
	public static function error( $message ) {
		self::_write_log( $message );
	}

	public static function enable_php_notices() {
		$error_reporting = error_reporting();
		$notices_enabled = $error_reporting & E_NOTICE;

		if ( ! $notices_enabled ) {
			error_reporting( $error_reporting | E_NOTICE );
		}
	}

	public static function php_notices_enabled() {
		$error_reporting = error_reporting();
		return $error_reporting & E_NOTICE;
	}
}
