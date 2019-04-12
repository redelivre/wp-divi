<?php

/**
 * Represent a simple value or a dynamic one.
 * Used for module attributes and content.
 *
 * @since 3.17.2
 */
class ET_Builder_Value {
	/**
	 * Flag whether the value is static or dynamic.
	 *
	 * @since 3.17.2
	 *
	 * @var bool
	 */
	protected $dynamic = false;

	/**
	 * Value content. Represents the dynamic content type when dynamic.
	 *
	 * @since 3.17.2
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Array of dynamic content settings.
	 *
	 * @since 3.17.2
	 *
	 * @var array<string, mixed>
	 */
	protected $settings = array();

	/**
	 * ET_Builder_Value constructor.
	 *
	 * @since 3.17.2
	 *
	 * @param boolean $dynamic
	 * @param string $content
	 * @param array $settings
	 */
	public function __construct( $dynamic, $content, $settings = array() ) {
		$this->dynamic = $dynamic;
		$this->content = $content;
		$this->settings = $settings;
	}

	/**
	 * Check if the value is dynamic or not.
	 *
	 * @since 3.17.2
	 *
	 * @return bool
	 */
	public function is_dynamic() {
		return $this->dynamic;
	}

	/**
	 * Get the resolved content.
	 *
	 * @since 3.17.2
	 *
	 * @param integer $post_id
	 *
	 * @return string
	 */
	public function resolve( $post_id ) {
		if ( ! $this->dynamic ) {
			return $this->content;
		}

		return et_builder_resolve_dynamic_content( $this->content, $this->settings, $post_id, 'display' );
	}

	/**
	 * Get the static content or a serialized representation of the dynamic one.
	 *
	 * @since 3.17.2
	 *
	 * @return string
	 */
	public function serialize() {
		if ( ! $this->dynamic ) {
			return $this->content;
		}

		return et_builder_serialize_dynamic_content( $this->dynamic, $this->content, $this->settings );
	}
}
