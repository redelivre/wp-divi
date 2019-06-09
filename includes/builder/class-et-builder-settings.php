<?php

require_once 'module/field/Factory.php';
require_once 'module/helpers/Overflow.php';


class ET_Builder_Settings {

	/**
	 * @var array
	 */
	protected static $_BUILDER_SETTINGS_FIELDS;

	/**
	 * @var array
	 */
	protected static $_BUILDER_SETTINGS_VALUES;

	/**
	 * @var array
	 */
	protected static $_PAGE_SETTINGS_FIELDS;

	/**
	 * @var array
	 */
	protected static $_PAGE_SETTINGS_FIELDS_META_KEY_MAP = array();

	/**
	 * @var array
	 */
	protected static $_PAGE_SETTINGS_IS_DEFAULT;

	/**
	 * @var array
	 */
	protected static $_PAGE_SETTINGS_VALUES;

	/**
	 * @var ET_Builder_Settings
	 */
	protected static $_instance;

	public function __construct() {
		if ( null !== self::$_instance ) {
			wp_die( esc_html( get_class( $this ) . 'is a singleton class. You cannot create a another instance.' ) );
		}

		$this->_initialize();
		$this->_register_callbacks();
	}

	protected static function _get_ab_testing_fields() {
		return array(
			'et_pb_enable_ab_testing'         => array(
				'type'        => 'yes_no_button',
				'options'     => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'id'          => 'et_pb_enable_ab_testing',
				'label'       => esc_html__( 'Enable Split Testing', 'et_builder' ),
				'autoload'    => false,
				'default'     => 'off',
				'class'       => 'et-pb-visible',
				'affects'     => array(
					'et_pb_ab_bounce_rate_limit',
					'et_pb_ab_stats_refresh_interval',
					'et_pb_enable_shortcode_tracking',
				),
				'tab_slug'    => 'content',
				'toggle_slug' => 'ab_testing',
			),
			'et_pb_ab_bounce_rate_limit'      => array(
				'type'            => 'range',
				'id'              => 'et_pb_ab_bounce_rate_limit',
				'label'           => esc_html__( 'Bounce Rate Limit', 'et_builder' ),
				'default'         => 5,
				'range_settings'  => array(
					'step' => 1,
					'min'  => 3,
					'max'  => 60,
				),
				'depends_show_if' => 'on',
				'mobile_options'  => false,
				'unitless'        => true,
				'depends_on'      => array(
					'et_pb_enable_ab_testing',
				),
				'tab_slug'        => 'content',
				'toggle_slug'     => 'ab_testing',
			),
			'et_pb_ab_stats_refresh_interval' => array(
				'type'            => 'select',
				'id'              => 'et_pb_ab_stats_refresh_interval',
				'label'           => esc_html__( 'Stats refresh interval', 'et_builder' ),
				'autoload'        => false,
				'depends_show_if' => 'on',
				'default'         => 'hourly',
				'options'         => array(
					'hourly' => esc_html__( 'Hourly', 'et_builder' ),
					'daily'  => esc_html__( 'Daily', 'et_builder' ),
				),
				'depends_on'      => array(
					'et_pb_enable_ab_testing',
				),
				'tab_slug'        => 'content',
				'toggle_slug'     => 'ab_testing',
			),
			'et_pb_enable_shortcode_tracking' => array(
				'type'            => 'yes_no_button',
				'options'         => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'id'              => 'et_pb_enable_shortcode_tracking',
				'label'           => esc_html__( 'Shortcode Tracking', 'et_builder' ),
				'depends_show_if' => 'on',
				'affects'         => array(
					'et_pb_ab_current_shortcode',
				),
				'depends_on'      => array(
					'et_pb_enable_ab_testing',
				),
				'tab_slug'        => 'content',
				'toggle_slug'     => 'ab_testing',
			),
			'et_pb_ab_current_shortcode'      => array(
				'type'            => 'textarea',
				'id'              => 'et_pb_ab_current_shortcode',
				'label'           => esc_html__( 'Shortcode for Tracking:', 'et_builder' ),
				'autoload'        => false,
				'readonly'        => 'readonly',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'et_pb_enable_shortcode_tracking',
				),
				'tab_slug'        => 'content',
				'toggle_slug'     => 'ab_testing',
			),
			'et_pb_ab_subjects'               => array(
				'id'          => 'et_pb_ab_subjects',
				'type'        => 'hidden',
				'tab_slug'    => 'content',
				'toggle_slug' => 'ab_testing',
				'autoload'    => false,
			),
		);
	}

	protected static function _get_builder_settings_fields() {
		$builder_settings_fields = array(
			'et_pb_static_css_file' => self::_get_static_css_generation_field( 'builder' ),
			'et_pb_css_in_footer'   => array(
				'type'            => 'yes_no_button',
				'id'              => 'et_pb_css_in_footer',
				'index'           => -1,
				'label'           => esc_html__( 'Output Styles Inline', 'et_builder' ),
				'description'     => esc_html__( 'With previous versions of the builder, css styles for the modules\' design settings were output inline in the footer. Enable this option to restore that behavior.', 'et_builder' ),
				'options'         => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'default'         => 'off',
				'validation_type' => 'simple_text',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'performance',
			),
			'et_pb_product_tour_global'   => array(
				'type'            => 'yes_no_button',
				'id'              => 'et_pb_product_tour_global',
				'index'           => -1,
				'label'           => esc_html__( 'Product Tour', 'et_builder' ),
				'description'     => esc_html__( 'If enabled Product Tour will be started automatically when Visual Builder launched for the first time', 'et_builder' ),
				'options'         => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'default'         => 'on',
				'validation_type' => 'simple_text',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'product_tour',
			),
			'et_enable_bfb'   => array(
				'type'              => 'yes_no_button',
				'id'                => 'et_enable_bfb',
				'index'             => -1,
				'label'             => esc_html__( 'Enable The Latest Divi Builder Experience', 'et_builder' ),
				'description'       => esc_html__( 'Disabling this option will load the legacy Divi Builder interface when editing a post using the classic WordPress post editor. The legacy builder lacks many features and interface improvements, but it can still be used if you are experiencing trouble with the new interface.', 'et_builder' ),
				'options'           => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'default'           => 'off',
				'validation_type'   => 'simple_text',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'enable_bfb',
				'main_setting_name' => 'et_bfb_settings',
				'sub_setting_name'  => 'enable_bfb',
				'is_global'         => true,
			),
			'et_enable_classic_editor'   => array(
				'type'            => 'yes_no_button',
				'id'              => 'et_enable_classic_editor',
				'index'           => -1,
				'label'           => esc_html__( 'Enable Classic Editor', 'et_builder' ),
				'description'     => esc_html__( 'Use Classic Editor instead of Gutenberg / Block Editor', 'et_builder' ),
				'options'         => array(
					'on'  => __( 'On', 'et_builder' ),
					'off' => __( 'Off', 'et_builder' ),
				),
				'default'         => 'off',
				'validation_type' => 'simple_text',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'enable_classic_editor',
			),
			'et_pb_post_type_integration' => array(
				'type'            => 'checkbox_list',
				'usefor'          => 'custom',
				'id'              => 'et_pb_post_type_integration',
				'index'           => -1,
				'label'           => esc_html__( 'Enable Divi Builder On Post Types', 'et_builder' ),
				'description'     => esc_html__( 'By default, the Divi Builder is only accessible on standard post types. This option lets you enable the builder on any custom post type currently registered on your website, however the builder may not be compatible with all custom post types.', 'et_builder' ),
				'options'         => 'ET_Builder_Settings::get_registered_post_type_options',
				'default'         => self::_get_post_type_options_defaults(),
				'validation_type' => 'on_off_array',
				'et_save_values'  => true,
				'tab_slug'        => 'post_type_integration',
				'toggle_slug'     => 'performance',
			),
		);

		// Remove "Enable Classic Editor" options for versions of WordPress
		// that don't have the Gutenberg editor.
		if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '<' ) ) {
			unset( $builder_settings_fields['et_enable_classic_editor'] );
		}

		return $builder_settings_fields;
	}

	protected static function _get_builder_settings_in_epanel_format() {
		$tabs   = self::get_tabs( 'builder' );
		$fields = self::get_fields( 'builder' );
		$result = array();

		$result[]    = array( 'name' => 'wrap-builder', 'type' => 'contenttab-wrapstart' );
		$result[]    = array( 'type' => 'subnavtab-start' );
		$tab_content = array();
		$index       = 0;

		foreach ( $tabs as $tab_slug => $tab_name ) {
			$index++;
			$tab_content_started = false;

			foreach ( $fields as $field_name => $field_info ) {
				if ( $field_info['tab_slug'] !== $tab_slug ) {
					continue;
				}

				if ( ! $tab_content_started ) {
					$result[]      = array( 'name' => "builder-{$index}", 'type' => 'subnav-tab', 'desc' => $tab_name );
					$tab_content[] = array( 'name' => "builder-{$index}", 'type' => 'subcontent-start' );

					$tab_content_started = true;
				}

				$field_type = $field_info['type'];

				if ( 'yes_no_button' === $field_type ) {
					$field_type = 'checkbox2';
				}

				$tab_content[] = array_merge( $field_info, array(
					'name'             => $field_info['label'],
					'id'               => $field_name,
					'type'             => $field_type,
					'std'              => $field_info['default'],
					'desc'             => $field_info['description'],
					'is_builder_field' => true,
				) );
			}

			if ( $tab_content_started ) {
				$tab_content[] = array( 'name' => "builder-{$index}", 'type' => 'subcontent-end' );
			}
		}

		$result[] = array( 'type' => 'subnavtab-end' );
		$result   = array_merge( $result, $tab_content );
		$result[] = array( 'name' => 'wrap-builder', 'type' => 'contenttab-wrapend' );

		return $result;
	}

	protected static function _get_builder_settings_values() {
		return array(
			'et_pb_static_css_file' => et_get_option( 'et_pb_static_css_file', 'on' ),
			'et_pb_css_in_footer'   => et_get_option( 'et_pb_css_in_footer', 'off' ),
		);
	}

	protected static function _get_page_settings_fields() {
		$fields = array();
		$overflow = ET_Builder_Module_Fields_Factory::get( 'Overflow' );

		if ( et_pb_is_allowed( 'ab_testing' ) ) {
			$fields = self::_get_ab_testing_fields();
		}

		$overflow_fields = $overflow->get_fields( array(
			'prefix'      => 'et_pb_',
			'tab_slug'    => 'advanced',
			'toggle_slug' => 'visibility',
		) );

		$fields = array_merge( $fields, $overflow_fields );

		$fields = array_merge( $fields, array(
			'et_pb_custom_css'                    => array(
				'type'        => 'codemirror',
				'id'          => 'et_pb_custom_css',
				'mode'        => 'css',
				'inline'      => false,
				'label'       => esc_html__( 'Custom CSS', 'et_builder' ),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'custom_css',
			),
			'et_pb_color_palette'                 => array(
				'type'        => 'colorpalette',
				'id'          => 'et_pb_color_palette',
				'label'       => esc_html__( 'Color Picker Color Pallete', 'et_builder' ),
				'default'     => implode( '|', et_pb_get_default_color_palette() ),
				'tab_slug'    => 'design',
				'toggle_slug' => 'color_palette',
			),
			'et_pb_page_gutter_width'             => array(
				'type'           => 'range',
				'id'             => 'et_pb_page_gutter_width',
				'meta_key'       => '_et_pb_gutter_width',
				'label'          => esc_html__( 'Gutter Width', 'et_builder' ),
				'range_settings' => array(
					'step'      => 1,
					'min'       => 1,
					'max'       => 4,
					'min_limit' => 1,
					'max_limit' => 4,
				),
				'default'        => et_get_option( 'gutter_width', 3 ),
				'mobile_options' => false,
				'validate_unit'  => false,
				'tab_slug'       => 'design',
				'toggle_slug'    => 'spacing',
			),
			'et_pb_light_text_color'              => array(
				'type'        => 'color-alpha',
				'id'          => 'et_pb_light_text_color',
				'label'       => esc_html__( 'Light Text Color', 'et_builder' ),
				'default'     => '#ffffff',
				'tab_slug'    => 'design',
				'toggle_slug' => 'text',
			),
			'et_pb_dark_text_color'               => array(
				'type'        => 'color-alpha',
				'id'          => 'et_pb_dark_text_color',
				'label'       => esc_html__( 'Dark Text Color', 'et_builder' ),
				'default'     => '#666666',
				'tab_slug'    => 'design',
				'toggle_slug' => 'text',
			),
			'et_pb_post_settings_title' => array(
				'type'        => 'text',
				'id'          => 'et_pb_post_settings_title',
				'show_in_bb'  => false,
				'post_field'  => 'post_title',
				'label'       => esc_html__( 'Title', 'et_builder' ),
				'default'     => '',
				'tab_slug'    => 'content',
				'toggle_slug' => 'main_content',
			),
			'et_pb_post_settings_excerpt' => array(
				'type'        => 'textarea',
				'id'          => 'et_pb_post_settings_excerpt',
				'show_in_bb'  => false,
				'post_field'  => 'post_excerpt',
				'label'       => esc_html__( 'Excerpt', 'et_builder' ),
				'default'     => '',
				'tab_slug'    => 'content',
				'toggle_slug' => 'main_content',
			),
			'et_pb_post_settings_image' => array(
				'type'               => 'upload',
				'id'                 => 'et_pb_post_settings_image',
				'show_in_bb'         => false,
				'meta_key'           => '_thumbnail_id',
				// This meta must not be updated during save_post or it will overwrite
				// the value set in the WP edit page....
				'save_post'          => false,
				'label'              => esc_html__( 'Featured Image', 'et_builder' ),
				'embed'              => false,
				'attachment_id'      => true,
				'upload_button_text' => esc_attr__( 'Select', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Set featured image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'tab_slug'           => 'content',
				'toggle_slug'        => 'main_content',
			),
			'et_pb_post_settings_categories' => array(
				'id'                   => 'et_pb_post_settings_categories',
				'show_in_bb'           => false,
				'label'                => esc_html__( 'Categories', 'et_builder' ),
				'type'                 => 'categories',
				'option_category'      => 'basic_option',
				'post_type'            => 'post',
				'taxonomy_name'        => 'category',
				'renderer_options'     => array(
					'use_terms'        => false,
				),
				'tab_slug'             => 'content',
				'toggle_slug'          => 'main_content',
				'depends_on_post_type' => array( 'post' ),
			),
			'et_pb_post_settings_tags' => array(
				'id'                   => 'et_pb_post_settings_tags',
				'show_in_bb'           => false,
				'label'                => esc_html__( 'Tags', 'et_builder' ),
				'type'                 => 'categories',
				'option_category'      => 'basic_option',
				'post_type'            => 'post',
				'taxonomy_name'        => 'post_tag',
				'renderer_options'     => array(
					'use_terms'        => false,
				),
				'tab_slug'             => 'content',
				'toggle_slug'          => 'main_content',
				'depends_on_post_type' => array( 'post' ),
			),
			'et_pb_post_settings_project_categories' => array(
				'id'                   => 'et_pb_post_settings_project_categories',
				'show_in_bb'           => false,
				'label'                => esc_html__( 'Categories', 'et_builder' ),
				'type'                 => 'categories',
				'option_category'      => 'basic_option',
				'post_type'            => 'project',
				'taxonomy_name'        => 'project_category',
				'renderer_options'     => array(
					'use_terms'        => false,
				),
				'tab_slug'             => 'content',
				'toggle_slug'          => 'main_content',
				'depends_on_post_type' => array( 'project' ),
			),
			'et_pb_post_settings_project_tags' => array(
				'id'                   => 'et_pb_post_settings_project_tags',
				'show_in_bb'           => false,
				'label'                => esc_html__( 'Tags', 'et_builder' ),
				'type'                 => 'categories',
				'option_category'      => 'basic_option',
				'post_type'            => 'project',
				'taxonomy_name'        => 'project_tag',
				'renderer_options'     => array(
					'use_terms'        => false,
				),
				'tab_slug'             => 'content',
				'toggle_slug'          => 'main_content',
				'depends_on_post_type' => array( 'project' ),
			),
			'et_pb_content_area_background_color' => array(
				'type'        => 'color-alpha',
				'id'          => 'et_pb_content_area_background_color',
				'label'       => esc_html__( 'Content Area Background Color', 'et_builder' ),
				'default'     => 'rgba(255,255,255,0)',
				'tab_slug'    => 'content',
				'toggle_slug' => 'background',
				'depends_on_post_type' => array( 'page' ),
			),
			'et_pb_section_background_color'      => array(
				'type'        => 'color-alpha',
				'id'          => 'et_pb_section_background_color',
				'label'       => esc_html__( 'Section Background Color', 'et_builder' ),
				'default'     => '#ffffff',
				'tab_slug'    => 'content',
				'toggle_slug' => 'background',
			),
			'et_pb_static_css_file'               => self::_get_static_css_generation_field( 'page' ),
		) );

		return $fields;
	}

	/**
	 * Get page setting fields' meta_key map. Most page settings' field meta key is identical to
	 * its field['id'] but some fields use different meta_key. Map might need in some situations
	 *
	 * @since 3.20
	 *
	 * @param bool $meta_key_to_id reverse mapping if set to false
	 *
	 * @return array
	 */
	public static function get_page_setting_meta_key_map( $meta_key_to_id = true ) {
		static $map = array();

		// Less likely to change, populate it once will be sufficient
		if ( empty( $map ) ) {
			foreach ( self::_get_page_settings_fields() as $field_id => $field ) {
				if ( isset( $field['meta_key'] ) ) {
					// The map can be reversed if needed
					if ( $meta_key_to_id ) {
						$map[ $field['meta_key'] ] = $field_id;
					} else {
						$map[ $field_id ] = $field['meta_key'];
					}
				}
			}
		}

		return $map;
	}

	protected static function _get_page_settings_values( $post_id ) {
		$post_id = $post_id ? $post_id : get_the_ID();

		if ( ! empty( self::$_PAGE_SETTINGS_VALUES[ $post_id ] ) ) {
			return self::$_PAGE_SETTINGS_VALUES[ $post_id ];
		}

		$overflow = et_pb_overflow();
		$OVERFLOW_DEFAULT = ET_Builder_Module_Helper_Overflow::OVERFLOW_DEFAULT;
		$is_default = array();

		// Page settings fields
		$fields = self::$_PAGE_SETTINGS_FIELDS;

		// Defaults
		$default_bounce_rate_limit = 5;

		// Get values
		$ab_bounce_rate_limit       = get_post_meta( $post_id, '_et_pb_ab_bounce_rate_limit', true );
		$et_pb_ab_bounce_rate_limit = '' !== $ab_bounce_rate_limit ? $ab_bounce_rate_limit : $default_bounce_rate_limit;
		$is_default[]               = $et_pb_ab_bounce_rate_limit === $default_bounce_rate_limit ? 'et_pb_ab_bounce_rate_limit' : '';

		$color_palette              = get_post_meta( $post_id, '_et_pb_color_palette', true );
		$default                    = $fields['et_pb_color_palette']['default'];
		$et_pb_color_palette        = '' !== $color_palette ? $color_palette : $default;
		$is_default[]               = $et_pb_color_palette === $default ? 'et_pb_color_palette' : '';

		$gutter_width               = get_post_meta( $post_id, '_et_pb_gutter_width', true );
		$default                    = $fields['et_pb_page_gutter_width']['default'];
		$et_pb_page_gutter_width    = '' !== $gutter_width ? $gutter_width : $default;
		$is_default[]               = $et_pb_page_gutter_width === $default ? 'et_pb_page_gutter_width' : '';

		$light_text_color           = get_post_meta( $post_id, '_et_pb_light_text_color', true );
		$default                    = $fields['et_pb_light_text_color']['default'];
		$et_pb_light_text_color     = '' !== $light_text_color ? $light_text_color : $default;
		$is_default[]               = strtolower( $et_pb_light_text_color ) === $default ? 'et_pb_light_text_color' : '';

		$dark_text_color            = get_post_meta( $post_id, '_et_pb_dark_text_color', true );
		$default                    = $fields['et_pb_dark_text_color']['default'];
		$et_pb_dark_text_color      = '' !== $dark_text_color ? $dark_text_color : $default;
		$is_default[]               = strtolower( $et_pb_dark_text_color ) === $default ? 'et_pb_dark_text_color' : '';

		$content_area_background_color       = get_post_meta( $post_id, '_et_pb_content_area_background_color', true );
		$default                             = $fields['et_pb_content_area_background_color']['default'];
		$et_pb_content_area_background_color = '' !== $content_area_background_color ? $content_area_background_color : $default;
		$is_default[]                        = strtolower( $et_pb_content_area_background_color ) === $default ? 'et_pb_content_area_background_color' : '';

		$section_background_color            = get_post_meta( $post_id, '_et_pb_section_background_color', true );

		$default                             = $fields['et_pb_section_background_color']['default'];
		$et_pb_section_background_color      = '' !== $section_background_color ? $section_background_color : $default;
		$is_default[]                        = strtolower( $et_pb_section_background_color ) === $default ? 'et_pb_section_background_color' : '';

		$overflow_x                          = (string) get_post_meta( $post_id, $overflow->get_field_x( '_et_pb_' ), true );
		$is_default[]                        = empty( $overflow_x ) || $overflow_x == $OVERFLOW_DEFAULT ? $overflow->get_field_x( 'et_pb_' ) : '';

		$overflow_y                          = (string) get_post_meta( $post_id, $overflow->get_field_y( '_et_pb_' ), true );
		$is_default[]                        = empty( $overflow_y ) || $overflow_y == $OVERFLOW_DEFAULT ? $overflow->get_field_y( 'et_pb_' ) : '';

		$static_css_file       = get_post_meta( $post_id, '_et_pb_static_css_file', true );
		$default               = $fields['et_pb_static_css_file']['default'];
		$et_pb_static_css_file = '' !== $static_css_file ? $static_css_file : $default;
		$is_default[]          = $et_pb_static_css_file === $default ? 'et_pb_static_css_file' : '';

		self::$_PAGE_SETTINGS_IS_DEFAULT = $is_default;

		$post = get_post( $post_id );
		$values = array(
			'et_pb_enable_ab_testing'                => et_is_ab_testing_active() ? 'on' : 'off',
			'et_pb_ab_bounce_rate_limit'             => $et_pb_ab_bounce_rate_limit,
			'et_pb_ab_stats_refresh_interval'        => et_pb_ab_get_refresh_interval( $post_id ),
			'et_pb_ab_subjects'                      => et_pb_ab_get_subjects( $post_id ),
			'et_pb_enable_shortcode_tracking'        => get_post_meta( $post_id, '_et_pb_enable_shortcode_tracking', true ),
			'et_pb_ab_current_shortcode'             => '[et_pb_split_track id="' . $post_id . '" /]',
			'et_pb_custom_css'                       => get_post_meta( $post_id, '_et_pb_custom_css', true ),
			'et_pb_color_palette'                    => $et_pb_color_palette,
			'et_pb_page_gutter_width'                => $et_pb_page_gutter_width,
			'et_pb_light_text_color'                 => strtolower( $et_pb_light_text_color ),
			'et_pb_dark_text_color'                  => strtolower( $et_pb_dark_text_color ),
			'et_pb_content_area_background_color'    => strtolower( $et_pb_content_area_background_color ),
			'et_pb_section_background_color'         => strtolower( $et_pb_section_background_color ),
			'et_pb_static_css_file'                  => $et_pb_static_css_file,
			'et_pb_post_settings_title'              => $post ? $post->post_title : '',
			'et_pb_post_settings_excerpt'            => $post ? $post->post_excerpt : '',
			'et_pb_post_settings_image'              => get_post_thumbnail_id( $post_id ),
			'et_pb_post_settings_categories'         => self::_get_object_terms( $post_id, 'category' ),
			'et_pb_post_settings_tags'               => self::_get_object_terms( $post_id, 'post_tag' ),
			'et_pb_post_settings_project_categories' => self::_get_object_terms( $post_id, 'project_category' ),
			'et_pb_post_settings_project_tags'       => self::_get_object_terms( $post_id, 'project_tag' ),
			et_pb_overflow()->get_field_x( 'et_pb_' ) => $overflow_x,
			et_pb_overflow()->get_field_y( 'et_pb_' ) => $overflow_y,
		);
		/**
		 * Filters Divi Builder page settings values.
		 *
		 * @since 3.0.45
		 *
		 * @param mixed[] $builder_settings {
		 *     Builder Settings Values
		 *
		 *     @type string $setting_name Setting value.
		 *     ...
		 * }
		 * @param string|int $post_id
		 */
		$values = self::$_PAGE_SETTINGS_VALUES[ $post_id ] = apply_filters( 'et_builder_page_settings_values', $values, $post_id );

		/**
		 * Filters the Divi Builder's page settings values.
		 *
		 * @deprecated {@see 'et_builder_page_settings_values'}
		 *
		 * @since      2.7.0
		 * @since      3.0.45 Deprecation.
		 */
		return apply_filters( 'et_pb_get_builder_settings_values', $values, $post_id );
	}

	protected static function _get_static_css_generation_field( $scope ) {
		$description = array(
			'page'    => esc_html__( "When this option is enabled, the builder's inline CSS styles for this page will be cached and served as a static file. Enabling this option can help improve performance.", 'et_builder' ),
			'builder' => esc_html__( "When this option is enabled, the builder's inline CSS styles for all pages will be cached and served as static files. Enabling this option can help improve performance.", 'et_builder' ),
		);

		return array(
			'type'            => 'yes_no_button',
			'id'              => 'et_pb_static_css_file',
			'index'           => -1,
			'label'           => esc_html__( 'Static CSS File Generation', 'et_builder' ),
			'description'     => $description[ $scope ],
			'options'         => array(
				'on'  => __( 'On', 'et_builder' ),
				'off' => __( 'Off', 'et_builder' ),
			),
			'default'         => 'on',
			'validation_type' => 'simple_text',
			'after'           => array(
				'type'             => 'button',
				'link'             => '#',
				'class'            => 'et_builder_clear_static_css',
				'title'            => esc_html_x( 'Clear', 'clear static css files', 'et_builder' ),
				'authorize'        => false,
				'is_after_element' => true,
			),
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'performance',
		);
	}

	protected static function _get_post_type_options_defaults() {
		$post_types = et_builder_get_enabled_builder_post_types();
		$post_type_options = array();

		foreach ( $post_types as $post_type ) {
			$post_type_options[ $post_type ] = 'on';
		}

		return $post_type_options;
	}

	/**
	 * Returns all taxonomy terms for a given post.
	 *
	 * @param int $post_id Post ID.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return string
	 */
	protected static function _get_object_terms( $post_id, $taxonomy ) {
		$terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
		return is_array( $terms ) ? implode( ',', $terms ) : '';
	}

	public static function get_registered_post_type_options() {
		return et_get_registered_post_type_options( 'ET_Builder_Settings::sort_post_types' );
	}

	public static function sort_post_types( $a, $b ) {
		// ASCII has a total of 127 characters, so 500 as the interval
		// should be a sufficiently high number.
		$rank_priority = array( 'page' => 1500, 'post' => 1000, 'project' => 500 );
		$a_rank = isset( $rank_priority[ $a->name ] ) ? $rank_priority[ $a->name ] : 0;
		$b_rank = isset( $rank_priority[ $b->name ] ) ? $rank_priority[ $b->name ] : 0;

		return strcasecmp( $a->label, $b->label ) - $a_rank + $b_rank;
	}

	protected function _initialize() {
		/**
		 * Filters Divi Builder settings field definitions.
		 *
		 * @since 3.0.45
		 */
		self::$_BUILDER_SETTINGS_FIELDS = apply_filters( 'et_builder_settings_definitions', self::_get_builder_settings_fields() );

		/**
		 * Filters Divi Builder settings values.
		 *
		 * @since 3.0.45
		 *
		 * @param mixed[] $builder_settings {
		 *     Builder Settings Values
		 *
		 *     @type string $setting_name Setting value.
		 *     ...
		 * }
		 */
		self::$_BUILDER_SETTINGS_VALUES = apply_filters( 'et_builder_settings_values', self::_get_builder_settings_values() );

		/**
		 * Filters Divi Builder page settings field definitions.
		 *
		 * @since 3.0.45
		 */
		self::$_PAGE_SETTINGS_FIELDS = apply_filters( 'et_builder_page_settings_definitions', self::_get_page_settings_fields() );

		/**
		 * Filters Divi Builder page settings field definitions.
		 *
		 * @deprecated {@see 'et_builder_page_settings_definitions'}
		 *
		 * @since      2.7.0
		 * @since      3.0.45 Deprecation.
		 */
		self::$_PAGE_SETTINGS_FIELDS = apply_filters( 'et_pb_get_builder_settings_configurations', self::$_PAGE_SETTINGS_FIELDS );

		self::$_PAGE_SETTINGS_VALUES = array();
	}

	protected static function _maybe_clear_cached_static_css_files( $setting, $setting_value ) {
		if ( in_array( $setting, array( 'et_pb_css_in_footer', 'et_pb_static_css_file' ) ) ) {
			 ET_Core_PageResource::remove_static_resources( 'all', 'all' );
		}
	}

	protected function _register_callbacks() {
		$class = get_class( $this );

		if ( ! is_admin() ) {
			// Setup post meta callback registration on preview page. Priority has to be less than 10
			// so get_post_meta used on self::_get_page_settings_values() are affected
			add_action( 'wp', array( $this, '_register_preview_post_metadata' ), 5 );

			return;
		}

		add_action( 'et_builder_settings_update_option', array( $class, 'update_option_cb'), 10, 3 );

		// setup plugin style options, rather than epanel
		if ( et_is_builder_plugin_active() ) {
			add_filter( 'et_builder_plugin_dashboard_sections', array( $class, 'add_plugin_dashboard_sections' ) );
			add_filter( 'et_builder_plugin_dashboard_fields_data', array( $class, 'add_plugin_dashboard_fields_data' ) );
			add_action( 'et_pb_builder_after_save_options', array( $class, 'plugin_dashboard_option_saved_cb' ), 10, 4 );
			add_action( 'et_pb_builder_option_value', array( $class, 'plugin_dashboard_option_value_cb' ), 10, 2 );
		} else {
			add_filter( 'et_epanel_tab_names', array( $class, 'add_epanel_tab' ) );
			add_filter( 'et_epanel_layout_data', array( $class, 'add_epanel_tab_content' ) );
			add_action( 'et_epanel_update_option', array( $class, 'update_option_cb' ), 10, 3 );
		}
	}

	/**
	 * Adds a tab for the builder to ePanel's tabs array.
	 * {@see 'et_epanel_tab_names'}
	 *
	 * @param string[] $tabs
	 *
	 * @return string[] $tabs
	 */
	public static function add_epanel_tab( $tabs ) {
		$builder_tab = esc_html_x( 'Builder', 'Divi Builder', 'et_builder' );
		$keys        = array_keys( $tabs );
		$values      = array_values( $tabs );

		array_splice( $keys, 2, 0, 'builder' );
		array_splice( $values, 2, 0, $builder_tab );

		return array_combine( $keys, $values );
	}

	/**
	 * Adds builder settings fields data to the builder plugin's options dashboard.
	 * {@see 'et_builder_plugin_dashboard_fields_data'}
	 *
	 * @param array[] $dashboard_data
	 *
	 * @return array[] $dashboard_data
	 */
	public static function add_plugin_dashboard_fields_data( $dashboard_data ) {
		$tabs    = self::get_tabs( 'builder' );
		$fields  = self::get_fields( 'builder' );
		$toggles = self::get_toggles();

		foreach ( $tabs as $tab_slug => $tab_name ) {
			$section                    = $tab_slug . '_main_options';

			if ( ! isset( $dashboard_data[ $section ] ) ) {
				$dashboard_data[ $section ] = array();
			}

			$dashboard_data[ $section ][] = array( 'type' => 'main_title', 'title' => '' );

			foreach ( $toggles as $toggle_slug => $toggle ) {
				$section_started = false;

				foreach ( $fields as $field_slug => $field_info ) {
					if ( $tab_slug !== $field_info['tab_slug'] || $toggle_slug !== $field_info['toggle_slug'] ) {
						continue;
					}

					if ( 'et_pb_css_in_footer' === $field_info['id'] ) {
						continue;
					}

					if ( ! $section_started ) {
						$dashboard_data[ $section ][] = array( 'type'  => 'section_start', 'title' => $toggles[ $toggle_slug ] );
						$section_started              = true;
					}

					$field_info['hint_text'] = $field_info['description'];
					$field_info['name']      = $field_info['id'];
					$field_info['title']     = $field_info['label'];

					$dashboard_data[ $section ][] = $field_info;

					if ( isset( $field_info['after'] ) ) {
						$dashboard_data[ $section ][] = $field_info['after'];
					}
				}

				if ( $section_started ) {
					$dashboard_data[ $section ][] = array( 'type' => 'section_end' );
				}
			}
		}

		return $dashboard_data;
	}

	/**
	 * Adds tabs for builder settings to the builder plugin's options dashboard.
	 * {@see 'et_builder_plugin_dashboard_sections'}
	 *
	 * @param array[] $sections
	 *
	 * @return array[] $sections
	 */
	public static function add_plugin_dashboard_sections( $sections ) {
		$tabs = self::get_tabs( 'builder' );

		foreach ( $tabs as $tab_slug => $tab_name ) {
			$sections[ $tab_slug ] = array(
				'title'    => et_core_esc_previously( $tab_name ),
				'contents' => array(
					'main' => esc_html__( 'Main', 'et_builder' ),
				),
			);
		}

		return $sections;
	}

	/**
	 * Adds builder settings to ePanel. {@see 'et_epanel_layout_data'}
	 *
	 * @param array $layout_data
	 *
	 * @return array $data
	 */
	public static function add_epanel_tab_content( $layout_data ) {
		$result = array();
		$done   = false;

		foreach ( $layout_data as $data ) {
			$result[] = $data;

			if ( $done || ! isset( $data['name'], $data['type'] ) ) {
				continue;
			}

			if ( 'wrap-navigation' === $data['name'] && 'contenttab-wrapend' === $data['type'] ) {
				$builder_options = self::_get_builder_settings_in_epanel_format();
				$result          = array_merge( $result, $builder_options );
				$done            = true;
			}
		}

		return $result;
	}

	public static function update_option_cb( $setting, $setting_value, $post_id = 'global' ) {
		self::_maybe_clear_cached_static_css_files( $setting, $setting_value );
	}

	/**
	 * Returns builder settings fields data for the provided settings scope.
	 *
	 * @param string $scope Get settings fields for scope (page|builder|all). Default 'page'.
	 *
	 * @return array[] See {@link ET_Builder_Element::get_fields()} for structure.
	 */
	public static function get_fields( $scope = 'page' ) {
		$fields = array();

		if ( 'builder' === $scope ) {
			$fields = self::$_BUILDER_SETTINGS_FIELDS;
		} else if ( 'page' === $scope ) {
			$fields = self::$_PAGE_SETTINGS_FIELDS;
		}

		return $fields;
	}

	/**
	 * @return ET_Builder_Settings
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new ET_Builder_Settings();
		}

		return self::$_instance;
	}

	/**
	 * Returns the localized tab names for the builder settings.
	 *
	 * @param string $scope
	 *
	 * @return string[] {
	 *     Localized Tab Names.
	 *
	 *     @type string $tab_slug Tab name
	 *     ...
	 * }
	 */
	public static function get_tabs( $scope = 'page' ) {
		$result   = array();
		$advanced = esc_html_x( 'Advanced', 'Design Settings', 'et_builder' );
		$post_type_integration = esc_html_x( 'Post Type Integration', 'Builder Settings', 'et_builder' );

		if ( 'page' === $scope ) {
			$result = array(
				'content'  => esc_html_x( 'Content', 'Content Settings', 'et_builder' ),
				'design'   => esc_html_x( 'Design', 'Design Settings', 'et_builder' ),
				'advanced' => $advanced,
			);
		} else if ( 'builder' === $scope ) {
			$result = array(
				'post_type_integration' => $post_type_integration,
				'advanced' => $advanced,
			);
		}

		/**
		 * Filters the builder's settings tabs.
		 *
		 * @since 3.0.45
		 *
		 * @param string[] $tabs {
		 *     Localized Tab Names.
		 *
		 *     @type string $tab_slug Tab name
		 *     ...
		 * }
		 * @param string   $scope Accepts 'page', 'builder'.
		 */
		return apply_filters( 'et_builder_settings_tabs', $result, $scope );
	}

	/**
	 * Returns the localized title of the builder page settings modal.
	 *
	 * @return string
	 */
	public static function get_title() {
		global $post;

		$post_type     = isset( $post->post_type ) ? $post->post_type : 'page';
		$post_type_obj = get_post_type_object( $post_type );
		$settings      = esc_html_x( '%s Settings', 'Page, Post, Product, etc.', 'et_builder' );

		/**
		 * Filters the title of the builder's page settings modal.
		 *
		 * @since 3.0.45
		 *
		 * @param string $title
		 */
		return apply_filters( 'et_builder_page_settings_modal_title', sprintf( $settings, $post_type_obj->labels->singular_name ) );
	}

	/**
	 * Returns the localized toggle/group names for the builder page settings modal.
	 *
	 * @return string[] {
	 *     Localized Toggle Names
	 *
	 *     @type string $toggle_slug Toggle name
	 * }
	 */
	public static function get_toggles() {
		$utils = ET_Core_Data_Utils::instance();

		// Get current post type singular name and use it as toggle title.
		$post_type = wp_doing_ajax() ? $utils->array_get( $_POST, 'et_post_type' ) : get_post_type( et_core_page_resource_get_the_ID() );

		$post_type_obj = get_post_type_object( $post_type );

		$toggles = array(
			'main_content'          => ! empty( $post_type_obj ) ? $post_type_obj->labels->singular_name : '',
			'background'            => esc_html__( 'Background', 'et_builder' ),
			'color_palette'         => esc_html__( 'Color Palette', 'et_builder' ),
			'custom_css'            => esc_html__( 'Custom CSS', 'et_builder' ),
			'enable_bfb'            => esc_html__( 'Enable The Latest Divi Builder Experience', 'et_builder' ),
			'enable_classic_editor' => esc_html__( 'Enable Classic Editor', 'et_builder' ),
			'performance'           => esc_html__( 'Performance', 'et_builder' ),
			'product_tour'          => esc_html__( 'Product Tour', 'et_builder' ),
			'spacing'               => esc_html__( 'Spacing', 'et_builder' ),
			'ab_testing'            => esc_html__( 'Split Testing', 'et_builder' ),
			'text'                  => esc_html__( 'Text', 'et_builder' ),
			'visibility'            => esc_html__( 'Visibility', 'et_builder' ),
		);

		/**
		 * Filters the builder page settings modal's option group toggles.
		 *
		 * @since 3.0.45
		 *
		 * @param string[] $toggles {
		 *     Localized Toggle Names
		 *
		 *     @type string $toggle_slug Toggle name
		 *     ...
		 * }
		 */
		return apply_filters( 'et_builder_page_settings_modal_toggles', $toggles );
	}

	/**
	 * Returns the values of builder settings for the provided settings scope.
	 *
	 * @param string     $scope   Get values for scope (page|builder|all). Default 'page'.
	 * @param string|int $post_id Optional. If not provided, {@link get_the_ID()} will be used.
	 *
	 * @return mixed[] {
	 *     Settings Values
	 *
	 *     @type mixed $setting_key The value for the setting.
	 *     ...
	 * }
	 */
	public static function get_values( $scope = 'page', $post_id = null, $exclude_defaults = false ) {
		$result = array();

		if ( 'builder' === $scope ) {
			$result = self::$_BUILDER_SETTINGS_VALUES;
		} else if ( 'page' === $scope ) {
			$result = self::_get_page_settings_values( $post_id );
		} else if ( 'all' === $scope ) {
			$result = array (
				'page'    => self::_get_page_settings_values( $post_id ),
				'builder' => self::$_BUILDER_SETTINGS_VALUES,
			);
		}

		if ( $exclude_defaults ) {
			'all' === $scope || $result = array( $result );

			foreach ( $result as $key => $settings ) {
				$result[ $key ] = array_diff_key( $result[ $key ], array_flip( self::$_PAGE_SETTINGS_IS_DEFAULT ) );
			}

			'all' === $scope || $result = $result[0];
		}

		return $result;
	}

	public static function plugin_dashboard_option_saved_cb( $processed_options, $option_name, $field_info, $output ) {
		if ( ! isset( $field_info['id'] ) ) {
			return;
		}

		$setting       = $field_info['id'];
		$setting_value = $processed_options[ $option_name ];

		if ( ! isset( self::$_BUILDER_SETTINGS_FIELDS[ $setting ] ) ) {
			return;
		}

		et_update_option( $setting, $setting_value );

		self::_maybe_clear_cached_static_css_files( $setting, $setting_value );
	}

	public static function plugin_dashboard_option_value_cb( $option_value, $option ) {
		if ( ! isset( $option['id'] ) ) {
			return $option_value;
		}

		$setting = $option['id'];

		if ( ! isset( self::$_BUILDER_SETTINGS_VALUES[ $setting ] ) ) {
			return $option_value;
		}

		return self::$_BUILDER_SETTINGS_VALUES[ $setting ];
	}

	/**
	 * Register filter callback for modifying page settings post meta value based on current
	 * autosave data if current page is valid builder preview page
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public static function _register_preview_post_metadata() {
		if ( ! is_user_logged_in() || ! is_preview() || ! et_pb_is_pagebuilder_used() ) {
			return;
		}

		// Populate page settings fields meta_key map. Most page setting field id is identical (sans
		// `_` prefix) to its meta_key name but some field has completely different meta_key name
		foreach ( self::$_PAGE_SETTINGS_FIELDS as $field_id => $field ) {
			$meta_key = isset( $field['meta_key'] ) ? $field['meta_key'] : '_' . $field_id;

			self::$_PAGE_SETTINGS_FIELDS_META_KEY_MAP[ $meta_key ] = $field_id;
		}

		// Register filter for modifying page setting's post_meta value
		add_filter( 'get_post_metadata', array( 'ET_Builder_Settings', 'modify_preview_post_metadata' ), 10, 4 );
	}

	/**
	 * Get page settings' post meta value in preview page. This method should only be called on
	 * preview page only
	 *
	 * @since 3.20
	 *
	 * @return array
	 */
	public static function get_preview_post_metadata() {
		static $preview_post_metadata = null;

		// Value retrieval should only be done once
		if ( is_null( $preview_post_metadata ) ) {
			// Get autosave data of current post of current user
			$current_user_id    = get_current_user_id();
			$preview_post_metadata = get_post_meta(
				get_the_ID(),
				"_et_builder_settings_autosave_{$current_user_id}",
				true
			);

			// Returned value should be array
			if ( ! is_array( $preview_post_metadata ) ) {
				$preview_post_metadata = array();
			}
		}

		return $preview_post_metadata;
	}

	/**
	 * Modify page settings' post meta value in preview page. This should only be hooked after
	 * checking whether current page is valid preview page or not
	 *
	 * @see get_metadata()
	 *
	 * @since 3.20
	 *
	 * @param null|array|string $value
	 * @param int               $object_id
	 * @param string            $meta_key
	 * @param bool              $single
	 *
	 * @return null|array|string
	 */
	public static function modify_preview_post_metadata( $value, $object_id, $meta_key, $single ) {
		$current_user_id = get_current_user_id();

		// Bail if $meta_key value is equal to meta_key value used to save current page autosave data
		if ( "_et_builder_settings_autosave_{$current_user_id}" === $meta_key ) {
			return $value;
		}

		// Bail if $meta_key is not page settings field's meta key
		if ( ! isset( self::$_PAGE_SETTINGS_FIELDS_META_KEY_MAP[ $meta_key ] ) ) {
			return $value;
		}

		// Bail if current $meta_key value doesn't exist on preview page autosave data
		$preview_post_meta_key = self::$_PAGE_SETTINGS_FIELDS_META_KEY_MAP[ $meta_key ];
		$preview_post_metadata = self::get_preview_post_metadata();

		if ( ! isset( $preview_post_metadata[ $preview_post_meta_key ] ) ) {
			return $value;
		}

		return $preview_post_metadata[ $preview_post_meta_key ];
	}
}


if ( ! function_exists( 'et_builder_settings_init' ) ):
/**
 * Initializes the builder settings class if needed.
 * {@see 'current_screen'}
 *
 * @param WP_Screen $screen Optional. Default `null`.
 */
function et_builder_settings_init( $screen = null ) {
	$init_settings = et_builder_should_load_framework() || wp_doing_ajax();

	if ( ! $init_settings && is_a( $screen, 'WP_Screen' ) ) {
		$init_settings = 1 === preg_match( '/et_\w+_options/', $screen->base );
	}

	if ( $init_settings ) {
		ET_Builder_Settings::get_instance();
	}
}
add_action( 'current_screen', 'et_builder_settings_init' );
endif;


if ( ! function_exists( 'et_builder_settings_get' ) ):
/**
 * Get a builder setting value. Default and global setting values are considered when applicable.
 *
 * @param string     $setting Page setting name.
 * @param string|int $post_id Optional. The post id.
 *
 * @return mixed
 */
function et_builder_settings_get( $setting, $post_id = '' ) {
	$builder_fields = ET_Builder_Settings::get_fields( 'builder' );
	$builder_values = ET_Builder_Settings::get_values( 'builder' );

	$page_fields = ET_Builder_Settings::get_fields();
	$page_values = ET_Builder_Settings::get_values( 'page', $post_id );

	$has_page   = isset( $page_fields[ $setting ] );
	$has_global = isset( $builder_fields[ $setting ] );

	$value = $global_value = '';
	$global_is_default = false;

	if ( ! $has_page && ! $has_global ) {
		return $value;
	}

	if ( $has_global ) {
		$global_value       = $builder_values[ $setting ];
		$global_has_default = isset( $builder_fields[ $setting ]['default'] );
		$global_is_default  = $global_has_default && $global_value === $builder_fields[ $setting ]['default'];
		$value              = $global_value;
	}

	if ( $has_page ) {
		$page_value       = $page_values[ $setting ];
		$page_has_default = isset( $page_fields[ $setting ]['default'] );
		$page_is_default  = $page_has_default && $page_value === $page_fields[ $setting ]['default'];
		$value            = $page_value;
	}

	if ( ! $has_page || ( $page_is_default && ! $global_is_default ) ) {
		$value = $global_value;
	} else if ( ! $has_global || ! $page_is_default ) {
		$value = $page_value;
	}

	return $value;
}
endif;


if ( ! function_exists( 'et_builder_setting_is_off' ) ):
/**
 * Whether or not a builder setting is off. Default and global setting values are
 * considered when applicable.
 *
 * @param string     $setting Page setting name.
 * @param string|int $post_id Optional. The post id.
 *
 * @return bool
 */
function et_builder_setting_is_off( $setting, $post_id = '' ) {
	return 'off' === et_builder_settings_get( $setting, $post_id );
}
endif;


if ( ! function_exists( 'et_builder_setting_is_on' ) ):
/**
 * Whether or not a builder setting is on. Default and global setting values are
 * considered when applicable.
 *
 * @param string     $setting Page setting name.
 * @param string|int $post_id Optional. The post id.
 *
 * @return bool
 */
function et_builder_setting_is_on( $setting, $post_id = '' ) {
	return 'on' === et_builder_settings_get( $setting, $post_id );
}
endif;
