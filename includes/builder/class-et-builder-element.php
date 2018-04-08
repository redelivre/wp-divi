<?php

if ( ! defined( 'ET_BUILDER_OPTIMIZE_TEMPLATES' ) ) {
	define( 'ET_BUILDER_OPTIMIZE_TEMPLATES', true );
}
define( 'ET_BUILDER_AJAX_TEMPLATES_AMOUNT', apply_filters( 'et_pb_templates_loading_amount', ET_BUILDER_OPTIMIZE_TEMPLATES ? 20 : 10 ) );

add_action( 'init', array( 'ET_Builder_Element', 'set_media_queries' ), 11 );

require_once 'module/field/Factory.php';

/**
 * Main class used for modules.
 *
 * @since [version]
 */
class ET_Builder_Element {
	public $name;
	public $slug;
	public $type;
	public $child_slug;
	public $decode_entities;
	public $fields = array();
	public $force_unwhitelisted_fields = false;
	public $whitelisted_fields = array();
	public $fields_unprocessed = array();
	public $main_css_element;
	public $custom_css_options = array();
	public $child_title_var;
	public $child_title_fallback_var;
	public $shortcode_atts = array();
	public $shortcode_content;
	public $post_types = array();
	public $main_tabs = array();
	public $used_tabs = array();
	public $custom_css_tab;
	public $fb_support = false;
	public $dbl_quote_exception_options = array( 'et_pb_font_icon', 'et_pb_button_one_icon', 'et_pb_button_two_icon', 'et_pb_button_icon', 'et_pb_content_new' );
	public $options_toggles = array();
	public $featured_image_background = false;

	public static $settings_migrations_initialized = false;
	public static $setting_advanced_styles = false;

	private static $_current_section_index = -1;
	private static $_current_row_index     = -1;
	private static $_current_column_index  = -1;
	private static $_current_module_index  = -1;
	private static $_current_module_item_index  = -1;
	private static $_unique_bb_keys_map = array();
	private static $_unique_bb_keys_values = array();
	private static $_unique_bb_strip = array( "\t", "\r", "\n" );

	// number of times shortcode_callback function has been executed
	private $_shortcode_callback_num;

	// number of times shortcode_callback function has been executed for the shop module
	// see the returned $object in the _shortcode_passthru_callback() method
	private static $_shop_shortcode_callback_num = 0;

	// priority number, applied to some CSS rules
	protected $_style_priority;

	/**
	 * Holds module styles for the current request.
	 *
	 * @var array
	 */
	private static $styles = array();
	private static $internal_modules_styles = array();

	private static $prepare_internal_styles = false;
	private static $internal_modules_counter = 10000;
	private static $media_queries = array();
	private static $modules_order;
	private static $inner_modules_order;
	private static $parent_modules = array();
	private static $child_modules = array();
	private static $ab_tests_processed = array();
	private static $ab_tests_saved_id;
	private static $current_module_index = 0;
	private static $structure_modules = array();
	private static $structure_module_slugs = array();
	private static $_module_slugs_by_post_type = array();

	private static $loading_backbone_templates = false;

	/**
	 * @var ET_Core_PageResource
	 */
	public static $advanced_styles_manager  = null;

	/**
	 * @var ET_Core_Data_Utils
	 */
	public static $data_utils = null;

	public static $field_dependencies = array();

	public static $can_reset_shortcode_indexes = true;

	const DEFAULT_PRIORITY = 10;
	const HIDE_ON_MOBILE   = 'et-hide-mobile';

	function __construct() {
		self::$current_module_index++;

		if ( ! self::$settings_migrations_initialized ) {
			self::$settings_migrations_initialized = true;

			require_once 'module/settings/Migration.php';
			ET_Builder_Module_Settings_Migration::init();

			add_filter( 'the_content', array( get_class( $this ), 'reset_shortcode_indexes' ), 9999 );
		}

		if ( self::$loading_backbone_templates || et_admin_backbone_templates_being_loaded() ) {
			if ( ! self::$loading_backbone_templates ) {
				self::$loading_backbone_templates = true;
			}

			$start_from = (int) sanitize_text_field( $_POST['et_templates_start_from'] );
			$post_type  = sanitize_text_field( $_POST['et_post_type'] );

			if ( 'layout' === $post_type ) {
				// need - 2 to include the et_pb_section and et_pb_row modules
				$start_from = ET_Builder_Element::get_modules_count( 'page' ) - 2;
			}

			$current_module_index = self::$current_module_index - 1;

			if ( ! ( $current_module_index >= $start_from && $current_module_index < ( ET_BUILDER_AJAX_TEMPLATES_AMOUNT + $start_from ) ) ) {
				return;
			}
		}

		if ( null === self::$advanced_styles_manager && ! is_admin() && ! et_fb_is_enabled() ) {
			self::_setup_advanced_styles_manager();
		}

		if ( null === self::$data_utils ) {
			self::$data_utils = ET_Core_Data_Utils::instance();
		}

		$this->init();
		$this->make_options_filterable();

		$this->process_whitelisted_fields();
		$this->set_fields();
		$this->set_factory_objects();

		$this->_additional_fields_options = array();
		$this->_add_additional_fields();
		$this->_add_custom_css_fields();

		$this->_maybe_add_defaults();

		if ( ! isset( $this->main_css_element ) ) {
			$this->main_css_element = '%%order_class%%';
		}


		$this->_shortcode_callback_num = 0;

		$this->type = isset( $this->type ) ? $this->type : '';

		$this->decode_entities = isset( $this->decode_entities ) ? (bool) $this->decode_entities : false;

		$this->_style_priority = (int) self::DEFAULT_PRIORITY;
		if ( isset( $this->type ) && 'child' === $this->type ) {
			$this->_style_priority = $this->_style_priority + 1;
		} else {
			// add default toggles
			$default_general_toggles = array(
				'admin_label' => array(
					'title'    => esc_html__( 'Admin Label', 'et_builder' ),
					'priority' => 99,
				),
			);

			$default_advanced_toggles = array(
				'visibility' => array(
					'title'    => esc_html__( 'Visibility', 'et_builder' ),
					'priority' => 99,
				),
			);

			$this->_add_option_toggles( 'general', $default_general_toggles );
			$this->_add_option_toggles( 'custom_css', $default_advanced_toggles );
		}

		$this->main_tabs = $this->get_main_tabs();

		$this->custom_css_tab = isset( $this->custom_css_tab ) ? $this->custom_css_tab : true;

		$post_types = ! empty( $this->post_types ) ? $this->post_types : et_builder_get_builder_post_types();

		// all modules should be assigned for et_pb_layout post type to work in the library
		if ( ! in_array( 'et_pb_layout', $post_types ) ) {
			$post_types[] = 'et_pb_layout';
		}

		$this->post_types = apply_filters( 'et_builder_module_post_types', $post_types, $this->slug, $this->post_types );

		foreach ( $this->post_types as $post_type ) {
			if ( ! in_array( $post_type, $this->post_types ) ) {
				$this->register_post_type( $post_type );
			}

			if ( ! isset( self::$_module_slugs_by_post_type[ $post_type ] ) ) {
				self::$_module_slugs_by_post_type[ $post_type ] = array();
			}

			if ( ! in_array( $this->slug, self::$_module_slugs_by_post_type[ $post_type ] ) ) {
				self::$_module_slugs_by_post_type[ $post_type ][] = $this->slug;
			}

			if ( 'child' == $this->type ) {
				self::$child_modules[ $post_type ][ $this->slug ] = $this;
			} else {
				self::$parent_modules[ $post_type ][ $this->slug ] = $this;
			}
		}

		if ( ! isset( $this->no_shortcode_callback ) ) {
			$shortcode_slugs = array( $this->slug );

			if ( ! empty( $this->additional_shortcode_slugs ) ) {
				$shortcode_slugs = array_merge( $shortcode_slugs, $this->additional_shortcode_slugs );
			}

			foreach ( $shortcode_slugs as $shortcode_slug ) {
				add_shortcode( $shortcode_slug, array( $this, '_shortcode_callback' ) );
			}

			if ( isset( $this->additional_shortcode ) ) {
				add_shortcode( $this->additional_shortcode, array( $this, 'additional_shortcode_callback' ) );
			}
		}
	}

	/**
	 * Setup the advanced styles manager
	 *
	 * {@internal
	 *   Before the styles manager was implemented, the advanced styles were output inline in the footer.
	 *   That resulted in them being the last styles parsed by the browser, thus giving them higher
	 *   priority than other styles on the page. With the styles manager, the advanced styles are
	 *   enqueued at the very end of the <head>. This is for backwards compatibility (to maintain
	 *   the same priority for the styles as before).}}
	 */
	private static function _setup_advanced_styles_manager() {
		if ( et_core_page_resource_is_singular() ) {
			$post_id = et_core_page_resource_get_the_ID();
		} else {
			$post_id = 0; // It doesn't matter because we're going to force inline styles.
		}

		$is_preview       = is_preview() || is_et_pb_preview();
		$forced_in_footer = $post_id && et_builder_setting_is_on( 'et_pb_css_in_footer', $post_id );
		$forced_inline    = ! $post_id || $is_preview || $forced_in_footer || et_builder_setting_is_off( 'et_pb_static_css_file', $post_id );
		$unified_styles   = ! $forced_inline && ! $forced_in_footer;

		$resource_owner = $unified_styles ? 'core' : 'builder';
		$resource_slug  = $unified_styles ? 'unified' : 'module-design';

		// If the post is password protected and a password has not been provided yet,
		// no content (including any custom style) will be printed.
		// When static css file option is enabled this will result in missing styles.
		if ( ! $forced_inline && post_password_required() ) {
			$forced_inline = true;
		}

		if ( $is_preview ) {
			// Don't let previews cause existing saved static css files to be modified.
			$resource_slug .= '-preview';
		}

		self::$advanced_styles_manager = et_core_page_resource_get( $resource_owner, $resource_slug, $post_id, 40 );

		if ( ! $forced_inline && ! $forced_in_footer && self::$advanced_styles_manager->has_file() ) {
			// This post currently has a fully configured styles manager.
			return;
		}

		self::$advanced_styles_manager->forced_inline       = $forced_inline;
		self::$advanced_styles_manager->write_file_location = 'footer';

		if ( $forced_in_footer || $forced_inline ) {
			// Restore legacy behavior--output inline styles in the footer.
			self::$advanced_styles_manager->set_output_location( 'footer' );
		}

		// Schedule callback to run in the footer so we can pass the module design styles to the page resource.
		add_action( 'wp_footer', array( 'ET_Builder_Element', 'set_advanced_styles' ), 19 );

		// Add filter for the resource data so we can prevent theme customizer css from being
		// included with the builder css inline on first-load (since its in the head already).
		add_filter( 'et_core_page_resource_get_data', array( 'ET_Builder_Element', 'filter_page_resource_data' ), 10, 3 );
	}

	/**
	 * Passes the module design styles for the current page to the advanced styles manager.
	 * {@see 'wp_footer' (19) Must run before the style manager's footer callback}
	 */
	public static function set_advanced_styles() {
		$styles = self::get_style() . self::get_style( true );

		if ( et_core_is_builder_used_on_current_request() ) {
			$styles .= et_pb_get_page_custom_css();
		}

		if ( ! $styles ) {
			return;
		}

		// Pass styles to page resource which will handle their output
		self::$advanced_styles_manager->set_data( $styles, 40 );
	}

	/**
	 * Filters the unified page resource data. The data is an array of arrays of strings keyed by
	 * priority. The builder's styles are set with a priority of 40. Here we want to make sure
	 * only the builder's styles are output in the footer on first-page load so we aren't
	 * duplicating the customizer and custom css styles which are already in the <head>.
	 * {@see 'et_core_page_resource_get_data'}
	 */
	public static function filter_page_resource_data( $data, $context, $resource ) {
		global $wp_current_filter;

		if ( 'inline' !== $context || ! in_array( 'wp_footer', $wp_current_filter ) ) {
			return $data;
		}

		if ( false === strpos( $resource->slug, 'unified' ) ) {
			return $data;
		}

		if ( 'footer' !== $resource->location ) {
			// This is the first load of a page that doesn't currently have a unified static css file.
			// The theme customizer and custom css have already been inlined in the <head> using the
			// unified resource's ID. It's invalid HTML to have duplicated IDs on the page so we'll
			// fix that here since it only applies to this page load anyway.
			$resource->slug = $resource->slug . '-2';
		}

		return isset( $data[40] ) ? array( 40 => $data[40] ) : array();
	}

	/**
	 * Get the slugs for all current builder modules.
	 *
	 * @since 3.0.85
	 *
	 * @param string $post_type Get module slugs for this post type. If falsey, all slugs are returned.
	 *
	 * @return array
	 */
	public static function get_module_slugs_by_post_type( $post_type = 'post' ) {
		if ( $post_type && isset( self::$_module_slugs_by_post_type[ $post_type ] ) ) {
			return self::$_module_slugs_by_post_type[ $post_type ];
		}

		return self::$_module_slugs_by_post_type;
	}

	function process_whitelisted_fields() {
		$fields = array();

		// Append _builder_version to all module
		$this->whitelisted_fields[] = '_builder_version';

		$this->whitelisted_fields = apply_filters( 'et_pb_module_whitelisted_fields', $this->whitelisted_fields, $this->slug );

		foreach ( $this->whitelisted_fields as $key ) {
			$fields[ $key ] = array();
		}

		$this->whitelisted_fields = $fields;
	}

	/**
	 * Create Factory objects
	 *
	 * @return void
	 */
	function set_factory_objects() {
		// Load features fields.
		$this->text_shadow = ET_Builder_Module_Fields_Factory::get( 'TextShadow' );
	}

	/**
	 * Set $this->fields_unprocessed property to all field settings on backend.
	 * Store only default settings for use in shortcode_callback() on frontend.
	 */
	function set_fields() {
		$fields_defaults = array();
		$module_defaults = isset( $this->fields_defaults ) && is_array( $this->fields_defaults )
			? $this->fields_defaults
			: array();

		if ( ! empty( $module_defaults ) ) {
			foreach ( $module_defaults as $key => $default_setting ) {
				$setting_fields = array();

				$default_value = $module_defaults[ $key ][0];

				$use_default_value = isset( $module_defaults[ $key ][1] ) && 'add_default_setting' === $module_defaults[ $key ][1];
				$use_only_default_value = isset( $module_defaults[ $key ][1] ) && 'only_default_setting' === $module_defaults[ $key ][1];

				/**
				 * If default value is set, it should be used for "shortcode_default",
				 * unless 'only_default_setting' is set
				 */
				if ( ! $use_only_default_value ) {
					$setting_fields['shortcode_default'] = $default_value;
				}

				/**
				 * Add "default" setting and set it to the default value,
				 * if 'add_default_setting' or 'only_default_setting' is provided
				 */
				if ( $use_default_value || $use_only_default_value ) {
					$setting_fields['default'] = $default_value;
				}

				$fields_defaults[ $key ] = $setting_fields;
			}
		}

		/**
		 * Only use whitelisted fields names on frontend.
		 * All fields settings are only needed in Page Builder.
		 *
		 * Logic summary:
		 * If is_admin(): do not load only whitelisted
		 * If !is_admin(): load only whitelisted if !et_fb_is_enabled()
		 */

		$only_whitelisted_fields = is_admin() ? false : ( et_fb_is_enabled() ? false : true );

		$fields = $only_whitelisted_fields ? $this->whitelisted_fields : $this->get_fields();

		/**
		 * See self::get_all_fields();
		 */

		if ( $this->force_unwhitelisted_fields ) {
			$fields = $this->get_fields();
		}

		# update settings with defaults
		foreach ( $fields as $key => $settings ) {
			if ( ! empty( $this->defaults ) && isset( $this->defaults[ $key ] ) ) {
				$fields[ $key ]['default'] = $this->defaults[ $key ];
				continue;
			}

			if ( ! isset( $fields_defaults[ $key ] ) ) {
				continue;
			}

			$settings = array_merge( $settings, $fields_defaults[ $key ] );

			$fields[ $key ] = $settings;
		}

		// Add _builder_version field to all modules
		$fields['_builder_version'] = array( 'type' => 'skip' );

		$this->fields_unprocessed = $fields;
	}

	private function register_post_type( $post_type ) {
		$this->post_types[] = $post_type;
		self::$parent_modules[ $post_type ] = array();
		self::$child_modules[ $post_type ] = array();
	}

	/**
	 * Double quote are saved as "%22" in shortcode attributes.
	 * Decode them back into "
	 *
	 * @return void
	 */
	private function _decode_double_quotes() {
		if ( ! isset( $this->shortcode_atts ) ) {
			return;
		}

		// need to encode HTML entities in Admin Area( for BB ) if Visual Editor disabled for the user.
		$need_html_entities_decode = is_admin() && ! user_can_richedit();

		$shortcode_attributes = array();
		$font_icon_options = array( 'font_icon', 'button_icon', 'button_one_icon', 'button_two_icon', 'hover_icon' );

		foreach ( $this->shortcode_atts as $attribute_key => $attribute_value ) {
			// decode HTML entities and remove trailing and leading quote if needed
			$processed_attr_value = $need_html_entities_decode ? trim( htmlspecialchars_decode( $attribute_value, ENT_QUOTES ), '"' ) : $attribute_value;

			// the icon shortcodes are fine.
			if ( in_array( $attribute_key, $font_icon_options, true ) ) {
				$shortcode_attributes[ $attribute_key ] = $processed_attr_value;
				// icon attributes must not be str_replaced
				continue;
			}

			// URLs are weird since they can allow non-ascii characters so we escape those separately.
			if ( in_array( $attribute_key, array( 'url', 'button_link', 'button_url' ), true ) ) {
				$shortcode_attributes[ $attribute_key ] = esc_url_raw( $processed_attr_value );
			} else {
				$shortcode_attributes[ $attribute_key ] = str_replace( array( '%22', '%92', '%91', '%93' ), array( '"', '\\', '&#91;', '&#93;' ), $processed_attr_value );
			}
		}

		$this->shortcode_atts = $shortcode_attributes;
	}

	/**
	 * Provide a way for sub-class to access $this->_shortcode_callback_num without a chance to alter its value
	 *
	 * @return int
	 */
	protected function shortcode_callback_num() {
		return $this->_shortcode_callback_num;
	}

	/**
	 * check whether ab testing enabled for current module and calculate whether it should be displayed currently or not
	 *
	 * @return bool
	 */
	private function _is_display_module( $shortcode_atts ) {
		$ab_subject_id = isset( $shortcode_atts['ab_subject_id'] ) && '' !== $shortcode_atts['ab_subject_id'] ? $shortcode_atts['ab_subject_id'] : false;

		// return true if testing is disabled or current module has no subject id.
		if ( ! $ab_subject_id ) {
			return true;
		}

		return $this->_check_ab_test_subject( $ab_subject_id );
	}

	/**
	 * check whether the current module should be displayed or not
	 *
	 * @return bool
	 */
	private function _check_ab_test_subject( $ab_subject_id = false ) {
		if ( ! $ab_subject_id ) {
			return true;
		}

		$ab_subject_id = intval( $ab_subject_id );

		$test_id = apply_filters( 'et_is_ab_testing_active_post_id', get_the_ID() );

		$test_id = (int) $test_id;

		// return false if the current ab module was processed already
		if ( isset( $this->ab_tests_processed[ $test_id ] ) && $this->ab_tests_processed[ $test_id ] ) {
			return false;
		}

		$user_unique_id = et_pb_get_visitor_id();
		$saved_module_id = $this->_get_saved_ab_module_id( $test_id, $user_unique_id );

		$current_ab_module_id = et_pb_ab_get_current_ab_module_id( $test_id, $saved_module_id );

		// return false if current module is not the module which should be displayed this time
		if ( (int) $current_ab_module_id !== (int) $ab_subject_id ) {
			return false;
		}

		// If current loop is advanced styles being populated, skip it
		if ( ! self::$setting_advanced_styles ) {
			// mark current ab module as processed
			$this->ab_tests_processed[ $test_id ] = true;
		}

		// Only log a stat if this is opened on actual frontend
		if ( false === $saved_module_id && ! is_admin() && ! et_fb_enabled() ) {

			// log the view_page event right away
			et_pb_add_stats_record( array(
					'test_id'     => $test_id,
					'subject_id'  => $ab_subject_id,
					'record_type' => 'view_page',
				)
			);

			// increment the module id for the next time
			et_pb_ab_increment_current_ab_module_id( $test_id, $user_unique_id );
		}

		return true;
	}

	private function _get_saved_ab_module_id( $test_id, $client_id ) {
		if ( ! empty( $this->ab_tests_saved_id[ $test_id ] ) ) {
			return $this->ab_tests_saved_id[ $test_id ];
		}

		$saved_module_id = et_pb_ab_get_saved_ab_module_id( $test_id, $client_id );

		if ( false !== $saved_module_id ) {
			// cache the retrieved value
			$this->ab_tests_saved_id[ $test_id ] = $saved_module_id;
		}

		return $saved_module_id;
	}

	public static function reset_shortcode_indexes( $content = '' ) {
		if ( ! self::$can_reset_shortcode_indexes || ! is_main_query() ) {
			return $content;
		}

		if ( '' !== $content && false === strpos( $content, '[et_pb_' ) ) {
			// At least one builder section should be present.
			return $content;
		}

		global $wp_current_filter;

		if ( in_array( 'the_content', $wp_current_filter ) ) {
			$call_counts = array_count_values( $wp_current_filter );

			if ( $call_counts['the_content'] > 1 ) {
				// This is a nested call. We only want to reset indexes after the top-most call.
				return $content;
			}
		}

		self::$_current_section_index       = -1;
		self::$_current_row_index           = -1;
		self::$_current_column_index        = -1;
		self::$_current_module_index        = -1;
		self::$_current_module_item_index   = -1;

		return $content;
	}

	function _get_current_shortcode_address() {
		// Yuck! :-/
		if ( false !== strpos( $this->slug, '_section' ) ) {
			self::$_current_section_index++;
			self::$_current_row_index         = -1;
			self::$_current_column_index      = -1;
			self::$_current_module_index      = -1;
			self::$_current_module_item_index = -1;
		} else if ( false !== strpos( $this->slug, '_row' ) ) {
			self::$_current_row_index++;
			self::$_current_column_index      = -1;
			self::$_current_module_index      = -1;
			self::$_current_module_item_index = -1;
		} else if ( false !== strpos( $this->slug, '_column' ) ) {
			self::$_current_column_index++;
			self::$_current_module_index      = -1;
			self::$_current_module_item_index = -1;
		} else if ( 'child' === $this->type ) {
			self::$_current_module_item_index++;
		} else {
			self::$_current_module_index++;
			self::$_current_module_item_index = -1;
		}

		$address = self::$_current_section_index;
		$parts   = array( self::$_current_row_index, self::$_current_column_index, self::$_current_module_index );

		foreach ( $parts as $part ) {
			if ( $part > -1 ) {
				$address .= ".{$part}";
			}
		}

		if ( 'child' === $this->type ) {
			$address .= '.' . self::$_current_module_item_index;
		}

		return $address;
	}

	/**
	 * Resolves conditional defaults
	 *
	 * @param array $values Fields.
	 *
	 * @return array
	 */
	function resolve_conditional_defaults( $values ) {
		global $et_fb_processing_shortcode_object;

		// VB handles conditional defaults itself in settings-modal.jsx
		if ( $et_fb_processing_shortcode_object ) {
			// Shortcode trimming for conditional defaults requires them to be resolved here too.
			// I'm leaving this code in place in case trimming has to be disabled for whatever reason.
			// return $this->get_shortcode_fields();
		}

		// Resolve conditional defaults for the FE
		$resolved = $this->get_shortcode_fields( $values );
		foreach ( $resolved as $field_name => $field_default ) {
			if ( is_array( $field_default ) && ! empty( $field_default[0] ) && is_array( $field_default[1] ) ) {
				// Looks like we have a conditional default
				// Get $depend_field value or use the first default if undefined.
				list ( $depend_field, $conditional_defaults ) = $field_default;
				reset( $conditional_defaults );
				$default_key = isset( $values[ $depend_field ] ) ? $values[ $depend_field ] : key( $conditional_defaults );
				// Set the resolved default
				$resolved[ $field_name ] = isset( $conditional_defaults[ $default_key ] ) ? $conditional_defaults[ $default_key ] : null;
			}
		}
		return $resolved;
	}

	/**
	 * Callback function used for WordPress when registering the shortcode.
	 * @param  array $atts               List of attributes
	 * @param  string $content            Content being processed.
	 * @param  string $function_name      Shortcode name.
	 * @param  string $parent_address     [description]
	 * @param  string $global_parent      [description]
	 * @param  string $global_parent_type [description]
	 * @return string                     The output of the shortcode.
	 */
	function _shortcode_callback( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
		global $et_fb_processing_shortcode_object;

		$this->shortcode_atts = shortcode_atts( $this->resolve_conditional_defaults($atts), $atts );

		$this->_decode_double_quotes();

		$this->_maybe_remove_default_atts_values();

		// Some module items need to inherit value from its module parent
		// This inheritance needs to be done before migration to make it compatible with migration process
		$this->maybe_inherit_values();

		$_address = $this->_get_current_shortcode_address();

		$this->shortcode_atts = apply_filters( 'et_pb_module_shortcode_attributes', $this->shortcode_atts, $atts, $this->slug, $_address );

		$global_shortcode_content = false;

		$ab_testing_enabled = et_is_ab_testing_active();

		$hide_subject_module = false;

		$post_id = apply_filters( 'et_is_ab_testing_active_post_id', get_the_ID() );

		$global_module_id = $this->shortcode_atts['global_module'];

		// If the section/row/module is disabled, hide it
		if ( isset( $this->shortcode_atts['disabled'] ) && 'on' === $this->shortcode_atts['disabled'] && ! $et_fb_processing_shortcode_object ) {
			return;
		}

		// need to perform additional check and some modifications in case AB testing enabled
		if ( $ab_testing_enabled ) {
			// check if ab testing enabled for this module and if it shouldn't be displayed currently
			if ( ! $et_fb_processing_shortcode_object && ! $this->_is_display_module( $this->shortcode_atts ) && ! et_pb_detect_cache_plugins() ) {
				return;
			}

			// add class to the AB testing subject if needed
			if ( isset( $this->shortcode_atts['ab_subject_id'] ) && '' !== $this->shortcode_atts['ab_subject_id'] ) {
				$subject_class = sprintf( ' et_pb_ab_subject et_pb_ab_subject_id-%1$s_%2$s',
					esc_attr( $post_id ),
					esc_attr( $this->shortcode_atts['ab_subject_id'] )
				);
				$this->shortcode_atts['module_class'] = isset( $this->shortcode_atts['module_class'] ) && '' !== $this->shortcode_atts['module_class'] ? $this->shortcode_atts['module_class'] . $subject_class : $subject_class;

				if ( et_pb_detect_cache_plugins() ) {
					$hide_subject_module = true;
				}
			}

			// add class to the AB testing goal if needed
			if ( isset( $this->shortcode_atts['ab_goal'] ) && 'on' === $this->shortcode_atts['ab_goal'] ) {
				$goal_class = sprintf( ' et_pb_ab_goal et_pb_ab_goal_id-%1$s', esc_attr( $post_id ) );
				$this->shortcode_atts['module_class'] = isset( $this->shortcode_atts['module_class'] ) && '' !== $this->shortcode_atts['module_class'] ? $this->shortcode_atts['module_class'] . $goal_class : $goal_class;
			}
		}

		//override module attributes for global module. Skip that step while processing Frontend Builder object
		if ( ! empty( $global_module_id ) && ! $et_fb_processing_shortcode_object ) {
			$global_content = et_pb_load_global_module( $global_module_id );

			if ( '' !== $global_content ) {
				$unsynced_global_attributes = get_post_meta( $global_module_id, '_et_pb_excluded_global_options' );
				$use_updated_global_sync_method = ! empty( $unsynced_global_attributes );

				$unsynced_options = ! empty( $unsynced_global_attributes[0] ) ? json_decode( $unsynced_global_attributes[0], true ) : array() ;
				$content_synced = $use_updated_global_sync_method && ! in_array( 'et_pb_content_field', $unsynced_options );

				// support legacy selective sync system
				if ( ! $use_updated_global_sync_method ) {
					$content_synced = ! isset( $this->shortcode_atts['saved_tabs'] ) || false !== strpos( $this->shortcode_atts['saved_tabs'], 'general' ) || 'all' === $this->shortcode_atts['saved_tabs'];
				}

				if ( $content_synced ) {
					$global_shortcode_content = et_pb_get_global_module_content( $global_content, $function_name );
				}

				// cleanup the shortcode string to avoid the attributes messing with content
				$global_content_processed = false !== $global_shortcode_content ? str_replace( $global_shortcode_content, '', $global_content ) : $global_content;
				$global_atts = shortcode_parse_atts( et_pb_remove_shortcode_content( $global_content_processed, $this->slug ) );

				// reset module addresses because global items will be processed once again and address will be incremented wrongly
				if ( false !== strpos( $this->slug, '_section' ) ) {
					self::$_current_section_index--;
					self::$_current_row_index    = -1;
					self::$_current_column_index = -1;
					self::$_current_module_index = -1;
					self::$_current_module_item_index = -1;
				} else if ( false !== strpos( $this->slug, '_row' ) ) {
					self::$_current_row_index--;
					self::$_current_column_index = -1;
					self::$_current_module_index = -1;
					self::$_current_module_item_index = -1;
				} else {
					self::$_current_module_index--;
					self::$_current_module_item_index = -1;
				}

				foreach( $this->shortcode_atts as $single_attr => $value ) {
					if ( isset( $global_atts[$single_attr] ) && ! in_array( $single_attr, $unsynced_options ) ) {
						// replace %22 with double quotes in options to make sure it's rendered correctly
						$this->shortcode_atts[$single_attr] = is_string( $global_atts[$single_attr] ) && ! array_intersect( array( "et_pb_{$single_attr}", $single_attr ), $this->dbl_quote_exception_options ) ? str_replace( '%22', '"', $global_atts[$single_attr] ) : $global_atts[$single_attr];
					}
				}
			}
		}

		self::set_order_class( $function_name );

		$this->pre_shortcode_content();

		$content = false !== $global_shortcode_content ? $global_shortcode_content : $content;

		if ( $et_fb_processing_shortcode_object ) {
			$this->shortcode_content = et_pb_fix_shortcodes( $content, $this->decode_entities );
		} else {
			$this->shortcode_content = ! ( isset( $this->is_structure_element ) && $this->is_structure_element ) ? do_shortcode( et_pb_fix_shortcodes( $content, $this->decode_entities ) ) : '';
		}

		$this->shortcode_atts();

		$this->process_additional_options( $function_name );
		$this->process_custom_css_options( $function_name );

		// load inline fonts if needed
		if ( isset( $this->shortcode_atts['inline_fonts'] ) ) {
			$this->process_inline_fonts_option( $this->shortcode_atts['inline_fonts'] );
		}

		// Prepare shortcode for the frontend building if enabled.
		$shortcode_callback = $et_fb_processing_shortcode_object ? '_shortcode_passthru_callback' : 'shortcode_callback';

		$animation_style            = isset( $this->shortcode_atts['animation_style'] ) ? $this->shortcode_atts['animation_style'] : false;
		$animation_repeat           = isset( $this->shortcode_atts['animation_repeat'] ) ? $this->shortcode_atts['animation_repeat'] : 'once';
		$animation_direction        = isset( $this->shortcode_atts['animation_direction'] ) ? $this->shortcode_atts['animation_direction'] : 'center';
		$animation_duration         = isset( $this->shortcode_atts['animation_duration'] ) ? $this->shortcode_atts['animation_duration'] : '500ms';
		$animation_delay            = isset( $this->shortcode_atts['animation_delay'] ) ? $this->shortcode_atts['animation_delay'] : '0ms';
		$animation_intensity        = isset( $this->shortcode_atts["animation_intensity_{$animation_style }"] ) ? $this->shortcode_atts["animation_intensity_{$animation_style }"] : '50%';
		$animation_starting_opacity = isset( $this->shortcode_atts['animation_starting_opacity'] ) ? $this->shortcode_atts['animation_starting_opacity'] : '0%';
		$animation_speed_curve      = isset( $this->shortcode_atts['animation_speed_curve'] ) ? $this->shortcode_atts['animation_speed_curve'] : 'ease-in-out';

		// Check if this is an AJAX request since this is how VB loads the initial module data
		// et_fb_enabled() always returns `false` here
		if ( $animation_style && 'none' !== $animation_style && ! wp_doing_ajax() ) {
			// Fade doesn't have direction
			if ( 'fade' === $animation_style ) {
				$animation_direction = '';
			}

			if ( in_array( $animation_direction, array( 'top', 'right', 'bottom', 'left' ) ) ) {
				$animation_style .= ucfirst( $animation_direction );
			}

			$module_class = ET_Builder_Element::get_module_order_class( $function_name );

			if ( $module_class ) {
				et_builder_handle_animation_data( array(
					'class'            => trim( $module_class ),
					'style'            => $animation_style,
					'repeat'           => $animation_repeat,
					'duration'         => $animation_duration,
					'delay'            => $animation_delay,
					'intensity'        => $animation_intensity,
					'starting_opacity' => $animation_starting_opacity,
					'speed_curve'      => $animation_speed_curve,
				) );
			}

			$this->shortcode_atts['module_class'] = empty( $this->shortcode_atts['module_class'] ) ? 'et_animated' : $this->shortcode_atts['module_class'] . ' et_animated';
		}

		$output = $this->{$shortcode_callback}( $atts, $content, $function_name, $parent_address, $global_parent, $global_parent_type );

		/**
		 * Filters builder module shortcode output. The dynamic portion of the filter name, `$function_name`,
		 * refers to the slug of the module for which the shortcode output was generated.
		 *
		 * @since 3.0.87
		 *
		 * @param string $output
		 * @param string $module_slug
		 */
		$output = apply_filters( "{$function_name}_shortcode_output", $output, $function_name );

		$this->_shortcode_callback_num++;

		// Hide module on specific screens if needed
		if ( isset( $this->shortcode_atts['disabled_on'] ) && '' !== $this->shortcode_atts['disabled_on'] ) {
			$disabled_on_array = explode( '|', $this->shortcode_atts['disabled_on'] );
			$i = 0;
			$current_media_query = 'max_width_767';

			foreach( $disabled_on_array as $value ) {
				if ( 'on' === $value ) {
					ET_Builder_Module::set_style( $function_name, array(
						'selector'    => '%%order_class%%',
						'declaration' => 'display: none !important;',
						'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
					) );
				}
				$i++;
				$current_media_query = 1 === $i ? '768_980' : 'min_width_981';
			}
		}

		if ( $hide_subject_module ) {
			$previous_subjects_cache = get_post_meta( $post_id, 'et_pb_subjects_cache', true );

			if ( empty( $previous_subjects_cache ) ) {
				$previous_subjects_cache = array();
			}

			if ( empty( $this->template_name ) ) {
				$previous_subjects_cache[ $this->shortcode_atts['ab_subject_id'] ] = $output;
			} else {
				$previous_subjects_cache[ $this->shortcode_atts['ab_subject_id'] ] = $this->shortcode_output();
			}

			// update the subjects cache in post meta to use it later
			update_post_meta( $post_id, 'et_pb_subjects_cache', $previous_subjects_cache );

			// generate the placeholder to output on front-end instead of actual content
			$subject_placeholder = sprintf( '<div class="et_pb_subject_placeholder et_pb_subject_placeholder_id_%1$s" style="display: none;"></div>', esc_attr( $this->shortcode_atts['ab_subject_id'] ) );

			return $subject_placeholder;
		}

		if ( empty( $this->template_name ) ) {
			return $output;
		}

		return $this->shortcode_output();
	}

	/**
	 * Delete default shortcode attribute values, defined in ET_Global_Settings class
	 * @return void
	 */
	private function _maybe_remove_default_atts_values() {
		$fields = $this->fields_unprocessed;

		// Non stylesheet attributes should've been passed
		$must_print_fields = apply_filters( $this->slug . '_must_print_attributes', array( 'text_orientation' ) );

		foreach ( $fields as $field_key => $field_settings ) {
			$global_setting_name  = $this->get_global_setting_name( $field_key );
			$global_setting_value = ET_Global_Settings::get_value( $global_setting_name );
			$shortcode_attr_value = ! empty( $this->shortcode_atts[ $field_key ] ) ? $this->shortcode_atts[ $field_key ] : '';

			// Don't do anything if there is no default or actual value for a setting
			// or shortcode attribute is no set
			if ( ! $global_setting_value || '' === $shortcode_attr_value ) {
				continue;
			}

			// Delete shortcode attribute value if it equals to the default global value
			if ( ! in_array( $field_key, $must_print_fields ) && $global_setting_value === $shortcode_attr_value ) {
				$this->shortcode_atts[ $field_key ] = '';
			}
		}
	}

	// intended to be overridden as needed
	function maybe_inherit_values(){}

	function shortcode_output() {
		$this->shortcode_atts['content'] = $this->shortcode_content;
		extract( $this->shortcode_atts );
		ob_start();
		require( locate_template( $this->template_name . '.php' ) );
		return ob_get_clean();
	}

	function shortcode_atts_to_data_atts( $atts = array() ) {
		if ( empty( $atts ) ) {
			return;
		}

		$output = array();
		foreach ( $atts as $attr ) {
			$output[] = 'data-' . esc_attr( $attr ) . '="' . esc_attr( $this->shortcode_atts[ $attr ] ) . '"';
		}

		return implode( ' ', $output );
	}

	// intended to be overridden as needed
	function shortcode_atts(){}

	// intended to be overridden as needed
	function pre_shortcode_content(){}

	// intended to be overridden as needed
	function shortcode_callback( $atts, $content = null, $function_name ){}

	function _shortcode_passthru_callback( $atts, $content = null, $function_name, $parent_address = '', $global_parent = '', $global_parent_type = '' ) {
		global $post;

		// this is called during pageload, but we want to ignore that round, as this data will be built and returned on separate ajax request instead
		if ( ! isset( $_POST['action'] ) ) {
			return false;
		}

		$attrs = array();
		$fields = $this->process_fields( $this->fields_unprocessed );
		$global_shortcode_content = false;
		$function_name_processed = et_fb_prepare_tag( $function_name );
		$unsynced_global_attributes = array();
		$use_updated_global_sync_method = false;
		$global_module_id = isset( $atts['global_module'] ) ? $atts['global_module'] : false;
		$is_global_template = false;

		// Add support of new selective sync feature for library modules in VB
		if ( isset( $_POST['et_post_type'], $_POST['et_post_id'], $_POST['et_layout_type'] ) && 'et_pb_layout' === $_POST['et_post_type'] && 'module' === $_POST['et_layout_type'] ) {
			$template_scope = wp_get_object_terms( $_POST['et_post_id'], 'scope' );
			$is_global_template = ! empty( $template_scope[0] ) && 'global' === $template_scope[0]->slug;

			if ( $is_global_template ) {
				$global_module_id = $_POST['et_post_id'];
			}
		}

		//override module attributes for global module
		if ( ! empty( $global_module_id ) ) {
			if ( ! in_array( $function_name, array( 'et_pb_section', 'et_pb_row', 'et_pb_row_inner', 'et_pb_column', 'et_pb_column_inner' ) ) ) {
				$processing_global_module = $global_module_id;
				$unsynced_global_attributes = get_post_meta( $processing_global_module, '_et_pb_excluded_global_options' );
				$use_updated_global_sync_method = ! empty( $unsynced_global_attributes );
			}

			$global_content = et_pb_load_global_module( $global_module_id );

			if ( '' !== $global_content ) {
				$unsynced_options = ! empty( $unsynced_global_attributes[0] ) ? json_decode( $unsynced_global_attributes[0], true ) : array() ;
				$content_synced = $use_updated_global_sync_method && ! in_array( 'et_pb_content_field', $unsynced_options );
				$is_module_fully_global = $use_updated_global_sync_method && empty( $unsynced_options );
				$unsynced_legacy_options = array();

				// support legacy selective sync system
				if ( ! $use_updated_global_sync_method ) {
					$content_synced = ! isset( $atts['saved_tabs'] ) || false !== strpos( $atts['saved_tabs'], 'general' ) || 'all' === $atts['saved_tabs'];
					$is_module_fully_global = ! isset( $atts['saved_tabs'] ) || 'all' === $atts['saved_tabs'];
				}

				if ( $content_synced && ! $is_global_template ) {
					$global_shortcode_content = et_pb_get_global_module_content( $global_content, $function_name_processed );
				}

				// remove the shortcode content to avoid conflicts of parent attributes with similar attrs from child modules
				if ( false !== $global_shortcode_content ) {
					$global_content_processed = str_replace( $global_shortcode_content, '', $global_content );
				} else {
					$global_content_processed = $global_content;
				}

				// Ensuring that all possible attributes exist to avoid remaining child attributes being used by global parents' attributes
				// Do that only in case the module is fully global
				if ( $is_module_fully_global ) {
					$global_atts = wp_parse_args(
						shortcode_parse_atts( et_pb_remove_shortcode_content( $global_content_processed, $this->slug ) ),
						array_map( '__return_empty_string', $this->whitelisted_fields )
					);
				} else {
					$global_atts = shortcode_parse_atts( $global_content_processed );
				}

				// Run et_pb_module_shortcode_attributes filter to apply migration system on attributes of global module
				$global_atts = apply_filters( 'et_pb_module_shortcode_attributes', $global_atts, $atts, $this->slug, $this->_get_current_shortcode_address() );

				foreach( $this->shortcode_atts as $single_attr => $value ) {
					if ( isset( $global_atts[$single_attr] ) && ! in_array( $single_attr, $unsynced_options ) ) {
						// replace %22 with double quotes in options to make sure it's rendered correctly
						if ( ! $is_global_template ) {
							$this->shortcode_atts[$single_attr] = is_string( $global_atts[$single_attr] ) && ! array_intersect( array( "et_pb_{$single_attr}", $single_attr ), $this->dbl_quote_exception_options ) ? str_replace( '%22', '"', $global_atts[$single_attr] ) : $global_atts[$single_attr];
						}
					} else if ( ! $use_updated_global_sync_method ) {
						// prepare array of unsynced options to migrate the legacy modules to new system
						$unsynced_legacy_options[] = $single_attr;
					} else {
						$unsynced_global_attributes[0] = $unsynced_options;
					}
				}

				// migrate unsynced options to the new selective sync method
				if ( ! $use_updated_global_sync_method ) {
					$unsynced_global_attributes[0] = $unsynced_legacy_options;

					// check the content and add it into list if needed.
					if ( ! $content_synced ) {
						$unsynced_global_attributes[0][] = 'et_pb_content_field';
					}
				} else {
					$unsynced_global_attributes[0] = $unsynced_options;
				}
			} else {
				// remove global_module attr if it doesn't exist in DB
				$this->shortcode_atts['global_module'] = '';
				$global_parent = '';
			}
		}

		foreach( $this->shortcode_atts as $shortcode_attr_key => $shortcode_attr_value ) {
			if ( isset( $fields[ $shortcode_attr_key ]['type'] ) && 'computed' === $fields[ $shortcode_attr_key ]['type'] ) {

				$field = $fields[ $shortcode_attr_key ];
				$depends_on = array();

				if ( isset( $field['computed_depends_on'] ) ) {
					foreach ( $field['computed_depends_on'] as $depends_on_field ) {
						$dependency_value = $this->shortcode_atts[ $depends_on_field ];

						if ( '' === $dependency_value ) {
							if ( isset( $this->fields_unprocessed[ $depends_on_field]['default'] ) ) {
								$dependency_value = $this->fields_unprocessed[ $depends_on_field ]['default'];
							}

							if ( isset( $this->fields_unprocessed[ $depends_on_field]['shortcode_default'] ) ) {
								$dependency_value = $this->fields_unprocessed[ $depends_on_field ]['shortcode_default'];
							}
						}

						$depends_on[ $depends_on_field ] = $dependency_value;
					}
				}

				if ( isset( $field['computed_variables'] ) ) {
					$depends_on['computed_variables'] = $field['computed_variables'];
				}

				if ( ! is_callable( $field['computed_callback'] ) ) {
					die( $shortcode_attr_key . ' Callback:' . $field['computed_callback'] . ' is not callable.... '); // TODO, fix this make it more graceful...
				}

				$value = call_user_func( $field['computed_callback'], $depends_on );
			} else {
				$value = $shortcode_attr_value;
			}

			// dont set the default, unless, lol, the value is literally 'default'
			if ( isset( $fields[ $shortcode_attr_key ]['default'] ) && $value === $fields[ $shortcode_attr_key ]['default'] && $value !== 'default' ) {
				$value = '';
			}

			// dont set the default, unless, lol, the value is literally 'default'
			if ( isset( $fields[ $shortcode_attr_key ]['shortcode_default'] ) && $value === $fields[ $shortcode_attr_key ]['shortcode_default'] && $value !== 'default' ) {
				$value = '';
			}

			// dont set the default, unless, lol, the value is literally 'default'
			if ( isset( $this->fields_defaults[ $shortcode_attr_key ] ) && $value === $this->fields_defaults[ $shortcode_attr_key ][0] && $value !== 'default' ) {
				$value = '';
			}

			// generic override, disabled=off is an unspoken default
			if ( $shortcode_attr_key === 'disabled' && $shortcode_attr_value === 'off' ) {
				$value = '';
			}

			// this override is necessary becuase et_pb_column and et_pb_column_inner type default is 4_4 and will get stomped
			// above since its default, but we need it explicitly set anyways, so we force set it
			if ( in_array( $function_name, array( 'et_pb_column', 'et_pb_column_inner' ) ) && $shortcode_attr_key === 'type' ) {
				$value = $shortcode_attr_value;
			}

			if ( '' !== $value ) {
				$attrs[$shortcode_attr_key] = is_string($value) ? html_entity_decode($value) : $value;
			}
		}

		// Format FB component path
		// TODO, move this to class method and property, and allow both to be overridden
		$component_path = str_replace( 'et_pb_' , '', $function_name_processed );
		$component_path = str_replace( '_', '-', $component_path );
		$component_path = $component_path;

		$_i = isset( $atts['_i'] ) ? $atts['_i'] : 0;
		$address = isset( $atts['_address'] ) ? $atts['_address'] : '0';

		// set the global parent if exists
		if ( ( ! isset( $attrs['global_module'] ) || '' === $attrs['global_module'] ) && '' !== $global_parent ) {
			$attrs['global_parent'] = $global_parent;
		}

		if ( isset( $this->is_structure_element ) && $this->is_structure_element ) {
			$this->fb_support = true;
		}

		if ( ! $this->fb_support ) {
			global $et_fb_processing_shortcode_object;
			$et_fb_processing_shortcode_object = false;
			$raw_child_content = $content;
			$content = $this->_shortcode_callback( $atts, $content, $function_name_processed );
			$executed_shortcode = do_shortcode( $content );
			$processed_content = false !== $global_shortcode_content ? $global_shortcode_content : $this->shortcode_content;
			$attrs['content_new'] = array_key_exists( 'content_new', $this->whitelisted_fields ) ? $processed_content : et_fb_process_shortcode( $processed_content, $address, $global_parent, $global_parent_type );
			$et_fb_processing_shortcode_object = true;
		} else {
			$processed_content = false !== $global_shortcode_content ? $global_shortcode_content : $this->shortcode_content;
			$content = array_key_exists( 'content_new', $this->whitelisted_fields ) || 'et_pb_code' === $function_name_processed || 'et_pb_fullwidth_code' === $function_name_processed ? $processed_content : et_fb_process_shortcode( $processed_content, $address, $global_parent, $global_parent_type );
		}

		$prepared_content = $content;

		if ( ! is_array( $content ) && ! ( $this->fb_support && count( preg_split('/\r\n*\n/', trim( $content ), -1, PREG_SPLIT_NO_EMPTY ) ) > 1 ) ) {
			$prepared_content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');
		}

		if ( empty( $attrs ) ) {
			// Visual Builder expects $attrs to be an object.
			// Associative array converted to an object by json_encode correctly, but empty array is not and it causes issues.
			$attrs = new stdClass();
		}

		$module_type = $this->type;

		// Ensuring that module which uses another module's template (i.e. accordion item uses toggle's
		// component) has correct $this->type value. This is covered on front-end, but it causes inheriting
		// module uses its template's value on _shortcode_passthru_callback()
		if ( $this->slug !== $function_name && isset( $_POST ) && isset( $_POST['et_post_type'] ) ) {
			$et_post_type = $_POST['et_post_type'];
			$parent_modules = self::get_parent_modules( $et_post_type);
			$function_module = false;

			if ( isset( $parent_modules[ $function_name ] ) ) {
				$function_module = $parent_modules[ $function_name ];
			} else {
				$child_modules = self::get_child_modules( $et_post_type );

				if ( isset( $child_modules[ $function_name] ) ) {
					$function_module = $child_modules[ $function_name ];
				}
			}

			if ( $function_module && isset( $function_module->type ) ) {
				$module_type = $function_module->type;
			}
		}

		// Get the current shortcode index
		$shortcode_index = $this->_shortcode_callback_num;

		// If this is a shop module use the Shop module shortcode index
		// Shop module creates a new class instance which resets the $_shortcode_callback_num value
		// ( see get_shop_html() method of ET_Builder_Module_Shop class in main-modules.php )
		// so we use a static property to track its proper shortcode index
		if ( 'et_pb_shop' === $function_name ) {
			$shortcode_index = self::$_shop_shortcode_callback_num;
			self::$_shop_shortcode_callback_num++;
		}

		// Build object.
		$object = array(
			'_i'                          => $_i,
			'_order'                      => $_i,
			// TODO make address be _address, its conflicting with 'address' prop in map module... (not sure how though, they are in diffent places...)
			'address'                     => $address,
			'child_slug'                  => $this->child_slug,
			'fb_support'                  => $this->fb_support,
			'parent_address'              => $parent_address,
			'shortcode_index'             => $shortcode_index,
			'type'                        => $function_name,
			'component_path'              => $component_path,
			'main_css_element'            => $this->main_css_element,
			'attrs'                       => $attrs,
			'content'                     => $prepared_content,
			'is_module_child'             => 'child' === $module_type,
			'prepared_styles'             => ! $this->fb_support ? ET_Builder_Element::get_style() : '',
			'child_title_var'             => isset( $this->child_title_var ) ? $this->child_title_var : '',
			'child_title_fallback_var'    => isset( $this->child_title_fallback_var ) ? $this->child_title_fallback_var : '',
			'advanced_setting_title_text' => isset( $this->advanced_setting_title_text ) ? $this->advanced_setting_title_text : '',
		);

		if ( ! empty( $unsynced_global_attributes ) ) {
			$object['unsyncedGlobalSettings'] = $unsynced_global_attributes[0];
		}

		if ( $is_global_template ) {
			$object['libraryModuleScope'] = 'global';
		}

		if ( ! $this->fb_support ) {
			if ( !empty( $this->child_slug ) ) {
				$object['raw_child_content'] = $raw_child_content;
			}
		}

		return $object;
	}

	// intended to be overridden as needed
	function additional_shortcode_callback( $atts, $content = null, $function_name ){}

	// intended to be overridden as needed
	function predefined_child_modules(){}

	/**
	 * Generate global setting name
	 *
	 * @param  string $option_slug Option slug
	 *
	 * @return string               Global setting name in the following format: "module_slug-option_slug"
	 */
	public function get_global_setting_name( $option_slug ) {
		$global_setting_name = sprintf(
			'%1$s-%2$s',
			isset( $this->global_settings_slug ) ? $this->global_settings_slug : $this->slug,
			$option_slug
		);

		return $global_setting_name;
	}

	/**
	 * Add global default values to all fields, if they don't have defaults set
	 *
	 * @return void
	 */
	private function _maybe_add_defaults() {
		// Don't add default settings to "child" modules
		if ( 'child' === $this->type ) {
			return;
		}

		$fields       = $this->fields_unprocessed;
		$ignored_keys = array(
			'custom_margin',
			'custom_padding',
		);

		// Font color settings have custom_color set to true, so add them to ingored keys array
		if ( ! empty( $this->advanced_options['fonts'] ) && is_array( $this->advanced_options['fonts'] ) ) {
			foreach ( $this->advanced_options['fonts'] as $font_key => $font_settings ) {
				$ignored_keys[] = sprintf( '%1$s_text_color', $font_key );
			}
		}

		$ignored_keys = apply_filters( 'et_builder_add_defaults_ignored_keys', $ignored_keys );

		foreach ( $fields as $field_key => $field_settings ) {
			if ( in_array( $field_key, $ignored_keys ) ) {
				continue;
			}

			$global_setting_name  = $this->get_global_setting_name( $field_key );
			$global_setting_value = ET_Global_Settings::get_value( $global_setting_name );

			if ( ! isset( $field_settings['default'] ) && $global_setting_value ) {
				$fields[ $field_key ]['default'] = $fields[ $field_key ]['shortcode_default'] = $global_setting_value;
			}
		}

		$this->fields_unprocessed = $fields;
	}

	private function _add_additional_fields() {
		if ( isset( $this->advanced_options ) ) {
			$this->_add_additional_font_fields();

			$this->_add_additional_background_fields();

			$this->_add_additional_border_fields();

			$this->_add_additional_text_fields();

			$this->_add_additional_max_width_fields();

			$this->_add_additional_custom_margin_padding_fields();

			$this->_add_additional_button_fields();

			// Add filter fields to modules if requested
			$this->_add_additional_filter_fields();

			// Add divider fields to section modules.
			$this->_add_additional_divider_fields();
		}

		// Add animation fields to all modules
		$this->_add_additional_animation_fields();

		$this->_add_additional_shadow_fields();

		// Add text shadow fields to all modules
		$this->_add_additional_text_shadow_fields();

		if ( ! isset( $this->_additional_fields_options ) ) {
			return false;
		}

		$additional_options = $this->_additional_fields_options;

		if ( ! empty( $additional_options ) ) {
			// delete second level advanced options default values
			if ( isset( $this->type ) && 'child' === $this->type && apply_filters( 'et_pb_remove_child_module_defaults', true ) ) {
				$default_keys = array( 'default', 'shortcode_default' );

				foreach ( $additional_options as $name => $settings ) {
					foreach ( $default_keys as $default_key ) {
						if ( isset( $additional_options[ $name ][ $default_key ] ) && ! isset( $additional_options[ $name ]['default_on_child'] ) ) {
							$additional_options[ $name ][ $default_key ] = '';
						}
					}
				}
			}

			$this->fields_unprocessed = array_merge( $this->fields_unprocessed, $additional_options );
		}
	}

	private function _add_additional_font_fields() {
		if ( ! isset( $this->advanced_options['fonts'] ) ) {
			return;
		}

		$advanced_font_options = $this->advanced_options['fonts'];

		$additional_options = array();
		$defaults = array(
			'all_caps' => 'off',
		);

		foreach ( $advanced_font_options as $option_name => $option_settings ) {
			$advanced_font_options[ $option_name ]['defaults'] = $defaults;
		}

		$this->advanced_options['fonts'] = $advanced_font_options;
		$font_options_count = 0;

		foreach ( $advanced_font_options as $option_name => $option_settings ) {
			$font_options_count++;

			$option_settings = wp_parse_args( $option_settings, array(
				'label'          => '',
				'font_size'      => array(),
				'letter_spacing' => array(),
				'font'           => array(),
				'text_align'     => array(),
			) );

			$toggle_disabled = isset( $option_settings['disable_toggle'] ) && $option_settings['disable_toggle'];
			$tab_slug = isset( $option_settings['tab_slug'] ) ? $option_settings['tab_slug'] : 'advanced';
			$toggle_slug = '';

			if ( ! $toggle_disabled ) {
				$toggle_slug = isset( $option_settings['toggle_slug'] ) ? $option_settings['toggle_slug'] : $option_name;
				$sub_toggle = isset( $option_settings['sub_toggle'] ) ? $option_settings['sub_toggle'] : '';

				if ( ! isset( $option_settings['toggle_slug'] ) ) {
					$font_toggle = array(
						$option_name => array(
							'title'    => sprintf( '%1$s %2$s', esc_html( $option_settings['label'] ), esc_html__( 'Text', 'et_builder' ) ),
							'priority' => 50 + $font_options_count,
						),
					);

					$this->_add_option_toggles( $tab_slug, $font_toggle );
				}
			}

			if ( isset( $option_settings['header_level'] ) ) {
				$additional_options["{$option_name}_level"] = array(
					'label'            => sprintf( esc_html__( '%1$s Heading Level', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'multiple_buttons',
					'option_category' => 'font_option',
					'options'         => array(
						'h1' => array( 'title' => 'H1', 'icon' => 'text-h1', ),
						'h2' => array( 'title' => 'H2', 'icon' => 'text-h2', ),
						'h3' => array( 'title' => 'H3', 'icon' => 'text-h3', ),
						'h4' => array( 'title' => 'H4', 'icon' => 'text-h4', ),
						'h5' => array( 'title' => 'H5', 'icon' => 'text-h5', ),
						'h6' => array( 'title' => 'H6', 'icon' => 'text-h6', ),
					),
					'default'          => isset( $option_settings['header_level']['default'] ) ? $option_settings['header_level']['default'] : 'h2',
					'tab_slug'         => $tab_slug,
					'toggle_slug'      => $toggle_slug,
					'sub_toggle'       => $sub_toggle,
					'advanced_options' => true,
				);

				if ( isset( $option_settings['header_level']['computed_affects'] ) ) {
					$additional_options["{$option_name}_level"]['computed_affects'] = $option_settings['header_level']['computed_affects'];
				}
			}

			if ( ! isset( $option_settings['hide_font'] ) || ! $option_settings['hide_font'] ) {
				$additional_options["{$option_name}_font"] = wp_parse_args( $option_settings['font'], array(
					'label'           => sprintf( esc_html__( '%1$s Font', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'font',
					'group_label'     => esc_html__( $option_settings['label'] ),
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $toggle_slug,
					'sub_toggle'      => $sub_toggle,
				) );

				// add reference to the obsolete "all caps" option if needed
				if ( isset( $option_settings['use_all_caps'] ) && $option_settings['use_all_caps'] ) {
					$additional_options["{$option_name}_font"]['attributes'] = array( 'data-old-option-ref' => "{$option_name}_all_caps" );
				}

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_font"]['depends_show_if'] = $option_settings['depends_show_if'];
				}
			}

			if ( ! isset( $option_settings['hide_text_align'] ) || ! $option_settings['hide_text_align'] ) {
				$additional_options["{$option_name}_text_align"] = wp_parse_args( $option_settings['text_align'], array(
					'label'            => sprintf( esc_html__( '%1$s Text Alignment', 'et_builder' ), $option_settings['label'] ),
					'type'             => 'text_align',
					'option_category'  => 'layout',
					'options'          => et_builder_get_text_orientation_options( array( 'justified' ), array( 'justify' => 'Justified' ) ),
					'tab_slug'         => $tab_slug,
					'toggle_slug'      => $toggle_slug,
					'sub_toggle'       => $sub_toggle,
					'advanced_options' => true,
				) );
			}

			if ( ! isset( $option_settings['hide_font_size'] ) || ! $option_settings['hide_font_size'] ) {
				$additional_options["{$option_name}_font_size"] = wp_parse_args( $option_settings['font_size'], array(
					'label'           => sprintf( esc_html__( '%1$s Text Size', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $toggle_slug,
					'sub_toggle'      => $sub_toggle,
					'mobile_options'  => true,
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					),
				) );

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_font_size"]['depends_show_if'] = $option_settings['depends_show_if'];
				}

				if ( isset( $option_settings['header_level'] ) ) {
					$header_level_default = isset( $option_settings['header_level']['default'] ) ? $option_settings['header_level']['default'] : 'h2';

					$additional_options["{$option_name}_font_size"]['default_value_depends'] = "{$option_name}_level";
					$additional_options["{$option_name}_font_size"]['default_values_mapping'] = array(
						'h1' => '30px',
						'h2' => '26px',
						'h3' => '22px',
						'h4' => '18px',
						'h5' => '16px',
						'h6' => '14px',
					);

					// remove default font-size for default header level to use option default
					unset( $additional_options["{$option_name}_font_size"]['default_values_mapping'][ $header_level_default ] );
				}

				$additional_options["{$option_name}_font_size_tablet"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
				$additional_options["{$option_name}_font_size_phone"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
				$additional_options["{$option_name}_font_size_last_edited"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
			}

			$additional_options["{$option_name}_text_color"] = array(
				'label'           => sprintf( esc_html__( '%1$s Text Color', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'color-alpha',
				'option_category' => 'font_option',
				'custom_color'    => true,
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'sub_toggle'      => $sub_toggle,
			);

			if ( ! isset( $option_settings['hide_text_color'] ) || ! $option_settings['hide_text_color'] ) {
				$additional_options["{$option_name}_text_color"] = array(
					'label'           => sprintf( esc_html__( '%1$s Text Color', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'color-alpha',
					'option_category' => 'font_option',
					'custom_color'    => true,
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $toggle_slug,
					'sub_toggle'      => $sub_toggle,
				);

				// add reference to the obsolete color option if needed
				if ( isset( $option_settings['text_color'] ) && isset( $option_settings['text_color']['old_option_ref'] ) ) {
					$additional_options["{$option_name}_text_color"]['attributes'] = array( 'data-old-option-ref' => "{$option_settings['text_color']['old_option_ref']}" );
				}

				// set default value if defined
				if ( isset( $option_settings['text_color'] ) && isset( $option_settings['text_color']['default'] ) ) {
					$additional_options["{$option_name}_text_color"]['default'] = $option_settings['text_color']['default'];
				}

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_text_color"]['depends_show_if'] = $option_settings['depends_show_if'];
				}
			}

			if ( ! isset( $option_settings['hide_letter_spacing'] ) || ! $option_settings['hide_letter_spacing'] ) {
				$additional_options["{$option_name}_letter_spacing"] = wp_parse_args( $option_settings['letter_spacing'], array(
					'label'           => sprintf( esc_html__( '%1$s Letter Spacing', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'mobile_options'  => true,
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $toggle_slug,
					'sub_toggle'      => $sub_toggle,
					'default'         => '0px',
					'range_settings'  => array(
						'min'  => '0',
						'max'  => '100',
						'step' => '1',
					),
				) );

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_letter_spacing"]['depends_show_if'] = $option_settings['depends_show_if'];
				}

				$additional_options["{$option_name}_letter_spacing_tablet"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
				$additional_options["{$option_name}_letter_spacing_phone"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
				$additional_options["{$option_name}_letter_spacing_last_edited"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
			}

			if ( ! isset( $option_settings['hide_line_height'] ) || ! $option_settings['hide_line_height'] ) {
				$default_option_line_height = array(
					'label'           => sprintf( esc_html__( '%1$s Line Height', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'range',
					'mobile_options'  => true,
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $toggle_slug,
					'sub_toggle'      => $sub_toggle,
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '3',
						'step' => '0.1',
					),
				);

				if ( isset( $option_settings['line_height'] ) ) {
					$additional_options["{$option_name}_line_height"] = wp_parse_args(
						$option_settings['line_height'],
						$default_option_line_height
					);
				} else {
					$additional_options["{$option_name}_line_height"] = $default_option_line_height;
				}

				// set the depends_show_if parameter if needed
				if ( isset( $option_settings['depends_show_if'] ) ) {
					$additional_options["{$option_name}_line_height"]['depends_show_if'] = $option_settings['depends_show_if'];
				}

				$additional_options["{$option_name}_line_height_tablet"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
				$additional_options["{$option_name}_line_height_phone"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
				$additional_options["{$option_name}_line_height_last_edited"] = array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				);
			}

			// Add text-shadow to font options
			if ( ! isset( $option_settings['hide_text_shadow'] ) || ! $option_settings['hide_text_shadow'] ) {
				$option = $this->text_shadow->get_fields(array(
					// Don't use an additional label for 'text' or else we'll end up with 'Text Text Shadow....'
					'label'           => 'text' === $option_name ? '' : $option_settings['label'],
					'prefix'          => $option_name,
					'option_category' => 'font_option',
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $toggle_slug,
					'sub_toggle'      => $sub_toggle,
				));
				$additional_options = array_merge( $additional_options, $option );
			};

			// The below option is obsolete. This code is for backward compatibility
			if ( isset( $option_settings['use_all_caps'] ) && $option_settings['use_all_caps'] ) {
				$additional_options["{$option_name}_all_caps"] = array(
					'type'              => 'hidden',
					'shortcode_default' => '',
					'tab_slug'          => $tab_slug,
					'toggle_slug'       => $toggle_slug,
					'sub_toggle'        => $sub_toggle,
				);
			}
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_background_fields() {
		if ( ! isset( $this->advanced_options['background'] ) ) {
			return;
		}

		$toggle_disabled = isset( $this->advanced_options['background']['settings']['disable_toggle'] ) && $this->advanced_options['background']['settings']['disable_toggle'];
		$tab_slug = isset( $this->advanced_options['background']['settings']['tab_slug'] ) ? $this->advanced_options['background']['settings']['tab_slug'] : 'general';
		$toggle_slug = '';

		if ( ! $toggle_disabled ) {
			$toggle_slug = isset( $this->advanced_options['background']['settings']['tab_slug'] ) ? $this->advanced_options['background']['settings']['tab_slug'] : 'background';

			$background_toggle = array(
				'background' => array(
					'title'    => esc_html__( 'Background', 'et_builder' ),
					'priority' => 80,
				),
			);

			$this->_add_option_toggles( $tab_slug, $background_toggle );
		}

		// Possible values for use_* attributes: true, false, or 'fields_only'
		$defaults = array(
			'use_background_color'          => true,
			'use_background_color_gradient' => true,
			'use_background_image'          => true,
			'use_background_video'          => true,
		);
		$this->advanced_options['background'] = wp_parse_args( $this->advanced_options['background'], $defaults );

		$additional_options = array();

		if ( $this->advanced_options['background']['use_background_color'] ) {
			$additional_options = array_merge(
				$additional_options,
				$this->generate_background_options( 'background', 'color', $tab_slug, $toggle_slug )
			);
		}

		if ( $this->advanced_options['background']['use_background_color_gradient'] ) {
			$additional_options = array_merge(
				$additional_options,
				$this->generate_background_options( 'background', 'gradient', $tab_slug, $toggle_slug )
			);
		}

		if ( $this->advanced_options['background']['use_background_image'] ) {
			$additional_options = array_merge(
				$additional_options,
				$this->generate_background_options( 'background', 'image', $tab_slug, $toggle_slug )
			);
		}

		if ( $this->advanced_options['background']['use_background_video'] ) {
			$additional_options = array_merge(
				$additional_options,
				$this->generate_background_options( 'background', 'video', $tab_slug, $toggle_slug )
			);
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );

		// Allow module to configure specific options
		if ( isset( $this->advanced_options['background']['options'] ) && is_array( $this->advanced_options['background']['options'] ) ) {
			foreach ( $this->advanced_options['background']['options'] as $option_slug => $options ) {
				if ( ! is_array( $options ) ) {
					continue;
				}

				foreach ( $options as $option_name => $option_value ) {
					$additional_options[ $option_slug ][ $option_name ] = $option_value;
				}
			}
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_text_fields() {
		if ( ! isset( $this->advanced_options['text'] ) ) {
			return;
		}

		$text_settings = $this->advanced_options['text'];
		$tab_slug      = isset( $text_settings['tab_slug'] ) ? $text_settings['tab_slug'] : 'advanced';
		$toggle_slug   = isset( $text_settings['toggle_slug'] ) ? $text_settings['toggle_slug'] : 'text';
		$sub_toggle   = isset( $text_settings['sub_toggle'] ) ? $text_settings['sub_toggle'] : '';
		$orientation_exclude_options = isset( $text_settings['text_orientation'] ) && isset( $text_settings['text_orientation']['exclude_options'] ) ? $text_settings['text_orientation']['exclude_options'] : array();

		// Make sure we can exclude text_orientation from Advanced/Text
		$setting_defaults   = array(
			'use_text_orientation' => true,
		);
		$text_settings = wp_parse_args( $text_settings, $setting_defaults );

		$this->_add_option_toggles( $tab_slug, array(
			$toggle_slug => array(
				'title'    => esc_html__( 'Text', 'et_builder' ),
				'priority' => 49,
			),
		) );

		$additional_options = array();
		if ( $text_settings['use_text_orientation'] ) {
			$additional_options = array(
				'text_orientation' => array(
					'label'            => esc_html__( 'Text Orientation', 'et_builder' ),
					'type'             => 'text_align',
					'option_category'  => 'layout',
					'options'          => et_builder_get_text_orientation_options( $orientation_exclude_options ),
					'tab_slug'         => $tab_slug,
					'toggle_slug'      => $toggle_slug,
					'description'      => esc_html__( 'This controls how your text is aligned within the module.', 'et_builder' ),
					'advanced_options' => true,
					'default'          => isset( $this->fields_defaults['text_orientation'] ) && isset( $this->fields_defaults['text_orientation'][0] ) ? $this->fields_defaults['text_orientation'][0] : '',
				),
			);
		}

		if ( '' !== $sub_toggle ) {
			$additional_options['text_orientation']['sub_toggle'] = $sub_toggle;
		}

		// Allow module to configure specific options
		if ( isset( $text_settings['options'] ) && is_array( $text_settings['options'] ) ) {
			foreach ( $text_settings['options'] as $option_slug => $options ) {
				if ( ! is_array( $options ) ) {
					continue;
				}

				foreach ( $options as $option_name => $option_value ) {
					$additional_options[ $option_slug ][ $option_name ] = $option_value;
				}
			}
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	/**
	 * Adds Rounded Corners and Border Styles options to each module
	 * By default uses the Border toggle.
	 * Can be overridden in child classes to add more border options. For example for the entire module
	 * container and for the image within the module.
	 */
	protected function _add_additional_border_fields() {
		$tab_slug      = 'advanced';
		$toggle_slug   = 'border';
		$border_toggle = array(
			$toggle_slug => array(
				'title'    => esc_html__( 'Border', 'et_builder' ),
				'priority' => 95,
			),
		);

		$this->_add_option_toggles( $tab_slug, $border_toggle );

		if ( ! isset( $this->advanced_options['border'] ) ) {
			$this->advanced_options['border'] = array();
		}

		$factory      = ET_Builder_Module_Fields_Factory::get( 'Border' );
		$factory_args = array_merge( $this->advanced_options['border'], array(
			'suffix'      => '',
			'tab_slug'    => $tab_slug,
			'toggle_slug' => $toggle_slug,
		) );

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $factory->get_fields( $factory_args ) );

		foreach ( array( 'border_radii', 'border_styles' ) as $border_key ) {
			if ( ! isset( $this->advanced_options['border'][ $border_key ] ) ) {
				$this->advanced_options['border'][ $border_key ] = array();
			}

			$this->advanced_options['border'][ $border_key ] = array_merge( $this->advanced_options['border'][ $border_key ], $this->_additional_fields_options[ $border_key ] );
		}
	}

	private function _add_additional_max_width_fields() {
		if ( ! isset( $this->advanced_options['max_width'] ) ) {
			return;
		}

		$max_width_settings = $this->advanced_options['max_width'];
		$tab_slug           = isset( $max_width_settings['tab_slug'] ) ? $max_width_settings['tab_slug'] : 'advanced';
		$toggle_slug        = isset( $max_width_settings['toggle_slug'] ) ? $max_width_settings['toggle_slug'] : 'width';
		$toggle_title       = isset( $max_width_settings['toggle_title'] ) ? $max_width_settings['toggle_title'] : esc_html__( 'Sizing', 'et_builder' );
		$toggle_priority    = isset( $max_width_settings['toggle_priority'] ) ? $max_width_settings['toggle_priority'] : 80;

		$setting_defaults   = array(
			'use_max_width'        => true,
			'use_module_alignment' => true,
		);
		$this->advanced_options['max_width'] = wp_parse_args( $this->advanced_options['max_width'], $setting_defaults );

		$this->_add_option_toggles( $tab_slug, array(
			$toggle_slug => array(
				'title'    => $toggle_title,
				'priority' => $toggle_priority,
			),
		) );

		// Added max width option
		if ( $this->advanced_options['max_width']['use_max_width'] ) {
			$additional_options = array(
				'max_width'             => array(
					'label'            => esc_html__( 'Width', 'et_builder' ),
					'type'             => 'range',
					'option_category'  => 'layout',
					'tab_slug'         => $tab_slug,
					'toggle_slug'      => $toggle_slug,
					'mobile_options'   => true,
					'default_on_child' => true,
					'validate_unit'    => true,
					'default'          => '100%',
					'allow_empty'      => true,
					'range_settings'   => array(
						'min'  => '0',
						'max'  => '100',
						'step' => '1',
					),
				),
				'max_width_tablet'      => array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				),
				'max_width_phone'       => array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				),
				'max_width_last_edited' => array(
					'type'        => 'skip',
					'tab_slug'    => $tab_slug,
					'toggle_slug' => $toggle_slug,
				),
			);
		}

		// Added module alignment option
		$allowed_children = array( 'et_pb_counter', 'et_pb_accordion_item' );
		$is_excluded      = isset( $this->type ) && 'child' === $this->type && ! in_array( $this->slug, $allowed_children );

		if ( $this->advanced_options['max_width']['use_module_alignment'] && ! $is_excluded ) {
			$additional_options['module_alignment'] = array(
				'label'           => esc_html__( 'Module Alignment', 'et_builder' ),
				'type'            => 'text_align',
				'option_category' => 'layout',
				'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
			);

			// Added max width & module alignment attributes which only make sense if both field exist
			if ( $this->advanced_options['max_width']['use_max_width'] ) {
				$additional_options['max_width']['responsive_affects'] = array(
					'module_alignment',
				);

				$additional_options['module_alignment']['depends_to'] = array(
					'max_width',
				);

				$additional_options['module_alignment']['depends_to_responsive'] = array(
					'max_width',
				);

				$additional_options['module_alignment']['depends_show_if_not'] = array(
					'',
					'100%',
				);
			}
		}

		// Allow module to configure specific options
		if ( isset( $max_width_settings['options'] ) && is_array( $max_width_settings['options'] ) ) {
			foreach ( $max_width_settings['options'] as $option_slug => $options ) {
				if ( ! is_array( $options ) ) {
					continue;
				}

				foreach ( $options as $option_name => $option_value ) {
					$additional_options[ $option_slug ][ $option_name ] = $option_value;
				}
			}
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_custom_margin_padding_fields() {
		if ( ! isset( $this->advanced_options['custom_margin_padding'] ) ) {
			return;
		}

		$additional_options = array();

		$defaults = array(
			'use_margin'  => true,
			'use_padding' => true,
		);
		$this->advanced_options['custom_margin_padding'] = wp_parse_args( $this->advanced_options['custom_margin_padding'], $defaults );

		$tab_slug = isset( $this->advanced_options['custom_margin_padding']['tab_slug'] ) ? $this->advanced_options['custom_margin_padding']['tab_slug'] : 'advanced';
		$toggle_disabled = isset( $this->advanced_options['custom_margin_padding']['disable_toggle'] ) && $this->advanced_options['custom_margin_padding']['disable_toggle'];
		$toggle_slug = isset( $this->advanced_options['custom_margin_padding']['toggle_slug'] ) ? $this->advanced_options['custom_margin_padding']['toggle_slug'] : 'custom_margin_padding';

		if ( ! $toggle_disabled ) {
			$margin_toggle = array(
				$toggle_slug => array(
					'title'    => esc_html__( 'Spacing', 'et_builder' ),
					'priority' => 90,
				),
			);

			$this->_add_option_toggles( $tab_slug, $margin_toggle );
		}

		if ( $this->advanced_options['custom_margin_padding']['use_margin'] ) {
			$additional_options['custom_margin'] = array(
				'label'           => esc_html__( 'Custom Margin', 'et_builder' ),
				'type'            => 'custom_margin',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
			);
			$additional_options['custom_margin_tablet'] = array(
				'type'     => 'skip',
				'tab_slug' => $tab_slug,
			);
			$additional_options['custom_margin_phone'] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			// make it possible to override/add options
			if ( ! empty( $this->advanced_options['custom_margin_padding']['custom_margin'] ) ) {
				$additional_options['custom_margin'] = array_merge( $additional_options['custom_margin'], $this->advanced_options['custom_margin_padding']['custom_margin'] );
			}

			$additional_options["custom_margin_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["padding_1_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["padding_2_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["padding_3_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["padding_4_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
		}

		if ( $this->advanced_options['custom_margin_padding']['use_padding'] ) {
			$additional_options['custom_padding'] = array(
				'label'           => esc_html__( 'Custom Padding', 'et_builder' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'option_category' => 'layout',
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
			);
			$additional_options['custom_padding_tablet'] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options['custom_padding_phone'] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			// make it possible to override/add options
			if ( ! empty( $this->advanced_options['custom_margin_padding']['custom_padding'] ) ) {
				$additional_options['custom_padding'] = array_merge( $additional_options['custom_padding'], $this->advanced_options['custom_margin_padding']['custom_padding'] );
			}

			$additional_options["custom_padding_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	private function _add_additional_button_fields() {
		if ( ! isset( $this->advanced_options['button'] ) ) {
			return;
		}

		// Auto-add attributes toggle
		$toggles_custom_css_tab = isset( $this->options_toggles['custom_css'] ) ? $this->options_toggles['custom_css'] : array();
		if ( ! isset( $toggles_custom_css_tab['toggles'] ) || ! isset( $toggles_custom_css_tab['toggles']['attributes'] ) ) {
			$this->_add_option_toggles( 'custom_css', array(
				'attributes' => array(
					'title'    => esc_html__( 'Attributes', 'et_builder' ),
					'priority' => 95,
				),
			) );
		}

		$additional_options = array();

		foreach ( $this->advanced_options['button'] as $option_name => $option_settings ) {
			$tab_slug = isset( $option_settings['tab_slug'] ) ? $option_settings['tab_slug'] : 'advanced';
			$toggle_disabled = isset( $option_settings['disable_toggle'] ) && $option_settings['disable_toggle'];
			$toggle_slug = '';

			if ( ! $toggle_disabled ) {
				$toggle_slug = isset( $option_settings['toggle_slug'] ) ? $option_settings['toggle_slug'] : $option_name;

				$button_toggle = array(
					$option_name => array(
						'title'    => esc_html( $option_settings['label'] ),
						'priority' => 70,
					),
				);

				$this->_add_option_toggles( $tab_slug, $button_toggle );
			}

			$additional_options["custom_{$option_name}"] = array(
				'label'             => sprintf( esc_html__( 'Use Custom Styles for %1$s ', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'yes_no_button',
				'option_category'   => 'button',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					"{$option_name}_text_color",
					"{$option_name}_text_size",
					"{$option_name}_border_width",
					"{$option_name}_border_radius",
					"{$option_name}_letter_spacing",
					"{$option_name}_spacing",
					"{$option_name}_bg_color",
					"{$option_name}_border_color",
					"{$option_name}_use_icon",
					"{$option_name}_font",
					"{$option_name}_text_color_hover",
					"{$option_name}_bg_color_hover",
					"{$option_name}_border_color_hover",
					"{$option_name}_border_radius_hover",
					"{$option_name}_letter_spacing_hover",
					"{$option_name}_text_shadow_style", // Add Text Shadow to button options
					"box_shadow_style_{$option_name}",
				),
				'shortcode_default' => 'off',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
			);

			$additional_options["{$option_name}_text_size"] = array(
				'label'           => sprintf( esc_html__( '%1$s Text Size', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'range',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				),
				'option_category' => 'button',
				'default'         => ET_Global_Settings::get_value( 'all_buttons_font_size' ),
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'mobile_options'  => true,
				'depends_default' => true,
			);

			$additional_options["{$option_name}_text_color"] = array(
				'label'             => sprintf( esc_html__( '%1$s Text Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_bg_color"] = array(
				'label'             => sprintf( esc_html__( '%1$s Background Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'background-field',
				'base_name'         => "{$option_name}_bg",
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => ET_Global_Settings::get_value( 'all_buttons_bg_color' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
				'background_fields' => $this->generate_background_options( "{$option_name}_bg", 'button', $tab_slug, $toggle_slug ),
			);

			$additional_options["{$option_name}_bg_color"]['background_fields']["{$option_name}_bg_color"]['default'] = ET_Global_Settings::get_value( 'all_buttons_bg_color' );
			$additional_options["{$option_name}_bg_color"]['background_fields']["{$option_name}_bg_color"]['shortcode_default'] = '';

			$additional_options = array_merge( $additional_options, $this->generate_background_options( "{$option_name}_bg", 'skip', $tab_slug, $toggle_slug ) );

			$additional_options["{$option_name}_border_width"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Width', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_width' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_color"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_radius"] = array(
				'label'             => sprintf( esc_html__( '%1$s Border Radius', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_radius' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_letter_spacing"] = array(
				'label'             => sprintf( esc_html__( '%1$s Letter Spacing', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_spacing' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'mobile_options'    => true,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_font"] = array(
				'label'           => sprintf( esc_html__( '%1$s Font', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'font',
				'option_category' => 'button',
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'depends_default' => true,
			);

			$additional_options["{$option_name}_use_icon"] = array(
				'label'           => sprintf( esc_html__( 'Show %1$s Icon', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'yes_no_button',
				'option_category' => 'button',
				'default'         => 'on',
				'options'         => array(
					'on'      => esc_html__( 'Yes', 'et_builder' ),
					'off'     => esc_html__( 'No', 'et_builder' ),
				),
				'affects' => array(
					"{$option_name}_icon_color",
					"{$option_name}_icon_placement",
					"{$option_name}_on_hover",
					"{$option_name}_icon",
				),
				'shortcode_default' => 'on',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_icon"] = array(
				'label'               => sprintf( esc_html__( '%1$s Icon', 'et_builder' ), $option_settings['label'] ),
				'type'                => 'text',
				'option_category'     => 'button',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'default'             => '',
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_icon_color"] = array(
				'label'               => sprintf( esc_html__( '%1$s Icon Color', 'et_builder' ), $option_settings['label'] ),
				'type'                => 'color-alpha',
				'option_category'     => 'button',
				'custom_color'        => true,
				'default'             => '',
				'shortcode_default'   => '',
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_icon_placement"] = array(
				'label'           => sprintf( esc_html__( '%1$s Icon Placement', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'select',
				'option_category' => 'button',
				'options'         => array(
					'right'   => esc_html__( 'Right', 'et_builder' ),
					'left'    => esc_html__( 'Left', 'et_builder' ),
				),
				'shortcode_default'   => 'right',
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_on_hover"] = array(
				'label'           => sprintf( esc_html__( 'Only Show Icon On Hover for %1$s', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'yes_no_button',
				'option_category' => 'button',
				'default'         => 'on',
				'options'         => array(
					'on'      => esc_html__( 'Yes', 'et_builder' ),
					'off'     => esc_html__( 'No', 'et_builder' ),
				),
				'shortcode_default'   => 'on',
				'tab_slug'            => $tab_slug,
				'toggle_slug'         => $toggle_slug,
				'depends_show_if_not' => 'off',
			);

			$additional_options["{$option_name}_text_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Text Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_bg_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Background Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_color_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Border Color', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'color-alpha',
				'option_category'   => 'button',
				'custom_color'      => true,
				'default'           => '',
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_border_radius_hover"] = array(
				'label'             => sprintf( esc_html__( '%1$s Hover Border Radius', 'et_builder' ), $option_settings['label'] ),
				'type'              => 'range',
				'option_category'   => 'button',
				'default'           => ET_Global_Settings::get_value( 'all_buttons_border_radius_hover' ),
				'shortcode_default' => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'depends_default'   => true,
			);

			$additional_options["{$option_name}_letter_spacing_hover"] = array(
				'label'           => sprintf( esc_html__( '%1$s Hover Letter Spacing', 'et_builder' ), $option_settings['label'] ),
				'type'            => 'range',
				'option_category' => 'button',
				'default'         => ET_Global_Settings::get_value( 'all_buttons_spacing_hover' ),
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'mobile_options'  => true,
				'depends_default' => true,
			);

			$additional_options["{$option_name}_text_size_tablet"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_text_size_phone"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_tablet"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_phone"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_hover_tablet"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_hover_phone"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			$additional_options["{$option_name}_text_size_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);
			$additional_options["{$option_name}_letter_spacing_hover_last_edited"] = array(
				'type'        => 'skip',
				'tab_slug'    => $tab_slug,
				'toggle_slug' => $toggle_slug,
			);

			if ( isset( $option_settings['use_alignment'] ) && $option_settings['use_alignment'] ) {
				$additional_options["{$option_name}_alignment"] = array(
					'label'           => esc_html__( 'Button Alignment', 'et_builder' ),
					'type'            => 'text_align',
					'option_category' => 'layout',
					'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
					'tab_slug'        => $tab_slug,
					'toggle_slug'     => $toggle_slug,
				);
			}

			// The configurable rel attribute field is added by default
			if ( ! isset( $option_settings['no_rel_attr'] ) ) {
				$additional_options["{$option_name}_rel"] = array(
					'label'           => sprintf( esc_html__( '%1$s Relationship', 'et_builder' ), $option_settings['label'] ),
					'type'            => 'multiple_checkboxes',
					'option_category' => 'configuration',
					'options'         => $this->get_rel_values(),
					'description'     => et_get_safe_localization( __( "Specify the value of your link's <em>rel</em> attribute. The <em>rel</em> attribute specifies the relationship between the current document and the linked document.<br><strong>Tip:</strong> Search engines can use this attribute to get more information about a link.", 'et_builder' ) ),
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'attributes',
					'shortcut_index'  => $option_name,
				);
			}

			// Add text-shadow to button options
			$option = $this->text_shadow->get_fields(array(
				'label'           => $option_settings['label'],
				'prefix'          => $option_name,
				'option_category' => 'font_option',
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'depends_default' => true,
			));

			$additional_options = array_merge( $additional_options, $option );
			$additional_options = $this->_add_button_box_shadow_fields(
				$additional_options,
				$option_name,
				$tab_slug,
				$toggle_slug
			);
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	protected function _add_button_box_shadow_fields( $fields, $option_name, $tab_slug, $toggle_slug ) {
		return array_merge( $fields, ET_Builder_Module_Fields_Factory::get( 'BoxShadow' )->get_fields( array(
			'suffix'          => "_{$option_name}",
			'label'           => esc_html__( 'Button Box Shadow', 'et_builder' ),
			'option_category' => 'layout',
			'tab_slug'        => $tab_slug,
			'toggle_slug'     => $toggle_slug,
			'depends_default' => true,
		) ) );
	}

	private function _add_additional_animation_fields() {
		$classname = get_class( $this );

		if ( isset( $this->type ) && 'child' === $this->type ) {
			return;
		}

		$this->options_toggles['advanced']['toggles']['animation'] = array(
			'title'    => esc_html__( 'Animation', 'et_builder' ),
			'priority' => 110,
		);

		$additional_options          = array();
		$animations_intensity_fields = array(
			'animation_intensity_slide',
			'animation_intensity_zoom',
			'animation_intensity_flip',
			'animation_intensity_fold',
			'animation_intensity_roll',
		);

		$additional_options['animation_style'] = array(
			'label'           => esc_html__( 'Animation Style', 'et_builder' ),
			'type'            => 'select_animation',
			'option_category' => 'configuration',
			'default'         => 'none',
			'description'     => esc_html__( 'Pick an animation style to enable animations for this element. Once enabled, you will be able to customize your animation style further. To disable animations, choose the None option.' ),
			'options'         => array(
				'none'   => esc_html__( 'None', 'et_builder' ),
				'fade'   => esc_html__( 'Fade', 'et_builder' ),
				'slide'  => esc_html__( 'Slide', 'et_builder' ),
				'bounce' => esc_html__( 'Bounce', 'et_builder' ),
				'zoom'   => esc_html__( 'Zoom', 'et_builder' ),
				'flip'   => esc_html__( 'Flip', 'et_builder' ),
				'fold'   => esc_html__( 'Fold', 'et_builder' ),
				'roll'   => esc_html__( 'Roll', 'et_builder' ),
			),
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'animation',
			'affects'     => array_merge( array(
				'animation_repeat',
				'animation_direction',
				'animation_duration',
				'animation_delay',
				'animation_starting_opacity',
				'animation_speed_curve',
			), $animations_intensity_fields ),
		);

		$additional_options['animation_repeat'] = array(
			'label'           => esc_html__( 'Animation Repeat', 'et_builder' ),
			'type'            => 'select',
			'option_category' => 'configuration',
			'default'         => 'once',
			'description'     => esc_html__( 'By default, animations will only play once. If you would like to loop your animation continuously you can choose the Loop option here.' ),
			'options'         => array(
				'once' => esc_html__( 'Once', 'et_builder' ),
				'loop' => esc_html__( 'Loop', 'et_builder' ),
			),
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'animation',
			'depends_show_if_not' => 'none',
		);

		$additional_options['animation_direction'] = array(
			'label'           => esc_html__( 'Animation Direction', 'et_builder' ),
			'type'            => 'select',
			'option_category' => 'configuration',
			'default'         => 'center',
			'description'     => esc_html__( 'Pick from up to five different animation directions, each of which will adjust the starting and ending position of your animated element.' ),
			'options'         => array(
				'center' => esc_html__( 'Center', 'et_builder' ),
				'left'   => esc_html__( 'Right', 'et_builder' ),
				'right'  => esc_html__( 'Left', 'et_builder' ),
				'bottom' => esc_html__( 'Up', 'et_builder' ),
				'top'    => esc_html__( 'Down', 'et_builder' ),
			),
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'animation',
			'depends_show_if_not' => array( 'none', 'fade' ),
		);

		$additional_options['animation_duration'] = array(
			'label'             => esc_html__( 'Animation Duration', 'et_builder' ),
			'type'              => 'range',
			'option_category'   => 'configuration',
			'range_settings'    => array(
				'min'  => 0,
				'max'  => 2000,
				'step' => 50,
			),
			'default'             => '1000ms',
			'description'         => esc_html__( 'Speed up or slow down your animation by adjusting the animation duration. Units are in milliseconds and the default animation duration is one second.' ),
			'validate_unit'       => true,
			'fixed_unit'          => 'ms',
			'fixed_range'         => true,
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'animation',
			'depends_show_if_not' => 'none',
			'reset_animation'     => true,
		);

		$additional_options['animation_delay'] = array(
			'label'           => esc_html__( 'Animation Delay', 'et_builder' ),
			'type'            => 'range',
			'option_category' => 'configuration',
			'range_settings'  => array(
				'min'  => 0,
				'max'  => 3000,
				'step' => 50,
			),
			'default'             => '0ms',
			'description'         => esc_html__( 'If you would like to add a delay before your animation runs you can designate that delay here in milliseconds. This can be useful when using multiple animated modules together.' ),
			'validate_unit'       => true,
			'fixed_unit'          => 'ms',
			'fixed_range'         => true,
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'animation',
			'depends_show_if_not' => 'none',
			'reset_animation'     => true,
		);

		foreach ( $animations_intensity_fields as $animations_intensity_field ) {
			$animation_style = str_replace( 'animation_intensity_', '', $animations_intensity_field );

			$additional_options[ $animations_intensity_field ] = array(
				'label'           => esc_html__( 'Animation Intensity', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'configuration',
				'range_settings'  => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'         => '50%',
				'description'     => esc_html__( 'Intensity effects how subtle or aggressive your animation will be. Lowering the intensity will create a smoother and more subtle animation while increasing the intensity will create a snappier more aggressive animation.' ),
				'validate_unit'   => true,
				'fixed_unit'      => '%',
				'fixed_range'     => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'depends_show_if' => $animation_style,
				'reset_animation' => true,
			);
		}

		$additional_options['animation_starting_opacity'] = array(
			'label'           => esc_html__( 'Animation Starting Opacity', 'et_builder' ),
			'type'            => 'range',
			'option_category' => 'configuration',
			'range_settings'  => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			'default'             => '0%',
			'description'         => esc_html__( 'By increasing the starting opacity, you can can reduce or remove the fade effect that is applied to all animation styles.' ),
			'validate_unit'       => true,
			'fixed_unit'          => '%',
			'fixed_range'         => true,
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'animation',
			'depends_show_if_not' => 'none',
			'reset_animation'     => true,
		);

		$additional_options['animation_speed_curve'] = array(
			'label'             => esc_html__( 'Animation Speed Curve', 'et_builder' ),
			'type'              => 'select',
			'option_category'   => 'configuration',
			'default'           => 'ease-in-out',
			'description'       => esc_html__( 'Here you can adjust the easing method of your animation. Easing your animation in and out will create a smoother effect when compared to a linear speed curve.' ),
			'options'         => array(
				'ease-in-out' => esc_html__( 'Ease-In-Out', 'et_builder' ),
				'ease'        => esc_html__( 'Ease', 'et_builder' ),
				'ease-in'     => esc_html__( 'Ease-In', 'et_builder' ),
				'ease-out'    => esc_html__( 'Ease-Out', 'et_builder' ),
				'linear'      => esc_html__( 'Linear', 'et_builder' ),
			),
			'tab_slug'            => 'advanced',
			'toggle_slug'         => 'animation',
			'depends_show_if_not' => 'none',
		);

		if ( isset( $this->slug ) && 'et_pb_fullwidth_menu' === $this->slug ) {
			$additional_options['dropdown_menu_animation'] = array(
				'label'           => esc_html__( 'Dropdown Menu Animation', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'fade'   => esc_html__( 'Fade', 'et_builder' ),
					'expand' => esc_html__( 'Expand', 'et_builder' ),
					'slide'  => esc_html__( 'Slide', 'et_builder' ),
					'flip'   => esc_html__( 'Flip', 'et_builder' ),
				),
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'animation',
				'default'      => 'fade',
			);
		}

		// Move existing "Animation" section fields under the new animations UI
		if ( isset( $this->slug ) && 'et_pb_fullwidth_portfolio' === $this->slug ) {
			$additional_options['auto'] = array(
				'label'           => esc_html__( 'Automatic Carousel Rotation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
				),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'If you the carousel layout option is chosen and you would like the carousel to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
				'default'         => 'off',
			);

			$additional_options['auto_speed'] = array(
				'label'           => esc_html__( 'Automatic Carousel Rotation Speed (in ms)', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( "Here you can designate how fast the carousel rotates, if 'Automatic Carousel Rotation' option is enabled above. The higher the number the longer the pause between each rotation. (Ex. 1000 = 1 sec)", 'et_builder' ),
				'default'         => '7000',
			);
		}

		if ( isset( $this->slug ) && 'et_pb_fullwidth_slider' === $this->slug ) {
			$additional_options['auto'] = array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
					'auto_ignore_hover',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
				'default'     => 'off',
			);

			$additional_options['auto_speed'] = array(
				'label'           => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
				'default'         => '7000',
			);

			$additional_options['auto_ignore_hover'] = array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options' => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'et_builder' ),
				'default'     => 'off',
			);
		}

		if ( isset( $this->slug ) && 'et_pb_fullwidth_post_slider' === $this->slug ) {
			$additional_options['auto'] = array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
					'auto_ignore_hover',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
				'default'     => 'off',
			);

			$additional_options['auto_speed'] = array(
				'label'           => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
				'default'         => '7000',
			);

			$additional_options['auto_ignore_hover'] = array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'et_builder' ),
				'default'     => 'off',
			);
		}

		if ( isset( $this->slug ) && 'et_pb_gallery' === $this->slug ) {
			$additional_options['auto'] = array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
				),
				'depends_show_if' => 'on',
				'depends_to'      => array(
					'fullwidth',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
				'default'     => 'off',
			);

			$additional_options['auto_speed'] = array(
				'label'           => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
				'default'         => '7000',
			);
		}

		if ( isset( $this->slug ) && 'et_pb_blurb' === $this->slug ) {
			$additional_options['animation'] = array(
				'label'           => esc_html__( 'Image/Icon Animation', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'top'    => esc_html__( 'Top To Bottom', 'et_builder' ),
					'left'   => esc_html__( 'Left To Right', 'et_builder' ),
					'right'  => esc_html__( 'Right To Left', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom To Top', 'et_builder' ),
					'off'    => esc_html__( 'No Animation', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
				'default'     => 'top',
			);
		}

		if ( isset( $this->slug ) && 'et_pb_slider' === $this->slug ) {
			$additional_options['auto'] = array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
					'auto_ignore_hover',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
				'default'     => 'off',
			);

			$additional_options['auto_speed'] = array(
				'label'           => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
				'default'         => '7000',
			);

			$additional_options['auto_ignore_hover'] = array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'et_builder' ),
				'default'     => 'off',
			);
		}

		if ( isset( $this->slug ) && 'et_pb_post_slider' === $this->slug ) {
			$additional_options['auto'] = array(
				'label'           => esc_html__( 'Automatic Animation', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'affects' => array(
					'auto_speed',
					'auto_ignore_hover',
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'If you would like the slider to slide automatically, without the visitor having to click the next button, enable this option and then adjust the rotation speed below if desired.', 'et_builder' ),
				'default'     => 'off',
			);

			$additional_options['auto_speed'] = array(
				'label'           => esc_html__( 'Automatic Animation Speed (in ms)', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_default' => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( "Here you can designate how fast the slider fades between each slide, if 'Automatic Animation' option is enabled above. The higher the number the longer the pause between each rotation.", 'et_builder' ),
				'default'         => '7000',
			);

			$additional_options['auto_ignore_hover'] = array(
				'label'           => esc_html__( 'Continue Automatic Slide on Hover', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'depends_default' => true,
				'options'         => array(
					'off' => esc_html__( 'Off', 'et_builder' ),
					'on'  => esc_html__( 'On', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'Turning this on will allow automatic sliding to continue on mouse hover.', 'et_builder' ),
				'default'     => 'off',
			);
		}

		if ( isset( $this->slug ) && 'et_pb_team_member' === $this->slug ) {
			$additional_options['animation'] = array(
				'label'             => esc_html__( 'Animation', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'off'     => esc_html__( 'No Animation', 'et_builder' ),
					'fade_in' => esc_html__( 'Fade In', 'et_builder' ),
					'left'    => esc_html__( 'Left To Right', 'et_builder' ),
					'right'   => esc_html__( 'Right To Left', 'et_builder' ),
					'top'     => esc_html__( 'Top To Bottom', 'et_builder' ),
					'bottom'  => esc_html__( 'Bottom To Top', 'et_builder' ),
				),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'animation',
				'description' => esc_html__( 'This controls the direction of the lazy-loading animation.', 'et_builder' ),
				'default'     => 'off',
			);
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	/**
	 * Add CSS filter controls (i.e. saturation, brightness, opacity) to the `_additional_fields_options` array.
	 *
	 * @return void
	 */
	protected function _add_additional_filter_fields() {

		if ( ! isset( $this->advanced_options['filters'] ) ) {
			return;
		}

		$filter_settings = self::$data_utils->array_get( $this->advanced_options, 'filters' );
		$tab_slug = self::$data_utils->array_get( $filter_settings, 'tab_slug', 'advanced' );
		$toggle_slug = self::$data_utils->array_get( $filter_settings, 'toggle_slug','filters' );
		$toggle_name = self::$data_utils->array_get( $filter_settings, 'toggle_name', esc_html__( 'Filters', 'et_builder' ) );

		$this->_add_option_toggles( $tab_slug, array(
			$toggle_slug => array(
				'title'    => $toggle_name,
				'priority' => 105,
			),
		) );

		$additional_options = array();

		$additional_options['filter_hue_rotate'] = array(
			'label'            => esc_html__( 'Hue', 'et_builder' ),
			'type'             => 'range',
			'option_category'  => 'layout',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 359,
				'step' => 1,
			),
			'default'          => '0deg',
			'default_on_child' => true,
			'description'      => esc_html__( 'Shift all colors by this amount.', 'et_builder' ),
			'validate_unit'    => true,
			'fixed_unit'       => 'deg',
			'fixed_range'      => true,
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$additional_options['filter_saturate'] = array(
			'label'            => esc_html__( 'Saturation', 'et_builder' ),
			'type'             => 'range',
			'option_category'  => 'layout',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 1,
			),
			'default'          => '100%',
			'default_on_child' => true,
			'description'      => esc_html__( 'Define how intense the color saturation should be.', 'et_builder' ),
			'validate_unit'    => true,
			'fixed_unit'       => '%',
			'fixed_range'      => true,
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$additional_options['filter_brightness'] = array(
			'label'            => esc_html__( 'Brightness', 'et_builder' ),
			'type'             => 'range',
			'option_category'  => 'layout',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 1,
			),
			'default'          => '100%',
			'default_on_child' => true,
			'description'      => esc_html__( 'Define how bright the colors should be.', 'et_builder' ),
			'validate_unit'    => true,
			'fixed_unit'       => '%',
			'fixed_range'      => true,
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$additional_options['filter_contrast'] = array(
			'label'            => esc_html__( 'Contrast', 'et_builder' ),
			'type'             => 'range',
			'option_category'  => 'layout',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 200,
				'step' => 1,
			),
			'default'          => '100%',
			'default_on_child' => true,
			'description'      => esc_html__( 'Define how distinct bright and dark areas should be.', 'et_builder' ),
			'validate_unit'    => true,
			'fixed_unit'       => '%',
			'fixed_range'      => true,
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$additional_options['filter_invert'] = array(
			'label'            => esc_html__( 'Invert', 'et_builder' ),
			'type'             => 'range',
			'option_category'  => 'layout',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			'default'          => '0%',
			'default_on_child' => true,
			'description'      => esc_html__( 'Invert the hue, saturation, and brightness by this amount.', 'et_builder' ),
			'validate_unit'    => true,
			'fixed_unit'       => '%',
			'fixed_range'      => true,
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$additional_options['filter_sepia'] = array(
			'label'            => esc_html__( 'Sepia', 'et_builder' ),
			'type'             => 'range',
			'option_category'  => 'layout',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			'default'          => '0%',
			'default_on_child' => true,
			'description'      => esc_html__( 'Travel back in time by this amount.', 'et_builder' ),
			'validate_unit'    => true,
			'fixed_unit'       => '%',
			'fixed_range'      => true,
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$additional_options['filter_opacity'] = array(
			'label'            => esc_html__( 'Opacity', 'et_builder' ),
			'type'             => 'range',
			'option_category'  => 'layout',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
			'default'          => '100%',
			'default_on_child' => true,
			'description'      => esc_html__( 'Define how transparent or opaque this should be.', 'et_builder' ),
			'validate_unit'    => true,
			'fixed_unit'       => '%',
			'fixed_range'      => true,
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$additional_options['filter_blur'] = array(
			'label'            => esc_html__( 'Blur', 'et_builder' ),
			'type'             => 'range',
			'option_category'  => 'layout',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 50,
				'step' => 1,
			),
			'default'          => '0px',
			'default_on_child' => true,
			'description'      => esc_html__( 'Blur by this amount.', 'et_builder' ),
			'validate_unit'    => true,
			'fixed_unit'       => 'px',
			'fixed_range'      => true,
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$additional_options['mix_blend_mode'] = array(
			'label'            => esc_html__( 'Blend Mode', 'et_builder' ),
			'type'             => 'select',
			'option_category'  => 'layout',
			'default'          => 'normal',
			'default_on_child' => true,
			'description'      => esc_html__( 'Modify how this element blends with any layers beneath it. To reset, choose the "Normal" option.' ),
			'options'          => array(
				'normal'      => esc_html__( 'Normal', 'et_builder' ),
				'multiply'    => esc_html__( 'Multiply', 'et_builder' ),
				'screen'      => esc_html__( 'Screen', 'et_builder' ),
				'overlay'     => esc_html__( 'Overlay', 'et_builder' ),
				'darken'      => esc_html__( 'Darken', 'et_builder' ),
				'lighten'     => esc_html__( 'Lighten', 'et_builder' ),
				'color-dodge' => esc_html__( 'Color Dodge', 'et_builder' ),
				'color-burn'  => esc_html__( 'Color Burn', 'et_builder' ),
				'hard-light'  => esc_html__( 'Hard Light', 'et_builder' ),
				'soft-light'  => esc_html__( 'Soft Light', 'et_builder' ),
				'difference'  => esc_html__( 'Difference', 'et_builder' ),
				'exclusion'   => esc_html__( 'Exclusion', 'et_builder' ),
				'hue'         => esc_html__( 'Hue', 'et_builder' ),
				'saturation'  => esc_html__( 'Saturation', 'et_builder' ),
				'color'       => esc_html__( 'Color', 'et_builder' ),
				'luminosity'  => esc_html__( 'Luminosity', 'et_builder' ),
			),
			'tab_slug'         => $tab_slug,
			'toggle_slug'      => $toggle_slug,
			'depends_default'  => null,
			'reset_animation'  => false,
		);

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );

		// Maybe add child filters (i.e. targeting only images within a module)
		if ( ! isset( $this->advanced_options['filters']['child_filters_target'] ) ) {
			return;
		}

		$child_filter = $this->advanced_options['filters']['child_filters_target'];

		$additional_child_options = array(
			'child_filter_hue_rotate' => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Hue', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'  => 0,
					'max'  => 359,
					'step' => 1,
				),
				'default'          => '0deg',
				'default_on_child' => true,
				'description'      => esc_html__( 'Shift all colors by this amount.', 'et_builder' ),
				'validate_unit'    => true,
				'fixed_unit'       => 'deg',
				'fixed_range'      => true,
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
			'child_filter_saturate'   => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Saturation', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
				'default'          => '100%',
				'default_on_child' => true,
				'description'      => esc_html__( 'Define how intense the color saturation should be.', 'et_builder' ),
				'validate_unit'    => true,
				'fixed_unit'       => '%',
				'fixed_range'      => true,
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
			'child_filter_brightness' => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Brightness', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
				'default'          => '100%',
				'default_on_child' => true,
				'description'      => esc_html__( 'Define how bright the colors should be.', 'et_builder' ),
				'validate_unit'    => true,
				'fixed_unit'       => '%',
				'fixed_range'      => true,
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
			'child_filter_contrast'   => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Contrast', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'  => 0,
					'max'  => 200,
					'step' => 1,
				),
				'default'          => '100%',
				'default_on_child' => true,
				'description'      => esc_html__( 'Define how distinct bright and dark areas should be.', 'et_builder' ),
				'validate_unit'    => true,
				'fixed_unit'       => '%',
				'fixed_range'      => true,
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
			'child_filter_invert'     => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Invert', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'          => '0%',
				'default_on_child' => true,
				'description'      => esc_html__( 'Invert the hue, saturation, and brightness by this amount.', 'et_builder' ),
				'validate_unit'    => true,
				'fixed_unit'       => '%',
				'fixed_range'      => true,
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
			'child_filter_sepia'      => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Sepia', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'          => '0%',
				'default_on_child' => true,
				'description'      => esc_html__( 'Travel back in time by this amount.', 'et_builder' ),
				'validate_unit'    => true,
				'fixed_unit'       => '%',
				'fixed_range'      => true,
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
			'child_filter_opacity'    => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Opacity', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'          => '100%',
				'default_on_child' => true,
				'description'      => esc_html__( 'Define how transparent or opaque this should be.', 'et_builder' ),
				'validate_unit'    => true,
				'fixed_unit'       => '%',
				'fixed_range'      => true,
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
			'child_filter_blur'       => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Blur', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				),
				'default'          => '0px',
				'default_on_child' => true,
				'description'      => esc_html__( 'Blur by this amount.', 'et_builder' ),
				'validate_unit'    => true,
				'fixed_unit'       => 'px',
				'fixed_range'      => true,
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
			'child_mix_blend_mode'    => array(
				'label'            => esc_html__( 'Image', 'et_builder' ) . ' ' . esc_html__( 'Blend Mode', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'default'          => 'normal',
				'default_on_child' => true,
				'description'      => esc_html__( 'Modify how this element blends with any layers beneath it. To reset, choose the "Normal" option.' ),
				'options'          => array(
					'normal'      => esc_html__( 'Normal', 'et_builder' ),
					'multiply'    => esc_html__( 'Multiply', 'et_builder' ),
					'screen'      => esc_html__( 'Screen', 'et_builder' ),
					'overlay'     => esc_html__( 'Overlay', 'et_builder' ),
					'darken'      => esc_html__( 'Darken', 'et_builder' ),
					'lighten'     => esc_html__( 'Lighten', 'et_builder' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'et_builder' ),
					'color-burn'  => esc_html__( 'Color Burn', 'et_builder' ),
					'hard-light'  => esc_html__( 'Hard Light', 'et_builder' ),
					'soft-light'  => esc_html__( 'Soft Light', 'et_builder' ),
					'difference'  => esc_html__( 'Difference', 'et_builder' ),
					'exclusion'   => esc_html__( 'Exclusion', 'et_builder' ),
					'hue'         => esc_html__( 'Hue', 'et_builder' ),
					'saturation'  => esc_html__( 'Saturation', 'et_builder' ),
					'color'       => esc_html__( 'Color', 'et_builder' ),
					'luminosity'  => esc_html__( 'Luminosity', 'et_builder' ),
				),
				'tab_slug'         => $child_filter['tab_slug'],
				'toggle_slug'      => $child_filter['toggle_slug'],
				'depends_default'  => null,
				'reset_animation'  => false,
			),
		);

		if(!array_key_exists('depends_show_if',$child_filter)){
			$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_child_options );
			return;
		}
		foreach ( $additional_child_options as $option => $value ) {
			unset($additional_child_options[$option]['depends_default']);
			$additional_child_options[$option]['depends_show_if'] = $child_filter['depends_show_if'];
		}

		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_child_options );
	}

	/**
	 * Add the divider options to the additional_fields_options array.
	 */
	protected function _add_additional_divider_fields() {
		// Make sure we only add this to sections.
		if ( 'et_pb_section' !== $this->slug ) {
			return;
		}

		$tab_slug       = 'advanced';
		$toggle_slug    = 'dividers';
		$divider_toggle = array(
			$toggle_slug => array(
				'title'    => esc_html__( 'Dividers', 'et_builder' ),
				'priority' => 65,
			),
		);

		// Add the toggle sections.
		$this->_add_option_toggles( $tab_slug, $divider_toggle );

		if ( ! isset( $this->advanced_options['dividers'] ) ) {
			$this->advanced_options['dividers'] = array();
		}

		$additional_options = ET_Builder_Module_Fields_Factory::get( 'Divider' )->get_fields( array(
			'tab_slug'    => $tab_slug,
			'toggle_slug' => $toggle_slug,
		) );

		// Return our merged options and toggles.
		$this->_additional_fields_options = array_merge( $this->_additional_fields_options, $additional_options );
	}

	/**
	 * Add additional Text Shadow fields to all modules
	 *
	 * @return array
	 */
	protected function _add_additional_text_shadow_fields() {
		// If no Advanced/Text toggle, do nothing.
		if ( empty( $this->options_toggles['advanced']['toggles']['text'] ) ) {
			return;
		}

		// Fetch the additional fields and add them.
		$this->_additional_fields_options = array_merge(
			$this->_additional_fields_options,
			$this->text_shadow->get_fields()
		);
	}

	protected function _add_additional_shadow_fields() {
		$this->options_toggles['advanced']['toggles']['box_shadow'] = array(
			'title'    => esc_html__( 'Box Shadow', 'et_builder' ),
			'priority' => 100,
		);

		$this->_additional_fields_options = array_merge(
			$this->_additional_fields_options,
			ET_Builder_Module_Fields_Factory::get( 'BoxShadow' )->get_fields( array(
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'box_shadow',
			) )
		);
	}

	private function _add_custom_css_fields() {
		if ( isset( $this->custom_css_tab ) && ! $this->custom_css_tab ) {
			return;
		}

		$custom_css_fields = array();
		$custom_css_options = array();
		$current_module_unique_class = '.' . $this->slug . '_' . "<%= typeof( module_order ) !== 'undefined' ?  module_order : '<span class=\"et_pb_module_order_placeholder\"></span>' %>";
		$main_css_element_output = isset( $this->main_css_element ) ? $this->main_css_element : '%%order_class%%';
		$main_css_element_output = str_replace( '%%order_class%%', $current_module_unique_class, $main_css_element_output );

		$custom_css_default_options = array(
			'before' => array(
				'label'    => esc_html__( 'Before', 'et_builder' ),
				'selector' => ':before',
				'no_space_before_selector' => true,
			),
			'main_element' => array(
				'label'    => esc_html__( 'Main Element', 'et_builder' ),
			),
			'after' => array(
				'label'    => esc_html__( 'After', 'et_builder' ),
				'selector' => ':after',
				'no_space_before_selector' => true,
			),
		);
		$custom_css_options = apply_filters( 'et_default_custom_css_options', $custom_css_default_options );

		if ( ! empty( $this->custom_css_options ) ) {
			$custom_css_options = array_merge( $custom_css_options, $this->custom_css_options );
		}

		$this->custom_css_options = apply_filters( 'et_custom_css_options_' . $this->slug, $custom_css_options );

		// optional settings names in custom css options
		$additional_option_slugs = array( 'description', 'priority' );

		foreach ( $custom_css_options as $slug => $option ) {
			$selector_value = isset( $option['selector'] ) ? $option['selector'] : '';
			$selector_contains_module_class = false !== strpos( $selector_value, '%%order_class%%' ) ? true : false;
			$selector_output = '' !== $selector_value ? str_replace( '%%order_class%%', $current_module_unique_class, $option['selector'] ) : '';
			$custom_css_fields[ "custom_css_{$slug}" ] = array(
				'label'    => sprintf(
					'%1$s:<span>%2$s%3$s%4$s</span>',
					$option['label'],
					! $selector_contains_module_class ? $main_css_element_output : '',
					! isset( $option['no_space_before_selector'] ) && isset( $option['selector'] ) ? ' ' : '',
					$selector_output
				),
				'type'        => 'custom_css',
				'tab_slug'    => 'custom_css',
				'toggle_slug' => 'custom_css',
				'no_colon' => true,
			);

			// update toggle slug for $this->custom_css_options
			$this->custom_css_options[ $slug ]['toggle_slug'] = 'custom_css';

			// add optional settings if needed
			foreach ( $additional_option_slugs as $option_slug ) {
				if ( isset( $option[ $option_slug ] ) ) {
					$custom_css_fields[ "custom_css_{$slug}" ][ $option_slug ] = $option[ $option_slug ];
				}
			}
		}

		if ( ! empty( $custom_css_fields ) ) {
			$this->fields_unprocessed = array_merge( $this->fields_unprocessed, $custom_css_fields );
		}

		$default_custom_css_toggles = array(
			'classes'    => esc_html__( 'CSS ID &amp; Classes', 'et_builder' ),
			'custom_css' => esc_html__( 'Custom CSS', 'et_builder' ),
		);

		$this->_add_option_toggles( 'custom_css', $default_custom_css_toggles );
	}

	protected function _add_option_toggles( $tab_slug, $toggles_array ) {
		if ( ! isset( $this->options_toggles[ $tab_slug ] ) ) {
			$this->options_toggles[ $tab_slug ] = array();
		}

		if ( ! isset( $this->options_toggles[ $tab_slug ]['toggles'] ) ) {
			$this->options_toggles[ $tab_slug ]['toggles'] = array();
		}

		// get the only toggles which do not exist.
		$processed_toggles = array_diff_key( $toggles_array, $this->options_toggles[ $tab_slug ]['toggles'] );

		$this->options_toggles[ $tab_slug ]['toggles'] = array_merge( $this->options_toggles[ $tab_slug ]['toggles'], $processed_toggles );
	}

	private function _get_fields() {
		$this->fields = array();

		$this->fields = $this->fields_unprocessed;

		$this->fields = $this->process_fields( $this->fields );

		$this->fields = apply_filters( 'et_builder_module_fields_' . $this->slug, $this->fields );

		foreach ( $this->fields as $field_name => $field ) {
			$this->fields[ $field_name ] = apply_filters('et_builder_module_fields_' . $this->slug . '_field_' . $field_name, $field );
			$this->fields[ $field_name ]['name'] = $field_name;
		}

		return $this->fields;
	}

	/**
	 * Checks if the field value equals its default value
	 *
	 * @param string $name Field name.
	 * @param mixed $value Field value.
	 */
	private function _is_field_default( $name, $value ) {
		if ( ! isset( $this->fields_unprocessed[ $name ] ) ) {
			// field do not exist
			return false;
		}
		$field = $this->fields_unprocessed[ $name ];

		if ( isset( $field['shortcode_default'] ) ) {
			// we have a shortcode default
			return $field['shortcode_default'] === $value;
		}

		// check if we have a default and compare
		return isset( $field['default'] ) && $field['default'] === $value;
	}

	// intended to be overridden as needed
	function process_fields( $fields ) {
		return apply_filters( 'et_pb_module_processed_fields', $fields, $this->slug );
	}

	/**
	 * Get the settings fields data for this element.
	 *
	 * @since 1.0
	 * @todo  Finish documenting return value's structure.
	 *
	 * @return array[] {
	 *     Settings Fields
	 *
	 *     @type mixed[] $setting_field_key {
	 *         Setting Field Data
	 *
	 *         @type string   $type                Setting field type.
	 *         @type string   $id                  CSS id for the setting.
	 *         @type string   $label               Text label for the setting. Translatable.
	 *         @type string   $description         Description for the settings. Translatable.
	 *         @type string   $class               Optional. Css class for the settings.
	 *         @type string[] $affects             Optional. The keys of all settings that depend on this setting.
	 *         @type string[] $depends_to          Optional. The keys of all settings that this setting depends on.
	 *         @type string   $depends_show_if     Optional. Only show this setting when the settings
	 *                                             on which it depends has a value equal to this.
	 *         @type string   $depends_show_if_not Optional. Only show this setting when the settings
	 *                                             on which it depends has a value that is not equal to this.
	 *         ...
	 *     }
	 *     ...
	 * }
	 */
	function get_fields() { return array(); }

	/**
	 * Returns module style priority.
	 *
	 * @return int
	 */
	function get_style_priority() {
		return $this->_style_priority;
	}

	function hex2rgb( $color ) {
		if ( substr( $color, 0, 1 ) == '#' ) {
			$color = substr( $color, 1 );
		}

		if ( strlen( $color ) == 6 ) {
			list( $r, $g, $b ) = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			list( $r, $g, $b ) = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return false;
		}

		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );

		return implode( ', ', array( $r, $g, $b ) );
	}

	function rgba_string_from_field_color_set( $color_set ) {
		if ( empty( $color_set ) || false === strpos($color_set, '|') ) {
			return false;
		}

		$color_set = explode('|', $color_set );

		$color_set_hex = $color_set[0];
		$color_set_rgb = $color_set[1];
		$color_set_alpha = $color_set[2];

		$color_set_rgba = 'rgba(' . $color_set_rgb . ', ' . $color_set_alpha . ')';
		return $color_set_rgba;
	}

	function get_post_type() {
		global $post, $et_builder_post_type;

		if ( is_a( $post, 'WP_POST' ) && ( is_admin() || ! isset( $et_builder_post_type ) ) ) {
			return $post->post_type;
		} else {
			return isset( $et_builder_post_type ) ? $et_builder_post_type : 'post';
		}
	}

	function module_classes( $classes = array() ) {
		if ( ! empty( $classes ) ) {
			if ( ! is_array( $classes ) ) {
				if ( strpos( $classes, ' ' ) !== false ) {
					$classes = explode( ' ', $classes );
				} else {
					$classes = array( $classes );
				}
			}
		}

		$classes = apply_filters( 'et_builder_module_classes', $classes, $this->slug );
		$classes = apply_filters( 'et_builder_module_classes_' . $this->slug, $classes );

		$classes = array_map( 'trim', $classes );

		$_classes = array();
		foreach( $classes as $class ) {
			if ( ! empty( $class ) ) {
				$_classes[] = $class;
			}
		}

		return $_classes;
	}

	static function optimize_bb_chunk( $content ) {
		if ( ! ET_BUILDER_OPTIMIZE_TEMPLATES ) {
			return $content;
		}
		return str_replace( self::$_unique_bb_strip, '', $content );
	}

	static function get_unique_bb_key( $content ) {
		if ( ! ET_BUILDER_OPTIMIZE_TEMPLATES ) {
			return $content;
		}
		$content = self::optimize_bb_chunk( $content );
		if ( isset( self::$_unique_bb_keys_map[ $content ] ) ) {
			$key = self::$_unique_bb_keys_map[ $content ];
		} else {
			self::$_unique_bb_keys_values[] = $content;
			$key = count( self::$_unique_bb_keys_values ) - 1;
			self::$_unique_bb_keys_map[ $content ] = $key;
		}
		$content = "<!-- $key -->";
		return $content;
	}

	function wrap_settings_option( $option_output, $field ) {
		$depends      = false;
		$new_depends  = isset( $field['show_if'] ) || isset( $field['show_if_not'] );
		$depends_attr = '';

		if ( isset( $field['depends_show_if'] ) || isset( $field['depends_show_if_not'] ) ) {
			$depends = true;
			if ( isset( $field['depends_show_if_not'] ) ) {
				$depends_show_if_not = is_array( $field['depends_show_if_not'] ) ? implode( ',', $field['depends_show_if_not'] ) : $field['depends_show_if_not'];

				$depends_attr = sprintf( ' data-depends_show_if_not="%s"', esc_attr( $depends_show_if_not ) );
			} else {
				$depends_attr = sprintf( ' data-depends_show_if="%s"', esc_attr( $field['depends_show_if'] ) );
			}
		}

		if ( isset( $field['depends_to_responsive'] ) ) {
			$depends_attr .= sprintf( ' data-depends_to_responsive="%s"', esc_attr( implode( ',', $field['depends_to_responsive'] ) ) );
		}

		// Overriding background color's attribute, turning it into appropriate background attributes
		if ( isset( $field['type'] ) && isset( $field['name' ] ) && 'background_color' === $field['name'] ) {
			$field['type'] = 'background';

			// Removing depends default variable which hides background color for unified background field UI
			if ( isset( $field['depends_default'] ) ) {
				unset( $field['depends_default'] );
			}
		}

		$output = sprintf(
			'%6$s<div class="et-pb-option et-pb-option--%10$s%1$s%2$s%3$s%8$s%9$s%12$s"%4$s tabindex="-1" data-option_name="%11$s">%5$s</div>%7$s',
			( ! empty( $field['type'] ) && 'tiny_mce' == $field['type'] ? ' et-pb-option-main-content' : '' ),
			( $depends || isset( $field['depends_default'] ) || $new_depends ) ? ' et-pb-depends' : '',
			( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? ' et_pb_hidden' : '' ),
			( $depends ? $depends_attr : '' ),
			"\n\t\t\t\t" . $option_output . "\n\t\t\t",
			"\t",
			"\n\n\t\t",
			( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? esc_attr( sprintf( ' et-pb-option-%1$s', $field['name'] ) ) : '' ),
			( ! empty( $field['option_class'] ) ? ' ' . $field['option_class'] : '' ),
			isset( $field['type'] ) ? esc_attr( $field['type'] ) : '',
			esc_attr( $field['name'] ),
			isset( $field['specialty_only'] ) && 'yes' === $field['specialty_only'] ? ' et-pb-specialty-only-option' : ''
		);

		return self::get_unique_bb_key($output);
	}

	/**
	 * Prepare module field (option) for use within BB microtemplates.
	 * The own field renderer can be used.
	 * @param $field Module field
	 *
	 * @return mixed|string Html code of the field
	 */
	public function wrap_settings_option_field( $field ) {
		$use_container_wrapper = isset( $field['use_container_wrapper'] ) && ! $field['use_container_wrapper'] ? false : true;

		if ( ! empty( $field['renderer'] ) && is_array( $field['renderer'] ) && ! empty( $field['renderer']['class'] ) ) {
			//cut off 'ET_Builder_Module_Field_Template_' part from renderer definition
			$class_name_without_prefix = strtolower ( str_replace ("ET_Builder_Module_Field_Template_", "", $field['renderer']['class'] ) );

			//split class name string by underscore symbol
			$file_name_parts = explode( '_', $class_name_without_prefix );

			if ( ! empty( $file_name_parts ) ) {
				//the first symbol of class name must be uppercase
				$last_index = count( $file_name_parts ) - 1;
				$file_name_parts[$last_index] = ucwords( $file_name_parts[$last_index] );

				//load renderer class from 'module/field/template/' directory accordingly class name and class directory hierarchy
				require_once ET_BUILDER_DIR . 'module/field/template/' . implode( DIRECTORY_SEPARATOR, $file_name_parts ) . '.php';
				$renderer = new $field['renderer']['class'];

				//before calling the 'render' method make sure the instantiated class is child of 'ET_Builder_Module_Field_Template_Base'
				if ( is_subclass_of( $field['renderer']['class'], "ET_Builder_Module_Field_Template_Base" ) ) {
					$field_el = call_user_func( array( $renderer, "render" ), $field, $this );
				}
			}
		} else if ( ! empty( $field['renderer'] ) ) {
			$renderer_options = isset( $field['renderer_options'] ) ? $field['renderer_options'] : $field;

			$field_el = is_callable( $field['renderer'] ) ? call_user_func( $field['renderer'], $renderer_options ) : $field['renderer'];

			if ( ! empty( $field['renderer_with_field'] ) && $field['renderer_with_field'] ) {
				$field_el .= $this->render_field( $field );
			}
		} else {
			$field_el = $this->render_field( $field );
		}

		$description = ! empty( $field['description'] ) ? sprintf( '%2$s<p class="description">%1$s</p>', $field['description'], "\n\t\t\t\t\t" ) : '';

		if ( '' === $description && ! $use_container_wrapper ) {
			$output = $field_el;
		} else {
			$output = sprintf(
				'%3$s<div class="et-pb-option-container et-pb-option-container--%6$s%5$s">
					%1$s
					%2$s
				%4$s</div>',
				$field_el,
				$description,
				"\n\n\t\t\t\t",
				"\t",
				( isset( $field['type'] ) && 'custom_css' === $field['type'] ? ' et-pb-custom-css-option' : '' ),
				isset( $field['type'] ) ? esc_attr( $field['type'] ) : ''
			);
		}

		return $output;
	}

	function wrap_settings_option_label( $field ) {
		if ( ! empty( $field['label'] ) ) {
			$label = $field['label'];
		} else {
			return '';
		}

		$field_name = $this->get_field_name( $field );
		if ( isset( $field['type'] ) && 'font' === $field['type'] ) {
			$field_name .= '_select';
		}

		$required = ! empty( $field['required'] ) ? '<span class="required">*</span>' : '';
		$attributes = ! ( isset( $field['type'] ) && in_array( $field['type'], array( 'custom_margin', 'custom_padding' )  ) )
			? sprintf( ' for="%1$s"', esc_attr( $field_name ) )
			: ' class="et_custom_margin_label"';

		$label = sprintf(
			'<label%1$s>%2$s%4$s %3$s</label>',
			$attributes,
			$label,
			$required,
			isset( $field['no_colon'] ) && true === $field['no_colon'] ? '' : ':'
		);

		return $label;
	}

	/**
	 * Get svg icon as string
	 *
	 * @param string icon name
	 *
	 * @return string div-wrapped svg icon
	 */
	function get_icon( $icon_name ) {
		$all_svg_icons = et_pb_get_svg_icons_list();
		$icon = isset( $all_svg_icons[ $icon_name ] ) ? $all_svg_icons[ $icon_name ] : '';

		if ( '' === $icon ) {
			return '';
		}

		return '<div class="et-pb-icon">
			<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision">' . $icon . '</svg>
		</div>';
	}

	/**
	 * Get structure of background UI tabs
	 *
	 * @return array
	 */
	function get_background_fields_structure( $base_name = 'background') {
		$is_background_attr            = 'background' === $base_name;
		$use_background_color_gradient = $is_background_attr ? 'use_background_color_gradient' : "{$base_name}_use_color_gradient";
		$prefix                        = $is_background_attr ? '' : "{$base_name}_";

		$structure = array(
			'color' => array(
				"{$base_name}_color",
			),
			'gradient' => array(
				"{$base_name}_color_gradient_start",
				"{$base_name}_color_gradient_end",
				$use_background_color_gradient,
				"{$base_name}_color_gradient_type",
				"{$base_name}_color_gradient_direction",
				"{$base_name}_color_gradient_direction_radial",
				"{$base_name}_color_gradient_start_position",
				"{$base_name}_color_gradient_end_position",
				"{$base_name}_color_gradient_overlays_image",
			),
			'image' => array(
				"{$base_name}_image",
				"{$prefix}parallax",
				"{$prefix}parallax_method",
				"{$base_name}_size",
				"{$base_name}_position",
				"{$base_name}_repeat",
				"{$base_name}_blend",
			),
			'video' => array(
				"{$base_name}_video_mp4",
				"{$base_name}_video_webm",
				"{$base_name}_video_width",
				"{$base_name}_video_height",
				"{$prefix}allow_player_pause",
				"{$base_name}_video_pause_outside_viewport",
			),
		);

		if ( $is_background_attr ) {
			$structure['color'][] = 'use_background_color';
			$structure['image'][] = 'bg_img'; // Column
		}

		return $structure;
	}

	/**
	 * Get list of background fields names in one dimensional array
	 *
	 * @return array
	 */
	function get_background_fields_names() {
		$background_structure = $this->get_background_fields_structure();
		$fields_names = array();

		foreach ( $background_structure as $tab_name ) {
			foreach ( $tab_name as $field_name ) {
				$fields_names[] = $field_name;
			}
		}

		return $fields_names;
	}

	/**
	 * Get / extract background fields from all modules fields
	 *
	 * @param array all modules fields
	 *
	 * @return array background fields multidimensional array grouped based on its tab
	 */
	function get_background_fields( $all_fields, $base_name = 'background' ) {
		$background_fields_structure = $this->get_background_fields_structure( $base_name );
		$background_tab_names        = array_keys( $background_fields_structure );
		$background_fields           = array_fill_keys( $background_tab_names, array() );

		foreach ( $all_fields as $field_name => $field ) {
			// Multiple foreaches seem overkill. Use single foreach with little bit if conditions
			// redundancy to get background fields grouped into multi-dimensional tab-based array
			if ( in_array( $field_name, $background_fields_structure['color'] ) ) {
				$background_fields['color'][$field_name] = $field;
			}

			if ( in_array( $field_name, $background_fields_structure['gradient'] ) ) {
				$background_fields['gradient'][$field_name] = $field;
			}

			if ( in_array( $field_name, $background_fields_structure['image'] ) ) {
				$background_fields['image'][$field_name] = $field;
			}

			if ( in_array( $field_name, $background_fields_structure['video'] ) ) {
				$background_fields['video'][$field_name] = $field;
			}
		}

		return $background_fields;
	}

	/**
	 * Generate background fields based on base name
	 *
	 * @param string background base name
	 * @param string background tab name
	 * @param string field's tab slug
	 * @param string field's toggle slug
	 *
	 * @return array of background fields
	 */
	function generate_background_options( $base_name = 'background', $background_tab, $tab_slug, $toggle_slug ) {
		$baseless_prefix = 'background' === $base_name ? '' : "{$base_name}_";
		$options         = array();

		// Not included on skip background tab because background-field is expected to be registered under "background_color" field
		if ( in_array( $background_tab, array( 'all', 'button', 'color' ) ) ) {
			$options["{$base_name}_color"] = array(
				'label'           => esc_html__( 'Background Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'option_category' => 'configuration',
				'custom_color'    => true,
				'tab_slug'        => $tab_slug,
				'toggle_slug'     => $toggle_slug,
				'field_template'  => 'color',
			);
		}

		if ( in_array( $background_tab, array( 'all', 'button', 'skip', 'gradient' ) ) ) {
			$use_background_color_gradient_name = 'background' === $base_name ? 'use_background_color_gradient' : "{$base_name}_use_color_gradient";

			$options[ $use_background_color_gradient_name ] = array(
				'label'             => esc_html__( 'Use Background Color Gradient', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => 'off',
				'shortcode_default' => 'off',
				'default_on_child'  => true,
				'affects'           => array(
					"{$base_name}_color_gradient_start",
					"{$base_name}_color_gradient_end",
					"{$base_name}_color_gradient_start_position",
					"{$base_name}_color_gradient_end_position",
					"{$base_name}_color_gradient_type",
					"{$base_name}_color_gradient_overlays_image",
				),
				'description'       => '',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'use_color_gradient',
			);

			$options["{$base_name}_color_gradient_start"] = array(
				'label'             => esc_html__( 'Gradient Start', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'color-alpha',
				'option_category'   => 'configuration',
				'description'       => '',
				'depends_show_if'   => 'on',
				'default'           => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
				'shortcode_default' => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
				'default_on_child'  => true,
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'color_gradient_start',
			);

			$options["{$base_name}_color_gradient_end"] = array(
				'label'             => esc_html__( 'Gradient End', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'color-alpha',
				'option_category'   => 'configuration',
				'description'       => '',
				'depends_show_if'   => 'on',
				'default'           => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
				'shortcode_default' => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
				'default_on_child'  => true,
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'color_gradient_end',
			);

			$options["{$base_name}_color_gradient_type"] = array(
				'label'             => esc_html__( 'Gradient Type', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'linear' => esc_html__( 'Linear', 'et_builder' ),
					'radial' => esc_html__( 'Radial', 'et_builder' ),
				),
				'affects'           => array(
					"{$base_name}_color_gradient_direction",
					"{$base_name}_color_gradient_direction_radial"
				),
				'default'           => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
				'shortcode_default' => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
				'default_on_child'  => true,
				'description'       => '',
				'depends_show_if'   => 'on',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'color_gradient_type',
			);

			$options["{$base_name}_color_gradient_direction"] = array(
				'label'             => esc_html__( 'Gradient Direction', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'range',
				'option_category'   => 'configuration',
				'range_settings'    => array(
					'min'  => 1,
					'max'  => 360,
					'step' => 1,
				),
				'default'           => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
				'shortcode_default' => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
				'default_on_child'  => true,
				'validate_unit'     => true,
				'fixed_unit'        => 'deg',
				'fixed_range'       => true,
				'depends_show_if'   => 'linear',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'color_gradient_direction',
			);

			$options["{$base_name}_color_gradient_direction_radial"] = array(
				'label'             => esc_html__( 'Radial Direction', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'center'       => esc_html__( 'Center', 'et_builder' ),
					'top left'     => esc_html__( 'Top Left', 'et_builder' ),
					'top'          => esc_html__( 'Top', 'et_builder' ),
					'top right'    => esc_html__( 'Top Right', 'et_builder' ),
					'right'        => esc_html__( 'Right', 'et_builder' ),
					'bottom right' => esc_html__( 'Bottom Right', 'et_builder' ),
					'bottom'       => esc_html__( 'Bottom', 'et_builder' ),
					'bottom left'  => esc_html__( 'Bottom Left', 'et_builder' ),
					'left'         => esc_html__( 'Left', 'et_builder' ),
				),
				'default'           => ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ),
				'shortcode_default' => ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ),
				'default_on_child'  => true,
				'description'       => '',
				'depends_show_if'   => 'radial',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'color_gradient_direction_radial',
			);

			$options["{$base_name}_color_gradient_start_position"] = array(
				'label'             => esc_html__( 'Start Position', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'range',
				'option_category'   => 'configuration',
				'range_settings'    => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'           => ET_Global_Settings::get_value( 'all_background_gradient_start_position' ),
				'shortcode_default' => ET_Global_Settings::get_value( 'all_background_gradient_start_position' ),
				'default_on_child'  => true,
				'validate_unit'     => true,
				'fixed_unit'        => '%',
				'fixed_range'       => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'color_gradient_start_position',
			);

			$options["{$base_name}_color_gradient_end_position"] = array(
				'label'             => esc_html__( 'End Position', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'range',
				'option_category'   => 'configuration',
				'range_settings'    => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'           => ET_Global_Settings::get_value( 'all_background_gradient_end_position' ),
				'shortcode_default' => ET_Global_Settings::get_value( 'all_background_gradient_end_position' ),
				'default_on_child'  => true,
				'validate_unit'     => true,
				'fixed_unit'        => '%',
				'fixed_range'       => true,
				'depends_show_if'   => 'on',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'color_gradient_end_position',
			);

			$options["${base_name}_color_gradient_overlays_image"] = array(
				'label'             => esc_html__( 'Place Gradient Above Background Image', 'et_builder' ),
				'type'              => 'skip' === $background_tab ? 'skip' : 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'           => ET_Global_Settings::get_value( 'all_background_gradient_overlays_image' ),
				'shortcode_default' => ET_Global_Settings::get_value( 'all_background_gradient_overlays_image' ),
				'default_on_child'  => true,
				'description'       => esc_html__( 'If enabled, gradient will be positioned on top of background-image', 'et_builder' ),
				'depends_show_if'   => 'on',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'color_gradient_overlays_image',
			);
		}

		if ( in_array( $background_tab, array( 'all', 'button', 'skip', 'image' ) ) ) {
			$options["{$base_name}_image"] = array(
				'label'              => esc_html__( 'Background Image', 'et_builder' ),
				'type'               => 'skip' === $background_tab ? 'skip' : 'upload',
				'option_category'    => 'configuration',
				'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background', 'et_builder' ),
				'tab_slug'           => $tab_slug,
				'toggle_slug'        => $toggle_slug,
				'field_template'    => 'image',
			);

			if ( 'button' !== $background_tab ) {
				$options["${baseless_prefix}parallax"] = array(
					'label'             => esc_html__( 'Use Parallax Effect', 'et_builder' ),
					'type'              => 'skip' === $background_tab ? 'skip' : 'yes_no_button',
					'option_category'   => 'configuration',
					'options'           => array(
						'off' => esc_html__( 'No', 'et_builder' ),
						'on'  => esc_html__( 'Yes', 'et_builder' ),
					),
					'default'           => 'off',
					'default_on_child'  => true,
					'affects'           => array(
						"${baseless_prefix}parallax_method",
						"{$base_name}_size",
						"{$base_name}_position",
						"{$base_name}_repeat",
						"{$base_name}_blend",
					),
					'description'       => esc_html__( 'If enabled, your background image will stay fixed as your scroll, creating a fun parallax-like effect.', 'et_builder' ),
					'tab_slug'          => $tab_slug,
					'toggle_slug'       => $toggle_slug,
					'field_template'    => 'parallax',
				);

				$options["${baseless_prefix}parallax_method"] = array(
					'label'             => esc_html__( 'Parallax Method', 'et_builder' ),
					'type'              => 'skip' === $background_tab ? 'skip' : 'select',
					'option_category'   => 'configuration',
					'options'           => array(
						'on'  => esc_html__( 'True Parallax', 'et_builder' ),
						'off' => esc_html__( 'CSS', 'et_builder' ),
					),
					'default'           => isset( $this->fields_defaults["${baseless_prefix}parallax_method"][0] ) ? $this->fields_defaults["${baseless_prefix}parallax_method"][0] : 'on',
					'default_on_child'  => true,
					'depends_show_if'   => 'on',
					'description'       => esc_html__( 'Define the method, used for the parallax effect.', 'et_builder' ),
					'tab_slug'          => $tab_slug,
					'toggle_slug'       => $toggle_slug,
					'field_template'    => 'parallax_method',
				);
			}

			$options["{$base_name}_size"] = array(
				'label'           => esc_html__( 'Background Image Size', 'et_builder' ),
				'type'            => 'skip' === $background_tab ? 'skip' : 'select',
				'option_category' => 'layout',
				'options'         => array(
					'cover'   => esc_html__( 'Cover', 'et_builder' ),
					'contain' => esc_html__( 'Fit', 'et_builder' ),
					'initial' => esc_html__( 'Actual Size', 'et_builder' ),
				),
				'default'         => 'cover',
				'default_on_child'=> true,
				'depends_show_if' => 'off',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'size',
			);

			$options["{$base_name}_position"] = array(
				'label'           => esc_html__( 'Background Image Position', 'et_builder' ),
				'type'            => 'skip' === $background_tab ? 'skip' : 'select',
				'option_category' => 'layout',
				'options' => array(
					'top_left'      => esc_html__( 'Top Left', 'et_builder' ),
					'top_center'    => esc_html__( 'Top Center', 'et_builder' ),
					'top_right'     => esc_html__( 'Top Right', 'et_builder' ),
					'center_left'   => esc_html__( 'Center Left', 'et_builder' ),
					'center'        => esc_html__( 'Center', 'et_builder' ),
					'center_right'  => esc_html__( 'Center Right', 'et_builder' ),
					'bottom_left'   => esc_html__( 'Bottom Left', 'et_builder' ),
					'bottom_center' => esc_html__( 'Bottom Center', 'et_builder' ),
					'bottom_right'  => esc_html__( 'Bottom Right', 'et_builder' ),
				),
				'default'           => 'center',
				'default_on_child'  => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'position',
			);

			$options["{$base_name}_repeat"] = array(
				'label'           => esc_html__( 'Background Image Repeat', 'et_builder' ),
				'type'            => 'skip' === $background_tab ? 'skip' : 'select',
				'option_category' => 'layout',
				'options' => array(
					'no-repeat' => esc_html__( 'No Repeat', 'et_builder' ),
					'repeat'    => esc_html__( 'Repeat', 'et_builder' ),
					'repeat-x'  => esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
					'repeat-y'  => esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
					'space'     => esc_html__( 'Space', 'et_builder' ),
					'round'     => esc_html__( 'Round', 'et_builder' ),
				),
				'default'          => 'no-repeat',
				'default_on_child' => true,
				'depends_show_if'  => 'off',
				'tab_slug'          => $tab_slug,
				'toggle_slug'       => $toggle_slug,
				'field_template'    => 'repeat',
			);

			$options["{$base_name}_blend"] = array(
				'label'            => esc_html__( 'Background Image Blend', 'et_builder' ),
				'type'             => 'skip' === $background_tab ? 'skip' : 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'normal'      => esc_html__( 'Normal', 'et_builder' ),
					'multiply'    => esc_html__( 'Multiply', 'et_builder' ),
					'screen'      => esc_html__( 'Screen', 'et_builder' ),
					'overlay'     => esc_html__( 'Overlay', 'et_builder' ),
					'darken'      => esc_html__( 'Darken', 'et_builder' ),
					'lighten'     => esc_html__( 'Lighten', 'et_builder' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'et_builder' ),
					'color-burn'  => esc_html__( 'Color Burn', 'et_builder' ),
					'hard-light'  => esc_html__( 'Hard Light', 'et_builder' ),
					'soft-light'  => esc_html__( 'Soft Light', 'et_builder' ),
					'difference'  => esc_html__( 'Difference', 'et_builder' ),
					'exclusion'   => esc_html__( 'Exclusion', 'et_builder' ),
					'hue'         => esc_html__( 'Hue', 'et_builder' ),
					'saturation'  => esc_html__( 'Saturation', 'et_builder' ),
					'color'       => esc_html__( 'Color', 'et_builder' ),
					'luminosity'  => esc_html__( 'Luminosity', 'et_builder' ),
				),
				'default'          => 'normal',
				'default_on_child' => true,
				'depends_show_if'  => 'off',
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'field_template'   => 'blend',
			);
		}

		if ( in_array( $background_tab, array( 'all', 'skip', 'video' ) ) ) {
			$options["{$base_name}_video_mp4"] = array(
				'label'              => esc_html__( 'Background Video MP4', 'et_builder' ),
				'type'               => 'skip' === $background_tab ? 'skip' : 'upload',
				'option_category'    => 'configuration',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
				'description'        => et_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .MP4 version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'et_builder' ) ),
				'tab_slug'           => $tab_slug,
				'toggle_slug'        => $toggle_slug,
				'computed_affects'   => array(
					"__video_{$base_name}",
				),
				'field_template'     => 'video_mp4',
			);

			$options["{$base_name}_video_webm"] = array(
				'label'              => esc_html__( 'Background Video Webm', 'et_builder' ),
				'type'               => 'skip' === $background_tab ? 'skip' : 'upload',
				'option_category'    => 'configuration',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
				'description'        => et_get_safe_localization( __( 'All videos should be uploaded in both .MP4 .WEBM formats to ensure maximum compatibility in all browsers. Upload the .WEBM version here. <b>Important Note: Video backgrounds are disabled from mobile devices. Instead, your background image will be used. For this reason, you should define both a background image and a background video to ensure best results.</b>', 'et_builder' ) ),
				'tab_slug'           => $tab_slug,
				'toggle_slug'        => $toggle_slug,
				'computed_affects'   => array(
					"__video_{$base_name}",
				),
				'field_template'     => 'video_webm',
			);

			$options["{$base_name}_video_width"] = array(
				'label'            => esc_html__( 'Background Video Width', 'et_builder' ),
				'type'             => 'skip' === $background_tab ? 'skip' : 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'In order for videos to be sized correctly, you must input the exact width (in pixels) of your video here.', 'et_builder' ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'computed_affects' => array(
					"__video_{$base_name}",
				),
				'field_template'   => 'video_width',
			);

			$options["{$base_name}_video_height"] = array(
				'label'            => esc_html__( 'Background Video Height', 'et_builder' ),
				'type'             => 'skip' === $background_tab ? 'skip' : 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'In order for videos to be sized correctly, you must input the exact height (in pixels) of your video here.', 'et_builder' ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'computed_affects' => array(
					"__video_{$base_name}",
				),
				'field_template'   => 'video_height',
			);

			$options["${baseless_prefix}allow_player_pause"] = array(
				'label'            => esc_html__( 'Pause Video When Another Video Plays', 'et_builder' ),
				'type'             => 'skip' === $background_tab ? 'skip' : 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'          => 'off',
				'default_on_child' => true,
				'description'      => esc_html__( 'Allow video to be paused by other players when they begin playing', 'et_builder' ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'field_template'   => 'allow_player_pause',
			);

			$options["${base_name}_video_pause_outside_viewport"] = array(
				'label'            => esc_html__( 'Pause Video While Not In View', 'et_builder' ),
				'type'             => 'skip' === $background_tab ? 'skip' : 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'default'          => 'on',
				'default_on_child' => true,
				'description'      => esc_html__( 'Allow video to be paused while it is not in the visible area.', 'et_builder' ),
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'field_template'   => 'video_pause_outside_viewport',
			);

			$options["__video_{$base_name}"] = array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Element', "get_video_background" ),
				'computed_depends_on' => array(
					"{$base_name}_video_mp4",
					"{$base_name}_video_webm",
					"{$base_name}_video_width",
					"{$base_name}_video_height",
				),
				'computed_minimum'    => array(
					"{$base_name}_video_mp4",
					"{$base_name}_video_webm",
				),
				'computed_variables'  => array(
					'base_name' => $base_name,
				),
			);
		}

		return $options;
	}

	/**
	 * Get string of background fields UI. Used in place of background_color fields UI
	 *
	 * @param array list of all module fields
	 *
	 * @return string background fields UI
	 */
	function wrap_settings_background_fields( $all_fields, $base_name = 'background' ) {
		$tab_structure     = $this->get_background_fields_structure( $base_name );
		$tab_names         = array_keys( $tab_structure );
		$background_fields = $this->get_background_fields( $all_fields, $base_name );

		// Concatenate background fields UI
		$background = '';

		// Label
		$background .= sprintf(
			'<label for="et_pb_background">%1$s</label>',
			esc_html__( 'Background:', 'et_builder' )
		);

		// Field wrapper
		$background .= sprintf(
			'<div class="et-pb-option-container et-pb-option-container-inner et-pb-option-container--background" data-base_name="%s">',
			esc_attr( $base_name )
		);

		$tab_names_processed = array();

		foreach ( $tab_names as $tab_nav_name ) {
			if ( ! empty( $background_fields[ $tab_nav_name ] ) ) {
				$tab_names_processed[] = sanitize_text_field( $tab_nav_name );
			}
		}

		// Tab Nav
		$background .= sprintf( '<%%= window.et_builder.options_template_output("background_tabs_nav",%1$s) %%>', json_encode( $tab_names_processed ) );

		// Tabs
		foreach ( $tab_names as $tab_name ) {
			$background .= sprintf(
				'<div class="et_pb_background-tab et_pb_background-tab--%1$s" data-tab="%1$s">',
				esc_attr( $tab_name )
			);

			// Get tab's fields
			$tab_fields = $background_fields[ $tab_name ];

			// Render gradient tab's preview
			if ( 'gradient' === $tab_name ) {
				$background .= '<%= window.et_builder.options_template_output("background_gradient_buttons") %>';
			}

			// Tab's fields
			foreach ( $tab_fields as $tab_field_name => $tab_field ) {

				if ( 'skip' === $tab_field['type'] ) {
					continue;
				}

				$preview_class = '';

				// Append field name
				$tab_field['name'] = $tab_field_name;

				// Append preview class name
				if ( in_array( $tab_field['name'], array( "{$base_name}_color", "{$base_name}_image", "{$base_name}_url", "{$base_name}_video_mp4", "{$base_name}_video_webm" ) ) ) {
					$tab_field['has_preview'] = true;
					$preview_class = ' et-pb-option--has-preview';
				}

				// Prepare field list attribute
				$depends      = false;
				$depends_attr = '';
				if ( isset( $tab_field['depends_show_if'] ) || isset( $tab_field['depends_show_if_not'] ) ) {
					$depends = true;
					if ( isset( $tab_field['depends_show_if_not'] ) ) {
						$depends_show_if_not = is_array( $tab_field['depends_show_if_not'] ) ? implode( ',', $tab_field['depends_show_if_not'] ) : $tab_field['depends_show_if_not'];

						$depends_attr = sprintf( ' data-depends_show_if_not="%s"', esc_attr( $depends_show_if_not ) );
					} else {
						$depends_attr = sprintf( ' data-depends_show_if="%s"', esc_attr( $tab_field['depends_show_if'] ) );
					}
				}

				// Append fields UI
				$background .= sprintf(
					'<div class="et_pb_background-option et_pb_background-option--%1$s et_pb_background-template--%6$s %5$s et-pb-option--%1$s%2$s"%3$s data-option_name="%4$s">',
					esc_attr( $tab_field_name ),
					esc_attr( $preview_class ),
					$depends_attr,
					esc_attr( $tab_field['name'] ),
					$tab_field['name'] === "{$base_name}_color" && 'background' !== $base_name ? 'et-pb-option-main' : 'et-pb-option',
					isset( $tab_field['field_template'] ) ? esc_attr( $tab_field['field_template'] ) : ''
				);

				$background .= $this->wrap_settings_option_label( $tab_field );
				$background .= $this->wrap_settings_option_field( $tab_field );
				$background .= '</div>';
			}

			$background .= '</div>';
		}

		// End of field wrapper
		$background .= '</div>';

		return $background;
	}

	function get_field_name( $field ) {
		// Don't add 'et_pb_' prefix to the "Admin Label" field
		if ( 'admin_label' === $field['name'] ) {
			return $field['name'];
		}

		return sprintf( 'et_pb_%s', $field['name'] );
	}

	function process_html_attributes( $field, &$attributes ) {
		if ( is_array( $field['attributes'] )  ) {
			foreach( $field['attributes'] as $attribute_key => $attribute_value ) {
				$attributes .= ' ' . esc_attr( $attribute_key ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		} else {
			$attributes = ' '.$field['attributes'];
		}
	}

	/**
	 * Returns an underscore template for the options settings.
	 * @param  array $field Associative array.
	 * array(
			'id'                  => (int),
			'label'               => (string),
			'description'         => (string),
			'class'               => (string),
			'type'                => (string),
			'validate_input'      => (bool),
			'name'                => (string),
			'default'             => (string),
			'defaults'            => (array),
			'options'             => (array),
			'option_category'     => (string),
			'attributes'          => (string),
			'affects'             => (string),
			'before'              => (string),
			'after'               => (string),
			'display_if'          => (string),
			'depends_to'          => (string),
			'depends_show_if'     => (string),
			'depends_show_if_not' => (string),
			'show_if'             => (string),
			'show_if_not'         => (string),
			'tab_slug'            => (string),
			'toggle_slug'         => (string),
			'composite_type'      => (string),
			'composite_structure' => (array),
			)
	 * @return string        HTML underscore template.
	 */
	function render_field( $field ) {
		$utils = ET_Core_Data_Utils::instance();
		$classes = array();
		$hidden_field = '';
		$field_el = '';
		$is_custom_color = isset( $field['custom_color'] ) && $field['custom_color'];
		$reset_button_html = '<span class="et-pb-reset-setting"></span>';
		$need_mobile_options = isset( $field['mobile_options'] ) && $field['mobile_options'] ? true : false;
		$only_options = isset( $field['only_options'] ) ? $field['only_options'] : false;
		$is_child = isset( $this->type ) && 'child' === $this->type;
		// Make sure 'type' is always set to prevent PHP notices
		if ( empty( $field['type'] ) ) {
			$field['type'] = 'no-type';
		}

		if ( $need_mobile_options ) {
			$mobile_settings_tabs = et_pb_generate_mobile_options_tabs();
		}

		if ( 0 !== strpos( $field['type'], 'select' ) ) {
			$classes = array( 'regular-text' );
		}

		foreach( $this->get_validation_class_rules() as $rule ) {
			if ( ! empty( $field[ $rule ] ) ) {
				$this->validation_in_use = true;
				$classes[] = $rule;
			}
		}

		if ( isset( $field['validate_unit'] ) && $field['validate_unit'] ) {
			$classes[] = 'et-pb-validate-unit';
		}

		if ( ! empty( $field['class'] ) ) {
			if ( is_string( $field['class'] ) ) {
				$field['class'] = array( $field['class'] );
			}

			$classes = array_merge( $classes, $field['class'] );
		}
		$field['class'] = implode(' ', $classes );

		$field_name = $this->get_field_name( $field );

		$field['id'] = ! empty( $field['id'] ) ? $field['id'] : $field_name;

		$field['name'] = $field_name;

		if ( $is_child ) {
			$field_name = "data.{$field_name}";
		}

		$default_arr = isset( $field['default'] ) ? $field['default'] : '';

		if ( is_array( $default_arr ) && isset( $default_arr[1] ) && is_array( $default_arr[1] ) ) {
			list($default_parent_id, $defaults_list) = $default_arr;
			$default_parent_id = sprintf( '%1$set_pb_%2$s', $is_child ? 'data.' : '', $default_parent_id );
			$default = esc_attr( json_encode( $default_arr ) );
			$default_value = sprintf(
				'(typeof(%1$s) !== \'undefined\' ? ( typeof(%1$s) === \'object\' ? (%2$s)[jQuery(%1$s).val()] : (%2$s)[%1$s] ) : \'\')',
				$default_parent_id,
				json_encode( $defaults_list )
			);

			$default_is_arr = true;
		} else {
			$default = $default_value = $default_arr;
			$default_is_arr = false;
		}

		if ( 'font' === $field['type'] ) {
			$default       = '' === $default ? '||||||||' : $default;
			$default_value = '' === $default_value ? '||||||||' : $default_value;
		}

		$font_icon_options = array( 'et_pb_font_icon', 'et_pb_button_icon', 'et_pb_button_one_icon', 'et_pb_button_two_icon', 'et_pb_hover_icon' );

		if ( in_array( $field_name, $font_icon_options ) ) {
			$field_value = esc_attr( $field_name );
		} else {
			$field_value = esc_attr( $field_name ) . '.replace(/%91/g, "[").replace(/%93/g, "]").replace(/%22/g, "\"")';
		}

		$value_html = $default_is_arr
			? ' value="<%%- typeof( %1$s ) !== \'undefined\' ?  %2$s : %3$s %%>" '
			: ' value="<%%- typeof( %1$s ) !== \'undefined\' ?  %2$s : \'%3$s\' %%>" ';
		$value = sprintf(
			$value_html,
			esc_attr( $field_name ),
			$field_value,
			$default_value
		);

		$attributes = '';
		if ( ! empty( $field['attributes'] ) ) {
			$this->process_html_attributes( $field, $attributes );
		}

		if ( ! empty( $field['affects'] ) ) {
			$field['class'] .= ' et-pb-affects';
			$attributes .= sprintf( ' data-affects="%s"', esc_attr( implode( ', ', $field['affects'] ) ) );
		}

		if ( ! empty( $field['responsive_affects'] ) ) {
			$field['class'] .= ' et-pb-responsive-affects';
			$attributes .= sprintf(
				' data-responsive-affects="%1$s" data-responsive-desktop-name="%2$s"',
				esc_attr( implode( ', ', $field['responsive_affects'] ) ),
				esc_attr( $field['name'] )
			);
		}

		if ( 'font' === $field['type'] ) {
			$field['class'] .= ' et-pb-font-select';
		}

		if ( in_array( $field['type'], array( 'font', 'hidden', 'multiple_checkboxes', 'select_with_option_groups', 'select_animation', 'presets', 'presets_shadow', 'select_box_shadow' ) ) && ! $only_options ) {
			$hidden_field = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="et-pb-main-setting %3$s" data-default="%4$s" %5$s %6$s/>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $default ),
				$value,
				$attributes
			);

			if ( 'select_with_option_groups' === $field['type'] ) {
				// Since we are using a hidden field to manage the value, we need to clear the data-affects attribute so that
				// it doesn't appear on both the `$field` AND the hidden field. This should probably be done for all of these
				// field types but don't want to risk breaking anything :-/
				$attributes = preg_replace( '/data-affects="[\w\s-,]*"/', 'data-affects=""', $attributes );
			}
		}

		foreach ( $this->get_validation_attr_rules() as $rule ) {
			if ( ! empty( $field[ $rule ] ) ) {
				$this->validation_in_use = true;
				$attributes .= ' data-rule-' . esc_attr( $rule ). '="' . esc_attr( $field[ $rule ] ) . '"';
			}
		}

		if ( isset( $field['before'] ) && ! $only_options ) {
			$field_el .= $this->render_field_before_after_element( $field['before'] );
		}

		switch( $field['type'] ) {
			case 'background-field':
				$field_el .= $this->wrap_settings_background_fields( $field['background_fields'], $field['base_name'] );
				break;
			case 'warning':
				$field_el .= sprintf(
					'<div class="et-pb-option-warning" data-name="%2$s" data-display_if="%3$s">%1$s</div>',
					html_entity_decode( esc_html( $field['message'] ) ),
					esc_attr( $field['name'] ),
					esc_attr( $field['display_if'] )
				);
				break;
			case 'tiny_mce':
				if ( ! empty( $field['tiny_mce_html_mode'] ) ) {
					$field['class'] .= ' html_mode';
				}

				$main_content_property_name = $main_content_field_name = 'et_pb_content_new';

				if ( isset( $this->type ) && 'child' === $this->type ) {
					$main_content_property_name = "data.{$main_content_property_name}";
				}

				$field_el .= sprintf(
					'<div id="%1$s"><%%= typeof( %2$s ) !== \'undefined\' ? %2$s : \'\' %%></div>',
					esc_attr( $main_content_field_name ),
					esc_html( $main_content_property_name )
				);

				break;
			case 'codemirror':
			case 'textarea':
			case 'custom_css':
			case 'options_list':
				$field_custom_value = esc_html( $field_name );
				if ( in_array( $field['type'], array( 'custom_css', 'options_list' ) ) ) {
					$field_custom_value .= '.replace( /\|\|/g, "\n" ).replace( /%22/g, "&quot;" ).replace( /%92/g, "\\\" )';
					$field_custom_value .= '.replace( /%91/g, "&#91;" ).replace( /%93/g, "&#93;" )';
				}

				if ( in_array( $field_name, array( 'et_pb_raw_content', 'et_pb_custom_message' ) ) ) {
					// escape html to make sure it's not rendered inside the Textarea field in Settings Modal.
					$field_custom_value = sprintf( '_.escape( %1$s )', $field_custom_value );
				}

				$field_el .= sprintf(
					'<textarea class="et-pb-main-setting large-text code%1$s" rows="4" cols="50" id="%2$s"><%%= typeof( %3$s ) !== \'undefined\' ? %4$s : \'\' %%></textarea>',
					esc_attr( $field['class'] ),
					esc_attr( $field['id'] ),
					esc_html( $field_name ),
					et_esc_previously( $field_custom_value )
				);

				if ( 'options_list' === $field['type'] ) {
					$radio_check = '';
					$row_class   = 'et_options_list_row';

					if ( isset( $field['checkbox'] ) && true === $field['checkbox'] ) {
						$radio_check = '<a href="#" class="et_options_list_check"></a>';
						$row_class   .= ' et_options_list_row_checkbox';
					}

					if ( isset( $field['radio'] ) && true === $field['radio'] ) {
						$radio_check = '<a href="#" class="et_options_list_check"></a>';
						$row_class   .= ' et_options_list_row_radio';
					}

					$field_el = sprintf(
						'<div class="et_options_list">
							<div class="%5$s">
								%6$s
								<input type="text" />
								<div class="et_options_list_actions">
									<a href="#" class="et_options_list_move"></a>
									<a href="#" class="et_options_list_copy"></a>
									<a href="#" class="et_options_list_remove"></a>
								</div>
							</div>
							<textarea class="et-pb-main-setting large-text code%1$s" rows="4" cols="50" id="%2$s"><%%= typeof( %3$s ) !== \'undefined\' ? %4$s : \'\' %%></textarea>
							<a href="#" class="et-pb-add-sortable-option"><span>%7$s</span></a>
						</div>',
						esc_attr( $field['class'] ),
						esc_attr( $field['id'] ),
						esc_html( $field_name ),
						et_esc_previously( $field_custom_value ),
						esc_attr( $row_class ),
						$radio_check,
						esc_html__( 'Add New Item', 'et_builder' )
					);
				}
				break;
			case 'conditional_logic':
				$field_custom_value = esc_html( $field_name );
				$field_custom_value .= '.replace( /\|\|/g, "\n" ).replace( /%22/g, "&quot;" ).replace( /%92/g, "\\\" )';
				$field_custom_value .= '.replace( /%91/g, "&#91;" ).replace( /%93/g, "&#93;" )';

				$field_selects = sprintf(
					'<select class="et_conditional_logic_field"></select>
					<select class="et_conditional_logic_condition">
						<option value="is">%1$s</option>
						<option value="is not">%2$s</option>
						<option value="is greater">%3$s</option>
						<option value="is less">%4$s</option>
						<option value="contains">%5$s</option>
						<option value="does not contain">%6$s</option>
						<option value="is empty">%7$s</option>
						<option value="is not empty">%8$s</option>
					</select>',
					esc_html__( 'equals', 'et_builder' ),
					esc_html__( 'does not equal', 'et_builder' ),
					esc_html__( 'is greater than', 'et_builder' ),
					esc_html__( 'is less than', 'et_builder' ),
					esc_html__( 'contains', 'et_builder' ),
					esc_html__( 'does not contain', 'et_builder' ),
					esc_html__( 'is empty', 'et_builder' ),
					esc_html__( 'is not empty', 'et_builder' )
				);

				$field_el = sprintf(
					'<div class="et_options_list et_conditional_logic" data-checked="%6$s" data-unchecked="%7$s">
						<div class="et_options_list_row">
							%5$s
							<a href="#" class="et_options_list_remove"></a>
						</div>
						<textarea class="et-pb-main-setting large-text code%1$s" rows="4" cols="50" id="%2$s"><%%= typeof( %3$s ) !== \'undefined\' ? %4$s : \'\' %%></textarea>
						<a href="#" class="et-pb-add-sortable-option"><span>%8$s</span></a>
					</div>',
					esc_attr( $field['class'] ),
					esc_attr( $field['id'] ),
					esc_html( $field_name ),
					et_esc_previously( $field_custom_value ),
					$field_selects,
					esc_html__( 'checked', 'et_builder' ),
					esc_html__( 'not checked', 'et_builder' ),
					esc_html__( 'Add New Rule', 'et_builder' )
				);
				break;
			case 'text_align':
			case 'select':
			case 'divider':
			case 'yes_no_button':
			case 'multiple_buttons':
			case 'font':
			case 'select_with_option_groups':
				if ( 'font' === $field['type'] ) {
					$field['id']    .= '_select';
					$field_name     .= '_select';
					$field['class'] .= ' et-pb-helper-field';
					$field['options'] = array();
				}

				if ( 'text_align' === $field['type'] ) {
					$field['class'] = 'et-pb-text-align-select';
				}

				$button_options = array();

				if ( 'yes_no_button' === $field['type'] ) {
					$button_options = isset( $field['button_options'] ) ? $field['button_options'] : array();
				}

				if ( isset( $field['default'] ) ) {
					$attributes .= sprintf( ' data-default="%1$s"', esc_attr( $default ) );
				}

				//If default is an array, then $default_value value is an js expression, so it doesn't need to be encoded
				//In other case it needs to be encoded
				$select_default = $default_is_arr ? $default_value : json_encode( $default_value );

				if ( 'font' === $field['type'] ) {
					$group_label = isset( $field['group_label'] ) ? $field['group_label'] : '';
					$select = $this->render_font_select( $field_name, $field['id'], $group_label );
				} else if ( 'multiple_buttons' === $field['type'] ) {
					if ( isset( $field['toggleable'] ) && $field['toggleable'] ) {
						$attributes .= ' data-toggleable="yes"';
					}
					if ( isset( $field['multi_selection'] ) && $field['multi_selection'] ) {
						$attributes .= ' data-multi="yes"';
					}

					$select = $this->render_multiple_buttons( $field_name, $field['options'], $field['id'], $field['class'], $attributes, $value, $default_value );
				} else {
					$select = $this->render_select( $field_name, $field['options'], $field['id'], $field['class'], $attributes, $field['type'], $button_options, $select_default, $only_options );
				}

				if ( $only_options ) {
					$field_el = $select;
				} else {
					$field_el .= $select;
				}

				if ( 'font' === $field['type'] ) {
					$font_style_button_html = sprintf(
						'<%%= window.et_builder.options_template_output("font_buttons",%1$s) %%>',
						json_encode( array( 'italic', 'uppercase', 'capitalize', 'underline', 'line_through' ) )
					);

					$field_el .= sprintf(
						'<div class="et_builder_font_styles mce-toolbar">
							%1$s
						</div>',
						$font_style_button_html
					);

					$field_el .= '<%= window.et_builder.options_template_output("font_line_styles") %>';

					$field_el .= $hidden_field;
				}

				if ( 'text_align' === $field['type'] ) {
					$text_align_options = ! empty( $field[ 'options' ] ) ? array_keys( $field[ 'options' ] ) : array( 'left', 'center', 'right', 'justified' );
					$is_module_alignment = in_array( $field['name'], array( 'et_pb_module_alignment', 'et_pb_button_alignment' ) ) || ( isset( $field['options_icon'] ) && 'module_align'  === $field['options_icon'] );

					$text_align_style_button_html = sprintf(
						'<%%= window.et_builder.options_text_align_buttons_output(%1$s, "%2$s") %%>',
						json_encode( $text_align_options ),
						$is_module_alignment ? 'module' : 'text'
					);

					$field_el .= sprintf(
						'<div class="et_builder_text_aligns mce-toolbar">
							%1$s
						</div>',
						$text_align_style_button_html
					);

					$field_el .= $hidden_field;
				}

				if ( 'select_with_option_groups' === $field['type'] ) {
					$field_el .= $hidden_field;
				}

				break;
			case 'select_animation':
				$options                 = $field['options'];
				$animation_buttons_array = array();

				foreach ( $options as $option_name => $option_title ) {
					$animation_buttons_array[ $option_name ] = sanitize_text_field( $option_title );
				}

				$animation_buttons = sprintf( '<%%= window.et_builder.options_template_output("animation_buttons",%1$s) %%>', json_encode( $animation_buttons_array ) );

				$field_el = sprintf(
					'<div class="et_select_animation et-pb-main-setting" data-default="none">
						%1$s
						%2$s
					</div>',
					$animation_buttons,
					$hidden_field
				);
				break;
			case 'presets_shadow':
			case 'select_box_shadow':
			case 'presets':
				$presets         = $field['presets'];
				$presets_buttons = '';

				foreach ( $presets as $preset ) {
					$fields = isset( $preset['fields'] )
						? htmlspecialchars( json_encode( $preset['fields'] ), ENT_QUOTES, 'UTF-8' )
						: '[]';
					$presets_buttons .= sprintf(
						'<div class="et-preset" data-value="%1$s" data-fields="%2$s">',
						esc_attr( $preset['value'] ),
						esc_attr( $fields )
					);
					if ( isset( $preset['title'] ) && ! empty( $preset['title'] ) ) {
						$presets_buttons .= sprintf(
							'<span class="et-preset-title" >%1$s</span>',
							$preset['title']
						);
					}

					if ( isset( $preset['icon'] ) && ! empty( $preset['icon'] ) ) {
						$presets_buttons .= sprintf(
							'<span class="et-preset-icon">%1$s</span>',
							$this->get_icon( $preset['icon'] )
						);
					}

					if ( isset( $preset['content'] ) && ! empty( $preset['content'] ) ) {
						if ( is_array( $preset['content'] ) ) {
							$content = isset( $preset['content']['content'] ) ? $preset['content']['content'] : '';
							$class   = isset( $preset['content']['class'] ) ? ' ' . $preset['content']['class'] : '';
						} else {
							$content = $preset['content'];
							$class = '';
						}

						$presets_buttons .= sprintf(
							'<span class="et-preset-content%2$s">%1$s</span>',
							$content,
							$class
						);
					}

					$presets_buttons .= '</div>';
				}

				$field_el = sprintf(
					'<div class="et-presets et-preset-container et-pb-main-setting %3$s" data-default="none">
						%1$s
						%2$s
					</div>',
					$presets_buttons,
					$hidden_field,
					esc_attr( $field['type'] )
				);
				break;
			case 'color':
			case 'color-alpha':
				$field['default'] = ! empty( $field['default'] ) ? $field['default'] : '';

				if ( $is_custom_color && ( ! isset( $field['default'] ) || '' === $field['default'] ) ) {
					$field['default'] = '';
				}

				$default = ! empty( $field['default'] ) && ! $is_custom_color ? sprintf( ' data-default-color="%1$s" data-default="%1$s"', esc_attr( $field['default'] ) ) : '';

				$color_id = sprintf( ' id="%1$s"', esc_attr( $field['id'] ) );
				$color_value_html = '<%%- typeof( %1$s ) !== \'undefined\' && %1$s !== \'\' ? %1$s : \'%2$s\' %%>';
				$main_color_value = sprintf( $color_value_html, esc_attr( $field_name ), $field['default'] );
				$hidden_color_value = sprintf( $color_value_html, esc_attr( $field_name ), '' );
				$has_preview = isset( $field['has_preview'] ) && $field['has_preview'];

				$field_el = sprintf(
					'<input%1$s class="et-pb-color-picker-hex%5$s%8$s%10$s" type="text"%6$s%7$s placeholder="%9$s" data-selected-value="%2$s" value="%2$s"%3$s />
					%4$s',
					( ! $is_custom_color || $has_preview ? $color_id : '' ),
					$main_color_value,
					$default,
					( ! empty( $field['additional_code'] ) ? $field['additional_code'] : '' ),
					( 'color-alpha' === $field['type'] ? ' et-pb-color-picker-hex-alpha' : '' ),
					( 'color-alpha' === $field['type'] ? ' data-alpha="true"' : '' ),
					( 'color' === $field['type'] ? ' maxlength="7"' : '' ),
					( ! $is_custom_color ? ' et-pb-main-setting' : '' ),
					esc_attr__( 'Hex Value', 'et_builder' ),
					$has_preview ? esc_attr( ' et-pb-color-picker-hex-has-preview' ) : ''
				);

				if ( $is_custom_color && ! $has_preview ) {
					$field_el = sprintf(
						'<span class="et-pb-custom-color-button et-pb-choose-custom-color-button"><span>%1$s</span></span>
						<div class="et-pb-custom-color-container et_pb_hidden">
							%2$s
							<input%3$s class="et-pb-main-setting et-pb-custom-color-picker" type="hidden" value="%4$s" %6$s />
							%5$s
						</div>',
						esc_html__( 'Choose Custom Color', 'et_builder' ),
						$field_el,
						$color_id,
						$hidden_color_value,
						$reset_button_html,
						$attributes
					);
				}
				break;
			case 'upload':
				$field_data_type = ! empty( $field['data_type'] ) ? $field['data_type'] : 'image';
				$field['upload_button_text'] = ! empty( $field['upload_button_text'] ) ? $field['upload_button_text'] : esc_attr__( 'Upload', 'et_builder' );
				$field['choose_text'] = ! empty( $field['choose_text'] ) ? $field['choose_text'] : esc_attr__( 'Choose image', 'et_builder' );
				$field['update_text'] = ! empty( $field['update_text'] ) ? $field['update_text'] : esc_attr__( 'Set image', 'et_builder' );
				$field['class'] = ! empty( $field['class'] ) ? ' ' . $field['class'] : '';
				$field_additional_button = ! empty( $field['additional_button'] ) ? "\n\t\t\t\t\t" . $field['additional_button'] : '';
				$field_el .= sprintf(
					'<input id="%1$s" type="text" class="et-pb-main-setting regular-text et-pb-upload-field%8$s" value="<%%- typeof( %2$s ) !== \'undefined\' ? %2$s : \'\' %%>" %9$s />
					<input type="button" class="button button-upload et-pb-upload-button" value="%3$s" data-choose="%4$s" data-update="%5$s" data-type="%6$s" />%7$s',
					esc_attr( $field['id'] ),
					esc_attr( $field_name ),
					esc_attr( $field['upload_button_text'] ),
					esc_attr( $field['choose_text'] ),
					esc_attr( $field['update_text'] ),
					esc_attr( $field_data_type ),
					$field_additional_button,
					esc_attr( $field['class'] ),
					$attributes
				);
				break;
			case 'checkbox':
				$field_el .= sprintf(
					'<input type="checkbox" name="%1$s" id="%2$s" class="et-pb-main-setting" value="on" <%%- typeof( %1$s ) !==  \'undefined\' && %1$s == \'on\' ? checked="checked" : "" %%>>',
					esc_attr( $field['name'] ),
					esc_attr( $field['id'] )
				);
				break;
			case 'multiple_checkboxes' :
				$checkboxes_set = '<div class="et_pb_checkboxes_wrapper">';

				if ( ! empty( $field['options'] ) ) {
					foreach( $field['options'] as $option_value => $option_label ) {
						$checkboxes_set .= sprintf(
							'%3$s<label><input type="checkbox" class="et_pb_checkbox_%1$s" value="%1$s"> %2$s</label><br/>',
							esc_attr( $option_value ),
							esc_html( $option_label ),
							"\n\t\t\t\t\t"
						);
					}
				}

				// additional option for disable_on option for backward compatibility
				if ( isset( $field['additional_att'] ) && 'disable_on' === $field['additional_att'] ) {
					$et_pb_disabled_value = sprintf(
						$value_html,
						esc_attr( 'et_pb_disabled' ),
						esc_attr( 'et_pb_disabled' ),
						''
					);

					$checkboxes_set .= sprintf(
						'<input type="hidden" id="et_pb_disabled" class="et_pb_disabled_option"%1$s>',
						$et_pb_disabled_value
					);
				}

				$field_el .= $checkboxes_set . $hidden_field . '</div>';
				break;
			case 'hidden':
				$field_el .= $hidden_field;
				break;
			case 'custom_margin':
			case 'custom_padding':

				$custom_margin_class = "";

				// Fill the array of values for tablet and phone
				if ( $need_mobile_options ) {
					$mobile_values_array = array();
					$has_saved_value = array();
					$mobile_desktop_class = ' et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active';
					$mobile_desktop_data = ' data-device="desktop"';

					foreach( array( 'tablet', 'phone' ) as $device ) {
						$mobile_values_array[] = sprintf(
							$value_html,
							esc_attr( $field_name . '_' . $device ),
							esc_attr( $field_name . '_' . $device ),
							$default_value
						);
						$has_saved_value[] = sprintf( ' data-has_saved_value="<%%- typeof( %1$s ) !== \'undefined\' ? \'yes\' : \'no\' %%>" ',
							esc_attr( $field_name . '_' . $device )
						);
					}

					$value_last_edited = sprintf(
						$value_html,
						esc_attr( $field_name . '_last_edited' ),
						esc_attr( $field_name . '_last_edited' ),
						''
					);
					// additional field to save the last edited field which will be opened automatically
					$additional_mobile_fields = sprintf( '<input id="%1$s" type="hidden" class="et_pb_mobile_last_edited_field"%2$s>',
						esc_attr( $field_name . '_last_edited' ),
						$value_last_edited
					);
				}

				// Add auto_important class to field which automatically append !important tag
				if ( isset( $this->advanced_options['custom_margin_padding']['css']['important'] ) ) {
					$custom_margin_class .= " auto_important";
				}

				$has_responsive_affects = isset( $field['responsive_affects'] );

				$single_fields_settings = array(
					'side' => '',
					'label' => '',
					'need_mobile' => $need_mobile_options ? 'need_mobile' : '',
					'class' => esc_attr( $custom_margin_class ),
				);

				$field_el .= sprintf(
					'<div class="et_custom_margin_padding">
						%6$s
						%7$s
						%8$s
						%9$s
						<input type="hidden" name="%1$s" data-default="%5$s" id="%2$s" class="et_custom_margin_main et-pb-main-setting%11$s%14$s"%12$s %3$s %4$s/>
						%10$s
						%13$s
					</div>',
					esc_attr( $field['name'] ),
					esc_attr( $field['id'] ),
					$value,
					$attributes,
					esc_attr( $default ), // #5
					! isset( $field['sides'] ) || ( ! empty( $field['sides'] ) && in_array( 'top', $field['sides'] ) ) ?
						sprintf( '<%%= window.et_builder.options_template_output("padding",%1$s) %%>',
							json_encode( array_merge( $single_fields_settings, array(
								'side' => 'top',
								'label' => esc_html__( 'Top', 'et_builder' ),
							) ) )
						) : '',
					! isset( $field['sides'] ) || ( ! empty( $field['sides'] ) && in_array( 'right', $field['sides'] ) ) ?
						sprintf( '<%%= window.et_builder.options_template_output("padding",%1$s) %%>',
							json_encode( array_merge( $single_fields_settings, array(
								'side' => 'right',
								'label' => esc_html__( 'Right', 'et_builder' ),
							) ) )
						) : '',
					! isset( $field['sides'] ) || ( ! empty( $field['sides'] ) && in_array( 'bottom', $field['sides'] ) ) ?
						sprintf( '<%%= window.et_builder.options_template_output("padding",%1$s) %%>',
							json_encode( array_merge( $single_fields_settings, array(
								'side' => 'bottom',
								'label' => esc_html__( 'Bottom', 'et_builder' ),
							) ) )
						) : '',
					! isset( $field['sides'] ) || ( ! empty( $field['sides'] ) && in_array( 'left', $field['sides'] ) ) ?
						sprintf( '<%%= window.et_builder.options_template_output("padding",%1$s) %%>',
							json_encode( array_merge( $single_fields_settings, array(
								'side' => 'left',
								'label' => esc_html__( 'Left', 'et_builder' ),
							) ) )
						) : '',
					$need_mobile_options ?
						sprintf(
							'<input type="hidden" name="%1$s_tablet" data-default="%4$s" id="%2$s_tablet" class="et-pb-main-setting et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_tablet%9$s" data-device="tablet" %5$s %3$s %7$s/>
							<input type="hidden" name="%1$s_phone" data-default="%4$s" id="%2$s_phone" class="et-pb-main-setting et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_phone%9$s" data-device="phone" %6$s %3$s %8$s/>',
							esc_attr( $field['name'] ),
							esc_attr( $field['id'] ),
							$attributes,
							esc_attr( $default ),
							$mobile_values_array[0],
							$mobile_values_array[1],
							$has_saved_value[0],
							$has_saved_value[1],
							$has_responsive_affects ? ' et-pb-responsive-affects' : ''
						)
						: '', // #10
					$need_mobile_options ? esc_attr( $mobile_desktop_class ) : '',
					$need_mobile_options ? $mobile_desktop_data : '',
					$need_mobile_options ? $additional_mobile_fields : '',
					$has_responsive_affects ? ' et-pb-responsive-affects' : '' // #14
				);
				break;
			case 'text':
			case 'number':
			case 'date_picker':
			case 'range':
			default:
				$validate_number = isset( $field['number_validation'] ) && $field['number_validation'] ? true : false;

				if ( 'date_picker' === $field['type'] ) {
					$field['class'] .= ' et-pb-date-time-picker';
				}

				$field['class'] .= 'range' === $field['type'] ? ' et-pb-range-input' : ' et-pb-main-setting';

				$type = in_array( $field['type'], array( 'text', 'number' ) ) ? $field['type'] : 'text';

				$field_el .= sprintf(
					'<input id="%1$s" type="%11$s" class="%2$s%5$s%9$s"%6$s%3$s%8$s%10$s %4$s/>%7$s',
					esc_attr( $field['id'] ),
					esc_attr( $field['class'] ),
					$value,
					$attributes,
					( $validate_number ? ' et-validate-number' : '' ),
					( $validate_number ? ' maxlength="3"' : '' ),
					( ! empty( $field['additional_button'] ) ? $field['additional_button'] : '' ),
					( '' !== $default
						? sprintf( ' data-default="%1$s"', esc_attr( $default ) )
						: ''
					),
					$need_mobile_options ? ' et_pb_setting_mobile et_pb_setting_mobile_active et_pb_setting_mobile_desktop' : '',
					$need_mobile_options ? ' data-device="desktop"' : '',
					$type
				);

				// generate additional fields for mobile settings switcher if needed
				if ( $need_mobile_options ) {
					$additional_fields = '';

					foreach( array( 'tablet', 'phone' ) as $device_type ) {
						$value_mobile = sprintf(
							$value_html,
							esc_attr( $field_name . '_' . $device_type ),
							esc_attr( $field_name . '_' . $device_type ),
							$default_value
						);
						// additional data attribute to handle default values for the responsive options
						$has_saved_value = sprintf( ' data-has_saved_value="<%%- typeof( %1$s ) !== \'undefined\' ? \'yes\' : \'no\' %%>" ',
							esc_attr( $field_name . '_' . $device_type )
						);

						$additional_fields .= sprintf( '<input id="%2$s" type="%11$s" class="%3$s%5$s et_pb_setting_mobile et_pb_setting_mobile_%9$s"%6$s%8$s%1$s data-device="%9$s" %4$s%10$s/>%7$s',
							$value_mobile,
							esc_attr( $field['id'] ) . '_' . $device_type,
							esc_attr( $field['class'] ),
							$attributes,
							( $validate_number ? ' et-validate-number' : '' ), // #5
							( $validate_number ? ' maxlength="3"' : '' ),
							( ! empty( $field['additional_button'] ) ? $field['additional_button'] : '' ),
							( '' !== $default
								? sprintf( ' data-default="%1$s"', esc_attr( $default ) )
								: ''
							),
							esc_attr( $device_type ),
							$has_saved_value, // #10,
							$type
						);
					}

					$value_last_edited = sprintf(
						$value_html,
						esc_attr( $field_name . '_last_edited' ),
						esc_attr( $field_name . '_last_edited' ),
						''
					);

					$class_last_edited = array(
						'et_pb_mobile_last_edited_field',
					);

					$attrs = '';

					if ( ! empty( $field['responsive_affects'] ) ) {
						$class_last_edited[] = 'et-pb-responsive-affects';

						$attrs .= sprintf(
							' data-responsive-affects="%1$s" data-responsive-desktop-name="%2$s"',
							esc_attr( implode( ', ', $field['responsive_affects'] ) ),
							esc_attr( $field['name'] )
						);
					}

					// additional field to save the last edited field which will be opened automatically
					$additional_fields .= sprintf( '<input id="%1$s" type="hidden" class="%3$s"%2$s%4$s>',
						esc_attr( $field_name . '_last_edited' ),
						$value_last_edited,
						esc_attr( implode( ' ', $class_last_edited ) ),
						$attrs
					);
				}

				if ( 'range' === $field['type'] ) {
					$range_value_html = $default_is_arr
						? ' value="<%%- typeof( %1$s ) !== \'undefined\' ?  %2$s :parseFloat(%3$s) %%>" '
						: ' value="<%%- typeof( %1$s ) !== \'undefined\' ?  %2$s : parseFloat(\'%3$s\') %%>" ';
					$value = sprintf(
						$range_value_html,
						esc_attr( $field_name ),
						esc_attr( sprintf( 'parseFloat( %1$s )', $field_name ) ),
						$default_value
					);
					$fixed_range = isset($field['fixed_range']) && $field['fixed_range'];

					$range_settings_html = '';
					$range_properties = apply_filters( 'et_builder_range_properties', array( 'min', 'max', 'step' ) );
					foreach ( $range_properties as $property ) {
						if ( isset( $field['range_settings'][ $property ] ) ) {
							$range_settings_html .= sprintf( ' %2$s="%1$s"',
								esc_attr( $field['range_settings'][ $property ] ),
								esc_html( $property )
							);
						}
					}

					$range_el = sprintf(
						'<input type="range" class="et-pb-main-setting et-pb-range%4$s%6$s" data-default="%2$s"%1$s%3$s%5$s />',
						$value,
						esc_attr( $default ),
						$range_settings_html,
						$need_mobile_options ? ' et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active' : '',
						$need_mobile_options ? ' data-device="desktop"' : '',
						$fixed_range ? ' et-pb-fixed-range' : ''
					);

					if ( $need_mobile_options ) {
						foreach( array( 'tablet', 'phone' ) as $device_type ) {
							// additional data attribute to handle default values for the responsive options
							$has_saved_value = sprintf( ' data-has_saved_value="<%%- typeof( %1$s ) !== \'undefined\' ? \'yes\' : \'no\' %%>" ',
								esc_attr( $field_name . '_' . $device_type )
							);
							$value_mobile_range = sprintf(
								$value_html,
								esc_attr( $field_name . '_' . $device_type ),
								esc_attr( sprintf( 'parseFloat( %1$s )', $field_name . '_' . $device_type ) ),
								$default_value
							);
							$range_el .= sprintf(
								'<input type="range" class="et-pb-main-setting et-pb-range et_pb_setting_mobile et_pb_setting_mobile_%3$s%6$s" data-default="%1$s"%4$s%2$s data-device="%3$s"%5$s/>',
								esc_attr( $default ),
								$range_settings_html,
								esc_attr( $device_type ),
								$value_mobile_range,
								$has_saved_value,
								$fixed_range ? ' et-pb-fixed-range' : ''
							);
						}
					}

					$field_el = $range_el . "\n" . $field_el;
				}

				if ( $need_mobile_options ) {
					$field_el = $field_el . $additional_fields;
				}

				break;
		}

		if ( isset( $field['has_preview'] ) && $field['has_preview'] ) {
			$field_el = sprintf(
				'<%%= window.et_builder.options_template_output("option_preview_buttons") %%>
				%1$s',
				$field_el
			);
		}

		if ( $need_mobile_options ) {
			$field_el = $mobile_settings_tabs . "\n" . $field_el;
			$field_el .= '<span class="et-pb-mobile-settings-toggle"></span>';
		}

		if ( isset( $field['type'] ) && isset( $field['tab_slug'] ) && 'advanced' === $field['tab_slug'] && ! $is_custom_color ) {
			$field_el .= $reset_button_html;
		}

		if ( isset( $field['after'] ) && ! $only_options ) {
			$field_el .= $this->render_field_before_after_element( $field['after'] );
		}

		return "\t" . $field_el;
	}

	public function render_field_before_after_element( $elements ) {
		$field_el = '';
		$elements = is_array( $elements ) ? $elements : array( $elements );

		foreach ( $elements as $element ) {
			$attributes = '';

			if ( ! empty( $element['attributes'] ) ) {
				$this->process_html_attributes( $element, $attributes );
			}

			switch ( $element['type'] ) {
				case 'button':
					$class     = isset( $element['class'] ) ? esc_attr( $element['class'] ) : '';
					$text      = isset( $element['text'] ) ? et_esc_previously( $element['text'] ) : '';
					$field_el .= sprintf( '<button class="button %1$s"%2$s>%3$s</button>', $class, $attributes, $text );

					break;
			}
		}

		return $field_el;
	}

	function render_font_select( $name, $id = '', $group_label ) {
		$options_output = '<%= window.et_builder.fonts_template() %>';
		$font_weight_output = '<%= window.et_builder.fonts_weight_template() %>';

		$output = sprintf(
			'<div class="et-pb-select-font-outer" data-group_label="%6$s">
				<div class="et-pb-settings-custom-select-wrapper et-pb-settings-option-select-searchable">
					<div class="et_pb_select_placeholder"></div>
					<ul class="et-pb-settings-option-select et-pb-settings-option-select-advanced et-pb-main-setting">
						<li class="et-pb-select-options-filter">
							<input type="text" class="et-pb-settings-option-input et-pb-main-setting regular-text et-pb-menu-filter" placeholder="Search Fonts">
						</li>
						<li class="et_pb_selected_item_container select-option-item">
						</li>
						<li class="et-pb-option-subgroup et-pb-recent-fonts">
							<p class="et-pb-subgroup-title">%4$s</p>
							<ul>
							</ul>
						</li>
						%3$s
					</ul>
				</div>
			</div>
			%5$s',
			esc_attr( $name ),
			( ! empty( $id ) ? sprintf(' id="%s"', esc_attr( $id ) ) : '' ),
			$options_output . "\n\t\t\t\t\t",
			esc_html__( 'Recent', 'et_builder' ),
			$font_weight_output,
			esc_attr( $group_label )
		);

		return $output;
	}

	function render_select( $name, $options, $id = '', $class = '', $attributes = '', $field_type = '', $button_options = array(), $default = '', $only_options = false ) {
		$options_output = '';
		$processed_options = $options;

		if ( 'select_with_option_groups' === $field_type ) {
			foreach ( $processed_options as $option_group_name => $option_group ) {
				$option_group_name = esc_attr( $option_group_name );
				$options_output   .= '0' !== $option_group_name ? "<optgroup label='{$option_group_name}'>" : '';
				$options_output   .= sprintf( '<%%= window.et_builder.options_template_output("select",%1$s,this.model.toJSON()) %%>',
					sprintf(
						'{select_name: "%1$s", list: %2$s, default: %3$s, }',
						$name,
						json_encode($option_group),
						$default
					)
				);
				$options_output   .= '0' !== $option_group_name ? '</optgroup>' : '';
			}

			$class = rtrim( $class );

			$name = $id = '';

		} else {
			$class           = rtrim( 'et-pb-main-setting ' . $class );
			$options_output .= sprintf( '<%%= window.et_builder.options_template_output("select",%1$s,this.model.toJSON()) %%>',
				sprintf(
					'{select_name: "%1$s", list: %2$s, default: %3$s, }',
					$name,
					json_encode($options),
					$default
				)
			);
		}

		$output = sprintf(
			'%6$s
				<select name="%1$s"%2$s%3$s%4$s class="%3$s %8$s"%9$s>%5$s</select>
			%7$s',
			esc_attr( $name ),
			( ! empty( $id ) ? sprintf(' id="%s"', esc_attr( $id ) ) : '' ),
			( ! empty( $class ) ? esc_attr( $class ) : '' ),
			( ! empty( $attributes ) ? $attributes : '' ),
			$options_output . "\n\t\t\t\t\t",
			'yes_no_button' === $field_type ?
				sprintf(
					'<div class="et_pb_yes_no_button_wrapper %2$s">
						%1$s',
					sprintf( '<%%= window.et_builder.options_template_output("yes_no_button",%1$s) %%>',
						json_encode( array(
							'on' => esc_html( $processed_options['on'] ),
							'off' => esc_html( $processed_options['off'] ),
						) )
					),
					( ! empty( $button_options['button_type'] ) && 'equal' === $button_options['button_type'] ? ' et_pb_button_equal_sides' : '' )
				) : '',
			'yes_no_button' === $field_type ? '</div>' : '',
			esc_attr( $field_type ),
			'' !== $name ? sprintf( ' data-saved_value="<%%= typeof( %1$s ) !== \'undefined\' ? %1$s : \'\' %%>"', esc_attr(  $name ) ) : ''
		);

		return $only_options ? $options_output : $output;
	}

	function render_multiple_buttons( $name, $options, $id = '', $class = '', $attributes = '', $value = '', $default = '' ) {
		$class = rtrim( 'et-pb-main-setting ' . $class );

		$output = sprintf(
			'<div class="et_pb_multiple_buttons_wrapper">
				<input id="%1$s" name="%7$s" type="hidden" class="%2$s" %3$s%5$s %4$s/>
				%6$s
			</div>',
			esc_attr( $id ),
			esc_attr( $class ),
			$value,
			$attributes,
			( '' !== $default
				? sprintf( ' data-default=%1$s', esc_attr( $default ) )
				: ''
			),
			sprintf( '<%%= window.et_builder.options_template_output("multiple_buttons",%1$s) %%>',
				json_encode( $options )
			),
			esc_attr( $name )
		);

		return $output;
	}

	function get_main_tabs() {
		$tabs = array(
			'general'    => esc_html__( 'Content', 'et_builder' ),
			'advanced'   => esc_html__( 'Design', 'et_builder' ),
			'custom_css' => esc_html__( 'Advanced', 'et_builder' ),
		);

		return apply_filters( 'et_builder_main_tabs', $tabs );
	}

	function get_validation_attr_rules() {
		return array(
			'minlength',
			'maxlength',
			'min',
			'max',
		);
	}

	function get_validation_class_rules() {
		return array(
			'required',
			'email',
			'url',
			'date',
			'dateISO',
			'number',
			'digits',
			'creditcard',
		);
	}

	function sort_fields( $fields ) {
		$tabs_fields   = array();
		$sorted_fields = array();
		$i = 0;

		// Sort fields array by tab name
		foreach ( $fields as $field_slug => $field_options ) {
			$field_options['_order_number'] = $i;

			$tab_slug = ! empty( $field_options['tab_slug'] ) ? $field_options['tab_slug'] : 'general';
			$tabs_fields[ $tab_slug ][ $field_slug ] = $field_options;

			$i++;
		}

		// Sort fields within tabs by priority
		foreach ( $tabs_fields as $tab_fields ) {
			uasort( $tab_fields, array( 'self', 'compare_by_priority' ) );
			$sorted_fields = array_merge( $sorted_fields, $tab_fields );
		}

		return $sorted_fields;
	}

	function get_options() {
		$output = '';
		$tab_output = '';
		$tab_slug = '';
		$toggle_slug = '';
		$toggle_all_options_slug = 'all_options';
		$toggles_used = isset( $this->options_toggles );
		$tabs_output = array( 'general' => array() );
		$all_fields = $this->sort_fields( $this->_get_fields() );
		$all_fields_keys = array_keys( $all_fields );
		$background_fields_names = $this->get_background_fields_names();
		$module_has_background_color_field = in_array( 'background_color', $all_fields_keys );

		foreach( $all_fields as $field_name => $field ) {
			if ( ! empty( $field['type'] ) && ( 'skip' === $field['type'] || 'computed' === $field['type'] ) ) {
				continue;
			}

			// add only options allowed for current user
			if (
				( ! et_pb_is_allowed( 'edit_colors' ) && ( ! empty( $field['type'] ) && in_array( $field['type'], array( 'color', 'color-alpha' ) ) || ( ! empty( $field['option_category'] ) && 'color_option' === $field['option_category'] ) ) )
				||
				( ! et_pb_is_allowed( 'edit_content' ) && ! empty( $field['option_category'] ) && 'basic_option' === $field['option_category'] )
				||
				( ! et_pb_is_allowed( 'edit_layout' ) && ! empty( $field['option_category'] ) && 'layout' === $field['option_category'] )
				||
				( ! et_pb_is_allowed( 'edit_configuration' ) && ! empty( $field['option_category'] ) && 'configuration' === $field['option_category'] )
				||
				( ! et_pb_is_allowed( 'edit_fonts' ) && ! empty( $field['option_category'] ) && ( 'font_option' === $field['option_category'] || ( 'button' === $field['option_category'] && ! empty( $field['type'] ) && 'font' === $field['type'] ) ) )
				||
				( ! et_pb_is_allowed( 'edit_buttons' ) && ! empty( $field['option_category'] ) && 'button' === $field['option_category'] )
			) {
				continue;
			}

			$option_output = '';

			if ( 'background_color' === $field_name ) {
				// append background UI
				$option_output .= $this->wrap_settings_background_fields( $all_fields );
			} elseif ( $module_has_background_color_field && in_array( $field_name , $background_fields_names ) ) {
				// remove background-related fields from setting modals since it'll be printed by background UI
				continue;
			} else {
				// append normal fields
				$option_output .= $this->wrap_settings_option_label( $field );
				$option_output .= $this->wrap_settings_option_field( $field );
			}

			$tab_slug = ! empty( $field['tab_slug'] ) ? $field['tab_slug'] : 'general';
			$is_toggle_option = isset( $field['toggle_slug'] ) && $toggles_used && isset( $this->options_toggles[ $tab_slug ] );
			$toggle_slug = $is_toggle_option ? $field['toggle_slug'] : $toggle_all_options_slug;
			$sub_toggle_slug = 'all_options' !== $toggle_slug && isset( $field['sub_toggle'] ) && '' !== $field['sub_toggle'] ? $field['sub_toggle'] : 'main';
			$tabs_output[ $tab_slug ][ $toggle_slug ][ $sub_toggle_slug ][] = $this->wrap_settings_option( $option_output, $field );
		}

		// make sure that custom_css tab is the last item in array
		if ( isset( $tabs_output['custom_css'] ) ) {
			$custom_css_output = $tabs_output['custom_css'];
			unset( $tabs_output['custom_css'] );
			$tabs_output['custom_css'] = $custom_css_output;
		}

		foreach ( $tabs_output as $tab_slug => $tab_settings ) {

			// Add only tabs allowed for current user
			if ( ! et_pb_is_allowed( $tab_slug . '_settings' ) ) {
				continue;
			}

			$tab_output        = '';
			$this->used_tabs[] = $tab_slug;
			$i = 0;

			if ( isset( $tabs_output[ $tab_slug ] ) ) {
				if ( isset( $this->options_toggles[ $tab_slug ] ) ) {
					$this->options_toggles[ $tab_slug ]['toggles'] = self::et_pb_order_toggles_by_priority( $this->options_toggles[ $tab_slug ]['toggles'] );

					foreach ( $this->options_toggles[ $tab_slug ]['toggles'] as $toggle_slug => $toggle_data ) {
						$toggle_heading = is_array( $toggle_data ) ? $toggle_data['title'] : $toggle_data;
						if ( ! isset( $tabs_output[ $tab_slug ][ $toggle_slug ] ) ) {
							continue;
						}

						$i++;
						$toggle_output = '';
						$is_accordion_enabled = isset( $this->options_toggles[ $tab_slug ]['settings']['bb_toggles_enabeld'] ) && $this->options_toggles[ $tab_slug ]['settings']['bb_toggles_enabled'] ? true : false;
						$is_tabbed_subtoggles = isset( $toggle_data['tabbed_subtoggles'] );
						$is_bb_icons_support = isset( $toggle_data['bb_icons_support'] );
						$subtoggle_tabs_nav = '';

						if ( is_array( $toggle_data ) && ! empty( $toggle_data ) ) {
							if ( ! isset( $toggle_data['sub_toggles'] ) ) {
								$toggle_data['sub_toggles'] = array( 'main' => '' );
							}

							foreach( $toggle_data['sub_toggles'] as $sub_toggle_id => $sub_toggle_data ) {
								if ( ! isset( $tabs_output[ $tab_slug ][ $toggle_slug ][ $sub_toggle_id ] ) ) {
									continue;
								}

								if ( $is_tabbed_subtoggles ) {
									$subtoggle_tabs_nav .= sprintf(
										'<li class="subtoggle_tabs_nav_item"><a class="subtoggle_tabs_nav_item_inner%3$s" data-tab_id="%1$s">%2$s</a></li>',
										$sub_toggle_id,
										$is_bb_icons_support ? '' : esc_html( $sub_toggle_data['name'] ),
										$is_bb_icons_support ? sprintf( ' subtoggle_tabs_nav_icon subtoggle_tabs_nav_icon-%1$s', esc_attr( $sub_toggle_data['icon'] ) ) : ''
									);
								}

								$subtoggle_options = '';

								foreach ( $tabs_output[ $tab_slug ][ $toggle_slug ][ $sub_toggle_id ] as $toggle_option_output ) {
									$subtoggle_options .= $toggle_option_output;
								}

								if ( 'main' === $sub_toggle_id ) {
									$toggle_output .= $subtoggle_options;
								} else {
									$toggle_output .= sprintf(
										'<div class="et_pb_subtoggle_section%2$s"%3$s>
											%1$s
										</div>',
										$subtoggle_options,
										$is_tabbed_subtoggles ? ' et_pb_tabbed_subtoggle' : '',
										$is_tabbed_subtoggles ? sprintf( ' data-tab_id="%1$s"', esc_attr( $sub_toggle_id ) ) : ''
									);
								}
							}
						} else {
							foreach ( $tabs_output[ $tab_slug ][ $toggle_slug ] as $toggle_option_id => $toggle_option_data ) {
								foreach( $toggle_option_data as $toggle_option_output ) {
									$toggle_output .= $toggle_option_output;
								}
							}
						}

						if ( '' === $toggle_output ) {
							continue;
						}

						$toggle_output = sprintf(
							'<div class="et-pb-options-toggle-container%3$s%4$s%5$s">
								<h3 class="et-pb-option-toggle-title">%1$s</h3>
								%6$s
								<div class="et-pb-option-toggle-content">
									%2$s
								</div>
							</div>',
							esc_html( $toggle_heading ),
							$toggle_output,
							( $is_accordion_enabled ? ' et-pb-options-toggle-enabled' : ' et-pb-options-toggle-disabled' ),
							( 1 === $i && $is_accordion_enabled ? ' et-pb-option-toggle-content-open' : '' ),
							$is_tabbed_subtoggles ? ' et_pb_contains_tabbed_subtoggle' : '',
							$is_tabbed_subtoggles && '' !== $subtoggle_tabs_nav ? sprintf( '<ul class="subtoggle_tabs_nav">%1$s</ul>', $subtoggle_tabs_nav ) : ''
						);

						$tab_output .= $toggle_output;
					}
				}

				if ( isset( $tabs_output[ $tab_slug ][ $toggle_all_options_slug ] ) ) {
					foreach ( $tabs_output[ $tab_slug ][ $toggle_all_options_slug ] as $no_toggle_option_data ) {
						foreach( $no_toggle_option_data as $subtoggle_id => $no_toggle_option_output ) {
							$tab_output .= $no_toggle_option_output;
						}
					}
				}
			}

			$output .= sprintf(
				'<div class="et-pb-options-tab et-pb-options-tab-%1$s">
					%3$s
					%2$s
				</div>',
				esc_attr( $tab_slug ),
				$tab_output,
				( 'general' === $tab_slug ? $this->children_settings() : '' )
			);
		}

		// return error message if no tabs allowed for current user
		if ( '' === $output ) {
			$output = esc_html__( "You don't have sufficient permissions to access the settings", 'et_builder' );
		}

		return $output;
	}

	function children_settings() {
		$output = '';
		if ( ! empty( $this->child_slug ) ) {
			$output = sprintf(
			'%6$s<div class="et-pb-option-advanced-module-settings" data-module_type="%1$s">
				<ul class="et-pb-sortable-options">
				</ul>
				<a href="#" class="et-pb-add-sortable-option"><span>%2$s</span></a>
			</div>
			<div class="et-pb-option et-pb-option-main-content et-pb-option-advanced-module">
				<label for="et_pb_content_new">%3$s</label>
				<div class="et-pb-option-container">
					<div id="et_pb_content_new"><%%= typeof( et_pb_content_new )!== \'undefined\' && \'\' !== et_pb_content_new.trim() ? et_pb_content_new : \'%7$s\' %%></div>
					<p class="description">%4$s</p>
				</div>
			</div>%5$s',
			esc_attr( $this->child_slug ),
			esc_html( $this->add_new_child_text() ),
			esc_html__( 'Content', 'et_builder' ),
			esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
			"\n\n",
			"\t",
			$this->predefined_child_modules()
			);
		}

		return $output;
	}

	function add_new_child_text() {
		$child_slug = ! empty( $this->child_item_text ) ? $this->child_item_text : '';

		$child_slug = '' === $child_slug ? esc_html__( 'Add New Item', 'et_builder' ) : sprintf( esc_html__( 'Add New %s', 'et_builder' ), $child_slug );

		return $child_slug;
	}

	function wrap_settings( $output ) {
		$tabs_output = '';
		$i = 0;
		$tabs = array();

		// General Settings Tab should be added to all modules if allowed
		if ( et_pb_is_allowed( 'general_settings' ) ) {
			$tabs['general'] = isset( $this->main_tabs['general'] ) ? $this->main_tabs['general'] : esc_html__( 'General Settings', 'et_builder' );
		}

		foreach ( $this->used_tabs as $tab_slug ) {
			if ( 'general' === $tab_slug ) {
				continue;
			}

			// Add only tabs allowed for current user
			if ( et_pb_is_allowed( $tab_slug . '_settings' ) ) {
				$tabs[ $tab_slug ] = $this->main_tabs[ $tab_slug ];
			}
		}

		$tabs_array = array();
		$tabs_json = '';

		foreach ( $tabs as $tab_slug => $tab_name ) {
			$i++;

			$tabs_array[$i] = array(
				'slug' => $tab_slug,
				'label' => $tab_name,
			);

			$tabs_json = json_encode( $tabs_array );
		}

		$tabs_output = sprintf( '<%%= window.et_builder.options_tabs_output(%1$s) %%>', $tabs_json );
		$preview_tabs_output = '<%= window.et_builder.preview_tabs_output() %>';

		$output = sprintf(
			'%2$s
			%3$s
			<div class="et-pb-options-tabs">
				%1$s
			</div>
			<div class="et-pb-preview-tab"></div>
			',
			$output,
			$tabs_output,
			$preview_tabs_output
		);

		return sprintf(
			'%2$s<div class="et-pb-main-settings">%1$s</div>%3$s',
			"\n\t\t" . $output,
			"\n\t\t",
			"\n"
		);
	}

	function wrap_validation_form( $output ) {
		return '<form class="et-builder-main-settings-form validate">' . $output . '</form>';
	}

	/**
	 * Get this module's shortcode fields mapped to their default values.
	 *
	 * @since 1.0
	 *
	 * @param array $values Optional. Shortcode fields mapped to custom values to be considered
	 *                      when determining default values of fields that have a `default_from`
	 *                      path defined.
	 *
	 * @return array
	 */
	function get_shortcode_fields( $values = array() ) {
		$fields = array();

		foreach( $this->process_fields( $this->fields_unprocessed ) as $field_name => $field ) {
			$value = '';

			if ( isset( $field['composite_type'], $field['composite_structure'] ) ) {
				require_once ET_BUILDER_DIR . 'module/field/attribute/composite/Parser.php';
				$composite_atts = ET_Builder_Module_Field_Attribute_Composite_Parser::parse( $field['composite_type'], $field['composite_structure'] );
				$fields         = array_merge( $fields, $composite_atts );
			} else {
				if ( isset( $field['shortcode_default'] ) ) {
					$value = $field['shortcode_default'];
				} else if( isset( $field['default'] ) ) {
					$value = $field['default'];
				}

				$fields[ $field_name ] = $value;
			}
		}

		$fields['disabled'] = 'off';
		$fields['disabled_on'] = '';
		$fields['global_module'] = '';
		$fields['temp_global_module'] = '';
		$fields['global_parent'] = '';
		$fields['temp_global_parent'] = '';
		$fields['saved_tabs'] = '';
		$fields['ab_subject'] = '';
		$fields['ab_subject_id'] = '';
		$fields['ab_goal'] = '';
		$fields['locked'] = '';
		$fields['template_type'] = '';
		$fields['inline_fonts'] = '';
		$fields['collapsed'] = '';

		return $fields;
	}

	function get_module_data_attributes() {
		$attributes = apply_filters(
			"{$this->slug}_data_attributes",
			array(),
			$this->shortcode_atts,
			$this->shortcode_callback_num()
		);

		$data_attributes = '';

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $name => $value ) {
				$data_attributes .= sprintf(
					' data-%1$s="%2$s"',
					sanitize_title( $name ),
					esc_attr( $value )
				);
			}
		}

		return $data_attributes;
	}

	function build_microtemplate() {
		$this->validation_in_use = false;
		$template_output = '';

		if ( 'child' === $this->type ) {
			$id_attr = sprintf( 'et-builder-advanced-setting-%s', $this->slug );
		} else {
			$id_attr = sprintf( 'et-builder-%s-module-template', $this->slug );
		}

		if ( ! isset( $this->settings_text ) ) {
			$settings_text = sprintf(
				__( '%1$s %2$s Settings', 'et_builder' ),
				esc_html( $this->name ),
				'child' === $this->type ? esc_html__( 'Item', 'et_builder' ) : esc_html__( 'Module', 'et_builder' )
			);
		} else {
			$settings_text = $this->settings_text;
		}

		if ( file_exists( ET_BUILDER_DIR . 'microtemplates/' . $this->slug . '.php' ) ) {
			ob_start();
			include ET_BUILDER_DIR . 'microtemplates/' . $this->slug . '.php';
			$output = ob_get_clean();
		} else {
			$output = $this->get_options();
		}

		$output = $this->wrap_settings( $output );
		if ( $this->validation_in_use ) {
			$output = $this->wrap_validation_form( $output );
		}

		$template_output = sprintf(
			'<script type="text/template" id="%1$s">
				<h3 class="et-pb-settings-heading">%2$s</h3>
				%3$s
			</script>',
			esc_attr( $id_attr ),
			esc_html( $settings_text ),
			$output
		);

		if ( $this->type == 'child' ) {
			$title_var = esc_js( $this->child_title_var );
			$title_var = false === strpos( $title_var, 'et_pb_' ) ? 'et_pb_'. $title_var : $title_var;
			$title_fallback_var = esc_js( $this->child_title_fallback_var );
			$title_fallback_var = false === strpos( $title_fallback_var, 'et_pb_' ) ? 'et_pb_'. $title_fallback_var : $title_fallback_var;
			$add_new_text = isset( $this->advanced_setting_title_text ) ? $this->advanced_setting_title_text : $this->add_new_child_text();

			$template_output .= sprintf(
				'%6$s<script type="text/template" id="et-builder-advanced-setting-%1$s-title">
					<%% if ( typeof( %2$s ) !== \'undefined\' && typeof( %2$s ) === \'string\' && %2$s !== \'\' ) { %%>
						<%%- %2$s.replace( /%%91/g, "[" ).replace( /%%93/g, "]" ) %%>
					<%% } else if ( typeof( %3$s ) !== \'undefined\' && typeof( %3$s ) === \'string\' && %3$s !== \'\' ) { %%>
						<%%- %3$s.replace( /%%91/g, "[" ).replace( /%%93/g, "]" ) %%>
					<%% } else { %%>
						<%%- \'%4$s\' %%>
					<%% } %%>
				</script>%5$s',
				esc_attr( $this->slug ),
				esc_html( $title_var ),
				esc_html( $title_fallback_var ),
				esc_html( $add_new_text ),
				"\n\n",
				"\t"
			);
		}

		return $template_output;
	}

	function get_gradient( $args ) {
		$defaults = apply_filters( 'et_pb_default_gradient', array(
			'type'             => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
			'direction'        => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
			'radial_direction' => ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ),
			'color_start'      => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
			'color_end'        => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
			'start_position'   => ET_Global_Settings::get_value( 'all_background_gradient_start_position' ),
			'end_position'     => ET_Global_Settings::get_value( 'all_background_gradient_end_position' ),
		) );

		$args           = wp_parse_args( array_filter( $args ), $defaults );
		$direction      = $args['type'] === 'linear' ? $args['direction'] : "circle at {$args['radial_direction']}";
		$start_position = et_sanitize_input_unit( $args['start_position'], false, '%' );
		$end_Position   = et_sanitize_input_unit( $args['end_position'], false, '%');

		return esc_html( "{$args['type']}-gradient(
			{$direction},
			{$args['color_start']} ${start_position},
			{$args['color_end']} ${end_Position}
		)" );
	}

	function get_rel_values() {
		return array(
			'bookmark',
			'external',
			'nofollow',
			'noreferrer',
			'noopener',
		);
	}

	function get_rel_attributes( $saved_value, $add_tag = true ) {
		$rel_attributes = array();

		if ( $saved_value ) {
			$rel_values    = $this->get_rel_values();
			$selected_rels = explode( '|', $saved_value );

			foreach ( $selected_rels as $index => $selected_rel ) {
				if ( ! $selected_rel || 'off' === $selected_rel ) {
					continue;
				}

				$rel_attributes[] = $rel_values[ $index ];
			}
		}

		$attr = empty( $rel_attributes ) ? '' : implode( ' ', $rel_attributes );

		return ( $add_tag && '' !== $attr ) ? sprintf( ' rel="%1$s"', esc_attr( $attr ) ) : $attr;
	}

	function get_text_orientation() {
		$text_orientation = isset( $this->shortcode_atts['text_orientation'] ) ? $this->shortcode_atts['text_orientation'] : '';

		return et_pb_get_alignment( $text_orientation );
	}

	function get_text_orientation_classname( $print_default = false ) {
		$text_orientation = $this->get_text_orientation();

		// Should be `justified` instead of justify in classname.
		$text_orientation = 'justify' === $text_orientation ? 'justified' : $text_orientation;

		$default_classname = $print_default ? ' et_pb_text_align_left' : '';

		return '' !== $text_orientation ? " et_pb_text_align_{$text_orientation}" : $default_classname;
	}

	// intended to be overridden as needed
	function get_max_width_additional_css() {
		return '';
	}

	/**
	 * Get type of element
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Remove suffix of a string
	 */
	function remove_suffix( $string, $separator = '_' ) {
		$stringAsArray = explode( $separator, $string );

		array_pop( $stringAsArray );

		return implode( $separator, $stringAsArray );
	}

	/**
	 * process the fields.
	 * @param  string $function_name String of the function_name
	 * @return void
	 */
	function process_additional_options( $function_name ) {
		if ( ! isset( $this->advanced_options ) ) {
			return false;
		}

		$this->process_advanced_fonts_options( $function_name );

		// Process Text Shadow CSS
		$this->text_shadow->process_advanced_css( $this, $function_name );

		$this->process_advanced_background_options( $function_name );

		$this->process_advanced_text_options( $function_name );

		$this->process_advanced_border_options( $function_name );

		$this->process_advanced_filter_options( $function_name );

		$this->process_max_width_options( $function_name );

		$this->process_advanced_custom_margin_options( $function_name );

		$this->process_advanced_button_options( $function_name );

		$this->process_box_shadow( $function_name );
	}

	function process_inline_fonts_option( $fonts_list ) {
		if ( '' === $fonts_list ) {
			return;
		}

		$fonts_list_array = explode( ',', $fonts_list );

		foreach( $fonts_list_array as $font_name ) {
			et_builder_enqueue_font( $font_name );
		}
	}

	function process_advanced_fonts_options( $function_name ) {
		if ( ! isset( $this->advanced_options['fonts'] ) ) {
			return;
		}

		$font_options = array();
		$slugs = array(
			'font',
			'font_size',
			'text_color',
			'letter_spacing',
			'line_height',
			'text_align',
		);
		$mobile_options_slugs = array(
			'font_size_tablet',
			'font_size_phone',
			'line_height_tablet',
			'line_height_phone',
			'letter_spacing_tablet',
			'letter_spacing_phone',
		);

		$slugs = array_merge( $slugs, $mobile_options_slugs ); // merge all slugs into single array to define them in one place

		// Separetely defined and merged *_last_edited slugs. It needs to be merged as reference but shouldn't be looped for calling mobile attributes
		$mobile_options_last_edited_slugs = array(
			'font_size_last_edited',
			'line_height_last_edited',
			'letter_spacing_last_edited',
		);

		$slugs = array_merge( $slugs, $mobile_options_last_edited_slugs );

		foreach ( $this->advanced_options['fonts'] as $option_name => $option_settings ) {
			$style = '';
			$important_options = array();
			$is_important_set = isset( $option_settings['css']['important'] );
			$is_placeholder = isset( $option_settings['css']['placeholder'] );

			$use_global_important = $is_important_set && 'all' === $option_settings['css']['important'];

			if ( ! $use_global_important && $is_important_set && 'plugin_only' === $option_settings['css']['important'] && et_is_builder_plugin_active() ) {
				$use_global_important = true;
			}

			if ( $is_important_set && is_array( $option_settings['css']['important'] ) ) {
				$important_options = $option_settings['css']['important'];

				if ( et_is_builder_plugin_active() && in_array( 'plugin_all', $option_settings['css']['important'] ) ) {
					$use_global_important = true;
				}
			}

			foreach ( $slugs as $font_option_slug ) {
				if ( isset( $this->shortcode_atts["{$option_name}_{$font_option_slug}"] ) ) {
					$font_options["{$option_name}_{$font_option_slug}"] = $this->shortcode_atts["{$option_name}_{$font_option_slug}"];
				}
			}

			$field_key = "{$option_name}_{$slugs[0]}";
			$global_setting_name  = $this->get_global_setting_name( $field_key );
			$global_setting_value = ET_Global_Settings::get_value( $global_setting_name );
			$field_option_value = isset( $font_options[ $field_key ] ) ? $font_options[ $field_key ] : '';

			if ( '' !== $field_option_value || ! $global_setting_value ) {
				$important = in_array( 'font', $important_options ) || $use_global_important ? ' !important' : '';
				$font_styles = et_builder_set_element_font( $field_option_value, ( '' !== $important ), $global_setting_value );

				if ( isset( $option_settings['css']['font'] ) ) {
					self::set_style( $function_name, array(
						'selector'    => $option_settings['css']['font'],
						'declaration' => rtrim( $font_styles ),
						'priority'    => $this->_style_priority,
					) );
				} else {
					$style .= $font_styles;
				}
			}

			$size_option_name = "{$option_name}_{$slugs[1]}";
			$default_size     = isset( $this->fields_unprocessed[ $size_option_name ]['default'] ) ? $this->fields_unprocessed[ $size_option_name ]['default'] : '';

			if ( isset( $font_options[ $size_option_name ] ) && ! in_array( trim( $font_options[ $size_option_name ] ), array( '', 'px', $default_size ) ) ) {
				$important = in_array( 'size', $important_options ) || $use_global_important ? ' !important' : '';

				$style .= sprintf(
					'font-size: %1$s%2$s; ',
					esc_html( et_builder_process_range_value( $font_options[ $size_option_name ] ) ),
					esc_html( $important )
				);
			}

			$text_color_option_name = "{$option_name}_{$slugs[2]}";

			if ( isset( $font_options[ $text_color_option_name ] ) && '' !== $font_options[ $text_color_option_name ] ) {
				// handle the value from old option
				$old_option_ref = isset( $option_settings['text_color'] ) && isset( $option_settings['text_color']['old_option_ref'] ) ? $option_settings['text_color']['old_option_ref'] : '';
				$old_option_val = '' !== $old_option_ref && isset( $this->shortcode_atts[ $old_option_ref ] ) ? $this->shortcode_atts[ $old_option_ref ] : '';
				$default_value = '' !== $old_option_val && isset( $option_settings['text_color'] ) && isset( $option_settings['text_color']['default'] ) ? $option_settings['text_color']['default'] : '';
				$important = ' !important';

				if ( $default_value !== $font_options[ $text_color_option_name ] ) {
					if ( isset( $option_settings['css']['color'] ) ) {
						self::set_style( $function_name, array(
							'selector'    => $option_settings['css']['color'],
							'declaration' => sprintf(
								'color: %1$s%2$s;',
								esc_html( $font_options[ $text_color_option_name ] ),
								esc_html( $important )
							),
							'priority'    => $this->_style_priority,
						) );
					} else {
						$style .= sprintf(
							'color: %1$s%2$s; ',
							esc_html( $font_options[ $text_color_option_name ] ),
							esc_html( $important )
						);
					}
				}
			}

			$letter_spacing_option_name = "{$option_name}_{$slugs[3]}";
			$default_letter_spacing     = isset( $this->fields_unprocessed[ $letter_spacing_option_name ]['default'] ) ? $this->fields_unprocessed[ $letter_spacing_option_name ]['default'] : '';

			if ( isset( $font_options[ $letter_spacing_option_name ] ) && ! in_array( trim( $font_options[ $letter_spacing_option_name ] ), array( '', 'px', $default_letter_spacing ) ) ) {
				$important = in_array( 'letter-spacing', $important_options ) || $use_global_important ? ' !important' : '';

				$style .= sprintf(
					'letter-spacing: %1$s%2$s; ',
					esc_html( et_builder_process_range_value( $font_options[ $letter_spacing_option_name ] ) ),
					esc_html( $important )
				);

				if ( isset( $option_settings['css']['letter_spacing'] ) ) {
					self::set_style( $function_name, array(
						'selector'    => $option_settings['css']['letter_spacing'],
						'declaration' => sprintf(
							'letter-spacing: %1$s%2$s;',
							esc_html( et_builder_process_range_value( $font_options[ $letter_spacing_option_name ], 'letter_spacing' ) ),
							esc_html( $important )
						),
						'priority'    => $this->_style_priority,
					) );
				}
			}

			$line_height_option_name = "{$option_name}_{$slugs[4]}";

			if ( isset( $font_options[ $line_height_option_name ] ) ) {
				$default_line_height     = isset( $this->fields_unprocessed[ $line_height_option_name ]['default'] ) ? $this->fields_unprocessed[ $line_height_option_name ]['default'] : '';

				if ( ! in_array( trim( $font_options[ $line_height_option_name ] ), array( '', 'px', $default_line_height ) ) ) {
					$important = in_array( 'line-height', $important_options ) || $use_global_important ? ' !important' : '';

					$style .= sprintf(
						'line-height: %1$s%2$s; ',
						esc_html( et_builder_process_range_value( $font_options[ $line_height_option_name ], 'line_height' ) ),
						esc_html( $important )
					);

					if ( isset( $option_settings['css']['line_height'] ) ) {
						self::set_style( $function_name, array(
							'selector'    => $option_settings['css']['line_height'],
							'declaration' => sprintf(
								'line-height: %1$s%2$s;',
								esc_html( et_builder_process_range_value( $font_options[ $line_height_option_name ], 'line_height' ) ),
								esc_html( $important )
							),
							'priority'    => $this->_style_priority,
						) );
					}
				}
			}

			$text_align_option_name = "{$option_name}_{$slugs[5]}";

			if ( isset( $font_options[ $text_align_option_name ] ) && '' !== $font_options[ $text_align_option_name ] ) {

				$important = in_array( 'text-align', $important_options ) || $use_global_important ? ' !important' : '';
				$text_align = et_pb_get_alignment( $font_options[ $text_align_option_name ] );

				if ( isset( $option_settings['css']['text_align'] ) ) {
					self::set_style( $function_name, array(
						'selector'    => $option_settings['css']['text_align'],
						'declaration' => sprintf(
							'text-align: %1$s%2$s;',
							esc_html( $text_align ),
							esc_html( $important )
						),
						'priority'    => $this->_style_priority,
					) );
				} else {
					$style .= sprintf(
						'text-align: %1$s%2$s; ',
						esc_html( $text_align ),
						esc_html( $important )
					);
				}



			}

			if ( isset( $option_settings['use_all_caps'] ) && $option_settings['use_all_caps'] && 'on' === $this->shortcode_atts["{$option_name}_all_caps"] ) {
				$important = in_array( 'all_caps', $important_options ) || $use_global_important ? ' !important' : '';

				$style .= sprintf( 'text-transform: uppercase%1$s; ', esc_html( $important ) );
			}

			if ( '' !== $style ) {
				$css_element = ! empty( $option_settings['css']['main'] ) ? $option_settings['css']['main'] : $this->main_css_element;

				// use different selector for plugin if defined
				if ( et_is_builder_plugin_active() && ! empty( $option_settings['css']['plugin_main'] ) ) {
					$css_element = $option_settings['css']['plugin_main'];
				}

				// $css_element might be an array, for example to apply the css for placeholders
				if ( is_array( $css_element ) ) {
					foreach( $css_element as $selector ) {
						self::set_style( $function_name, array(
							'selector'    => $selector,
							'declaration' => rtrim( $style ),
							'priority'    => $this->_style_priority,
						) );
					}
				} else {
					self::set_style( $function_name, array(
						'selector'    => $css_element,
						'declaration' => rtrim( $style ),
						'priority'    => $this->_style_priority,
					) );

					if ( $is_placeholder ) {
						self::set_style( $function_name, array(
							'selector'    => $css_element . '::-webkit-input-placeholder',
							'declaration' => rtrim( $style ),
							'priority'    => $this->_style_priority,
						) );

						self::set_style( $function_name, array(
							'selector'    => $css_element . '::-moz-placeholder',
							'declaration' => rtrim( $style ),
							'priority'    => $this->_style_priority,
						) );

						self::set_style( $function_name, array(
							'selector'    => $css_element . '::-ms-input-placeholder',
							'declaration' => rtrim( $style ),
							'priority'    => $this->_style_priority,
						) );
					}
				}
			}

			// process mobile options
			foreach( $mobile_options_slugs as $mobile_option ) {
				$current_option_name = "{$option_name}_{$mobile_option}";

				if ( isset( $font_options[ $current_option_name ] ) && '' !== $font_options[ $current_option_name ] ) {
					$current_desktop_option = $this->remove_suffix($mobile_option);
					$current_last_edited_slug = "{$option_name}_{$current_desktop_option}_last_edited";
					$current_last_edited = isset( $font_options[ $current_last_edited_slug ] ) ? $font_options[ $current_last_edited_slug ] : '';
					$current_responsive_status = et_pb_get_responsive_status( $current_last_edited );

					// Don't print mobile styles if responsive UI isn't toggled on
					if ( ! $current_responsive_status ) {
						continue;
					}

					$current_media_query = false === strpos( $mobile_option, 'phone' ) ? 'max_width_980' : 'max_width_767';
					$main_option_name = str_replace( array( '_tablet', '_phone' ), '', $mobile_option );
					$css_property = str_replace( '_', '-', $main_option_name );
					$css_option_name = 'font-size' === $css_property ? 'size' : $css_property;
					$important = in_array( $css_option_name, $important_options ) || $use_global_important ? ' !important' : '';

					// Allow specific selector tablet and mobile, simply add _tablet or _phone suffix
					if ( isset( $option_settings['css'][ $mobile_option ] ) && "" !== $option_settings['css'][ $mobile_option ] ) {
						$selector = $option_settings['css'][ $mobile_option ];
					} elseif ( isset( $option_settings['css'][ $main_option_name ] ) || isset( $option_settings['css']['main'] ) ) {
						$selector = isset( $option_settings['css'][ $main_option_name ] ) ? $option_settings['css'][ $main_option_name ] : $option_settings['css']['main'];
					} elseif ( et_is_builder_plugin_active() && ! empty( $option_settings['css']['plugin_main'] ) ) {
						$selector = $option_settings['css']['plugin_main'];
					} else {
						$selector = $this->main_css_element;
					}

					// $selector might be an array, for example to apply the css for placeholders
					if ( is_array( $selector ) ) {
						foreach( $selector as $selector_item ) {
							self::set_style( $function_name, array(
								'selector'    => $selector_item,
								'declaration' => sprintf(
									'%1$s: %2$s%3$s;',
									esc_html( $css_property ),
									esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
									esc_html( $important )
								),
								'priority'    => $this->_style_priority,
								'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
							) );
						}
					} else {
						self::set_style( $function_name, array(
							'selector'    => $selector,
							'declaration' => sprintf(
								'%1$s: %2$s%3$s;',
								esc_html( $css_property ),
								esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
								esc_html( $important )
							),
							'priority'    => $this->_style_priority,
							'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
						) );

						if ( $is_placeholder ) {
							self::set_style( $function_name, array(
								'selector'    => $selector . '::-webkit-input-placeholder',
								'declaration' => sprintf(
									'%1$s: %2$s%3$s;',
									esc_html( $css_property ),
									esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
									esc_html( $important )
								),
								'priority'    => $this->_style_priority,
								'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
							) );

							self::set_style( $function_name, array(
								'selector'    => $selector . '::-moz-placeholder',
								'declaration' => sprintf(
									'%1$s: %2$s%3$s;',
									esc_html( $css_property ),
									esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
									esc_html( $important )
								),
								'priority'    => $this->_style_priority,
								'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
							) );

							self::set_style( $function_name, array(
								'selector'    => $selector . '::-ms-input-placeholder',
								'declaration' => sprintf(
									'%1$s: %2$s%3$s;',
									esc_html( $css_property ),
									esc_html( et_builder_process_range_value( $font_options[ $current_option_name ] ) ),
									esc_html( $important )
								),
								'priority'    => $this->_style_priority,
								'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
							) );
						}
					}
				}
			}
		}
	}

	function process_advanced_background_options( $function_name ) {
		if ( ! isset( $this->advanced_options['background'] ) ) {
			return;
		}

		$style = '';
		$settings = $this->advanced_options['background'];
		$important = isset( $settings['css']['important'] ) && $settings['css']['important'] ? ' !important' : '';

		// Possible values for use_background_* variables are true, false, or 'fields_only'
		$use_background_color_gradient_options = $this->advanced_options['background']['use_background_color_gradient'];
		$use_background_image_options          = $this->advanced_options['background']['use_background_image'];
		$use_background_color_options          = $this->advanced_options['background']['use_background_color'];

		$background_images = array();

		if ( $use_background_color_gradient_options && 'fields_only' !== $use_background_color_gradient_options ) {
			$use_background_color_gradient              = $this->shortcode_atts['use_background_color_gradient'];
			$background_color_gradient_type             = $this->shortcode_atts['background_color_gradient_type'];
			$background_color_gradient_direction        = $this->shortcode_atts['background_color_gradient_direction'];
			$background_color_gradient_direction_radial = $this->shortcode_atts['background_color_gradient_direction_radial'];
			$background_color_gradient_start            = $this->shortcode_atts['background_color_gradient_start'];
			$background_color_gradient_end              = $this->shortcode_atts['background_color_gradient_end'];
			$background_color_gradient_start_position   = $this->shortcode_atts['background_color_gradient_start_position'];
			$background_color_gradient_end_position     = $this->shortcode_atts['background_color_gradient_end_position'];
			$background_color_gradient_overlays_image   = $this->shortcode_atts['background_color_gradient_overlays_image'];

			if ( 'on' === $use_background_color_gradient ) {
				$has_background_color_gradient = true;

				$background_images[] = $this->get_gradient( array(
					'type'             => $background_color_gradient_type,
					'direction'        => $background_color_gradient_direction,
					'radial_direction' => $background_color_gradient_direction_radial,
					'color_start'      => $background_color_gradient_start,
					'color_end'        => $background_color_gradient_end,
					'start_position'   => $background_color_gradient_start_position,
					'end_position'     => $background_color_gradient_end_position,
				) );
			}
		}

		if ( $use_background_image_options && 'fields_only' !== $use_background_image_options ) {
			$background_image            = $this->shortcode_atts['background_image'];
			$background_size_default     = isset( $this->fields_unprocessed[ 'background_size' ]['default'] ) ? $this->fields_unprocessed[ 'background_size' ]['default'] : '';
			$background_size             = $this->shortcode_atts['background_size'];
			$background_position_default = isset( $this->fields_unprocessed[ 'background_position' ]['default'] ) ? $this->fields_unprocessed[ 'background_position' ]['default'] : '';
			$background_position         = $this->shortcode_atts['background_position'];
			$background_repeat_default   = isset( $this->fields_unprocessed[ 'background_repeat' ]['default'] ) ? $this->fields_unprocessed[ 'background_repeat' ]['default'] : '';
			$background_repeat           = $this->shortcode_atts['background_repeat'];
			$background_blend_default    = isset( $this->fields_unprocessed[ 'background_blend' ]['default'] ) ? $this->fields_unprocessed[ 'background_blend' ]['default'] : '';
			$background_blend            = $this->shortcode_atts['background_blend'];
			$parallax                    = $this->shortcode_atts['parallax'];

			if ( $this->featured_image_background ) {
				$featured_image         = isset( $this->shortcode_atts['featured_image'] ) ? $this->shortcode_atts['featured_image'] : '';
				$featured_placement     = isset( $this->shortcode_atts['featured_placement'] ) ? $this->shortcode_atts['featured_placement'] : '';
				$featured_image_src_obj = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
				$featured_image_src     = isset( $featured_image_src_obj[0] ) ? $featured_image_src_obj[0] : '';

				if ( 'on' === $featured_image && 'background' === $featured_placement && '' !== $featured_image_src ) {
					$background_image = $featured_image_src;
				}
			}

			if ( '' !== $background_image && 'on' !== $parallax ) {
				$has_background_image = true;

				$background_images[] = sprintf( 'url(%1$s)', esc_html( $background_image ) );

				if ( '' !== $background_size && $background_size_default !== $background_size ) {
					$style .= sprintf(
						'background-size: %1$s; ',
						esc_html( $background_size )
					);
				}

				if ( '' !== $background_position && $background_position_default !== $background_position ) {
					$style .= sprintf(
						'background-position: %1$s; ',
						esc_html( str_replace( '_', ' ', $background_position ) )
					);
				}

				if ( '' !== $background_repeat && $background_repeat_default !== $background_repeat ) {
					$style .= sprintf(
						'background-repeat: %1$s; ',
						esc_html( $background_repeat )
					);
				}

				if ( '' !== $background_blend && $background_blend_default !== $background_blend ) {
					$style .= sprintf(
						'background-blend-mode: %1$s; ',
						esc_html( $background_blend )
					);

					// Force background-color: initial;
					if ( isset( $has_background_color_gradient, $has_background_image ) ) {
						$style .= sprintf( 'background-color: initial%1$s; ', esc_html( $important ) );
					}
				}
			}
		}

		if ( ! empty( $background_images ) ) {
			// The browsers stack the images in the opposite order to what you'd expect.
			if ( 'on' !== $background_color_gradient_overlays_image ) {
				$background_images = array_reverse( $background_images );
			}

			$style .= sprintf(
				'background-image: %1$s%2$s;',
				esc_html( join( ', ', $background_images ) ),
				$important
			);
		}

		if ( $use_background_color_options && 'fields_only' !== $use_background_color_options ) {
			if ( ! isset( $has_background_color_gradient, $has_background_image ) ) {
				$background_color = $this->shortcode_atts['background_color'];

				if ( '' !== $background_color ) {
					$style .= sprintf(
						'background-color: %1$s%2$s; ',
						esc_html( $background_color ),
						esc_html( $important )
					);
				}
			}
		}

		if ( '' !== $style ) {
			$css_element = ! empty( $settings['css']['main'] ) ? $settings['css']['main'] : $this->main_css_element;

			self::set_style( $function_name, array(
				'selector'    => $css_element,
				'declaration' => rtrim( $style ),
				'priority'    => $this->_style_priority,
			) );
		}
	}

	function process_advanced_text_options( $function_name ) {
		if ( ! isset( $this->advanced_options['text'] ) ) {
			return;
		}

		$text_options = $this->advanced_options['text'];

		if ( isset( $text_options['css'] ) && is_array( $text_options['css'] ) ) {
			$text_css                 = $text_options['css'];
			$text_orientation_default = isset( $this->fields_unprocessed['text_orientation']['default'] ) ? $this->fields_unprocessed['text_orientation']['default'] : '';
			$text_orientation         = $this->get_text_orientation() !== $text_orientation_default ? $this->get_text_orientation() : '';

			// Normally, text orientation attr adds et_pb_text_align_* class name to its module wrapper
			// In some cases, it needs to target particular children inside the module. Thus, only prints
			// styling if selector is given
			if ( isset( $text_css['text_orientation'] ) && '' !== $text_orientation ) {
				self::set_style( $function_name, array(
					'selector'    => $text_css['text_orientation'],
					'declaration' => sprintf( 'text-align: %1$s;', esc_attr( $text_orientation ) ),
					'priority'    => $this->_style_priority,
				) );
			}
		}
	}

	/**
	 * Adds Rounded Corners and Border Styles styles to the page custom css code
	 * Can be overridden in child classes to add more css code from multiple border options. For example for the entire module
	 * container and for the image within the module.
	 */
	function process_advanced_border_options( $function_name ) {
		global $et_fb_processing_shortcode_object;

		$border_field   = ET_Builder_Module_Fields_Factory::get( 'Border' );
		$border_options = self::$data_utils->array_get( $this->advanced_options, 'border', array() );

		if ( $this->slug !== $function_name ) {
			// This module's shortcode callback is being used to render another module (like accordion item uses toggle ) so we need to make
			// sure border option overrides are taken from the other module instead of this one.
			$fields         = self::get_advanced_fields( $this->get_post_type(), 'all', $function_name );
			$border_options = self::$data_utils->array_get( $fields, 'advanced_common.border', array() );
		}

		// Do not add overflow:hidden for some modules.
		$overflow = ! in_array( $function_name,
			array(
				'et_pb_social_media_follow',
				'et_pb_social_media_follow_network',
				'et_pb_fullwidth_menu',
			)
		);
		self::set_style( $function_name, array(
			'selector'    => self::$data_utils->array_get( $border_options, 'css.main.border_radii', $this->main_css_element ),
			'declaration' => $border_field->get_radii_style( $this->shortcode_atts, $this->advanced_options, '', $overflow ),
			'priority'    => $this->_style_priority,
		) );

		self::set_style( $function_name, array(
			'selector'    => self::$data_utils->array_get( $border_options, 'css.main.border_styles', $this->main_css_element ),
			'declaration' => $border_field->get_borders_style( $this->shortcode_atts, $this->advanced_options ),
			'priority'    => $this->_style_priority,
		) );

		if ( ! $et_fb_processing_shortcode_object && $border_field->needs_border_reset_class( $function_name, $this->shortcode_atts ) ) {
			add_filter( "{$function_name}_shortcode_output", array( $border_field, 'add_border_reset_class' ), 10, 2 );
		}
	}

	/**
	 * Adds Filter styles to the page custom css code
	 *
	 * Wrapper for `generate_css_filters` used for module defaults
	 */
	function process_advanced_filter_options( $function_name ) {
		return $this->generate_css_filters( $function_name );
	}

	function process_max_width_options( $function_name ) {
		if ( ! isset( $this->advanced_options['max_width'] ) ) {
			return;
		}

		// Usage setting
		$setting_defaults   = array(
			'use_max_width'        => true,
			'use_module_alignment' => true,
		);
		$this->advanced_options['max_width'] = wp_parse_args( $this->advanced_options['max_width'], $setting_defaults );

		$is_max_width_customized = false;

		// Max width
		if ( $this->advanced_options['max_width']['use_max_width'] ) {
			$max_width_default     = $this->fields_unprocessed['max_width']['default'];
			$max_width             = $this->shortcode_atts['max_width'] !== $max_width_default ? $this->shortcode_atts['max_width'] : '';
			$max_width_tablet      = $this->shortcode_atts['max_width_tablet'] !== $max_width_default ? $this->shortcode_atts['max_width_tablet'] : '';
			$max_width_phone       = $this->shortcode_atts['max_width_phone'] !== $max_width_default ? $this->shortcode_atts['max_width_phone'] : '';
			$max_width_last_edited = $this->shortcode_atts['max_width_last_edited'];

			if ( '' !== $max_width_tablet || '' !== $max_width_phone || '' !== $max_width ) {
				$max_width_options           = $this->advanced_options['max_width'];
				$max_width_options_css       = isset( $max_width_options['css'] ) ? $max_width_options['css'] : array();
				$max_width_responsive_active = et_pb_get_responsive_status( $max_width_last_edited );
				$selector                    = isset( $max_width_options_css['main'] ) ? $max_width_options_css['main'] : '%%order_class%%';
				$additional_css              = $this->get_max_width_additional_css();
				$max_width_attrs             = array( 'max_width' );

				// Append !important tag
				if ( isset( $max_width_options_css['important'] ) ) {
					$additional_css = ' !important;';
				}

				if ( $max_width_responsive_active ) {
					$max_width_values = array(
						'desktop_only' => $max_width,
						'tablet'       => $max_width_tablet,
						'phone'        => $max_width_phone,
					);

					$max_width_attrs = array_merge( $max_width_attrs, array( 'max_width_tablet', 'max_width_phone' ) );
				} else {
					$max_width_values = array(
						'desktop' => $max_width,
					);
				}

				// Update $is_max_width_customized if one of max_width* value is modified
				foreach ( $max_width_attrs as $max_width_attr ) {
					if ( $is_max_width_customized ) {
						break;
					}

					if ( ! in_array( $this->shortcode_atts[ $max_width_attr ], array( '', '100%' ) ) ) {
						$is_max_width_customized = true;
					}
				}

				et_pb_generate_responsive_css(
					$max_width_values,
					$selector,
					'max-width',
					$function_name,
					$additional_css
				);
			}
		}

		// Module Alignment
		if ( $this->advanced_options['max_width']['use_module_alignment'] ) {
			$module_alignment_styles = array(
				'left'   => 'margin-left: 0px !important; margin-right: auto !important;',
				'center' => 'margin-left: auto !important; margin-right: auto !important;',
				'right'  => 'margin-left: auto !important; margin-right: 0px !important;',
			);
			$module_alignment = isset( $this->shortcode_atts['module_alignment'] ) ? $this->shortcode_atts['module_alignment'] : '';

			if ( ( $is_max_width_customized || ! $this->advanced_options['max_width']['use_max_width'] ) && isset( $module_alignment_styles[ $module_alignment ] ) ) {
				$module_alignment_selector = isset( $this->advanced_options['max_width']['css'] ) && isset( $this->advanced_options['max_width']['css']['module_alignment'] ) ? $this->advanced_options['max_width']['css']['module_alignment'] : '%%order_class%%.et_pb_module';

				self::set_style( $function_name, array(
					'selector'    => $module_alignment_selector,
					'declaration' => $module_alignment_styles[ $module_alignment ],
					'priority'    => 20,
				) );
			}
		}
	}

	function process_advanced_custom_margin_options( $function_name ) {
		if ( ! isset( $this->advanced_options['custom_margin_padding'] ) ) {
			return;
		}

		$style                = '';
		$style_padding        = '';
		$style_margin         = '';
		$style_mobile         = array();
		$style_mobile_padding = array();
		$style_mobile_margin  = array();
		$important_options    = array();
		$is_important_set     = isset( $this->advanced_options['custom_margin_padding']['css']['important'] );
		$use_global_important = $is_important_set && 'all' === $this->advanced_options['custom_margin_padding']['css']['important'];
		$css                  = isset( $this->advanced_options['custom_margin_padding']['css'] ) ? $this->advanced_options['custom_margin_padding']['css'] : array();
		$item_mappings        = array(
			'top'    => 0,
			'right'  => 1,
			'bottom' => 2,
			'left'   => 3,
		);

		if ( $is_important_set && is_array( $this->advanced_options['custom_margin_padding']['css']['important'] ) ) {
			$important_options = $this->advanced_options['custom_margin_padding']['css']['important'];
		}

		$custom_margin  = $this->advanced_options['custom_margin_padding']['use_margin'] ? $this->shortcode_atts['custom_margin'] : '';
		$custom_padding = $this->advanced_options['custom_margin_padding']['use_padding'] ? $this->shortcode_atts['custom_padding'] : '';

		$custom_margin_responsive_active = isset( $this->shortcode_atts['custom_margin_last_edited'] ) ? et_pb_get_responsive_status( $this->shortcode_atts['custom_margin_last_edited'] ) : false;
		$custom_margin_mobile = $custom_margin_responsive_active && $this->advanced_options['custom_margin_padding']['use_margin'] && ( isset( $this->shortcode_atts['custom_margin_tablet'] ) || isset( $this->shortcode_atts['custom_margin_phone'] ) )
			? array (
				'tablet' => isset( $this->shortcode_atts['custom_margin_tablet'] ) ? $this->shortcode_atts['custom_margin_tablet'] : '',
				'phone' => isset( $this->shortcode_atts['custom_margin_phone'] ) ? $this->shortcode_atts['custom_margin_phone'] : '',
			)
			: '';

		$custom_padding_responsive_active = isset( $this->shortcode_atts['custom_padding_last_edited'] ) ? et_pb_get_responsive_status( $this->shortcode_atts['custom_padding_last_edited'] ) : false;
		$custom_padding_mobile = $custom_padding_responsive_active && $this->advanced_options['custom_margin_padding']['use_padding'] && ( isset( $this->shortcode_atts['custom_padding_tablet'] ) || isset( $this->shortcode_atts['custom_padding_phone'] ) )
			? array (
				'tablet' => isset( $this->shortcode_atts['custom_padding_tablet'] ) ? $this->shortcode_atts['custom_padding_tablet'] : '',
				'phone' => isset( $this->shortcode_atts['custom_padding_phone'] ) ? $this->shortcode_atts['custom_padding_phone'] : '',
			)
			: '';

		if ( '' !== $custom_padding || ! empty( $custom_padding_mobile ) ) {
			$important            = in_array( 'custom_padding', $important_options ) || $use_global_important ? true : false;
			$has_padding_selector = isset( $this->advanced_options['custom_margin_padding']['css'] ) && isset( $this->advanced_options['custom_margin_padding']['css']['padding'] );
			$padding_styling      = '' !== $custom_padding ? et_builder_get_element_style_css( $custom_padding, 'padding', $important ) : '';

			if ( $has_padding_selector ) {
				$style_padding .= $padding_styling;
			} else {
				$style .= $padding_styling;
			}

			if ( ! empty( $custom_padding_mobile ) ) {
				foreach ( $custom_padding_mobile as $device => $settings ) {
					$padding_mobile_styling = '' !== $settings ? et_builder_get_element_style_css( $settings, 'padding', $important ) : '';

					if ( $has_padding_selector ) {
						$style_mobile_padding[ $device ][] = $padding_mobile_styling;
					} else {
						$style_mobile[ $device ][] = $padding_mobile_styling;
					}
				}
			}

			// Selective Paddings
			$selective_paddings = array_filter( array(
				'top'    => isset( $css['padding-top'] ) ? $css['padding-top'] : false,
				'right'  => isset( $css['padding-right'] ) ? $css['padding-right'] : false,
				'bottom' => isset( $css['padding-bottom'] ) ? $css['padding-bottom'] : false,
				'left'   => isset( $css['padding-left'] ) ? $css['padding-left'] : false,
			) );

			// Only run the following if selective-padding selector is defined
			if ( ! empty( $selective_paddings ) ) {

				// Loop each padding sides. Selective padding works by creating filtered custom_margin value on the fly, then pass it to existin declaration builder
				// Ie custom_padding = 10px|10px|10px|10px. Selective padding for padding-top works by creating 10px||| value on the fly then pass it to declaration builder
				foreach ( $selective_paddings as $corner => $selective_padding_selectors ) {
					// Default selective padding value: empty on all sides
					$selective_padding = array( '', '', '', '' );

					// Get padding order key. Expected order: top|right|bottom|left
					$selective_padding_key = $item_mappings[ $corner ];

					// Explode custom padding value into array
					$selective_padding_array = explode( '|', $custom_padding );

					// Pick current padding side's value
					$selective_padding_value = isset( $selective_padding_array[ $selective_padding_key ] ) ? $selective_padding_array[ $selective_padding_key ] : '';

					// Set selective padding value to $selective_padding
					$selective_padding[ $selective_padding_key ] = $selective_padding_value;

					// If selective padding for current side is found, set style for it
					$selective_padding = array_filter( $selective_padding );
					if ( ! empty( $selective_padding ) ) {
						self::set_style( $function_name, array(
							'selector'    => $selective_padding_selectors,
							'declaration' => rtrim( et_builder_get_element_style_css( implode( '|', $selective_padding ), 'padding' ) ),
							'priority'    => $this->_style_priority,
						) );
					}

					// Check wheter responsive padding is activated and padding has mobile value
					if ( $custom_padding_responsive_active && is_array( $custom_padding_mobile ) ) {
						// Assume no mobile padding value first
						$has_selective_padding_mobile = false;

						// Set default selective padding mobile
						$selective_padding_mobile = array(
							'tablet' => array( '', '', '', '' ),
							'phone'  => array( '', '', '', '' ),
						);

						// Loop padding mobile. This results per-breakpoint padding value
						foreach ( $custom_padding_mobile as $breakpoint => $custom_padding_device ) {
							// Explode per-breakpoint padding value into array
							$custom_padding_device_array = explode( '|', $custom_padding_device );

							// Get current padding side value on current breakpoint
							$selective_padding_mobile_value = isset( $custom_padding_device_array[ $selective_padding_key ] ) ? $custom_padding_device_array[ $selective_padding_key ] : '';

							// Set picked value into current padding side on current breakpoint
							$selective_padding_mobile[ $breakpoint ][ $selective_padding_key ] = $selective_padding_mobile_value;

							// If the side of padding on current breakpoint has value, build CSS declaration for it mark selective padding mobile as exist
							$selective_padding_mobile[ $breakpoint ] = array_filter( $selective_padding_mobile[ $breakpoint ] );
							if ( ! empty( $selective_padding_mobile[ $breakpoint ] ) ) {
								$selective_padding_mobile[ $breakpoint ] = array( et_builder_get_element_style_css( implode( '|', $selective_padding_mobile[ $breakpoint ] ), 'padding' ) );

								$has_selective_padding_mobile = true;
							}
						}

						// Set style for selective padding on mobile
						if ( $has_selective_padding_mobile ) {
							$this->process_advanced_mobile_margin_options(
								$function_name,
								$selective_padding_mobile,
								$selective_padding_selectors
							);
						}
					}
				}
			}
		}

		if ( '' !== $custom_margin || ! empty( $custom_margin_mobile ) ) {
			$important           = in_array( 'custom_margin', $important_options ) || $use_global_important ? true : false;
			$has_margin_selector = isset( $this->advanced_options['custom_margin_padding']['css'] ) && isset( $this->advanced_options['custom_margin_padding']['css']['margin'] );
			$margin_styling      = '' !== $custom_margin ? et_builder_get_element_style_css( $custom_margin, 'margin', $important ) : '';

			if ( $has_margin_selector ) {
				$style_margin .= $margin_styling;
			} else {
				$style .= $margin_styling;
			}

			if ( ! empty( $custom_margin_mobile ) ) {
				foreach ( $custom_margin_mobile as $device => $settings ) {
					$margin_mobile_styling = '' !== $settings ? et_builder_get_element_style_css( $settings, 'margin', $important ) : '';

					if ( $has_margin_selector ) {
						$style_mobile_margin[ $device ][] = $margin_mobile_styling;
					} else {
						$style_mobile[ $device ][] = $margin_mobile_styling;
					}
				}
			}
		}

		if ( '' !== $style_padding ) {
			$css_element_padding = $this->advanced_options['custom_margin_padding']['css']['padding'];

			self::set_style( $function_name, array(
				'selector'    => $css_element_padding,
				'declaration' => rtrim( $style_padding ),
				'priority'    => $this->_style_priority,
			) );
		}

		if ( '' !== $style_margin ) {
			$css_element_margin = $this->advanced_options['custom_margin_padding']['css']['margin'];

			self::set_style( $function_name, array(
				'selector'    => $css_element_margin,
				'declaration' => rtrim( $style_margin ),
				'priority'    => $this->_style_priority,
			) );
		}

		if ( '' !== $style ) {
			$css_element = ! empty( $this->advanced_options['custom_margin_padding']['css']['main'] ) ? $this->advanced_options['custom_margin_padding']['css']['main'] : $this->main_css_element;

			self::set_style( $function_name, array(
				'selector'    => $css_element,
				'declaration' => rtrim( $style ),
				'priority'    => $this->_style_priority,
			) );
		}

		if ( ! empty( $style_mobile_padding ) ) {
			$this->process_advanced_mobile_margin_options(
				$function_name,
				$style_mobile_padding,
				$this->advanced_options['custom_margin_padding']['css']['padding']
			);
		}

		if ( ! empty( $style_mobile_margin ) ) {
			$this->process_advanced_mobile_margin_options(
				$function_name,
				$style_mobile_margin,
				$this->advanced_options['custom_margin_padding']['css']['margin']
			);
		}

		if ( ! empty( $style_mobile ) ) {
			$css_element = ! empty( $this->advanced_options['custom_margin_padding']['css']['main'] ) ? $this->advanced_options['custom_margin_padding']['css']['main'] : $this->main_css_element;

			$this->process_advanced_mobile_margin_options(
				$function_name,
				$style_mobile,
				$css_element
			);
		}
	}

	function process_advanced_mobile_margin_options( $function_name, $style_mobile, $css_element ) {
		foreach( $style_mobile as $device => $style ) {
			if ( ! empty( $style ) ) {
				$current_media_query = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
				$current_media_css = '';
				foreach( $style as $css_code ) {
					$current_media_css .= $css_code;
				}
				if ( '' === $current_media_css ) {
					continue;
				}

				self::set_style( $function_name, array(
					'selector'    => $css_element,
					'declaration' => rtrim( $current_media_css ),
					'priority'    => $this->_style_priority,
					'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
				) );
			}
		}
	}

	function process_advanced_button_options( $function_name ) {
		if ( ! isset( $this->advanced_options['button'] ) ) {
			return;
		}

		foreach ( $this->advanced_options['button'] as $option_name => $option_settings ) {
			$button_custom                      = $this->shortcode_atts["custom_{$option_name}"];
			$button_text_size                   = $this->shortcode_atts["{$option_name}_text_size"];
			$button_text_size_tablet            = $this->shortcode_atts["{$option_name}_text_size_tablet"];
			$button_text_size_phone             = $this->shortcode_atts["{$option_name}_text_size_phone"];
			$button_text_size_last_edited       = $this->shortcode_atts["{$option_name}_text_size_last_edited"];
			$button_text_color                  = $this->shortcode_atts["{$option_name}_text_color"];
			$button_bg_color                    = $this->shortcode_atts["{$option_name}_bg_color"];
			$button_border_width                = $this->shortcode_atts["{$option_name}_border_width"];
			$button_border_color                = $this->shortcode_atts["{$option_name}_border_color"];
			$button_border_radius               = $this->shortcode_atts["{$option_name}_border_radius"];
			$button_font                        = $this->shortcode_atts["{$option_name}_font"];
			$button_letter_spacing              = $this->shortcode_atts["{$option_name}_letter_spacing"];
			$button_letter_spacing_tablet       = $this->shortcode_atts["{$option_name}_letter_spacing_tablet"];
			$button_letter_spacing_phone        = $this->shortcode_atts["{$option_name}_letter_spacing_phone"];
			$button_letter_spacing_last_edited  = $this->shortcode_atts["{$option_name}_letter_spacing_last_edited"];
			$button_use_icon                    = $this->shortcode_atts["{$option_name}_use_icon"];
			$button_icon                        = $this->shortcode_atts["{$option_name}_icon"];
			$button_icon_color                  = $this->shortcode_atts["{$option_name}_icon_color"];
			$button_icon_placement              = $this->shortcode_atts["{$option_name}_icon_placement"];
			$button_on_hover                    = $this->shortcode_atts["{$option_name}_on_hover"];
			$button_text_color_hover            = $this->shortcode_atts["{$option_name}_text_color_hover"];
			$button_bg_color_hover              = $this->shortcode_atts["{$option_name}_bg_color_hover"];
			$button_border_color_hover          = $this->shortcode_atts["{$option_name}_border_color_hover"];
			$button_border_radius_hover         = $this->shortcode_atts["{$option_name}_border_radius_hover"];
			$button_letter_spacing_hover        = $this->shortcode_atts["{$option_name}_letter_spacing_hover"];
			$button_letter_spacing_hover_tablet = $this->shortcode_atts["{$option_name}_letter_spacing_hover_tablet"];
			$button_letter_spacing_hover_phone  = $this->shortcode_atts["{$option_name}_letter_spacing_hover_phone"];
			$button_letter_spacing_hover_last_edited  = $this->shortcode_atts["{$option_name}_letter_spacing_hover_last_edited"];

			$button_icon_pseudo_selector = $button_icon_placement === 'left' ? ':before' : ':after';

			// Specific selector needs to be explicitly defined to make button alignment works
			if ( isset( $option_settings['use_alignment'] ) && $option_settings['use_alignment'] && isset( $option_settings['css'] ) && isset( $option_settings['css']['alignment'] ) ) {
				$button_alignment          = $this->shortcode_atts["{$option_name}_alignment"];
				$button_alignment_selector = $option_settings['css']['alignment'];

				if ( '' !== $button_alignment && '' !== $button_alignment_selector ) {
					self::set_style( $function_name, array(
						'selector'    => $button_alignment_selector,
						'declaration' => "text-align: {$button_alignment};",
					) );
				}
			}

			if ( 'on' === $button_custom ) {
				$is_default_button_text_size = $this->_is_field_default( 'button_text_size', $button_text_size );
				$is_default_button_icon_placement = $this->_is_field_default( 'button_icon_placement', $button_icon_placement );
				$is_default_button_on_hover = $this->_is_field_default( 'button_on_hover', $button_on_hover );
				$is_default_button_icon = $this->_is_field_default( 'button_icon', $button_icon );
				$is_default_hover_placement = $is_default_button_on_hover && $is_default_button_icon_placement;

				$button_text_size_processed = $is_default_button_text_size ? '20px' : et_builder_process_range_value( $button_text_size );
				$button_border_radius_processed = '' !== $button_border_radius && 'px' !== $button_border_radius ? et_builder_process_range_value( $button_border_radius ) : '';
				$button_border_radius_hover_processed = '' !== $button_border_radius_hover && 'px' !== $button_border_radius_hover ? et_builder_process_range_value( $button_border_radius_hover ) : '';
				$button_use_icon = '' === $button_use_icon ? 'on' : $button_use_icon;

				$css_element = ! empty( $option_settings['css']['main'] ) ? $option_settings['css']['main'] : $this->main_css_element . ' .et_pb_button';

				if ( et_is_builder_plugin_active() && ! empty( $option_settings['css']['plugin_main'] ) ) {
					$css_element = $option_settings['css']['plugin_main'];
				}

				$css_element_processed = et_is_builder_plugin_active() ? $css_element : 'body #page-container ' . $css_element;

				if ( et_is_builder_plugin_active() ) {
					$button_bg_color .= '' !== $button_bg_color ? ' !important' : '';
					$button_border_radius_processed .= '' !== $button_border_radius_processed ? ' !important' : '';
					$button_border_radius_hover_processed .= '' !== $button_border_radius_hover_processed ? ' !important' : '';
				}

				$global_use_icon_value = et_builder_option( 'all_buttons_icon' );

				$main_element_styles_padding_important = 'no' === $global_use_icon_value && 'off' !== $button_use_icon;

				$main_element_styles = sprintf(
					'%1$s
					%2$s
					%3$s
					%4$s
					%5$s
					%6$s
					%7$s
					%8$s
					%9$s',
					'' !== $button_text_color ? sprintf( 'color:%1$s !important;', $button_text_color ) : '',
					'' !== $button_bg_color && 'et_pb_button' !== $this->slug ? sprintf( 'background:%1$s;', $button_bg_color ) : '',
					'' !== $button_border_width && 'px' !== $button_border_width ? sprintf( 'border-width:%1$s !important;', et_builder_process_range_value( $button_border_width ) ) : '',
					'' !== $button_border_color ? sprintf( 'border-color:%1$s;', $button_border_color ) : '',
					'' !== $button_border_radius_processed ? sprintf( 'border-radius:%1$s;', $button_border_radius_processed ) : '',
					'' !== $button_letter_spacing && 'px' !== $button_letter_spacing ? sprintf( 'letter-spacing:%1$s;', et_builder_process_range_value( $button_letter_spacing ) ) : '',
					! $is_default_button_text_size  ? sprintf( 'font-size:%1$s;', $button_text_size_processed ) : '',
					'' !== $button_font ? et_builder_set_element_font( $button_font, true ) : '',
					'off' === $button_on_hover ?
						sprintf( 'padding-left:%1$s%3$s; padding-right: %2$s%3$s;',
							'left' === $button_icon_placement ? '2em' : '0.7em',
							'left' === $button_icon_placement ? '0.7em' : '2em',
							$main_element_styles_padding_important ? ' !important' : ''
						)
						: ''
				);

				self::set_style( $function_name, array(
					'selector'    => $css_element_processed,
					'declaration' => rtrim( $main_element_styles ),
				) );

				// if button has default icon position or disabled globally and not enabled in module then no padding css should be generated.
				$on_hover_padding = $is_default_button_icon_placement || ('default' === $button_use_icon && 'no' === $global_use_icon_value)
					? ''
					: sprintf( 'padding-left:%1$s%3$s; padding-right: %2$s%3$s;',
						'left' === $button_icon_placement ? '2em' : '0.7em',
						'left' === $button_icon_placement ? '0.7em' : '2em',
						$main_element_styles_padding_important ? ' !important' : ''
					);

				// Avoid adding useless style when value equals its default
				$button_letter_spacing_hover = $this->_is_field_default('button_letter_spacing_hover', $button_letter_spacing_hover) ? '' : $button_letter_spacing_hover;

				$main_element_styles_hover = sprintf(
					'%1$s
					%2$s
					%3$s
					%4$s
					%5$s
					%6$s',
					'' !== $button_text_color_hover ? sprintf( 'color:%1$s !important;', $button_text_color_hover ) : '',
					'' !== $button_bg_color_hover ? sprintf( 'background:%1$s !important;', $button_bg_color_hover ) : '',
					'' !== $button_border_color_hover ? sprintf( 'border-color:%1$s !important;', $button_border_color_hover ) : '',
					'' !== $button_border_radius_hover_processed ? sprintf( 'border-radius:%1$s;', $button_border_radius_hover_processed ) : '',
					'' !== $button_letter_spacing_hover && 'px' !== $button_letter_spacing_hover ? sprintf( 'letter-spacing:%1$s;', et_builder_process_range_value( $button_letter_spacing_hover ) ) : '',
					'off' === $button_on_hover ? '' : $on_hover_padding
				);

				self::set_style( $function_name, array(
					'selector'    => $css_element_processed . ':hover',
					'declaration' => rtrim( $main_element_styles_hover ),
				) );

				if ( 'off' === $button_use_icon ) {
					$main_element_styles_after = 'display:none !important;';
					$no_icon_styles = 'padding: 0.3em 1em !important;';

					$selector = sprintf( '%1$s:before, %1$s:after', $css_element_processed );

					self::set_style( $function_name, array(
						'selector'    => $css_element . ',' . $css_element . ':hover',
						'declaration' => rtrim( $no_icon_styles ),
					) );
				} else {
					$button_icon_code = '' !== $button_icon ? str_replace( ';', '', str_replace( '&#x', '', html_entity_decode( et_pb_process_font_icon( $button_icon ) ) ) ) : '';
					if ( '' !== $button_text_size_processed ) {
						$button_icon_size = '35' !== $button_icon_code ? '1em' : '1.6em';
					}

					$main_element_styles_after = sprintf(
						'%1$s
						%2$s
						%3$s
						%4$s
						%5$s
						%6$s
						%7$s',
						'' !== $button_icon_color ? sprintf( 'color:%1$s;', $button_icon_color ) : '',
						'' !== $button_icon_code ?
							sprintf( 'line-height:%1$s;', '35' !== $button_icon_code ? '1.7em' : '1em' )
							: '',
						'' !== $button_icon_code ? sprintf( 'font-size:%1$s !important;', $button_icon_size ) : '',
						$is_default_hover_placement ? '' : sprintf( 'opacity:%1$s;', 'off' !== $button_on_hover ? '0' : '1' ),
						'off' !== $button_on_hover && '' !== $button_icon_code ?
							sprintf( 'margin-left: %1$s; %2$s: auto;',
								'left' === $button_icon_placement ? '-1.3em' : '-1em',
								'left' === $button_icon_placement ? 'right' : 'left'
							)
							: '',
						'off' === $button_on_hover ?
							sprintf( 'margin-left: %1$s; %2$s:auto;',
								'left' === $button_icon_placement ? '-1.3em' : '.3em',
								'left' === $button_icon_placement ? 'right' : 'left'
							)
							: '',
						( ! $is_default_button_icon_placement && in_array( $button_use_icon , array( 'default', 'on' ) ) ? 'display: inline-block;' : '' )
					);

					// Reverse icon position
					if ( 'left' === $button_icon_placement ) {
						$button_icon_left_content = '' !== $button_icon_code ? 'content: attr(data-icon);' : '';

						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ':after',
							'declaration' => 'display: none;',
						) );

						if ( et_is_builder_plugin_active() ) {
							self::set_style( $function_name, array(
								'selector'    => '.et_pb_row ' . $css_element_processed . ':hover',
								'declaration' => 'padding-right: 1em; padding-left: 2em;',
							) );
						}

						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ':before',
							'declaration' => $button_icon_left_content . ' ; font-family: "ETmodules" !important;',
						) );
					}

					// if button has default icon/hover/placement and disabled globally or not enabled in module then no :after:hover css should be generated.
					if ( ! ( $is_default_button_icon && $is_default_hover_placement ) &&
						( 'default' !== $button_use_icon || 'no' !== $global_use_icon_value ) ) {
						$hover_after_styles = sprintf(
							'%1$s
							%2$s
							%3$s',
							'' !== $button_icon_code ?
								sprintf( 'margin-left:%1$s;', '35' !== $button_icon_code ? '.3em' : '0' )
								: '',
							'' !== $button_icon_code ?
								sprintf( '%1$s: auto; margin-left: %2$s;',
									'left' === $button_icon_placement ? 'right' : 'left',
									'left' === $button_icon_placement ? '-1.3em' : '.3em'
								)
								: '',
							'off' !== $button_on_hover ? 'opacity: 1;' : ''
						);

						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ':hover' . $button_icon_pseudo_selector,
							'declaration' => rtrim( $hover_after_styles ),
						) );
					}

					if ( '' === $button_icon && ! $is_default_button_text_size ) {
						$default_icons_size = '1.6em';
						$custom_icon_size = $button_text_size_processed;

						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . $button_icon_pseudo_selector,
							'declaration' => sprintf( 'font-size:%1$s;', $default_icons_size ),
						) );

						self::set_style( $function_name, array(
							'selector'    => 'body.et_button_custom_icon #page-container ' . $css_element . $button_icon_pseudo_selector,
							'declaration' => sprintf( 'font-size:%1$s;', $custom_icon_size ),
						) );
					}

					$selector = $css_element_processed . $button_icon_pseudo_selector;
				}

				foreach( array( 'tablet', 'phone' ) as $device ) {
					$current_media_query = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
					$current_text_size = 'tablet' === $device ? et_builder_process_range_value( $button_text_size_tablet ) : et_builder_process_range_value( $button_text_size_phone );
					$current_letter_spacing = 'tablet' === $device ? et_builder_process_range_value( $button_letter_spacing_tablet ) : et_builder_process_range_value( $button_letter_spacing_phone );
					$current_letter_spacing_hover = 'tablet' === $device ? et_builder_process_range_value( $button_letter_spacing_hover_tablet ) : et_builder_process_range_value( $button_letter_spacing_hover_phone );

					$current_text_size_responsive_active = et_pb_get_responsive_status( $button_text_size_last_edited );
					$current_letter_spacing_responsive_active = et_pb_get_responsive_status( $button_letter_spacing_last_edited );
					$current_letter_spacing_hover_responsive_active = et_pb_get_responsive_status( $button_letter_spacing_hover_last_edited );

					if ( ( '' !== $current_text_size && '0px' !== $current_text_size ) || '' !== $current_letter_spacing ) {
						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ',' . $css_element_processed . $button_icon_pseudo_selector,
							'declaration' => sprintf(
								'%1$s
								%2$s',
								$current_text_size_responsive_active && '' !== $current_text_size && '0px' !== $current_text_size ? sprintf( 'font-size:%1$s !important;', $current_text_size ) : '',
								$current_letter_spacing_responsive_active && '' !== $current_letter_spacing ? sprintf( 'letter-spacing:%1$s;', $current_letter_spacing ) : ''
							),
							'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
						) );
					}

					if ( $current_letter_spacing_hover_responsive_active && '' !== $current_letter_spacing_hover ) {
						self::set_style( $function_name, array(
							'selector'    => $css_element_processed . ':hover',
							'declaration' => sprintf(
								'letter-spacing:%1$s;',
								$current_letter_spacing_hover
							),
							'media_query' => ET_Builder_Element::get_media_query( $current_media_query ),
						) );
					}
				}

				self::set_style( $function_name, array(
					'selector'    => $selector,
					'declaration' => rtrim( $main_element_styles_after ),
				) );

				// Background Options Styling
				$background_base_name = "{$option_name}_bg";
				$background_images    = array();
				$background_prefix    = "{$background_base_name}_";
				$background_style     = '';

				// Background Gradient
				$use_background_color_gradient              = $this->shortcode_atts["{$background_prefix}use_color_gradient"];
				$background_color_gradient_type             = $this->shortcode_atts["{$background_prefix}color_gradient_type"];
				$background_color_gradient_direction        = $this->shortcode_atts["{$background_prefix}color_gradient_direction"];
				$background_color_gradient_direction_radial = $this->shortcode_atts["{$background_prefix}color_gradient_direction_radial"];
				$background_color_gradient_start            = $this->shortcode_atts["{$background_prefix}color_gradient_start"];
				$background_color_gradient_end              = $this->shortcode_atts["{$background_prefix}color_gradient_end"];
				$background_color_gradient_start_position   = $this->shortcode_atts["{$background_prefix}color_gradient_start_position"];
				$background_color_gradient_end_position     = $this->shortcode_atts["{$background_prefix}color_gradient_end_position"];
				$background_color_gradient_overlays_image   = $this->shortcode_atts["{$background_prefix}color_gradient_overlays_image"];

				if ( 'on' === $use_background_color_gradient ) {
					$has_background_color_gradient = true;

					$background_images[] = $this->get_gradient( array(
						'type'             => $background_color_gradient_type,
						'direction'        => $background_color_gradient_direction,
						'radial_direction' => $background_color_gradient_direction_radial,
						'color_start'      => $background_color_gradient_start,
						'color_end'        => $background_color_gradient_end,
						'start_position'   => $background_color_gradient_start_position,
						'end_position'     => $background_color_gradient_end_position,
					) );
				}

				// Background Image
				$background_image            = $this->shortcode_atts["{$background_prefix}image"];
				$background_size_default     = isset( $this->fields_unprocessed["{$background_prefix}size"]["default"] ) ? $this->fields_unprocessed[ "{$background_prefix}size" ]["default"] : "";
				$background_size             = $this->shortcode_atts["{$background_prefix}size"];
				$background_position_default = isset( $this->fields_unprocessed["{$background_prefix}position"]["default"] ) ? $this->fields_unprocessed[ "{$background_prefix}position" ]["default"] : "";
				$background_position         = $this->shortcode_atts["{$background_prefix}position"];
				$background_repeat_default   = isset( $this->fields_unprocessed["{$background_prefix}repeat"]["default"] ) ? $this->fields_unprocessed[ "{$background_prefix}repeat" ]["default"] : "";
				$background_repeat           = $this->shortcode_atts["{$background_prefix}repeat"];
				$background_blend_default    = isset( $this->fields_unprocessed["{$background_prefix}blend"]["default"] ) ? $this->fields_unprocessed[ "{$background_prefix}blend" ]["default"] : "";
				$background_blend            = $this->shortcode_atts["{$background_prefix}blend"];
				$parallax                    = $this->shortcode_atts["{$background_prefix}parallax"];

				if ( '' !== $background_image && 'on' !== $parallax ) {
					$has_background_image = true;

					$background_images[] = sprintf( 'url(%1$s)', esc_html( $background_image ) );

					if ( '' !== $background_size && $background_size_default !== $background_size ) {
						$background_style .= sprintf(
							'background-size: %1$s; ',
							esc_html( $background_size )
						);
					}

					if ( '' !== $background_position && $background_position_default !== $background_position ) {
						$background_style .= sprintf(
							'background-position: %1$s; ',
							esc_html( str_replace( '_', ' ', $background_position ) )
						);
					}

					if ( '' !== $background_repeat && $background_repeat_default !== $background_repeat ) {
						$background_style .= sprintf(
							'background-repeat: %1$s; ',
							esc_html( $background_repeat )
						);
					}

					if ( '' !== $background_blend && $background_blend_default !== $background_blend ) {
						$background_style .= sprintf(
							'background-blend-mode: %1$s; ',
							esc_html( $background_blend )
						);

						// Force background-color: initial;
						if ( isset( $has_background_color_gradient, $has_background_image ) ) {
							$background_style .= sprintf( 'background-color: initial%1$s; ', esc_html( ' !important' ) );
						}
					}
				}

				if ( ! empty( $background_images ) ) {
					// The browsers stack the images in the opposite order to what you'd expect.
					if ( 'on' !== $background_color_gradient_overlays_image ) {
						$background_images = array_reverse( $background_images );
					}

					$background_style .= sprintf(
						'background-image: %1$s !important;',
						esc_html( join( ', ', $background_images ) )
					);
				}

				// Reset color if gradient and image is used together
				if ( ! isset( $has_background_color_gradient, $has_background_image ) ) {
					$background_color = $this->shortcode_atts["{$background_prefix}color"];

					if ( '' !== $background_color ) {
						$background_style .= sprintf(
							'background-color: %1$s; ',
							esc_html( $background_color )
						);
					}
				}

				if ( '' !== $background_style ) {
					self::set_style( $function_name, array(
						'selector'    => $css_element_processed,
						'declaration' => rtrim( $background_style ),
						'priority'    => $this->_style_priority,
					) );
				}
			}
		}
	}

	function process_custom_css_options( $function_name ) {
		if ( empty( $this->custom_css_options ) ) {
			return false;
		}

		foreach ( $this->custom_css_options as $slug => $option ) {
			$css      = $this->shortcode_atts["custom_css_{$slug}"];
			$order_class = isset( $this->main_css_element ) && count( explode( ' ', $this->main_css_element ) ) === 1 ? $selector = $this->main_css_element : '%%order_class%%';
			$selector = ! empty( $option['selector'] ) ? $option['selector'] : '';

			if ( false === strpos( $selector, '%%order_class%%' ) ) {
				if ( ! ( isset( $option['no_space_before_selector'] ) && $option['no_space_before_selector'] ) && '' !== $selector ) {
					$selector = " {$selector}";
				}

				$selector = "{$order_class}{$selector}";
			}

			if ( '' !== $css ) {
				self::set_style( $function_name, array(
					'selector'    => $selector,
					'declaration' => trim( $css ),
				) );
			}
		}
	}

	function process_box_shadow( $function_name ) {
		/**
		 * @var ET_Builder_Module_Field_BoxShadow $boxShadow
		 */
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );

		self::set_style( $function_name, array(
			'selector'    => '%%order_class%%',
			'declaration' => $boxShadow->get_value( $this->shortcode_atts )
		) );
	}

	function make_options_filterable() {
		if ( isset( $this->advanced_options ) ) {
			$this->advanced_options = apply_filters(
				"{$this->slug}_advanced_options",
				$this->advanced_options,
				$this->slug,
				$this->main_css_element
			);
		}

		if ( isset( $this->custom_css_options ) ) {
			$this->custom_css_options = apply_filters(
				"{$this->slug}_custom_css_options",
				$this->custom_css_options,
				$this->slug,
				$this->main_css_element
			);
		}

	}

	function disable_wptexturize( $shortcodes ) {
		$shortcodes[] = $this->slug;

		return $shortcodes;
	}

	static function compare_by_priority( $a, $b ) {
		$a_priority = ! empty( $a['priority'] ) ? (int) $a['priority'] : self::DEFAULT_PRIORITY;
		$b_priority = ! empty( $b['priority'] ) ? (int) $b['priority'] : self::DEFAULT_PRIORITY;

		if ( isset( $a['_order_number'], $b['_order_number'] ) && ( $a_priority === $b_priority ) ) {
			return $a['_order_number'] - $b['_order_number'];
		}

		return $a_priority - $b_priority;
	}

	/*
	 * Reorder toggles based on the priority with respect to manually ordered items with no priority
	 *
	 */
	static function et_pb_order_toggles_by_priority( $toggles_array ) {
		if ( empty( $toggles_array ) ) {
			return array();
		}

		$high_priority_toggles = array();
		$low_priority_toggles = array();
		$manually_ordered_toggles = array();

		// fill 3 arrays based on priority
		foreach ( $toggles_array as $toggle_id => $toggle_data ) {
			if ( isset( $toggle_data['priority'] ) ) {
				if ( $toggle_data['priority'] < 10 ) {
					$high_priority_toggles[ $toggle_id ] = $toggle_data;
				} else {
					$low_priority_toggles[ $toggle_id ] = $toggle_data;
				}
			} else {
				// keep the original order of options without priority defined
				$manually_ordered_toggles[ $toggle_id ] = $toggle_data;
			}
		}

		// order high and low priority toggles
		uasort( $high_priority_toggles, array( 'self', 'compare_by_priority' ) );
		uasort( $low_priority_toggles, array( 'self', 'compare_by_priority' ) );

		// merge 3 arrays to get the correct order of toggles.
		return array_merge( $high_priority_toggles, $manually_ordered_toggles, $low_priority_toggles );
	}

	static function compare_by_name( $a, $b ) {
		return strcasecmp( $a->name, $b->name );
	}

	static function get_modules_count( $post_type ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );
		$overall_count  = count( $parent_modules ) + count( $child_modules );

		return $overall_count;
	}

	static function get_modules_js_array( $post_type ) {
		$modules = array();

		$parent_modules = self::get_parent_modules( $post_type );
		if ( ! empty( $parent_modules ) ) {
			/**
			 * Sort modules alphabetically by name.
			 */
			$sorted_modules = $parent_modules;

			uasort( $sorted_modules, array( 'self', 'compare_by_name' ) );

			foreach( $sorted_modules as $module ) {
				/**
				 * Replace single and double quotes with %% and || respectively
				 * to avoid js conflicts
				 */
				$module_name = str_replace( array( '"', '&quot;', '&#34;', '&#034;' ) , '%%', $module->name );
				$module_name = str_replace( array( "'", '&#039;', '&#39;' ) , '||', $module_name );

				$modules[] = sprintf(
					'{ "title" : "%1$s", "label" : "%2$s"%3$s}',
					esc_js( $module_name ),
					esc_js( $module->slug ),
					( isset( $module->fullwidth ) && $module->fullwidth ? ', "fullwidth_only" : "on"' : '' )
				);
			}
		}

		return '[' . implode( ',', $modules ) . ']';
	}

	static function get_shortcodes_with_children( $post_type ) {
		$shortcodes = array();
		if ( ! empty( self::$parent_modules[ $post_type ] ) ) {
			foreach( self::$parent_modules[ $post_type ] as $module ) {
				if ( ! empty( $module->child_slug ) ) {
					$shortcodes[] = sprintf(
						'"%1$s":"%2$s"',
						esc_js( $module->slug ),
						esc_js( $module->child_slug )
					);
				}
			}
		}

		return '{' . implode( ',', $shortcodes ) . '}';
	}

	static function get_modules_array( $post_type = '', $include_child = false, $fb_ignore_unsupported = false ) {
		$modules = array();

		if ( ! empty( $post_type ) ) {
			$parent_modules = self::get_parent_modules( $post_type );

			if ( $include_child ) {
				$parent_modules = array_merge( $parent_modules, self::get_child_modules( $post_type ));
			}

			if ( ! empty( $parent_modules ) ) {
				$sorted_modules = $parent_modules;
			}
		} else {
			$parent_modules = self::get_parent_modules();

			if ( $include_child ) {
				$parent_modules = array_merge( $parent_modules, self::get_child_modules());
			}

			if ( ! empty( $parent_modules ) ) {

				$all_modules = array();
				foreach( $parent_modules as $post_type => $post_type_modules ) {
					foreach ( $post_type_modules as $module_slug => $module ) {
						$all_modules[ $module_slug ] = $module;
					}
				}

				$sorted_modules = $all_modules;
			}
		}

		if ( ! empty( $sorted_modules ) ) {
			/**
			 * Sort modules alphabetically by name.
			 */
			uasort( $sorted_modules, array( 'self', 'compare_by_name' ) );

			foreach( $sorted_modules as $module ) {
				if ( $fb_ignore_unsupported && ( ! isset( $module->fb_support ) || ! $module->fb_support ) ) {
					continue;
				}

				/**
				 * Replace single and double quotes with %% and || respectively
				 * to avoid js conflicts
				 */
				$module_name = str_replace( '"', '%%', $module->name );
				$module_name = str_replace( "'", '||', $module_name );

				$_module = array(
					'title' => esc_attr( $module_name ),
					'label' => esc_attr( $module->slug ),
					'is_parent' => $module->type === 'child' ? 'off' : 'on',
					'fb_support' => isset( $module->fb_support ) && $module->fb_support ? 'on' : 'off',
				);

				if ( isset( $module->fullwidth ) && $module->fullwidth ) {
					$_module['fullwidth_only'] = 'on';
				}

				$modules[] = $_module;
			}
		}

		return $modules;
	}

	static function get_fb_unsupported_modules() {
		$parent_modules = self::get_parent_modules();
		$unsupported_modules_array = array();

		foreach( $parent_modules as $post_type => $post_type_modules ) {
			foreach ( $post_type_modules as $module_slug => $module ) {
				if ( ! isset( $module->fb_support ) || ! $module->fb_support ) {
					$unsupported_modules_array[] = $module_slug;
				}
			}
		}

		return array_unique( $unsupported_modules_array );
	}

	static function get_parent_shortcodes( $post_type ) {
		$shortcodes = array();
		$parent_modules = self::get_parent_modules( $post_type );
		if ( ! empty( $parent_modules ) ) {
			foreach( $parent_modules as $module ) {
				$shortcodes[] = $module->slug;
			}
		}

		return implode( '|', $shortcodes );
	}

	static function get_child_shortcodes( $post_type ) {
		$shortcodes = array();
		$child_modules = self::get_child_modules( $post_type );
		if ( ! empty( $child_modules ) ) {
			foreach( $child_modules as $module ) {
				if ( ! empty( $module->slug ) ) {
					$shortcodes[] = $module->slug;
				}
			}
		}

		return implode( '|', $shortcodes );
	}

	static function get_child_slugs( $post_type ) {
		$child_slugs = array();
		$child_modules = self::get_parent_modules( $post_type );
		if ( ! empty( $child_modules ) ) {
			foreach( $child_modules as $module ) {
				if ( ! empty( $module->child_slug ) ) {
					$child_slugs[ $module->slug ] = $module->child_slug;
				}
			}
		}

		return $child_slugs;
	}

	static function get_raw_content_shortcodes( $post_type ) {
		$shortcodes = array();

		$parent_modules = self::get_parent_modules( $post_type );
		if ( ! empty( $parent_modules ) ) {
			foreach( $parent_modules as $module ) {
				if ( isset( $module->use_row_content ) && $module->use_row_content ) {
					$shortcodes[] = $module->slug;
				}
			}
		}

		$child_modules = self::get_child_modules( $post_type );
		if ( ! empty( $child_modules ) ) {
			foreach( $child_modules as $module ) {
				if ( isset( $module->use_row_content ) && $module->use_row_content ) {
					$shortcodes[] = $module->slug;
				}
			}
		}

		return implode( '|', $shortcodes );
	}

	static function get_modules_templates( $post_type, $slugs_array ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules = self::get_child_modules( $post_type );
		$all_modules = array_merge( $parent_modules, $child_modules );
		$templates_array = array();

		if ( empty( $slugs_array ) ) {
			return;
		}

		foreach ( $slugs_array as $slug ) {
			if ( ! isset( $all_modules[ $slug ] ) ) {
				return '';
			}

			$module = $all_modules[ $slug ];

			$templates_array[] = array(
				'slug'     => $slug,
				'template' => $module->build_microtemplate(),
			);
		}

		if ( ET_BUILDER_OPTIMIZE_TEMPLATES ) {
			$templates_array = array(
				'templates' => $templates_array,
				'unique'    => self::$_unique_bb_keys_values,
			);
		}

		return $templates_array;
	}

	static function output_templates( $post_type = '', $start_from = 0, $amount = 999 ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules = self::get_child_modules( $post_type );
		$all_modules = array_merge( $parent_modules, $child_modules );

		$modules_names = array_keys( $all_modules );

		$output = array();
		$output['templates'] = array();

		if ( ! empty( $all_modules ) ) {
			for ( $i = 0; $i < ET_BUILDER_AJAX_TEMPLATES_AMOUNT; $i++ ) {
				if ( isset( $modules_names[ $i ] ) ) {
					$module = $all_modules[ $modules_names[ $i ] ];
					$output['templates'][ $module->slug ] = self::optimize_bb_chunk( $module->build_microtemplate() );
				} else {
					break;
				}
			}
		}

		if ( ET_BUILDER_OPTIMIZE_TEMPLATES ) {
			$output['unique'] = self::$_unique_bb_keys_values;
		}
		return $output;
	}

	static function get_structure_module_slugs() {

		if ( ! empty( self::$structure_module_slugs ) ) {
			return self::$structure_module_slugs;
		}

		$structure_modules = self::get_structure_modules();
		self::$structure_module_slugs = array();
		foreach( $structure_modules as $structural_module ) {
			self::$structure_module_slugs[] = $structural_module->slug;
		}

		return self::$structure_module_slugs;
	}

	static function get_structure_modules() {
		if ( ! empty( self::$structure_modules ) ) {
			return self::$structure_modules;
		}

		$parent_modules = self::get_parent_modules( 'et_pb_layout' );
		self::$structure_modules = array();
		foreach ( $parent_modules as $parent_module ) {
			if ( isset( $parent_module->is_structure_element ) && $parent_module->is_structure_element ) {
				self::$structure_modules[] = $parent_module;
			}
		}

		return self::$structure_modules;
	}

	static function get_parent_modules( $post_type = '' ) {
		if ( ! empty( $post_type ) ) {
			$parent_modules = ! empty( self::$parent_modules[ $post_type ] ) ? self::$parent_modules[ $post_type ] : array();
		} else {
			$parent_modules = self::$parent_modules;
		}

		return apply_filters( 'et_builder_get_parent_modules', $parent_modules, $post_type );
	}

	static function get_child_modules( $post_type = '' ) {
		if ( ! empty( $post_type ) ) {
			$child_modules = ! empty( self::$child_modules[ $post_type ] ) ? self::$child_modules[ $post_type ] : array();
		} else {
			$child_modules = self::$child_modules;
		}

		return apply_filters( 'et_builder_get_child_modules', $child_modules, $post_type );
	}

	static function get_fields_defaults( $post_type = '', $mode = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_fields_defaults = array();
		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			$module_fields_defaults[ $_module_slug ] = isset( $_module->fields_defaults ) ? $_module->fields_defaults : array();
			$module_fields_defaults[ $_module_slug ]['defaultProps'] = array();

			$child_items_default_fields = array( 'advanced_setting_title_text', 'child_title_fallback_var', 'child_title_var' );

			foreach( $child_items_default_fields as $single_field ) {
				if ( isset( $_module->$single_field ) ) {
					$module_fields_defaults[ $_module_slug ]['defaultProps'][ $single_field ] = $_module->$single_field ;
				}
			}
		}

		return $module_fields_defaults;
	}

	static function get_featured_image_background_modules( $post_type = '' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$featured_image_background_modules = array();

		foreach ( $parent_modules as $slug => $parent_module ) {
			if ( $parent_module->featured_image_background ) {
				$featured_image_background_modules[] = $slug;
			}
		}

		return apply_filters( 'et_pb_featured_image_background_modules', $featured_image_background_modules );
	}

	static function get_defaults( $post_type = '', $mode = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_defaults = array();
		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			$module_defaults[ $_module_slug ] = isset( $_module->defaults ) ? $_module->defaults : array();
		}

		return $module_defaults;
	}

	static function get_toggles( $post_type ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );
		$toggles_array = array();

		$_modules = array_merge( $parent_modules, $child_modules );

		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			$toggles_array[ $_module_slug ] = isset( $_module->options_toggles ) ? $_module->options_toggles : array();

			foreach( array( 'general', 'advanced', 'custom_css' ) as $tab ) {
				// sort toggles by priority
				if ( isset( $toggles_array[ $_module_slug ][ $tab ]['toggles'] ) ) {
					$toggles_array[ $_module_slug ][ $tab ]['toggles'] = self::et_pb_order_toggles_by_priority( $toggles_array[ $_module_slug ][ $tab ]['toggles'] );
				}
			}
		}

		return $toggles_array;
	}

	static function get_all_fields( $post_type = '' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		$_modules = array_merge( $parent_modules, $child_modules );

		$module_fields = array();

		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			$dependables = array();

			$_module->force_unwhitelisted_fields = true;
			$_module->set_fields();
			$_module->_add_additional_fields();
			$_module->_add_custom_css_fields();

			$_module->_maybe_add_defaults();

			foreach ( $_module->fields_unprocessed as $field_key => $field ) {
				// do not add the fields with 'skip' type. These fields used for rendering shortcode on Front End only
				if ( isset( $field['type'] ) && 'skip' === $field['type'] ) {
					continue;
				}

				$field['name'] = $field_key;
				$module_fields[ $_module_slug ][ $field_key ] = $field;
			}
		}

		return $module_fields;
	}

	static function get_general_fields( $post_type = '', $mode = 'all', $module_type = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_fields = array();

		foreach( $_modules as $_module_slug => $_module ) {

			// skip modules without fb support
			if ( ! $_module->fb_support ) {
				continue;
			}

			// filter modules by slug if needed
			if ( 'all' !== $module_type && $module_type !== $_module_slug ) {
				continue;
			}

			$dependables = array();

			foreach ( $_module->fields_unprocessed as $field_key => $field ) {
				if ( isset( $field['affects'] ) ) {
					$dependables[ $field_key ] = $field['affects'];
				}

				if ( isset( $field['tab_slug'] ) && 'general' !== $field['tab_slug'] ) {
					continue;
				}

				$field['name'] = $field_key;
				$module_fields[ $_module_slug ][ $field_key ] = $field;
			}

			if ( ! empty( $dependables ) ) {
				foreach ( $dependables as $dependable_field => $affects ) {
					foreach ( $affects as $affect ) {
						// Avoid pushing depends_to attributte to non-existent module field data
						if ( ! isset( $module_fields[ $_module_slug ][ $affect ] ) ) {
							continue;
						}

						$module_fields[ $_module_slug ][ $affect ]['depends_to'][] = $dependable_field;
					}
				}
			}
		}

		if ( 'all' !== $module_type ) {
			return $module_fields[ $module_type ];
		}

		return $module_fields;
	}

	static function get_advanced_fields( $post_type = '', $mode = 'all', $module_type = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_fields = array();

		foreach( $_modules as $_module_slug => $_module ) {
			// filter modules by slug if needed
			if ( 'all' !== $module_type && $module_type !== $_module_slug ) {
				continue;
			}

			$dependables = array();

			foreach ( $_module->fields_unprocessed as $field_key => $field ) {
				// do not add the fields with 'skip' type. These fields used for rendering shortcode on Front End only
				if ( isset( $field['type'] ) && 'skip' === $field['type'] ) {
					continue;
				}

				if ( isset( $field['affects'] ) ) {
					$dependables[ $field_key ] = $field['affects'];
				}

				if ( ! isset( $field['tab_slug'] ) || 'advanced' !== $field['tab_slug'] ) {
					continue;
				}

				if ( isset( $field['default'] ) ) {
					$module_fields[ $_module_slug ]['advanced_defaults'][ $field_key ] = $field['default'];
				}

				$field['name'] = $field_key;
				$module_fields[ $_module_slug ][ $field_key ] = $field;
			}

			if ( ! empty( $dependables ) ) {
				foreach ( $dependables as $dependable_field => $affects ) {
					foreach ( $affects as $affect ) {
						if ( isset( $module_fields[ $_module_slug ][ $affect ] ) ) {
							$module_fields[ $_module_slug ][ $affect ]['depends_to'][] = $dependable_field;
						}
					}
				}
			}

			if ( ! empty( $_module->advanced_options ) ) {
				$module_fields[ $_module_slug ]['advanced_common'] = $_module->advanced_options;

				if ( isset( $_module->advanced_options['border']['border_styles'] ) ) {
					$module_fields[ $_module_slug ]['border_styles'] = array_merge( $module_fields[ $_module_slug ]['border_styles'], $_module->advanced_options['border']['border_styles'] );
				}

				if ( isset( $_module->advanced_options['border']['border_radii'] ) ) {
					$module_fileds[ $_module_slug ]['border_radii'] = array_merge( $module_fields[ $_module_slug ]['border_radii'], $_module->advanced_options['border']['border_radii'] );
				}
			}
		}

		if ( 'all' !== $module_type ) {
			return $module_fields[ $module_type ];
		}

		return $module_fields;
	}

	static function get_custom_css_fields( $post_type = '', $mode = 'all', $module_type = 'all' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );

		switch ( $mode ) {
			case 'parent':
				$_modules = $parent_modules;
				break;

			case 'child':
				$_modules = $child_modules;
				break;

			default:
				$_modules = array_merge( $parent_modules, $child_modules );
				break;
		}

		$module_fields = array();

		$custom_css_unwanted_types = array( 'custom_css', 'column_settings_css', 'column_settings_css_fields', 'column_settings_custom_css' );
		foreach( $_modules as $_module_slug => $_module ) {
			// filter modules by slug if needed
			if ( 'all' !== $module_type && $module_type !== $_module_slug ) {
				continue;
			}

			$dependables = array();

			$module_fields[ $_module_slug ] = $_module->custom_css_options;

			// Automatically added module ID and module class fields to setting modal's CSS tab
			if ( ! empty( $_module->fields_unprocessed ) ) {
				foreach ( $_module->fields_unprocessed as $field_unprocessed_key => $field_unprocessed ) {
					if ( isset( $field_unprocessed['tab_slug'] ) && 'custom_css' === $field_unprocessed['tab_slug'] &&
						 isset( $field_unprocessed['type'] ) && ! in_array( $field_unprocessed['type'], $custom_css_unwanted_types ) ) {
						$module_fields[ $_module_slug ][ $field_unprocessed_key ] = $field_unprocessed;

						if ( isset( $field_unprocessed['affects'] ) ) {
							$dependables[ $field_unprocessed_key ] = $field_unprocessed['affects'];
						}
					}
				}

				if ( ! empty( $dependables ) ) {
					foreach ( $dependables as $dependable_field => $affects ) {
						foreach ( $affects as $affect ) {
							if ( isset( $module_fields[ $_module_slug ][ $affect ] ) ) {
								$module_fields[ $_module_slug ][ $affect ]['depends_to'][] = $dependable_field;
							}
						}
					}
				}
			}
		}

		if ( 'all' !== $module_type ) {
			return $module_fields[ $module_type ];
		}

		return $module_fields;
	}

	static function get_module_fields( $post_type, $module ) {
		$_modules = array_merge( self::get_parent_modules( $post_type ), self::get_child_modules( $post_type ) );

		if ( ! empty( $_modules[ $module ] ) ) {
			return $_modules[ $module ]->fields_unprocessed;
		}
		return false;
	}

	static function get_parent_module_fields( $post_type, $module ) {
		if ( ! empty( self::$parent_modules[ $post_type ][ $module ] ) ) {
			return self::$parent_modules[ $post_type ][ $module ]->get_fields();
		}
		return false;
	}

	static function get_child_module_fields( $post_type, $module ) {
		if ( ! empty( self::$child_modules[ $post_type ][ $module ] ) ) {
			return self::$child_modules[ $post_type ][ $module ]->get_fields();
		}
		return false;
	}

	static function get_parent_module_field( $post_type, $module, $field ) {
		$fields = self::get_parent_module_fields( $post_type, $module );
		if ( ! empty( $fields[ $field ] ) ) {
			return $fields[ $field ];
		}
		return false;
	}

	static function get_font_icon_fields( $post_type = '' ) {
		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );
		$_modules       = array_merge( $parent_modules, $child_modules );
		$module_fields  = array();

		foreach ( $_modules as $module_name => $module ) {
			foreach ($module->fields_unprocessed as $module_field_name => $module_field) {
				if ( isset( $module_field['renderer'] ) && 'et_pb_get_font_icon_list' === $module_field['renderer'] ) {
					$module_fields[ $module_name ][ $module_field_name ] = true;
				}
			}
		}

		return $module_fields;
	}

	static function get_media_quries( $for_js=false ) {
		$media_queries = array(
			'min_width_1405' => '@media only screen and ( min-width: 1405px )',
			'1100_1405'      => '@media only screen and ( min-width: 1100px ) and ( max-width: 1405px)',
			'981_1405'       => '@media only screen and ( min-width: 981px ) and ( max-width: 1405px)',
			'981_1100'       => '@media only screen and ( min-width: 981px ) and ( max-width: 1100px )',
			'min_width_981'  => '@media only screen and ( min-width: 981px )',
			'max_width_980'  => '@media only screen and ( max-width: 980px )',
			'768_980'        => '@media only screen and ( min-width: 768px ) and ( max-width: 980px )',
			'max_width_767'  => '@media only screen and ( max-width: 767px )',
			'max_width_479'  => '@media only screen and ( max-width: 479px )',
		);

		$media_queries['mobile'] = $media_queries['max_width_767'];

		$media_queries = apply_filters( 'et_builder_media_queries', $media_queries );

		if ( 'for_js' === $for_js ) {
			$processed_queries = array();

			foreach ( $media_queries as $key => $value ) {
				$processed_queries[] = array( $key, $value );
			}
		} else {
			$processed_queries = $media_queries;
		}

		return $processed_queries;
	}

	static function set_media_queries() {
		self::$media_queries = self::get_media_quries();
	}

	static function get_media_query( $name ) {
		if ( ! isset( self::$media_queries[ $name ] ) ) {
			return false;
		}

		return self::$media_queries[ $name ];
	}

	static function get_style( $internal = false ) {
		// use appropriate array depending on which styles we need
		$styles_array = $internal ? self::$internal_modules_styles : self::$styles;

		if ( empty( $styles_array ) ) {
			return '';
		}

		global $et_user_fonts_queue;

		$output = '';

		if ( ! empty( $et_user_fonts_queue ) ) {
			$output .= et_builder_enqueue_user_fonts( $et_user_fonts_queue );
		}

		$styles_by_media_queries = $styles_array;
		$styles_count            = (int) count( $styles_by_media_queries );
		$media_queries_order     = array_merge( array( 'general' ), array_values( self::$media_queries ) );

		// make sure styles in the array ordered by media query correctly from bigger to smaller screensize
		$styles_by_media_queries_sorted = array_merge( array_flip( $media_queries_order ), $styles_by_media_queries );

		foreach ( $styles_by_media_queries_sorted as $media_query => $styles ) {
			// skip wrong values which were added during the array sorting
			if ( ! is_array( $styles ) ) {
				continue;
			}

			$media_query_output    = '';
			$wrap_into_media_query = 'general' !== $media_query;

			// sort styles by priority
			uasort( $styles, array( 'self', 'compare_by_priority' ) );

			// get each rule in a media query
			foreach ( $styles as $selector => $settings ) {
				$media_query_output .= sprintf(
					'%3$s%4$s%1$s { %2$s }',
					$selector,
					$settings['declaration'],
					"\n",
					( $wrap_into_media_query ? "\t" : '' )
				);
			}

			// All css rules that don't use media queries are assigned to the "general" key.
			// Wrap all non-general settings into media query.
			if ( $wrap_into_media_query ) {
				$media_query_output = sprintf(
					'%3$s%3$s%1$s {%2$s%3$s}',
					$media_query,
					$media_query_output,
					"\n"
				);
			}

			$output .= $media_query_output;
		}

		return $output;
	}

	static function get_column_video_background( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		if ( empty( $args ) ) {
			return false;
		}

		$formatted_args = array();

		foreach ( $args as $key => $value) {
			$key_length = strlen( $key );
			$formatted_args[ substr( $key, 0, ( $key_length - 2 ) ) ] = $value;
		}

		return self::get_video_background( $formatted_args, $conditional_tags, $current_page );
	}

	static function get_video_background( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$base_name = isset( $args['computed_variables'] ) && isset( $args['computed_variables']['base_name'] ) ? $args['computed_variables']['base_name'] : 'background';

		$defaults = array(
			"{$base_name}_video_mp4"    => '',
			"{$base_name}_video_webm"   => '',
			"{$base_name}_video_width"  => '',
			"{$base_name}_video_height" => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( '' === $args["{$base_name}_video_mp4"] && '' === $args["{$base_name}_video_webm"] ) {
			return false;
		}

		return do_shortcode( sprintf( '
			<video loop="loop" autoplay playsinline muted %3$s%4$s>
				%1$s
				%2$s
			</video>',
			( '' !== $args["{$base_name}_video_mp4"] ? sprintf( '<source type="video/mp4" src="%s" />', esc_url( $args["{$base_name}_video_mp4"] ) ) : '' ),
			( '' !== $args["{$base_name}_video_webm"] ? sprintf( '<source type="video/webm" src="%s" />', esc_url( $args["{$base_name}_video_webm"] ) ) : '' ),
			( '' !== $args["{$base_name}_video_width"] ? sprintf( ' width="%s"', esc_attr( intval( $args["{$base_name}_video_width"] ) ) ) : '' ),
			( '' !== $args["{$base_name}_video_height"] ? sprintf( ' height="%s"', esc_attr( intval( $args["{$base_name}_video_height"] ) ) ) : '' )
		) );
	}

	static function clean_internal_modules_styles( $need_internal_styles = true ) {
		// clean the styles array
		self::$internal_modules_styles = array();
		// set the flag to make sure new styles will be saved to the correct place
		self::$prepare_internal_styles = $need_internal_styles;
		// generate unique number to make sure module classes will be unique if shortcode is generated via ajax
		self::$internal_modules_counter = rand( 10000, 99999 );
	}

	/**
	 * Set the field dependencies based on the `show_if` or `show_if_not` key from the
	 * field.
	 * @param string $slug       The module's slug. ie `et_pb_section`
	 * @param string $field_id   The field id. id `background_color`
	 * @param array $field_info  Associative array of the field's data.
	 */
	protected static function set_field_dependencies( $slug, $field_id, $field_info ) {
		// bail if the field_info is not an array.
		if ( ! is_array( $field_info ) ) {
			return;
		}

		// otherwise we keep going.
		foreach ( array( 'show_if', 'show_if_not' ) as $dependency_type ) {
			if ( ! isset( $field_info[ $dependency_type ] ) ) {
				continue;
			}

			if ( ! self::$data_utils->is_assoc_array( $field_info[ $dependency_type ] ) ) {
				continue;
			}

			foreach ( $field_info[ $dependency_type ] as $dependency => $value ) {
				// dependency -> dependent (eg. et_pb_signup.provider.affects.first_name_field.show_if: mailchimp)
				$address = array( $slug, $dependency, 'affects', $field_id, $dependency_type );

				self::$data_utils->array_set( self::$field_dependencies, $address, $value );

				// dependent -> dependency (eg. et_pb_signup.first_name_field.show_if.provider: mailchimp)
				$address = array( $slug, $field_id, $dependency_type, $dependency );

				self::$data_utils->array_set( self::$field_dependencies, $address, $value );
			}
		}
	}

	public static function get_field_dependencies( $post_type ) {
		if ( self::$field_dependencies ) {
			return self::$field_dependencies;
		}

		$parent_modules = self::get_parent_modules( $post_type );
		$child_modules  = self::get_child_modules( $post_type );
		$all_modules    = array_merge( $parent_modules, $child_modules );

		foreach ( $all_modules as $module_slug => $module ) {
			// Get all the fields.
			$all_fields = $module->sort_fields( $module->_get_fields() );
			foreach ( $all_fields as $field_id => $field_info ) {
				if ( isset( $field_info['type'] ) && 'composite' === $field_info['type'] ) {
					foreach ( $field_info['composite_structure'] as $field ) {
						foreach ( $field['controls'] as $control => $data ) {
							self::set_field_dependencies( $module_slug, $control, $data );
						}
					}
				}
				self::set_field_dependencies( $module_slug, $field_id, $field_info );
			}
		}

		return self::$field_dependencies;
	}

	static function set_style( $function_name, $style ) {
		$declaration = rtrim($style['declaration']);
		if ( empty($declaration) ) {
			// Do not add empty declarations
			return;
		}
		$builder_post_types = et_builder_get_builder_post_types();
		$allowed_post_types = apply_filters( 'et_builder_set_style_allowed_post_types', $builder_post_types );

		if ( $builder_post_types != $allowed_post_types ) {
			$matches = array_intersect( $allowed_post_types, array_keys( self::$_module_slugs_by_post_type ) );
			$allowed = false;

			foreach ( $matches as $post_type ) {
				if ( ! isset( self::$_module_slugs_by_post_type[ $post_type ] ) ) {
					continue;
				}

				if ( in_array( $function_name, self::$_module_slugs_by_post_type[ $post_type ] ) ) {
					$allowed = true;
					break;
				}
			}

			if ( ! $allowed ) {
				return;
			}
		}

		global $et_pb_rendering_column_content;

		// do not process all the styles if FB enabled. Only those for modules without fb support and styles for the internal modules from Blog/Slider
		if ( et_fb_is_enabled() && ! in_array( $function_name, self::get_fb_unsupported_modules() ) && ! $et_pb_rendering_column_content ) {
			return;
		}

		$order_class_name = self::get_module_order_class( $function_name );

		$selector    = str_replace( '%%order_class%%', ".{$order_class_name}", $style['selector'] );
		$selector    = str_replace( '%order_class%', ".{$order_class_name}", $selector );

		if ( false !== strpos( $selector, '%%parent_class%%' ) ) {
			$parent_class = str_replace( '_item', '', $function_name );
			$selector     = str_replace( '%%parent_class%%', ".{$parent_class}", $selector );
		}

		$selector = apply_filters( 'et_pb_set_style_selector', $selector, $function_name );

		// Prepend .et_divi_builder class before all CSS rules in the Divi Builder plugin
		if ( et_is_builder_plugin_active() ) {
			$selector = ".et_divi_builder #et_builder_outer_content $selector";

			// add the prefix for all the selectors in a string.
			$selector = str_replace( ',', ',.et_divi_builder #et_builder_outer_content ', $selector );
		}

		// New lines are saved as || in CSS Custom settings, remove them
		$declaration = preg_replace( '/(\|\|)/i', '', $declaration );

		$media_query = isset( $style[ 'media_query' ] ) ? $style[ 'media_query' ] : 'general';

		// prepare styles for internal content. Used in Blog/Slider modules if they contain Divi modules
		if ( $et_pb_rendering_column_content && self::$prepare_internal_styles ) {
			if ( isset( self::$internal_modules_styles[ $media_query ][ $selector ]['declaration'] ) ) {
				self::$internal_modules_styles[ $media_query ][ $selector ]['declaration'] = sprintf(
					'%1$s %2$s',
					self::$internal_modules_styles[ $media_query ][ $selector ]['declaration'],
					$declaration
				);
			} else {
				self::$internal_modules_styles[ $media_query ][ $selector ]['declaration'] = $declaration;
			}

			if ( isset( $style['priority'] ) ) {
				self::$internal_modules_styles[ $media_query ][ $selector ]['priority'] = (int) $style['priority'];
			}
		} else {
			if ( isset( self::$styles[ $media_query ][ $selector ]['declaration'] ) ) {
				self::$styles[ $media_query ][ $selector ]['declaration'] = sprintf(
					'%1$s %2$s',
					self::$styles[ $media_query ][ $selector ]['declaration'],
					$declaration
				);
			} else {
				self::$styles[ $media_query ][ $selector ]['declaration'] = $declaration;
			}

			if ( isset( $style['priority'] ) ) {
				self::$styles[ $media_query ][ $selector ]['priority'] = (int) $style['priority'];
			}
		}
	}

	static function get_module_order_class( $function_name ) {
		global $et_pb_rendering_column_content;

		// determine whether we need to get the internal module class or regular
		$get_inner_module_class = $et_pb_rendering_column_content;

		if ( $get_inner_module_class ) {
			if ( ! isset( self::$inner_modules_order[ $function_name ] ) ) {
				return false;
			}
		} else {
			if ( ! isset( self::$modules_order[ $function_name ] ) ) {
				return false;
			}
		}

		$shortcode_order_num = $get_inner_module_class ? self::$inner_modules_order[ $function_name ] : self::$modules_order[ $function_name ];

		$order_class_name = sprintf( '%1$s_%2$s', $function_name, $shortcode_order_num );

		return $order_class_name;
	}

	static function set_order_class( $function_name ) {
		global $et_pb_rendering_column_content;

		// determine whether we need to update the internal module class or regular
		$process_inner_module_class = $et_pb_rendering_column_content;

		if ( $process_inner_module_class ) {
			if ( ! isset( self::$inner_modules_order ) ) {
				self::$inner_modules_order = array();
			}

			self::$inner_modules_order[ $function_name ] = isset( self::$inner_modules_order[ $function_name ] ) ? (int) self::$inner_modules_order[ $function_name ] + 1 : self::$internal_modules_counter;
		} else {
			if ( ! isset( self::$modules_order ) ) {
				self::$modules_order = array();
			}

			self::$modules_order[ $function_name ] = isset( self::$modules_order[ $function_name ] ) ? (int) self::$modules_order[ $function_name ] + 1 : 0;
		}


	}

	static function add_module_order_class( $module_class, $function_name ) {
		$order_class_name = self::get_module_order_class( $function_name );

		return "{$module_class} {$order_class_name}";
	}

	function video_background( $args = array(), $base_name = 'background' ) {
		$attr_prefix   = "{$base_name}_";
		$custom_prefix = 'background' === $base_name ? '' : "{$base_name}_";

		if ( ! empty( $args ) ) {
			$background_video = self::get_video_background( $args );

			$pause_outside_viewport = isset( $args[ "{$attr_prefix}video_pause_outside_viewport" ] ) ? $args[ "{$attr_prefix}video_pause_outside_viewport" ] : '';
			$allow_player_pause          = isset( $args[ "{$custom_prefix}allow_player_pause" ] ) ? $args[ "{$custom_prefix}allow_player_pause" ] : 'off';
		} else {
			$background_video = self::get_video_background( array(
				"{$attr_prefix}video_mp4"    => isset( $this->shortcode_atts["{$attr_prefix}video_mp4"] ) ? $this->shortcode_atts["{$attr_prefix}video_mp4"] : '',
				"{$attr_prefix}video_webm"   => isset( $this->shortcode_atts["{$attr_prefix}video_webm"] ) ? $this->shortcode_atts["{$attr_prefix}video_webm"] : '',
				"{$attr_prefix}video_width"  => isset( $this->shortcode_atts["{$attr_prefix}video_width"] ) ? $this->shortcode_atts["{$attr_prefix}video_width"] : '',
				"{$attr_prefix}video_height" => isset( $this->shortcode_atts["{$attr_prefix}video_height"] ) ? $this->shortcode_atts["{$attr_prefix}video_height"] : '',
				'computed_variables'         => array(
					'base_name' => $base_name,
				),
			) );

			$pause_outside_viewport = isset( $this->shortcode_atts["{$attr_prefix}video_pause_outside_viewport"] ) ? $this->shortcode_atts["{$attr_prefix}video_pause_outside_viewport"] : '';
			$allow_player_pause          = isset( $this->shortcode_atts["{$custom_prefix}allow_player_pause"] ) ? $this->shortcode_atts["{$custom_prefix}allow_player_pause"] : 'off';
		}

		$video_background = '';

		if ( $background_video ) {
			$video_background = sprintf(
				'<span class="et_pb_section_video_bg%2$s%3$s">
					%1$s
				</span>',
				$background_video,
				( 'on' === $allow_player_pause ? ' et_pb_allow_player_pause' : '' ),
				( 'off' === $pause_outside_viewport ? ' et_pb_video_play_outside_viewport' : '' )
			);

			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}

		return $video_background;
	}

	function get_parallax_image_background( $base_name = 'background' ) {
		$attr_prefix   = "{$base_name}_";
		$custom_prefix = 'background' === $base_name ? '' : "{$base_name}_";

		$background_image    = $this->shortcode_atts["{$attr_prefix}image"];
		$parallax            = $this->shortcode_atts["{$custom_prefix}parallax"];
		$parallax_method     = $this->shortcode_atts["{$custom_prefix}parallax_method"];
		$parallax_background = '';

		if ( $this->featured_image_background ) {
			$featured_image         = isset( $this->shortcode_atts['featured_image'] ) ? $this->shortcode_atts['featured_image'] : '';
			$featured_placement     = isset( $this->shortcode_atts['featured_placement'] ) ? $this->shortcode_atts['featured_placement'] : '';
			$featured_image_src_obj = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			$featured_image_src     = isset( $featured_image_src_obj[0] ) ? $featured_image_src_obj[0] : '';

			if ( 'on' === $featured_image && 'background' === $featured_placement && '' !== $featured_image_src ) {
				$background_image = $featured_image_src;
			}
		}

		if ( '' !== $background_image && 'on' == $parallax ) {
			$parallax_classname = array( 'et_parallax_bg' );

			if ( 'off' === $parallax_method ) {
				$parallax_classname[] = 'et_pb_parallax_css';
			}

			$parallax_background = sprintf(
				'<span
					class="%1$s"
					style="background-image: url(%2$s);"
				></span>',
				esc_attr( implode( ' ', $parallax_classname ) ),
				esc_url( $background_image )
			);
		}

		return $parallax_background;
	}

	/**
	 * Generate CSS Filters
	 * Check our shortcode arguments for CSS `filter` properties. If found, set the style rules for this block. (This
	 * function reads options set by the 'Filters' and 'Image Filters' builder menu fields.)
	 *
	 * @param string $function_name Builder module's function name (keeps the CSS rules straight)
	 * @param string $prefix        Optional string prepended to the field name (i.e., `filter_saturate` -> `child_filter_saturate`)
	 * @param mixed  $selectors     Array or string containing all target DOM element(s), ID(s), and/or class(es)
	 *
	 * @return string Any additional CSS classes (added if filters were applied).
	 */
	function generate_css_filters( $function_name = '', $prefix = '', $selectors = array('%%order_class%%') ) {

		if ( '' === $function_name ) {
			ET_Core_Logger::error( '$function_name is required.' );
			return;
		}

		// If `$selectors` is a string, convert to an array before we continue
		$selectors_prepared = $selectors;
		if ( ! is_array( $selectors ) ) {
			$selectors_prepared = explode( ',', et_intentionally_unescaped( $selectors, 'fixed_string' ) );
		}

		$additional_classes = '';

		// If we don't have a target selector, get out now
		if ( ! $selectors_prepared ) {
			return $additional_classes;
		}

		// Blend Mode
		$mix_blend_mode = self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}mix_blend_mode", '' );

		// Filters
		$filter = array(
			'hue_rotate' => self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}filter_hue_rotate", ''),
			'saturate'   => self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}filter_saturate", ''),
			'brightness' => self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}filter_brightness", ''),
			'contrast'   => self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}filter_contrast", ''),
			'invert'     => self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}filter_invert", ''),
			'sepia'      => self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}filter_sepia", ''),
			'opacity'    => self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}filter_opacity", ''),
			'blur'       => self::$data_utils->array_get( $this->shortcode_atts, "{$prefix}filter_blur", ''),
		);

		// Remove any filters with null or default values
		$filter = array_filter( $filter, 'strlen' );

		// Optional: CSS `mix-blend-mode` rule
		$mix_blend_mode_default = ET_Global_Settings::get_value( 'all_mix_blend_mode', 'default' );
		if ( $mix_blend_mode && $mix_blend_mode !== $mix_blend_mode_default ) {
			foreach ( $selectors_prepared as $selector ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $selector,
					'declaration' => sprintf(
						'mix-blend-mode: %1$s;',
						esc_html( $mix_blend_mode )
					),
				) );
			}
			$additional_classes .= ' et_pb_css_mix_blend_mode';
		} else if ( 'et_pb_column' === $function_name ) {
			// Columns need to pass through
			$additional_classes .= ' et_pb_css_mix_blend_mode_passthrough';
		}

		// Optional: CSS `filter` rule
		if ( empty( $filter ) ) {
			return $additional_classes;
		}

		$css_value = '';
		$css_value_fb_hover = '';

		foreach ( $filter as $label => $value ) {
			// Check against our default settings, and only append the rule if it differs
			if ( ET_Global_Settings::get_value( 'all_filter_' . $label, 'default' ) === $value ) {
				continue;
			}

			$value = et_sanitize_input_unit( $value, false, 'deg' );
			$label_css_format = str_replace( '_', '-', $label );

			// Construct string of all CSS Filter values
			$css_value .= esc_html( " ${label_css_format}(${value})" );

			// Construct Visual Builder hover rules
			if ( ! in_array( $label, array( 'opacity', 'blur' ) ) ) {
				// Skip those, because they mess with VB controls
				$css_value_fb_hover .= esc_html( " ${label_css_format}(${value})" );
			}
		}

		// Append our new CSS rules
		if ( trim( $css_value ) ) {
			foreach ( $selectors_prepared as $selector ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $selector,
					'declaration' => sprintf(
						'filter: %1$s;',
						$css_value
					),
				) );
			}
			$additional_classes .= ' et_pb_css_filters';
		}

		// If we have VB hover-friendly CSS rules, we'll gather those and append them here
		if ( trim( $css_value_fb_hover ) ) {
			foreach ( $selectors_prepared as $selector ) {
				$selector_hover = str_replace(
					'%%order_class%%',
					'html:not(.et_fb_edit_enabled) #et-fb-app %%order_class%%:hover',
					$selector
				);
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => $selector_hover,
					'declaration' => sprintf(
						'filter: %1$s;',
						$css_value_fb_hover
					),
				) );

			}
			$additional_classes .= ' et_pb_css_filters_hover';
		}

		return $additional_classes;
	}

	/**
	 * Convert smart quotes and &amp; entity to their applicable characters
	 *
	 * @param  string $text Input text
	 *
	 * @return string
	 */
	static function convert_smart_quotes_and_amp( $text ) {
		$smart_quotes = array(
			'&#8220;',
			'&#8221;',
			'&#8243;',
			'&#8216;',
			'&#8217;',
			'&#x27;',
			'&amp;',
		);

		$replacements = array(
			'&quot;',
			'&quot;',
			'&quot;',
			'&#39;',
			'&#39;',
			'&#39;',
			'&',
		);

		if ( 'fr_FR' === get_locale() ) {
			$french_smart_quotes = array(
				'&nbsp;&raquo;',
				'&Prime;&gt;',
			);

			$french_replacements = array(
				'&quot;',
				'&quot;&gt;',
			);

			$smart_quotes = array_merge( $smart_quotes, $french_smart_quotes );
			$replacements = array_merge( $replacements, $french_replacements );
		}

		$text = str_replace( $smart_quotes, $replacements, $text );

		return $text;
	}

	public function process_multiple_checkboxes_field_value( $value_map, $value ) {
		$result = array();
		$index  = 0;

		foreach ( explode( '|', $value ) as $checkbox_value ) {
			if ( 'on' === $checkbox_value ) {
				$result[] = $value_map[ $index ];
			}

			$index++;
		}

		return implode( '|', $result );
	}
}

do_action( 'et_pagebuilder_module_init' );

class ET_Builder_Module extends ET_Builder_Element {}

class ET_Builder_Structure_Element extends ET_Builder_Element {
	public $is_structure_element = true;

	function wrap_settings_option( $option_output, $field ) {
		$field_type = ! empty( $field['type'] ) ? $field['type'] : '';

		switch( $field_type ) {
			case 'column_settings_background' :
				$output = $this->generate_columns_settings_background();
				break;
			case 'column_settings_padding' :
				$output = $this->generate_columns_settings_padding();
				break;
			case 'column_settings_css_fields' :
				$output = $this->generate_columns_settings_css_fields();
				break;
			case 'column_settings_css' :
				$output = $this->generate_columns_settings_css();
				break;
			default:
				$depends = false;
				if ( isset( $field['depends_show_if'] ) || isset( $field['depends_show_if_not'] ) ) {
					$depends = true;
					if ( isset( $field['depends_show_if_not'] ) ) {
						$depends_show_if_not = is_array( $field['depends_show_if_not'] ) ? implode( ',', $field['depends_show_if_not'] ) : $field['depends_show_if_not'];

						$depends_attr = sprintf( ' data-depends_show_if_not="%s"', esc_attr( $depends_show_if_not ) );
					} else {
						$depends_attr = sprintf( ' data-depends_show_if="%s"', esc_attr( $field['depends_show_if'] ) );
					}
				}

				// Overriding background color's attribute, turning it into appropriate background attributes
				if ( isset( $field['type'] ) && isset( $field['name' ] ) && in_array( $field['name'], array( 'background_color' ) ) ) {

					$field['type'] = 'background';

					// Appending background class
					if ( isset( $field['option_class'] ) ) {
						$field['option_class'] .= ' et-pb-option--background';
					} else {
						$field['option_class'] = 'et-pb-option--background';
					}

					// Removing depends default variable which hides background color for unified background field UI
					$depends = false;

					if ( isset( $field['depends_default'] ) ) {
						unset( $field['depends_default'] );
					}
				}

				$output = sprintf(
					'%6$s<div class="et-pb-option et-pb-option--%11$s%1$s%2$s%3$s%8$s%9$s%10$s"%4$s>%5$s</div>%7$s',
					( ! empty( $field['type'] ) && 'tiny_mce' == $field['type'] ? ' et-pb-option-main-content' : '' ),
					( ( $depends || isset( $field['depends_default'] ) ) ? ' et-pb-depends' : '' ),
					( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? ' et_pb_hidden' : '' ),
					( $depends ? $depends_attr : '' ),
					"\n\t\t\t\t" . $option_output . "\n\t\t\t",
					"\t",
					"\n\n\t\t",
					( ! empty( $field['type'] ) && 'hidden' == $field['type'] ? esc_attr( sprintf( ' et-pb-option-%1$s', $field['name'] ) ) : '' ),
					( ! empty( $field['option_class'] ) ? ' ' . $field['option_class'] : '' ),
					isset( $field['specialty_only'] ) && 'yes' === $field['specialty_only'] ? ' et-pb-specialty-only-option' : '',
					isset( $field['type'] ) ? esc_attr( $field['type'] ) : ''
				);
				break;
		}

		return self::get_unique_bb_key( $output );
	}

	function generate_column_vars_css() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_module_id_value = typeof et_pb_module_id_%1$s !== \'undefined\' ? et_pb_module_id_%1$s : \'\',
					current_module_class_value = typeof et_pb_module_class_%1$s !== \'undefined\' ? et_pb_module_class_%1$s : \'\',
					current_custom_css_before_value = typeof et_pb_custom_css_before_%1$s !== \'undefined\' ? et_pb_custom_css_before_%1$s : \'\',
					current_custom_css_main_value = typeof et_pb_custom_css_main_%1$s !== \'undefined\' ? et_pb_custom_css_main_%1$s : \'\',
					current_custom_css_after_value = typeof et_pb_custom_css_after_%1$s !== \'undefined\' ? et_pb_custom_css_after_%1$s : \'\';
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_column_vars_bg() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_value_bg = typeof et_pb_background_color_%1$s !== \'undefined\' ? et_pb_background_color_%1$s : \'\',
					current_value_bg_img = typeof et_pb_bg_img_%1$s !== \'undefined\' ? et_pb_bg_img_%1$s : \'\';
					current_background_size_cover = typeof et_pb_background_size_%1$s !== \'undefined\' && et_pb_background_size_%1$s === \'cover\' ? \' selected="selected"\' : \'\';
					current_background_size_contain = typeof et_pb_background_size_%1$s !== \'undefined\' && et_pb_background_size_%1$s === \'contain\' ? \' selected="selected"\' : \'\';
					current_background_size_initial = typeof et_pb_background_size_%1$s !== \'undefined\' && et_pb_background_size_%1$s === \'initial\' ? \' selected="selected"\' : \'\';
					current_background_position_topleft = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'top_left\' ? \' selected="selected"\' : \'\';
					current_background_position_topcenter = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'top_center\' ? \' selected="selected"\' : \'\';
					current_background_position_topright = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'top_right\' ? \' selected="selected"\' : \'\';
					current_background_position_centerleft = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'center_left\' ? \' selected="selected"\' : \'\';
					current_background_position_center = typeof et_pb_background_position_%1$s === \'undefined\' || et_pb_background_position_%1$s === \'center\' ? \' selected="selected"\' : \'\';
					current_background_position_centerright = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'center_right\' ? \' selected="selected"\' : \'\';
					current_background_position_bottomleft = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'bottom_left\' ? \' selected="selected"\' : \'\';
					current_background_position_bottomcenter = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'bottom_center\' ? \' selected="selected"\' : \'\';
					current_background_position_bottomright = typeof et_pb_background_position_%1$s !== \'undefined\' && et_pb_background_position_%1$s === \'bottom_right\' ? \' selected="selected"\' : \'\';
					current_background_repeat_repeat = typeof et_pb_background_repeat_%1$s === \'undefined\' || et_pb_background_repeat_%1$s === \'repeat\' ? \' selected="selected"\' : \'\';
					current_background_repeat_repeatx = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'repeat-x\' ? \' selected="selected"\' : \'\';
					current_background_repeat_repeaty = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'repeat-y\' ? \' selected="selected"\' : \'\';
					current_background_repeat_space = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'space\' ? \' selected="selected"\' : \'\';
					current_background_repeat_round = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'round\' ? \' selected="selected"\' : \'\';
					current_background_repeat_norepeat = typeof et_pb_background_repeat_%1$s !== \'undefined\' && et_pb_background_repeat_%1$s === \'no-repeat\' ? \' selected="selected"\' : \'\';
					current_background_blend_normal = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'normal\' ? \' selected="selected"\' : \'\';
					current_background_blend_multiply = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'multiply\' ? \' selected="selected"\' : \'\';
					current_background_blend_screen = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'screen\' ? \' selected="selected"\' : \'\';
					current_background_blend_overlay = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'overlay\' ? \' selected="selected"\' : \'\';
					current_background_blend_darken = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'darken\' ? \' selected="selected"\' : \'\';
					current_background_blend_lighten = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'lighten\' ? \' selected="selected"\' : \'\';
					current_background_blend_colordodge = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'color-dodge\' ? \' selected="selected"\' : \'\';
					current_background_blend_colorburn = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'color-burn\' ? \' selected="selected"\' : \'\';
					current_background_blend_hardlight = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'hard-light\' ? \' selected="selected"\' : \'\';
					current_background_blend_softlight = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'soft-light\' ? \' selected="selected"\' : \'\';
					current_background_blend_difference = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'difference\' ? \' selected="selected"\' : \'\';
					current_background_blend_exclusion = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'hue\' ? \' selected="selected"\' : \'\';
					current_background_blend_hue = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'saturation\' ? \' selected="selected"\' : \'\';
					current_background_blend_saturation = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'color\' ? \' selected="selected"\' : \'\';
					current_background_blend_color = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'normal\' ? \' selected="selected"\' : \'\';
					current_background_blend_luminosity = typeof et_pb_background_blend_%1$s !== \'undefined\' && et_pb_background_blend_%1$s === \'luminosity\' ? \' selected="selected"\' : \'\';
					current_use_background_color_gradient = typeof et_pb_use_background_color_gradient_%1$s !== \'undefined\' && \'on\' === et_pb_use_background_color_gradient_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_start = typeof et_pb_background_color_gradient_start_%1$s !== \'undefined\' ? et_pb_background_color_gradient_start_%1$s : \'%2$s\';
					current_background_color_gradient_end = typeof et_pb_background_color_gradient_end_%1$s !== \'undefined\' ? et_pb_background_color_gradient_end_%1$s : \'%3$s\';
					current_background_color_gradient_type = typeof et_pb_background_color_gradient_type_%1$s !== \'undefined\' && \'radial\' === et_pb_background_color_gradient_type_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction = typeof et_pb_background_color_gradient_direction_%1$s !== \'undefined\' ? et_pb_background_color_gradient_direction_%1$s : \'%4$s\';
					current_background_color_gradient_direction_radial_center = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'center\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_top_left = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'top left\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_top = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'top\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_top_right = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'top right\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_right = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'right\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_bottom_right = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'bottom right\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_bottom = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'bottom\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_bottom_left = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'bottom left\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_direction_radial_left = typeof et_pb_background_color_gradient_direction_radial_%1$s !== \'undefined\' && \'left\' === et_pb_background_color_gradient_direction_radial_%1$s ? \' selected="selected"\' : \'\';
					current_background_color_gradient_start_position = typeof et_pb_background_color_gradient_start_position_%1$s !== \'undefined\' ? et_pb_background_color_gradient_start_position_%1$s : \'%5$s\';
					current_background_color_gradient_end_position = typeof et_pb_background_color_gradient_end_position_%1$s !== \'undefined\' ? et_pb_background_color_gradient_end_position_%1$s : \'%6$s\';
					current_background_color_gradient_overlays_image = typeof et_pb_background_color_gradient_overlays_image_%1$s !== \'undefined\' && \'on\' === et_pb_background_color_gradient_overlays_image_%1$s ? \' selected="selected"\' : \'\';
					current_background_video_mp4 = typeof et_pb_background_video_mp4_%1$s !== \'undefined\' ? et_pb_background_video_mp4_%1$s : \'\';
					current_background_video_webm = typeof et_pb_background_video_webm_%1$s !== \'undefined\' ? et_pb_background_video_webm_%1$s : \'\';
					current_background_video_width = typeof et_pb_background_video_width_%1$s !== \'undefined\' ? et_pb_background_video_width_%1$s : \'\';
					current_background_video_height = typeof et_pb_background_video_height_%1$s !== \'undefined\' ? et_pb_background_video_height_%1$s : \'\';
					current_allow_played_pause = typeof et_pb_allow_player_pause_%1$s !== \'undefined\' &&  \'on\' === et_pb_allow_player_pause_%1$s ? \' selected="selected"\' : \'\';
					current_background_video_pause_outside_viewport = typeof et_pb_background_video_pause_outside_viewport_%1$s !== \'undefined\' &&  \'off\' === et_pb_background_video_pause_outside_viewport_%1$s ? \' selected="selected"\' : \'\';
					current_value_parallax = typeof et_pb_parallax_%1$s !== \'undefined\' && \'on\' === et_pb_parallax_%1$s ? \' selected="selected"\' : \'\';
					current_value_parallax_method = typeof et_pb_parallax_method_%1$s !== \'undefined\' && \'on\' !== et_pb_parallax_method_%1$s ? \' selected="selected"\' : \'\';
					break; ',
				esc_attr( $i ),
				esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_start' ) ),
				esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_end' ) ),
				esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_direction' ) ),
				esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_start_position' ) ), // #5
				esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_end_position' ) )
			);
		}

		return $output;
	}

	function generate_column_vars_padding() {
		$output = '';
		for ( $i = 1; $i < 5; $i++ ) {
			$output .= sprintf(
				'case %1$s :
					current_value_pt = typeof et_pb_padding_top_%1$s !== \'undefined\' ? et_pb_padding_top_%1$s : \'\',
					current_value_pr = typeof et_pb_padding_right_%1$s !== \'undefined\' ? et_pb_padding_right_%1$s : \'\',
					current_value_pb = typeof et_pb_padding_bottom_%1$s !== \'undefined\' ? et_pb_padding_bottom_%1$s : \'\',
					current_value_pl = typeof et_pb_padding_left_%1$s !== \'undefined\' ? et_pb_padding_left_%1$s : \'\',
					current_value_padding_tablet = typeof et_pb_padding_%1$s_tablet !== \'undefined\' ? et_pb_padding_%1$s_tablet : \'\',
					current_value_padding_phone = typeof et_pb_padding_%1$s_phone !== \'undefined\' ? et_pb_padding_%1$s_phone : \'\',
					last_edited_padding_field = typeof et_pb_padding_%1$s_last_edited !== \'undefined\' ?  et_pb_padding_%1$s_last_edited : \'\',
					has_tablet_padding = typeof et_pb_padding_%1$s_tablet !== \'undefined\' ? \'yes\' : \'no\',
					has_phone_padding = typeof et_pb_padding_%1$s_phone !== \'undefined\' ? \'yes\' : \'no\';
					break; ',
				esc_attr( $i )
			);
		}

		return $output;
	}

	function generate_columns_settings_background() {
		$output = sprintf(
			'<%% var columns = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter = 1;
				_.each( columns, function ( column_type ) {
					var current_value_bg,
						current_value_bg_img,
						current_value_parallax,
						current_value_parallax_method,
						current_background_size_cover,
						current_background_size_contain,
						current_background_size_initial,
						current_background_position_topleft,
						current_background_position_topcenter,
						current_background_position_topright,
						current_background_position_centerleft,
						current_background_position_center,
						current_background_position_centerright,
						current_background_position_bottomleft,
						current_background_position_bottomcenter,
						current_background_position_bottomright,
						current_background_repeat_repeat,
						current_background_repeat_repeatx,
						current_background_repeat_repeaty,
						current_background_repeat_space,
						current_background_repeat_round,
						current_background_repeat_norepeat,
						current_background_blend_normal,
						current_background_blend_multiply,
						current_background_blend_screen,
						current_background_blend_overlay,
						current_background_blend_darken,
						current_background_blend_lighten,
						current_background_blend_colordodge,
						current_background_blend_colorburn,
						current_background_blend_hardlight,
						current_background_blend_softlight,
						current_background_blend_difference,
						current_background_blend_exclusion,
						current_background_blend_hue,
						current_background_blend_saturation,
						current_background_blend_color,
						current_background_blend_luminosity,
						current_use_background_color_gradient,
						current_background_color_gradient_start,
						current_background_color_gradient_end,
						current_background_color_gradient_type,
						current_background_color_gradient_direction,
						current_background_color_gradient_direction_radial_center,
						current_background_color_gradient_direction_radial_top_left,
						current_background_color_gradient_direction_radial_top,
						current_background_color_gradient_direction_radial_top_right,
						current_background_color_gradient_direction_radial_right,
						current_background_color_gradient_direction_radial_bottom_right,
						current_background_color_gradient_direction_radial_bottom,
						current_background_color_gradient_direction_radial_bottom_left,
						current_background_color_gradient_direction_radial_left,
						current_background_color_gradient_start_position,
						current_background_color_gradient_end_position,
						current_background_color_gradient_overlays_image,
						current_background_video_mp4,
						current_background_video_webm,
						current_background_video_width,
						current_background_video_height,
						current_allow_played_pause,
						current_background_video_pause_outside_viewport;
					switch ( counter ) {
						%1$s
					}
			%%>',
			$this->generate_column_vars_bg()
		);

		$tab_navs = sprintf(
			'<ul class="et_pb_background-tab-navs">
				<li>
					<a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--color" data-tab="color" title="%1$s">
						%5$s
					</a>
				</li><li>
					<a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--gradient" data-tab="gradient" title="%2$s">
						%6$s
					</a>
				</li><li>
					<a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--image" data-tab="image" title="%3$s">
						%7$s
					</a>
				</li><li>
					<a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--video" data-tab="video" title="%4$s">
						%8$s
					</a>
				</li>
			</ul>',
			esc_html__( 'Color', 'et_builder' ),
			esc_html__( 'Gradient', 'et_builder' ),
			esc_html__( 'Image', 'et_builder' ),
			esc_html__( 'Video', 'et_builder' ),
			$this->get_icon( 'background-color' ),
			$this->get_icon( 'background-gradient' ),
			$this->get_icon( 'background-image' ),
			$this->get_icon( 'background-video' )
		);

		$tab_color = sprintf(
			'<div class="et_pb_background-tab et_pb_background-tab--color" data-tab="color">
				<div class="et_pb_background-option et_pb_background-option--background_color et-pb-option et-pb-option--background_color et-pb-option--has-preview">
					<label for="et_pb_background_color">%1$s: </label>
					<div class="et-pb-option-container et-pb-option-container--color-alpha">
						<div class="et-pb-option-preview et-pb-option-preview--empty">
							<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
								%2$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
								%3$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
								%4$s
							</button>
						</div>
						<input id="et_pb_background_color_<%%= counter %%>" class="et-pb-color-picker-hex et-pb-color-picker-hex-alpha et-pb-color-picker-hex-has-preview" type="text" data-alpha="true" placeholder="%5$s" data-selected-value="" value="<%%= current_value_bg %%>">
					</div>
				</div>
			</div>',
			esc_html__( 'Background Color', 'et_builder' ),
			$this->get_icon( 'add' ),
			$this->get_icon( 'setting' ),
			$this->get_icon( 'delete' ),
			esc_html__( 'Hex Value', 'et_builder' )
		);

		$tab_gradient = sprintf(
			'<div class="et_pb_background-tab et_pb_background-tab--gradient" data-tab="gradient">
				<div class="et-pb-option-preview et-pb-option-preview--empty">
					<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
						%1$s
					</button>
					<button class="et-pb-option-preview-button et-pb-option-preview-button--swap">
						%2$s
					</button>
					<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
						%3$s
					</button>
				</div>
				<div class="et_pb_background-option et_pb_background-option--use_background_color_gradient et_pb_background-template--use_color_gradient et-pb-option et-pb-option--use_background_color_gradient">
					<label for="et_pb_use_background_color_gradient_<%%= counter %%>">%4$s: </label>
					<div class="et-pb-option-container et-pb-option-container--yes_no_button">
						<div class="et_pb_yes_no_button_wrapper ">
							<div class="et_pb_yes_no_button et_pb_off_state">
								<span class="et_pb_value_text et_pb_on_value">%5$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%6$s</span>
							</div>
							<select name="et_pb_use_background_color_gradient_<%%= counter %%>" id="et_pb_use_background_color_gradient_<%%= counter %%>" class="et-pb-main-setting regular-text et-pb-affects" data-affects="background_color_gradient_start_<%%= counter %%>, background_color_gradient_end_<%%= counter %%>, background_color_gradient_start_position_<%%= counter %%>, background_color_gradient_end_position_<%%= counter %%>, background_color_gradient_type_<%%= counter %%>, background_color_gradient_overlays_image_<%%= counter %%>" data-default="off">
								<option value="off">%6$s</option>
								<option value="on" <%%= current_use_background_color_gradient %%>>%5$s</option>
							</select>
						</div><span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_start et_pb_background-template--color_gradient_start et-pb-option et-pb-option--background_color_gradient_start" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_start_<%%= counter %%>">%7$s: </label>
					<div class="et-pb-option-container et-pb-option-container--color-alpha">
						<div class="wp-picker-container">
							<input id="et_pb_background_color_gradient_start_<%%= counter %%>" class="et-pb-color-picker-hex et-pb-color-picker-hex-alpha et-pb-main-setting" type="text" data-alpha="true" placeholder="%8$s" data-selected-value="<%%= current_background_color_gradient_start %%>" value="<%%= current_background_color_gradient_start %%>" data-default-color="%26$s" data-default="%26$s">
						</div>
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_end et_pb_background-template--color_gradient_end et-pb-option et-pb-option--background_color_gradient_end" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_end_<%%= counter %%>">%9$s: </label>
					<div class="et-pb-option-container et-pb-option-container--color-alpha">
						<div class="wp-picker-container">
							<input id="et_pb_background_color_gradient_end_<%%= counter %%>" class="et-pb-color-picker-hex et-pb-color-picker-hex-alpha et-pb-main-setting" type="text" data-alpha="true" placeholder="%8$s" data-selected-value="<%%= current_background_color_gradient_end %%>" value="<%%= current_background_color_gradient_end %%>" data-default-color="%27$s" data-default="%27$s">
						</div>
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_type et_pb_background-template--color_gradient_type et-pb-option et-pb-option--background_color_gradient_type" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_type_<%%= counter %%>">%10$s: </label>
					<div class="et-pb-option-container et-pb-option-container--select">
						<select name="et_pb_background_color_gradient_type_<%%= counter %%>" id="et_pb_background_color_gradient_type_<%%= counter %%>" class="et-pb-main-setting  et-pb-affects" data-affects="background_color_gradient_direction_<%%= counter %%>, background_color_gradient_direction_radial_<%%= counter %%>" data-default="linear">
							<option value="linear">%11$s</option>
							<option value="radial" <%%= current_background_color_gradient_type %%>>%12$s</option>
						</select>
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_direction et_pb_background-template--color_gradient_direction et-pb-option et-pb-option--background_color_gradient_direction" data-depends_show_if="linear">
					<label for="et_pb_background_color_gradient_direction_<%%= counter %%>">%13$s: </label>
					<div class="et-pb-option-container et-pb-option-container--range">
						<input type="range" class="et-pb-main-setting et-pb-range et-pb-fixed-range" data-default="180" value="<%%= current_background_color_gradient_direction %%>" min="0" max="360" step="1">
						<input id="et_pb_background_color_gradient_direction_<%%= counter %%>" type="text" class="regular-text et-pb-validate-unit et-pb-range-input" value="<%%= current_background_color_gradient_direction %%>" data-default="180deg">
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_direction_radial et_pb_background-template--color_gradient_direction_radial et-pb-option et-pb-option--background_color_gradient_direction_radial" data-depends_show_if="radial">
					<label for="et_pb_background_color_gradient_direction_radial_<%%= counter %%>">%14$s: </label>
					<div class="et-pb-option-container et-pb-option-container--select">
						<select name="et_pb_background_color_gradient_direction_radial_<%%= counter %%>" id="et_pb_background_color_gradient_direction_radial_<%%= counter %%>" class="et-pb-main-setting" data-default="center">
							<option value="center" <%%= current_background_color_gradient_direction_radial_center %%>>%15$s</option>
							<option value="top left" <%%= current_background_color_gradient_direction_radial_top_left %%>>%16$s</option>
							<option value="top" <%%= current_background_color_gradient_direction_radial_top %%>>%17$s</option>
							<option value="top right" <%%= current_background_color_gradient_direction_radial_top_right %%>>%18$s</option>
							<option value="right" <%%= current_background_color_gradient_direction_radial_right %%>>%19$s</option>
							<option value="bottom right" <%%= current_background_color_gradient_direction_radial_bottom_right %%>>%20$s</option>
							<option value="bottom" <%%= current_background_color_gradient_direction_radial_bottom %%>>%21$s</option>
							<option value="bottom left" <%%= current_background_color_gradient_direction_radial_bottom_left %%>>%22$s</option>
							<option value="left" <%%= current_background_color_gradient_direction_radial_left %%>>%23$s</option>
						</select>
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_start_position et_pb_background-template--color_gradient_start_position et-pb-option et-pb-option--background_color_gradient_start_position" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_start_position_<%%= counter %%>">%24$s: </label>
					<div class="et-pb-option-container et-pb-option-container--range">
						<input type="range" class="et-pb-main-setting et-pb-range et-pb-fixed-range" data-default="0" value="<%%= parseInt( current_background_color_gradient_start_position.trim() ) %%>" min="0" max="100" step="1">
						<input id="et_pb_background_color_gradient_start_position_<%%= counter %%>" type="text" class="regular-text et-pb-validate-unit et-pb-range-input" value="<%%= current_background_color_gradient_start_position %%>" data-default="0%%">
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_end_position et_pb_background-template--color_gradient_end_position et-pb-option et-pb-option--background_color_gradient_end_position" data-depends_show_if="on">
					<label for="et_pb_background_color_gradient_end_position_<%%= counter %%>">%25$s: </label>
					<div class="et-pb-option-container et-pb-option-container--range">
						<input type="range" class="et-pb-main-setting et-pb-range et-pb-fixed-range" data-default="100" value="<%%= parseInt( current_background_color_gradient_end_position.trim() ) %%>" min="0" max="100" step="1">
						<input id="et_pb_background_color_gradient_end_position_<%%= counter %%>" type="text" class="regular-text et-pb-validate-unit et-pb-range-input" value="<%%= current_background_color_gradient_end_position %%>" data-default="100%%">
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_color_gradient_overlays_image et_pb_background-template--use_color_gradient et-pb-option et-pb-option--background_color_gradient_overlays_image">
					<label for="et_pb_background_color_gradient_overlays_image_<%%= counter %%>">%35$s: </label>
					<div class="et-pb-option-container et-pb-option-container--yes_no_button">
						<div class="et_pb_yes_no_button_wrapper ">
							<div class="et_pb_yes_no_button et_pb_off_state">
								<span class="et_pb_value_text et_pb_on_value">%5$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%6$s</span>
							</div>
							<select name="et_pb_background_color_gradient_overlays_image_<%%= counter %%>" id="et_pb_background_color_gradient_overlays_image_<%%= counter %%>" class="et-pb-main-setting regular-text" data-depends_show_if="on" data-default="off">
								<option value="off">%6$s</option>
								<option value="on" <%%= current_background_color_gradient_overlays_image %%>>%5$s</option>
							</select>
						</div><span class="et-pb-reset-setting"></span>
					</div>
				</div>

			</div>',
			$this->get_icon( 'add' ),
			$this->get_icon( 'swap' ),
			$this->get_icon( 'delete' ),
			esc_html__( 'Background Gradient', 'et_builder' ),
			esc_html__( 'On', 'et_builder' ), // #5
			esc_html__( 'Off', 'et_builder' ),
			esc_html__( 'Gradient Start', 'et_builder' ),
			esc_html__( 'Hex Value', 'et_builder' ),
			esc_html__( 'Gradient End', 'et_builder' ),
			esc_html__( 'Gradient Type', 'et_builder' ), // #10
			esc_html__( 'Linear', 'et_builder' ),
			esc_html__( 'Radial', 'et_builder' ),
			esc_html__( 'Gradient Direction', 'et_builder' ),
			esc_html__( 'Radial Direction', 'et_builder' ),
			esc_html__( 'Center', 'et_builder' ), // #15
			esc_html__( 'Top Left', 'et_builder' ),
			esc_html__( 'Top', 'et_builder' ),
			esc_html__( 'Top Right', 'et_builder' ),
			esc_html__( 'Right', 'et_builder' ),
			esc_html__( 'Bottom Right', 'et_builder' ), // #20
			esc_html__( 'Bottom', 'et_builder' ),
			esc_html__( 'Bottom Left', 'et_builder' ),
			esc_html__( 'Left', 'et_builder' ),
			esc_html__( 'Start Position', 'et_builder' ),
			esc_html__( 'End Position', 'et_builder' ), // #25
			esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_start' ) ),
			esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_end' ) ),
			esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_type' ) ),
			esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_direction' ) ),
			esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ) ), // #30
			esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_start_position' ) ),
			esc_attr( intval( ET_Global_Settings::get_value( 'all_background_gradient_start_position' ) ) ),
			esc_attr( ET_Global_Settings::get_value( 'all_background_gradient_end_position' ) ),
			esc_attr( intval( ET_Global_Settings::get_value( 'all_background_gradient_end_position' ) ) ),
			esc_html__( 'Place Gradient Above Background Image', 'et_builder' ) // #35
		);

		$select_background_size = sprintf(
			'<select name="et_pb_background_size_<%%= counter %%>" id="et_pb_background_size_<%%= counter %%>" class="et-pb-main-setting" data-default="cover">
				<option value="cover"<%%= current_background_size_cover %%>>%1$s</option>
				<option value="contain"<%%= current_background_size_contain %%>>%2$s</option>
				<option value="initial"<%%= current_background_size_initial %%>>%3$s</option>
			</select>',
			esc_html__( 'Cover', 'et_builder' ),
			esc_html__( 'Fit', 'et_builder' ),
			esc_html__( 'Actual Size', 'et_builder' )
		);

		$select_background_position = sprintf(
			'<select name="et_pb_background_position_<%%= counter %%>" id="et_pb_background_position_<%%= counter %%>" class="et-pb-main-setting" data-default="center">
				<option value="top_left"<%%= current_background_position_topleft %%>>%1$s</option>
				<option value="top_center"<%%= current_background_position_topcenter %%>>%2$s</option>
				<option value="top_right"<%%= current_background_position_topright %%>>%3$s</option>
				<option value="center_left"<%%= current_background_position_centerleft %%>>%4$s</option>
				<option value="center"<%%= current_background_position_center %%>>%5$s</option>
				<option value="center_right"<%%= current_background_position_centerright %%>>%6$s</option>
				<option value="bottom_left"<%%= current_background_position_bottomleft %%>>%7$s</option>
				<option value="bottom_center"<%%= current_background_position_bottomcenter %%>>%8$s</option>
				<option value="bottom_right"<%%= current_background_position_bottomright %%>>%9$s</option>
			</select>',
			esc_html__( 'Top Left', 'et_builder' ),
			esc_html__( 'Top Center', 'et_builder' ),
			esc_html__( 'Top Right', 'et_builder' ),
			esc_html__( 'Center Left', 'et_builder' ),
			esc_html__( 'Center', 'et_builder' ),
			esc_html__( 'Center Right', 'et_builder' ),
			esc_html__( 'Bottom Left', 'et_builder' ),
			esc_html__( 'Bottom Center', 'et_builder' ),
			esc_html__( 'Bottom Right', 'et_builder' )
		);

		$select_background_repeat = sprintf(
			'<select name="et_pb_background_repeat_<%%= counter %%>" id="et_pb_background_repeat_<%%= counter %%>" class="et-pb-main-setting" data-default="repeat">
				<option value="no-repeat"<%%= current_background_repeat_norepeat %%>>%1$s</option>
				<option value="repeat"<%%= current_background_repeat_repeat %%>>%2$s</option>
				<option value="repeat-x"<%%= current_background_repeat_repeatx %%>>%3$s</option>
				<option value="repeat-y"<%%= current_background_repeat_repeaty %%>>%4$s</option>
				<option value="space"<%%= current_background_repeat_space %%>>%5$s</option>
				<option value="round"<%%= current_background_repeat_round %%>>%6$s</option>
			</select>',
			esc_html__( 'No Repeat', 'et_builder' ),
			esc_html__( 'Repeat', 'et_builder' ),
			esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
			esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
			esc_html__( 'Space', 'et_builder' ),
			esc_html__( 'Round', 'et_builder' )
		);

		$select_background_blend = sprintf(
			'<select name="et_pb_background_blend_<%%= counter %%>" id="et_pb_background_blend_<%%= counter %%>" class="et-pb-main-setting" data-default="normal">
				<option value="normal"<%%= current_background_blend_normal %%>>%1$s</option>
				<option value="multiply"<%%= current_background_blend_multiply %%>>%2$s</option>
				<option value="screen"<%%= current_background_blend_screen %%>>%3$s</option>
				<option value="overlay"<%%= current_background_blend_overlay %%>>%4$s</option>
				<option value="darken"<%%= current_background_blend_darken %%>>%5$s</option>
				<option value="lighten"<%%= current_background_blend_lighten %%>>%6$s</option>
				<option value="color-dodge"<%%= current_background_blend_colordodge %%>>%7$s</option>
				<option value="color-burn"<%%= current_background_blend_colorburn %%>>%8$s</option>
				<option value="hard-light"<%%= current_background_blend_hardlight %%>>%9$s</option>
				<option value="soft-light"<%%= current_background_blend_softlight %%>>%10$s</option>
				<option value="difference"<%%= current_background_blend_difference %%>>%11$s</option>
				<option value="exclusion"<%%= current_background_blend_exclusion %%>>%12$s</option>
				<option value="hue"<%%= current_background_blend_hue %%>>%13$s</option>
				<option value="saturation"<%%= current_background_blend_saturation %%>>%14$s</option>
				<option value="color"<%%= current_background_blend_color %%>>%15$s</option>
				<option value="luminosity"<%%= current_background_blend_luminosity %%>>%16$s</option>
			</select>',
			esc_html__( 'Normal', 'et_builder' ),
			esc_html__( 'Multiply', 'et_builder' ),
			esc_html__( 'Screen', 'et_builder' ),
			esc_html__( 'Overlay', 'et_builder' ),
			esc_html__( 'Darken', 'et_builder' ),
			esc_html__( 'Lighten', 'et_builder' ),
			esc_html__( 'Color Dodge', 'et_builder' ),
			esc_html__( 'Color Burn', 'et_builder' ),
			esc_html__( 'Hard Light', 'et_builder' ),
			esc_html__( 'Soft Light', 'et_builder' ),
			esc_html__( 'Difference', 'et_builder' ),
			esc_html__( 'Exclusion', 'et_builder' ),
			esc_html__( 'Hue', 'et_builder' ),
			esc_html__( 'Saturation', 'et_builder' ),
			esc_html__( 'Color', 'et_builder' ),
			esc_html__( 'Luminosity', 'et_builder' )
		);

		$tab_image = sprintf(
			'<div class="et_pb_background-tab et_pb_background-tab--image" data-tab="image">
				<div class="et_pb_background-option et_pb_background-option--background_image et-pb-option et-pb-option--background_image et-pb-option--has-preview">
					<label for="et_pb_bg_img_<%%= counter %%>">%1$s: </label>
					<div class="et-pb-option-container et-pb-option-container--upload">
						<div class="et-pb-option-preview et-pb-option-preview--empty">
							<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
								%2$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
								%3$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
								%4$s
							</button>
						</div>
						<input id="et_pb_bg_img_<%%= counter %%>" type="text" class="et-pb-main-setting regular-text et-pb-upload-field" value="<%%= current_value_bg_img  %%>">
						<input type="button" class="button button-upload et-pb-upload-button" value="%5$s" data-choose="%6$s" data-update="%7$s" data-type="image">
						<span class="et-pb-reset-setting" style="display: none;"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--parallax et_pb_background-template--parallax et-pb-option et-pb-option--parallax">
					<label for="et_pb_parallax_<%%= counter %%>">%8$s: </label>
					<div class="et-pb-option-container et-pb-option-container--yes_no_button">
						<div class="et_pb_yes_no_button_wrapper ">
							<div class="et_pb_yes_no_button et_pb_off_state">
								<span class="et_pb_value_text et_pb_on_value">%9$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%10$s</span>
							</div>
							<select name="et_pb_parallax_<%%= counter %%>" id="et_pb_parallax_<%%= counter %%>" class="et-pb-main-setting regular-text et-pb-affects" data-affects="parallax_method_<%%= counter %%>, background_size_<%%= counter %%>, background_position_<%%= counter %%>, background_repeat_<%%= counter %%>, background_blend_<%%= counter %%>" data-default="off">
								<option value="off">%10$s</option>
								<option value="on" <%%= current_value_parallax %%>>%9$s</option>
							</select>
						</div><span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--parallax_method et_pb_background-template--parallax_method et-pb-option et-pb-option--parallax_method" data-depends_show_if="on">
					<label for="et_pb_parallax_method_<%%= counter %%>">%11$s: </label>
					<div class="et-pb-option-container et-pb-option-container--select">
						<select name="et_pb_parallax_method_<%%= counter %%>" id="et_pb_parallax_method_<%%= counter %%>" class="et-pb-main-setting" data-default="on">
							<option value="on">%12$s</option>
							<option value="off" <%%= current_value_parallax_method %%>>%13$s</option>
						</select>
						<span class="et-pb-reset-setting" style="display: none;"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_size et_pb_background-template--size et-pb-option et-pb-option--background_size" data-depends_show_if="off" data-option_name="background_size">
					<label for="et_pb_background_size">%14$s:</label>
					<div class="et-pb-option-container et-pb-option-container--select">
						%15$s
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_position et_pb_background-template--position et-pb-option et-pb-option--background_position" data-depends_show_if="off" data-option_name="background_position">
					<label for="et_pb_background_position">%16$s:</label>
					<div class="et-pb-option-container et-pb-option-container--select">
						%17$s
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_repeat et_pb_background-template--repeat et-pb-option et-pb-option--background_repeat" data-depends_show_if="off" data-option_name="background_repeat">
					<label for="et_pb_background_repeat">%18$s:</label>
					<div class="et-pb-option-container et-pb-option-container--select">
						%19$s
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_blend et_pb_background-template--blend et-pb-option et-pb-option--background_blend" data-depends_show_if="off" data-option_name="background_blend">
					<label for="et_pb_background_blend">%20$s: </label>
					<div class="et-pb-option-container et-pb-option-container--select">
						%21$s
					</div>
				</div>
			</div>',
			esc_html__( 'Background Image', 'et_builder' ),
			$this->get_icon( 'add' ),
			$this->get_icon( 'setting' ),
			$this->get_icon( 'delete' ),
			esc_html__( 'Upload an image', 'et_builder' ), // #5
			esc_html__( 'Choose a Background Image', 'et_builder' ),
			esc_html__( 'Set As Background', 'et_builder' ),
			esc_html__( 'Use Parallax Effect', 'et_builder' ),
			esc_html__( 'On', 'et_builder' ),
			esc_html__( 'Off', 'et_builder' ), // #10
			esc_html__( 'Parallax Method', 'et_builder' ),
			esc_html__( 'True Parallax', 'et_builder' ),
			esc_html__( 'CSS', 'et_builder' ),
			esc_html__( 'Background Image Size', 'et_builder' ),
			$select_background_size, // #15
			esc_html__( 'Background Image Position', 'et_builder' ),
			$select_background_position,
			esc_html__( 'Background Image Repeat', 'et_builder' ),
			$select_background_repeat,
			esc_html__( 'Background Image Blend', 'et_builder' ), // #20
			$select_background_blend
		);

		$tab_video = sprintf(
			'<div class="et_pb_background-tab et_pb_background-tab--video" data-tab="video">
				<div class="et_pb_background-option et_pb_background-option--background_video_mp4 et_pb_background-template--video_mp4 et-pb-option et-pb-option--background_video_mp4 et-pb-option--has-preview">
					<label for="et_pb_background_video_mp4_<%%= counter %%>">%1$s: </label>
					<div class="et-pb-option-container et-pb-option-container--upload">
						<div class="et-pb-option-preview et-pb-option-preview--empty">
							<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
								%2$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
								%3$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
								%4$s
							</button>
						</div>
						<input id="et_pb_background_video_mp4_<%%= counter %%>" type="text" class="et-pb-main-setting regular-text et-pb-upload-field" value="<%%= current_background_video_mp4 %%>">
						<input type="button" class="button button-upload et-pb-upload-button" value="%5$s" data-choose="%6$s" data-update="%7$s" data-type="video">
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_video_webm et_pb_background-template--video_webm et-pb-option et-pb-option--background_video_webm et-pb-option--has-preview">
					<label for="et_pb_background_video_webm_<%%= counter %%>">%8$s: </label>
					<div class="et-pb-option-container et-pb-option-container--upload">
						<div class="et-pb-option-preview et-pb-option-preview--empty">
							<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
								%2$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
								%3$s
							</button>
							<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
								%4$s
							</button>
						</div>
						<input id="et_pb_background_video_webm_<%%= counter %%>" type="text" class="et-pb-main-setting regular-text et-pb-upload-field" value="<%%= current_background_video_webm %%>">
						<input type="button" class="button button-upload et-pb-upload-button" value="%5$s" data-choose="%9$s" data-update="%7$s" data-type="video">
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_video_width et_pb_background-template--video_width et-pb-option et-pb-option--background_video_width">
					<label for="et_pb_background_video_width_<%%= counter %%>">%10$s: </label>
					<div class="et-pb-option-container et-pb-option-container--text">
						<input id="et_pb_background_video_width_<%%= counter %%>" type="text" class="regular-text et-pb-main-setting" value="<%%= current_background_video_width %%>">
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_video_height et_pb_background-template--video_height et-pb-option et-pb-option--background_video_height">
					<label for="et_pb_background_video_height_<%%= counter %%>">%11$s: </label>
					<div class="et-pb-option-container et-pb-option-container--text">
						<input id="et_pb_background_video_height_<%%= counter %%>" type="text" class="regular-text et-pb-main-setting" value="<%%= current_background_video_height %%>">
						<span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--allow_player_pause et_pb_background-template--allow_player_pause et-pb-option et-pb-option--allow_player_pause">
					<label for="et_pb_allow_player_pause_<%%= counter %%>">%12$s: </label>
					<div class="et-pb-option-container et-pb-option-container--yes_no_button">
						<div class="et_pb_yes_no_button_wrapper ">
							<div class="et_pb_yes_no_button et_pb_off_state">
								<span class="et_pb_value_text et_pb_on_value">%13$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%14$s</span>
							</div>
							<select name="et_pb_allow_player_pause_<%%= counter %%>" id="et_pb_allow_player_pause_<%%= counter %%>" class="et-pb-main-setting regular-text" data-default="off">
								<option value="off">%14$s</option>
								<option value="on" <%%= current_allow_played_pause %%>>%13$s</option>
							</select>
						</div><span class="et-pb-reset-setting"></span>
					</div>
				</div>
				<div class="et_pb_background-option et_pb_background-option--background_video_pause_outside_viewport et_pb_background-template--background_video_pause_outside_viewport et-pb-option et-pb-option--background_video_pause_outside_viewport">
					<label for="et_pb_background_video_pause_outside_viewport_<%%= counter %%>">%15$s: </label>
					<div class="et-pb-option-container et-pb-option-container--yes_no_button">
						<div class="et_pb_yes_no_button_wrapper ">
							<div class="et_pb_yes_no_button et_pb_off_state">
								<span class="et_pb_value_text et_pb_on_value">%13$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%14$s</span>
							</div>
							<select name="et_pb_background_video_pause_outside_viewport_<%%= counter %%>" id="et_pb_background_video_pause_outside_viewport_<%%= counter %%>" class="et-pb-main-setting regular-text" data-default="on">
								<option value="on">%13$s</option>
								<option value="off" <%%= current_background_video_pause_outside_viewport %%>>%14$s</option>
							</select>
						</div><span class="et-pb-reset-setting"></span>
					</div>
				</div>
			</div>',
			esc_html__( 'Background Video MP4', 'et_builder' ),
			$this->get_icon( 'add' ),
			$this->get_icon( 'setting' ),
			$this->get_icon( 'delete' ),
			esc_html__( 'Upload a video', 'et_builder' ), // #5
			esc_html__( 'Choose a Background Video MP4 File', 'et_builder' ),
			esc_html__( 'Set As Background Video', 'et_builder' ),
			esc_html__( 'Background Video Webm', 'et_builder' ),
			esc_html__( 'Choose a Background Video WEBM File', 'et_builder' ),
			esc_html__( 'Background Video Width', 'et_builder' ), // #10
			esc_html__( 'Background Video Height', 'et_builder' ),
			esc_html__( 'Pause Video When Another Video Plays', 'et_builder' ),
			esc_html__( 'On', 'et_builder' ),
			esc_html__( 'Off', 'et_builder' ),
			esc_html__( 'Pause Video While Not In View', 'et_builder' ) // #15
		);

		$output .= sprintf(
			'<div class="et_pb_subtoggle_section">
				<div class="et-pb-option-toggle-content">
					<div class="et-pb-option et-pb-option--background">
						<label for="et_pb_background">
							%1$s
							<%% if ( "4_4" !== column_type ) { %%>
								<%%= counter + " " %%>
							<%% } %%>
							%2$s:
						</label>
						<div class="et-pb-option-container et-pb-option-container--background" data-column-index="<%%= counter %%>">
							%3$s

							%4$s

							%5$s

							%6$s

							%7$s
						</div>
					</div>
				</div>
			</div>
			<%% counter++;
			}); %%>',
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'Background', 'et_builder' ),
			$tab_navs,
			$tab_color,
			$tab_gradient, // #5
			$tab_image,
			$tab_video
		);

		return $output;
	}

	function generate_columns_settings_padding() {
		$output = sprintf(
			'<%% var columns = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter = 1;
				_.each( columns, function ( column_type ) {
					var current_value_pt,
						current_value_pr,
						current_value_pb,
						current_value_pl,
						current_value_padding_tablet,
						current_value_padding_phone,
						has_tablet_padding,
						has_phone_padding;
					switch ( counter ) {
						%1$s
					}
			%%>',
			$this->generate_column_vars_padding()
		);

		$output .= sprintf(
			'<div class="et_pb_subtoggle_section">
				<div class="et-pb-option-toggle-content">
					<div class="et-pb-option">
						<label for="et_pb_padding_<%%= counter %%>">
							%1$s
							<%% if ( "4_4" !== column_type ) { %%>
								<%%= counter + " " %%>
							<%% } %%>
							%2$s:
						</label>
						<div class="et-pb-option-container">
						%7$s
							<div class="et_custom_margin_padding">
								<label>
									%3$s
									<input type="text" class="medium-text et_custom_margin et_custom_margin_top et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_top_<%%= counter %%>" name="et_pb_padding_top_<%%= counter %%>" value="<%%= current_value_pt %%>" data-device="desktop">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_top et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_top et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
								</label>
								<label>
									%4$s
									<input type="text" class="medium-text et_custom_margin et_custom_margin_right et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_right_<%%= counter %%>" name="et_pb_padding_right_<%%= counter %%>" value="<%%= current_value_pr %%>" data-device="desktop">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_right et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_right et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
								</label>
								<label>
									%5$s
									<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_bottom_<%%= counter %%>" name="et_pb_padding_bottom_<%%= counter %%>" value="<%%= current_value_pb %%>" data-device="desktop">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_bottom et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
								</label>
								<label>
									%6$s
									<input type="text" class="medium-text et_custom_margin et_custom_margin_left et-pb-validate-unit et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active" id="et_pb_padding_left_<%%= counter %%>" name="et_pb_padding_left_<%%= counter %%>" value="<%%= current_value_pl %%>" data-device="desktop">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_left et_pb_setting_mobile et_pb_setting_mobile_tablet" data-device="tablet">
									<input type="text" class="medium-text et_custom_margin et_custom_margin_left et_pb_setting_mobile et_pb_setting_mobile_phone" data-device="phone">
								</label>
								<input type="hidden" class="et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_desktop et-pb-main-setting et_pb_setting_mobile_active" value="<%%= \'\' === current_value_pt && \'\' === current_value_pr && \'\' === current_value_pb && \'\' === current_value_pl ? \'\' : current_value_pt + \'|\' + current_value_pr + \'|\' + current_value_pb + \'|\' + current_value_pl %%>" data-device="desktop">
								<input type="hidden" class="et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_tablet et-pb-main-setting" id="et_pb_padding_<%%= counter %%>_tablet" name="et_pb_padding_<%%= counter %%>_tablet" value="<%%= current_value_padding_tablet %%>" data-device="tablet" data-has_saved_value="<%%= has_tablet_padding %%>">
								<input type="hidden" class="et_custom_margin_main et_pb_setting_mobile et_pb_setting_mobile_phone et-pb-main-setting" id="et_pb_padding_<%%= counter %%>_phone" name="et_pb_padding_<%%= counter %%>_phone" value="<%%= current_value_padding_phone %%>" data-device="phone" data-has_saved_value="<%%= has_phone_padding %%>">
								<input id="et_pb_padding_<%%= counter %%>_last_edited" type="hidden" class="et_pb_mobile_last_edited_field" value="<%%= last_edited_padding_field %%>">
							</div>
							<span class="et-pb-mobile-settings-toggle"></span>
							<span class="et-pb-reset-setting"></span>
						</div>
					</div>
				</div>
			</div>
			<%% counter++;
			}); %%>',
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'Padding', 'et_builder' ),
			esc_html__( 'Top', 'et_builder' ),
			esc_html__( 'Right', 'et_builder' ),
			esc_html__( 'Bottom', 'et_builder' ), // #5
			esc_html__( 'Left', 'et_builder' ),
			et_pb_generate_mobile_options_tabs() // #7
		);

		return $output;
	}

	function generate_columns_settings_css() {
		$output = sprintf(
			'<%%
			var columns_css = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter_css = 1;

			_.each( columns_css, function ( column_type ) {
				var current_module_id_value,
					current_module_class_value,
					current_custom_css_before_value,
					current_custom_css_main_value,
					current_custom_css_after_value;
				switch ( counter_css ) {
					%1$s
				} %%>
				<div class="et_pb_subtoggle_section">
					<div class="et-pb-option-toggle-content">
						<div class="et-pb-option et-pb-option--custom_css">
							<label for="et_pb_custom_css_before_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%3$s:<span>.et_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%>:before</span>
							</label>

							<div class="et-pb-option-container et-pb-custom-css-option">
								<textarea id="et_pb_custom_css_before_<%%= counter_css %%>" class="et-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_before_value.replace( /\|\|/g, "\n" ) %%></textarea>
							</div>
						</div>

						<div class="et-pb-option et-pb-option--custom_css">
							<label for="et_pb_custom_css_main_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%4$s:<span>.et_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%></span>
							</label>

							<div class="et-pb-option-container et-pb-custom-css-option">
								<textarea id="et_pb_custom_css_main_<%%= counter_css %%>" class="et-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_main_value.replace( /\|\|/g, "\n" ) %%></textarea>
							</div>
						</div>

						<div class="et-pb-option et-pb-option--custom_css">
							<label for="et_pb_custom_css_after_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%5$s:<span>.et_pb_column_<%%= \'row_inner\' === module_type ? \'inner_\' : \'\' %%><%%= typeof columns_order !== \'undefined\' && typeof columns_order[counter_css-1] !== \'undefined\' ?  columns_order[counter_css-1] : \'\' %%>:after</span>
							</label>

							<div class="et-pb-option-container et-pb-custom-css-option">
								<textarea id="et_pb_custom_css_after_<%%= counter_css %%>" class="et-pb-main-setting large-text coderegular-text" rows="4" cols="50"><%%= current_custom_css_after_value.replace( /\|\|/g, "\n" ) %%></textarea>
							</div>
						</div>
					</div>
				</div>

			<%% counter_css++;
			}); %%>',
			$this->generate_column_vars_css(),
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'Before', 'et_builder' ),
			esc_html__( 'Main Element', 'et_builder' ),
			esc_html__( 'After', 'et_builder' )
		);

		return $output;
	}

	function generate_columns_settings_css_fields() {
		$output = sprintf(
			'<%%
			var columns_css = typeof columns_layout !== \'undefined\' ? columns_layout.split(",") : [],
				counter_css = 1;

			_.each( columns_css, function ( column_type ) {
				var current_module_id_value,
					current_module_class_value;
				switch ( counter_css ) {
					%1$s
				} %%>
				<div class="et_pb_subtoggle_section">
					<div class="et-pb-option-toggle-content">
						<div class="et-pb-option et_pb_custom_css_regular">
							<label for="et_pb_module_id_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%3$s:
							</label>

							<div class="et-pb-option-container">
								<input id="et_pb_module_id_<%%= counter_css %%>" type="text" class="regular-text et_pb_custom_css_regular et-pb-main-setting" value="<%%= current_module_id_value %%>">
							</div>
						</div>

						<div class="et-pb-option et_pb_custom_css_regular">
							<label for="et_pb_module_class_<%%= counter_css %%>">
								%2$s
								<%% if ( "4_4" !== column_type ) { %%>
									<%%= counter_css + " " %%>
								<%% } %%>
								%4$s:
							</label>

							<div class="et-pb-option-container">
								<input id="et_pb_module_class_<%%= counter_css %%>" type="text" class="regular-text et_pb_custom_css_regular et-pb-main-setting" value="<%%= current_module_class_value %%>">
							</div>
						</div>
					</div>
				</div>
			<%% counter_css++;
			}); %%>',
			$this->generate_column_vars_css(),
			esc_html__( 'Column', 'et_builder' ),
			esc_html__( 'CSS ID', 'et_builder' ),
			esc_html__( 'CSS Class', 'et_builder' )
		);

		return $output;
	}
}
