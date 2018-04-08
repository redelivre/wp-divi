<?php

/**
 * Wrapper for HubSpot's API.
 *
 * @since   3.0.72
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_HubSpot extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.hubapi.com/contacts/v1';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.hubapi.com/contacts/v1/lists/static';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBE_URL = 'https://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/@email@';

	/**
	 * @inheritDoc
	 */
	public $name = 'HubSpot';

	/**
	 * @inheritDoc
	 */
	public $slug = 'hubspot';

	protected function _get_list_add_contact_url( $list_id ) {
		$url = "{$this->BASE_URL}/lists/{$list_id}/add";

		return add_query_arg( 'hapikey', $this->data['api_key'], $url );
	}

	protected function _maybe_set_urls( $email = '' ) {
		if ( empty( $this->data['api_key'] ) ) {
			return;
		}

		$this->LISTS_URL     = add_query_arg( 'hapikey', $this->data['api_key'], $this->LISTS_URL );
		$this->SUBSCRIBE_URL = add_query_arg( 'hapikey', $this->data['api_key'], $this->SUBSCRIBE_URL );

		if ( $email ) {
			$this->SUBSCRIBE_URL = str_replace( '@email@', rawurlencode( $email ), $this->SUBSCRIBE_URL );
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
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$keymap = array(
			'list'       => array(
				'list_id'           => 'listId',
				'name'              => 'name',
				'subscribers_count' => 'metaData.size',
			),
			'subscriber' => array(),
			'error'      => array(
				'error_message' => 'message',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_maybe_set_urls();

		/**
		 * The maximum number of subscriber lists to request from Hubspot's API at a time.
		 *
		 * @since 3.0.75
		 *
		 * @param int $max_lists Value must be <= 250.
		 */
		$max_lists = (int) apply_filters( 'et_core_api_email_hubspot_max_lists', 250 );

		$this->LISTS_URL = add_query_arg( 'count', $max_lists, $this->LISTS_URL );

		$this->response_data_key = 'lists';

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_maybe_set_urls( $args['email'] );

		$data = array(
			'properties' => array(
				array(
					'property' => 'email',
					'value'    => et_sanitized_previously( $args['email'] ),
				),
				array(
					'property' => 'firstname',
					'value'    => et_sanitized_previously( $args['name'] ),
				),
				array(
					'property' => 'lastname',
					'value'    => et_sanitized_previously( $args['last_name'] ),
				),
			),
		);

		$this->prepare_request( $this->SUBSCRIBE_URL, 'POST', false, $data, true );
		$this->make_remote_request();

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		$url  = $this->_get_list_add_contact_url( $args['list_id'] );
		$data = array(
			'emails' => array( $args['email'] ),
		);

		$this->prepare_request( $url, 'POST', false, $data, true );
		$this->make_remote_request();

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		return 'success';
	}
}
