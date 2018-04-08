<?php

/**
 * Wrapper for MailPoet's API.
 *
 * @since   3.0.76
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MailPoet3 extends ET_Core_API_Email_Provider {

	public static $PLUGIN_REQUIRED;

	/**
	 * @inheritDoc
	 */
	public $name = 'MailPoet';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailpoet';

	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		if ( null === self::$PLUGIN_REQUIRED ) {
			self::$PLUGIN_REQUIRED = esc_html__( 'MailPoet plugin is either not installed or not activated.', 'et_core' );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array();
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$keymap = array(
			'list'       => array(
				'list_id' => 'id',
				'name'    => 'name',
			),
			'subscriber' => array(
				'name'      => 'first_name',
				'last_name' => 'last_name',
				'email'     => 'email',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( ! class_exists( '\MailPoet\API\API' ) ) {
			return self::$PLUGIN_REQUIRED;
		}

		$data = \MailPoet\API\API::MP( 'v1' )->getLists();

		if ( ! empty( $data ) ) {
			$this->data['lists'] = $this->_process_subscriber_lists( $data );
		}

		$this->data['is_authorized'] = true;
		$this->save_data();

		return 'success';
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( ! class_exists( '\MailPoet\API\API' ) ) {
			ET_Core_Logger::error( self::$PLUGIN_REQUIRED );

			return esc_html__( 'An error occurred. Please try again later.', 'et_core' );
		}

		$args            = et_sanitized_previously( $args );
		$subscriber_data = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$result          = 'success';
		$lists           = array( $args['list_id'] );

		try {
			\MailPoet\API\API::MP( 'v1' )->addSubscriber( $subscriber_data, $lists );
		} catch ( Exception $exception ) {
			$result = $exception->getMessage();
		}

		return $result;
	}
}
