<?php

/**
 * Wrapper for ConstantContact's API.
 *
 * @since   3.0.75
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_ConstantContact extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.constantcontact.com/v2';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.constantcontact.com/v2/lists';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBE_URL = 'https://api.constantcontact.com/v2/contacts';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBERS_URL = 'https://api.constantcontact.com/v2/contacts';

	/**
	 * @inheritDoc
	 */
	public $name = 'ConstantContact';

	/**
	 * @inheritDoc
	 */
	public $slug = 'constant_contact';

	/**
	 * ET_Core_API_Email_ConstantContact constructor.
	 *
	 * @inheritDoc
	 */
	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->_maybe_set_custom_headers();
	}

	protected function _create_subscriber_data_array( $args ) {
		return $this->transform_data_to_provider_format( $args, 'subscriber' );
	}

	protected function _get_list_from_subscriber( $subscriber, $list_id ) {
		if ( ! isset( $subscriber['lists'] ) ) {
			return false;
		}

		foreach ( $subscriber['lists'] as &$list ) {
			if ( $list['id'] === $list_id ) {
				return $list;
			}
		}

		return false;
	}

	protected function _maybe_set_custom_headers() {
		if ( empty( $this->custom_headers ) && isset( $this->data['token'] ) ) {
			$this->custom_headers = array(
				'Authorization' => 'Bearer ' . sanitize_text_field( $this->data['token'] ),
			);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'api_key' => array(
				'label' => esc_html__( 'API Key', 'et_core' ),
			),
			'token'   => array(
				'label' => esc_html__( 'Access Token', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$keymap = array(
			'list'       => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'contact_count',
			),
			'subscriber' => array(
				'name'      => 'first_name',
				'last_name' => 'last_name',
				'email'     => 'email_addresses.[0].email_address',
				'list_id'   => 'lists.[0].id',
			),
			'error'      => array(
				'error_message' => '[0].error_message',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	public function get_subscriber( $email ) {
		$url = add_query_arg( 'email', $email, $this->SUBSCRIBERS_URL );

		$this->prepare_request( $url, 'GET', false );
		$this->make_remote_request();

		if ( $this->response->ERROR || ! isset( $this->response->DATA['results'] ) ) {
			return array();
		}

		return $this->response->DATA['results'];
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) || empty( $this->data['token'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_maybe_set_custom_headers();

		$this->response_data_key = false;

		$this->LISTS_URL = add_query_arg( 'api_key', $this->data['api_key'], $this->LISTS_URL );

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$this->SUBSCRIBERS_URL = add_query_arg( 'api_key', $this->data['api_key'], $this->SUBSCRIBERS_URL );
		$result                = null;
		$args['list_id']       = (string) $args['list_id'];

		$subscriber = $this->get_subscriber( $args['email'] );
		$subscriber = $subscriber ? $subscriber[0] : $subscriber;

		$query_args = array( 'api_key' => $this->data['api_key'], 'action_by' => 'ACTION_BY_VISITOR' );

		if ( $subscriber ) {
			$list   = &$this->_get_list_from_subscriber( $subscriber, $args['list_id'] );
			$result = 'success';

			if ( ! $list || 'ACTIVE' !== $list['status'] ) {
				$list
					? $list['status'] = 'UNCONFIRMED'
					: $subscriber['lists'][] = array( 'id' => $args['list_id'], 'status' => 'UNCONFIRMED' );

				$url = add_query_arg( $query_args, "{$this->SUBSCRIBE_URL}/{$subscriber['id']}" );

				$this->prepare_request( $url, 'PUT', false, $subscriber, true );
			}

		} else {
			$url        = add_query_arg( $query_args, $this->SUBSCRIBE_URL );
			$subscriber = $this->_create_subscriber_data_array( $args );

			$this->prepare_request( $url, 'POST', false, $subscriber, true );
		}

		if ( 'success' !== $result ) {
			$result = parent::subscribe( $args, $this->SUBSCRIBE_URL );
		}

		return $result;
	}
}
