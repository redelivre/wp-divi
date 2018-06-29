<?php

function et_fb_shortcode_tags() {
	global $shortcode_tags;

	$shortcode_tag_names = array();
	foreach ( $shortcode_tags as $shortcode_tag_name => $shortcode_tag_cb ) {
		$shortcode_tag_names[] = $shortcode_tag_name;
	}
	return implode( '|', $shortcode_tag_names );
}

function et_fb_prepare_library_cats() {
	$raw_categories_array = apply_filters( 'et_pb_new_layout_cats_array', get_terms( 'layout_category', array( 'hide_empty' => false ) ) );
	$clean_categories_array = array();

	if ( is_array( $raw_categories_array ) && ! empty( $raw_categories_array ) ) {
		foreach( $raw_categories_array as $category ) {
			$clean_categories_array[] = array(
				'name' => html_entity_decode( $category->name ),
				'id' => $category->term_id,
				'slug' => $category->slug,
			);
		}
	}

	return $clean_categories_array;
}

function et_fb_get_layout_type( $post_id ) {
	return et_fb_get_layout_term_slug( $post_id, 'layout_type' );
}

function et_fb_get_layout_term_slug( $post_id, $term_name ) {
	$post_terms  = wp_get_post_terms( $post_id, $term_name );
	$slug = $post_terms[0]->slug;

	return $slug;
}

function et_fb_comments_template() {
	return ET_BUILDER_DIR . 'comments_template.php';
}

function et_fb_modify_comments_request( $params ) {
	// modify the request parameters the way it doesn't change the result just to make request with unique parameters
	$params->query_vars['type__not_in'] = 'et_pb_comments_random_type_9999';
}

function et_fb_comments_submit_button( $submit_button ) {
		return sprintf(
			'<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			esc_attr( 'submit' ),
			esc_attr( 'et_pb_submit' ),
			esc_attr( 'submit et_pb_button' ),
			esc_html_x( 'Submit Comment', 'et_builder' )
		);
}

// comments template cannot be generated via AJAX so prepare it beforehand
function et_fb_get_comments_markup() {
	// Modify the comments request to make sure it's unique.
	// Otherwise WP generates SQL error and doesn't allow multiple comments sections on single page
	add_action( 'pre_get_comments', 'et_fb_modify_comments_request', 1 );

	// include custom comments_template to display the comment section with Divi style
	add_filter( 'comments_template', 'et_fb_comments_template' );

	// Modify submit button to be advanced button style ready
	add_filter( 'comment_form_submit_button', 'et_fb_comments_submit_button' );

	// Custom action before calling comments_template.
	do_action( 'et_fb_before_comments_template' );

	ob_start();
	comments_template( '', true );
	$comments_content = ob_get_contents();
	ob_end_clean();

	// Custom action after calling comments_template.
	do_action( 'et_fb_after_comments_template' );

	// remove all the actions and filters to not break the default comments section from theme
	remove_filter( 'comments_template', 'et_fb_comments_template' );
	remove_action( 'pre_get_comments', 'et_fb_modify_comments_request', 1 );

	return $comments_content;
}

// List of shortcode wrappers that requires adjustment in VB. Plugins which uses fullscreen dimension
// tend to apply negative positioning which looks inappropriate on VB's shortcode mechanism
function et_fb_known_shortcode_wrappers() {
	return apply_filters( 'et_fb_known_shortcode_wrappers', array(
		'removeLeft' => array(
			'.fullscreen-container', // revolution slider,
			'.esg-container-fullscreen-forcer', // essential grid
			'.ls-wp-fullwidth-helper', // layer slider
		),
	) );
}

function et_builder_autosave_interval() {
	return apply_filters( 'et_builder_autosave_interval', et_builder_heartbeat_interval() / 2 );
}

function et_fb_heartbeat_settings($settings) {
	$settings['suspension'] = 'disable';
	$settings['interval'] = et_builder_heartbeat_interval();
	return $settings;
}
add_filter( 'heartbeat_settings', 'et_fb_heartbeat_settings', 11 );

function et_fb_backend_helpers() {
	global $post;

	$utils       = ET_Core_Data_Utils::instance();
	$layout_type = '';
	$layout_scope = '';

	$post_type    = isset( $post->post_type ) ? $post->post_type : false;
	$post_id      = isset( $post->ID ) ? $post->ID : false;
	$post_status  = isset( $post->post_status ) ? $post->post_status : false;
	$post_title   = isset( $post->post_title ) ? esc_attr( $post->post_title ) : false;

	if ( 'et_pb_layout' === $post_type ) {
		$layout_type = et_fb_get_layout_type( $post_id );
		$layout_scope = et_fb_get_layout_term_slug( $post_id, 'scope' );
	}

	$use_google_fonts = et_core_use_google_fonts();
	$websafe_fonts = et_builder_get_websafe_fonts();
	$default_fonts_set = array_merge( array( 'Default' => array() ), $websafe_fonts );
	$google_fonts = $use_google_fonts ? array_merge( $default_fonts_set, et_builder_get_google_fonts() ) : $default_fonts_set;
	$custom_user_fonts = et_builder_get_custom_fonts();
	$current_user = wp_get_current_user();
	$current_url  = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	// disable product tour on the app launch, so it won't be started next time.
	if ( et_builder_is_product_tour_enabled() ) {
		et_fb_disable_product_tour();
	}

	$updates_options = get_site_option( 'et_automatic_updates_options', array() );
	$et_account      = array(
		'et_username' => $utils->array_get( $updates_options, 'username', '' ),
		'et_api_key'  => $utils->array_get( $updates_options, 'api_key', '' ),
		'status'      => get_site_option( 'et_account_status', 'not_active' ),
	);
	/**
	 * Filters modules list.
	 *
	 * @param array $modules_array.
	 */
	$fb_modules_array = apply_filters( 'et_fb_modules_array', ET_Builder_Element::get_modules_array( $post_type, true ) );

	/**
	 * Filters modules list which affect "Add New Row" button position.
	 *
	 * @param array $modules_list.
	 */
	$modules_row_overlapping_add_new = apply_filters( 'et_fb_modules_row_overlapping_add_new', array(
		'et_pb_counters',
		'et_pb_post_nav',
		'et_pb_search',
		'et_pb_social_media_follow',
	) );

	$modules_defaults = array(
		'title'    => _x( 'Your Title Goes Here', 'Modules dummy content', 'et_builder' ),
		'subtitle' => _x( 'Subtitle goes Here', 'et_builder' ),
		'body'     => _x( 'Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.',
			'et_builder' ),
		'number'   => 50,
		'button'   => _x( 'Click Here', 'Modules dummy content', 'et_builder' ),
		'image'    => array(
			'landscape' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA4MCIgaGVpZ2h0PSI1NDAiIHZpZXdCb3g9IjAgMCAxMDgwIDU0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZmlsbD0iI0VCRUJFQiIgZD0iTTAgMGgxMDgwdjU0MEgweiIvPgogICAgICAgIDxwYXRoIGQ9Ik00NDUuNjQ5IDU0MGgtOTguOTk1TDE0NC42NDkgMzM3Ljk5NSAwIDQ4Mi42NDR2LTk4Ljk5NWwxMTYuMzY1LTExNi4zNjVjMTUuNjItMTUuNjIgNDAuOTQ3LTE1LjYyIDU2LjU2OCAwTDQ0NS42NSA1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iLjA1IiBmaWxsPSIjMDAwIiBjeD0iMzMxIiBjeT0iMTQ4IiByPSI3MCIvPgogICAgICAgIDxwYXRoIGQ9Ik0xMDgwIDM3OXYxMTMuMTM3TDcyOC4xNjIgMTQwLjMgMzI4LjQ2MiA1NDBIMjE1LjMyNEw2OTkuODc4IDU1LjQ0NmMxNS42Mi0xNS42MiA0MC45NDgtMTUuNjIgNTYuNTY4IDBMMTA4MCAzNzl6IiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgIDwvZz4KPC9zdmc+Cg==',
			'portrait'  => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDUwMCA1MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxwYXRoIGZpbGw9IiNFQkVCRUIiIGQ9Ik0wIDBoNTAwdjUwMEgweiIvPgogICAgICAgIDxyZWN0IGZpbGwtb3BhY2l0eT0iLjEiIGZpbGw9IiMwMDAiIHg9IjY4IiB5PSIzMDUiIHdpZHRoPSIzNjQiIGhlaWdodD0iNTY4IiByeD0iMTgyIi8+CiAgICAgICAgPGNpcmNsZSBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBjeD0iMjQ5IiBjeT0iMTcyIiByPSIxMDAiLz4KICAgIDwvZz4KPC9zdmc+Cg==',
		),
		'video'    => 'https://www.youtube.com/watch?v=FkQuawiGWUw',
	);

	$helpers = array(
		'debug'                        => defined( 'ET_DEBUG' ) && ET_DEBUG,
		'autosaveInterval'             => et_builder_autosave_interval(),
		'postId'                       => $post_id,
		'postTitle'                    => $post_title,
		'postStatus'                   => $post_status,
		'postType'                     => $post_type,
		'layoutType'                   => $layout_type,
		'layoutScope'                  => $layout_scope,
		'publishCapability'            => ( is_page() && ! current_user_can( 'publish_pages' ) ) || ( ! is_page() && ! current_user_can( 'publish_posts' ) ) ? 'no_publish' : 'publish',
		'shortcodeObject'              => array(),
		'autosaveShortcodeObject'      => array(),
		'ajaxUrl'                      => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
		'tinymceSkinUrl'               => ET_FB_ASSETS_URI . '/vendors/tinymce-skin',
		'tinymceCSSFiles'              => esc_url( includes_url( 'js/tinymce' ) . '/skins/wordpress/wp-content.css' ),
		'images_uri'                   => ET_BUILDER_URI .'/images',
		'componentDefinitions'         => array(
			'generalFields'                => array(),
			'advancedFields'               => array(),
			'customCssFields'              => array(),
			'fieldsDefaults'               => array(),
			'defaults'                     => array(),
			'optionsToggles'               => array(),
			'customTabs'                   => array(),
			'customLayoutsTabs'            => array(),
		),
		'et_account'                   => $et_account,
		'productTourStatus'            => et_builder_is_product_tour_enabled() ? 'on' : 'off',
		'pageCreationOptions'          => array(
			'build_from_scratch'    => array(
				'className'       => 'accent-blue',
				'imgSrc'          => 'scratch.png',
				'imgSrcHover'     => 'scratch.gif',
				'titleText'       => esc_html__( 'Build From Scratch', 'et_builder' ),
				'descriptionText' => esc_html__( 'Build your page from the ground up. Don’t worry, you can access our premade layouts at any time.', 'et_builder' ),
				'buttonText'      => esc_html__( 'Start Building', 'et_builder' ),
				'permission'      => array( 'add_module' ),
			),
			'use_existing_content'  => array(
				'className'       => 'accent-blue',
				'imgSrc'          => 'existing.png',
				'imgSrcHover'     => 'existing.gif',
				'titleText'       => esc_html__( 'Use Existing Content', 'et_builder' ),
				'descriptionText' => esc_html__( 'Use the Divi Builder while retaining your existing page content.', 'et_builder' ),
				'buttonText'      => esc_html__( 'Start Building', 'et_builder' ),
				'permission'      => array( 'edit_module' ),
			),
			'choose_premade_layout' => array(
				'className'       => 'accent-purple',
				'imgSrc'          => 'premade.png',
				'imgSrcHover'     => 'premade.gif',
				'titleText'       => esc_html__( 'Choose a premade Layout' ),
				'descriptionText' => esc_html__( 'Choose from hundreds of world-class premade layouts or start from any of your existing saved layouts.', 'et_builder' ),
				'buttonText'      => esc_html__( 'Browse Layouts', 'et_builder' ),
				'permission'      => array( 'load_layout' ),
			),
			'clone_existing_page'   => array(
				'className'       => 'accent-green',
				'imgSrc'          => 'clone.png',
				'imgSrcHover'     => 'clone.gif',
				'titleText'       => esc_html__( 'Clone Existing page', 'et_builder' ),
				'descriptionText' => esc_html__( 'Jump start your layout design by duplicating another page that you’ve already built.', 'et_builder' ),
				'buttonText'      => esc_html__( 'Choose Page', 'et_builder' ),
				'permission'      => array( 'clone_page' ),
			),
		),
		'dividers'                     => ET_Builder_Module_Fields_Factory::get( 'Divider' )->dividers,
		'moduleParentShortcodes'       => ET_Builder_Element::get_parent_slugs_regex( $post_type ),
		'moduleChildShortcodes'        => ET_Builder_Element::get_child_slugs_regex( $post_type ),
		'moduleChildSlugs'             => ET_Builder_Element::get_child_slugs( $post_type ),
		'moduleRawContentShortcodes'   => ET_Builder_Element::get_raw_content_slugs( $post_type ),
		'modules'                      => $fb_modules_array,
		'modulesCount'                 => count( $fb_modules_array ),
		'modulesWithChildren'          => ET_Builder_Element::get_slugs_with_children( $post_type ),
		'modulesShowOnCancelDropClassname' => apply_filters( 'et_fb_modules_show_on_cancel_drop_classname', array( 'et_pb_gallery', 'et_pb_filterable_portfolio') ),
		'modulesFeaturedImageBackground' => ET_Builder_Element::get_featured_image_background_modules( $post_type ),
		'modulesRowOverlappingAddNew'  => $modules_row_overlapping_add_new,
		'structureModules'             => ET_Builder_Element::get_structure_modules(),
		'et_builder_css_media_queries' => ET_Builder_Element::get_media_quries( 'for_js' ),
		'builderOptions'               => et_builder_options(),
		'builderVersion'               => ET_BUILDER_PRODUCT_VERSION,
		'commentsModuleMarkup'         => et_fb_get_comments_markup(),
		'shortcode_tags'               => et_fb_shortcode_tags(),
		'failureNotification'          => et_builder_get_failure_notification_modal(),
		'exitNotification'             => et_builder_get_exit_notification_modal(),
		'browserAutosaveNotification'  => et_builder_get_browser_autosave_notification_modal(),
		'serverAutosaveNotification'   => et_builder_get_server_autosave_notification_modal(),
		'unsavedNotification'          => et_builder_get_unsaved_notification_modal(),
		'backupLabel'                  => __( 'Backup of %s', 'et_builder' ),

		/**
		 * Filters taxonomies array.
		 *
		 * @param array Array of all registered taxonomies.
		 */
		'getTaxonomies'                => apply_filters( 'et_fb_taxonomies', et_fb_get_taxonomy_terms() ),

		/**
		 * Filters taxonomy labels.
		 *
		 * @param array Array of labels for all registered taxonomies.
		 */
		'getTaxonomyLabels'            => apply_filters( 'et_fb_taxonomy_labels', et_fb_get_taxonomy_labels() ),
		'googleAPIKey'                 => et_pb_is_allowed( 'theme_options' ) ? get_option( 'et_google_api_settings' ) : '',
		'useGoogleFonts'               => $use_google_fonts,
		'googleFontsList'              => array_keys( $google_fonts ),
		'googleFonts'                  => $google_fonts,
		'websafeFonts'                 => $websafe_fonts,
		'customFonts'                  => $custom_user_fonts,
		'removedFonts'                 => et_builder_old_fonts_mapping(),
		'allFontWeights'               => et_builder_get_font_weight_list(),
		'allFontFormats'               => et_pb_get_supported_font_formats(),
		'gutterWidth'                  => et_get_option( 'gutter_width', 3 ),
		'sectionPadding'               => et_get_option( 'section_padding', 4 ),
		'fontIcons'                    => et_pb_get_font_icon_symbols(),
		'fontIconsDown'                => et_pb_get_font_down_icon_symbols(),
		'widgetAreas'                  => et_builder_get_widget_areas_list(),
		'site_url'                     => get_site_url(),
		'cookie_path'                  => SITECOOKIEPATH,
		'blog_id'                      => get_current_blog_id(),
		'etBuilderAccentColor'         => et_builder_accent_color(),
		'gmt_offset_string'            => et_pb_get_gmt_offset_string(),
		'et_builder_fonts_data'        => et_builder_get_fonts(),
		'currentUserDisplayName'       => $current_user->display_name,
		'locale'                       => get_locale(),
		'roleSettings'                 => et_pb_get_role_settings(),
		'optionsCategoriesPermissions' => array_keys( ET_Builder_Element::get_options_categories() ),
		'currentRole'                  => et_pb_get_current_user_role(),
		'exportUrl'                    => et_fb_get_portability_export_url(),
		'urls'                         => array(
			'loginFormUrl'             => esc_url( site_url( 'wp-login.php', 'login_post' ) ),
			'forgotPasswordUrl'        => esc_url( wp_lostpassword_url() ),
			'logoutUrl'                => esc_url( wp_logout_url() ),
			'logoutUrlRedirect'        => esc_url( wp_logout_url( $current_url ) ),
			'themeOptionsUrl'          => esc_url( et_pb_get_options_page_link() ),
			'builderPreviewStyle'      => ET_BUILDER_URI . '/styles/preview.css',
		),
		'nonces'                       => et_fb_get_nonces(),
		'conditionalTags'              => et_fb_conditional_tag_params(),
		'currentPage'                  => et_fb_current_page_params(),
		'appPreferences'               => et_fb_app_preferences(),
		'classNames'                   => array(
			'hide_on_mobile_class'     => 'et-hide-mobile',
		),
		'columnLayouts'                => et_builder_get_columns(),
		'pageSettingsFields'           => ET_Builder_Settings::get_fields(),
		'pageSettingsValues'           => ET_Builder_Settings::get_values(),
		'abTestingSubjects'            => false !== ( $all_subjects_raw = get_post_meta( $post_id, '_et_pb_ab_subjects' , true ) ) ? explode( ',', $all_subjects_raw ) : array(),
		'searchFilterItems'            => array(
			'show_only' => array(
				'styles_modified'   => esc_html__( 'Modified Styles', 'et_builder' ),
				'styles_responsive' => esc_html__( 'Responsive Styles', 'et_builder' ),
				// 'styles_hover'      => esc_html__( 'Hover Styles', 'et_builder' ), // TODO Uncomment this once the hover options feature has been released.
				'active_content'    => esc_html__( 'Active Content', 'et_builder' ),
			),
		),
		'defaults'                     => array(
			'et_pb_accordion_item'    => array(
				'title'   => $modules_defaults['title'],
				'content' => $modules_defaults['body'],
			),
			'et_pb_audio'             => array(
				'title'       => $modules_defaults['title'],
				'artist_name' => _x( 'Artist Name', 'Modules dummy content', 'et_builder' ),
			),
			'et_pb_counter'           => array(
				'content' => $modules_defaults['title'],
				'percent' => $modules_defaults['number'],
			),
			'et_pb_blurb'             => array(
				'title'   => $modules_defaults['title'],
				'content' => $modules_defaults['body'],
				'image'   => $modules_defaults['image']['landscape'],
			),
			'et_pb_button'            => array(
				'button_text' => $modules_defaults['button'],
			),
			'et_pb_cta'               => array(
				'title'       => $modules_defaults['title'],
				'content'     => $modules_defaults['body'],
				'button_text' => $modules_defaults['button'],
			),
			'et_pb_circle_counter'    => array(
				'title'  => $modules_defaults['title'],
				'number' => $modules_defaults['number'],
			),
			'et_pb_signup'            => array(
				'title'       => $modules_defaults['title'],
				'description' => $modules_defaults['body'],
			),
			'et_pb_image'             => array(
				'src' => $modules_defaults['image']['landscape'],
			),
			'et_pb_fullwidth_image'   => array(
				'src' => $modules_defaults['image']['landscape'],
			),
			'et_pb_login'             => array(
				'title'   => $modules_defaults['title'],
				'content' => $modules_defaults['body'],
			),
			'et_pb_number_counter'    => array(
				'title'  => $modules_defaults['title'],
				'number' => $modules_defaults['number'],
			),
			'et_pb_team_member'       => array(
				'name'      => _x( 'Name Goes Here', 'Modules dummy content', 'et_builder' ),
				'content'   => $modules_defaults['body'],
				'image_url' => $modules_defaults['image']['portrait'],
				'position'  => _x( 'Position', 'Modules dummy content', 'et_builder' ),
			),
			'et_pb_pricing_table'     => array(
				'title'    => _x( 'Table Title', 'Modules dummy content', 'et_builder' ),
				'subtitle' => $modules_defaults['subtitle'],
				'currency' => _x( '$', 'Modules dummy content', 'et_builder' ),
				'content'  => _x(
					"+ This feature is included\n+ This feature is included\n+ This feature is included\n+ This feature is included\n- This feature is not included\n- This feature is not included",
					'Modules dummy content',
					'et_builder'
				),
				'sum'      => $modules_defaults['number'],
			),
			'et_pb_slide'             => array(
				'heading'     => $modules_defaults['title'],
				'content'     => $modules_defaults['body'],
				'button_text' => $modules_defaults['button'],
			),
			'et_pb_tab'               => array(
				'title'   => _x( 'Tab Title', 'Modules dummy content', 'et_builder' ),
				'content' => $modules_defaults['body'],
			),
			'et_pb_testimonial'       => array(
				'author'      => _x( 'Name Goes Here', 'Modules dummy content', 'et_builder' ),
				'portrait_url' => $modules_defaults['image']['portrait'],
				'content'      => $modules_defaults['body'],
			),
			'et_pb_text'              => array(
				'content' => $modules_defaults['body'],
			),
			'et_pb_toggle'            => array(
				'title'   => $modules_defaults['title'],
				'content' => $modules_defaults['body'],
			),
			'et_pb_countdown_timer'   => array(
				'title'   => $modules_defaults['title'],
				'date_time' => gmdate( 'Y-m-d H:i', current_time( 'timestamp' ) + ( 30 * 86400 ) ), // next 30 days from current day
			),
			'et_pb_video'             => array(
				'src' => $modules_defaults['video'],
			),
			'et_pb_video_slider_item' => array(
				'src'                => $modules_defaults['video'],
				'__is_oembed'        => ET_Builder_Module_Video_Slider_Item::is_oembed( array( 'src' => $modules_defaults['video'] ) ),
				'__get_oembed'       => ET_Builder_Module_Video_Slider_Item::get_oembed( array( 'src' => $modules_defaults['video'] ) ),
				'__oembed_thumbnail' => ET_Builder_Module_Video_Slider_Item::get_oembed_thumbnail( array( 'src' => $modules_defaults['video'] ) ),
			),
			'et_pb_fullwidth_header'  => array(
				'title'           => $modules_defaults['title'],
				'content'         => $modules_defaults['body'],
				'button_one_text' => $modules_defaults['button'],
			),
			'et_pb_social_media_follow_network' => array(
				'social_network'   => 'facebook',
				'content'          => 'facebook',
				'background_color' => '#3b5998',
			),
			'contactFormInputs'        => array(),
			'backgroundOptions'        => array(
				'type'                 => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
				'direction'            => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
				'radialDirection'      => ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ),
				'colorStart'           => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
				'colorEnd'             => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
				'startPosition'        => ET_Global_Settings::get_value( 'all_background_gradient_start_position' ),
				'endPosition'          => ET_Global_Settings::get_value( 'all_background_gradient_end_position' ),
			),
			'filterOptions'            => array(
				'hue_rotate'     => ET_Global_Settings::get_value( 'all_filter_hue_rotate' ),
				'saturate'       => ET_Global_Settings::get_value( 'all_filter_saturate' ),
				'brightness'     => ET_Global_Settings::get_value( 'all_filter_brightness' ),
				'contrast'       => ET_Global_Settings::get_value( 'all_filter_contrast' ),
				'invert'         => ET_Global_Settings::get_value( 'all_filter_invert' ),
				'sepia'          => ET_Global_Settings::get_value( 'all_filter_sepia' ),
				'opacity'        => ET_Global_Settings::get_value( 'all_filter_opacity' ),
				'blur'           => ET_Global_Settings::get_value( 'all_filter_blur' ),
				'mix_blend_mode' => ET_Global_Settings::get_value( 'all_mix_blend_mode' ),
			),
		),
		'saveModuleLibraryCategories'  => et_fb_prepare_library_cats(),
		'emailNameFieldOnlyProviders'  => array_keys( ET_Builder_Module_Signup::providers()->names_by_slug( 'all', 'name_field_only' ) ),
		'emailPredefinedCustomFields'  => ET_Core_API_Email_Providers::instance()->custom_fields_data(),
		'emailCustomFieldProviders'    => array_keys( ET_Builder_Module_Signup::providers()->names_by_slug( 'all', 'custom_fields' ) ),
		'columnSettingFields'          => array(
			'general' => array(
				'bg_img_%s' => array(
					'label'              => esc_html__( 'Column %s Background Image', 'et_builder' ),
					'type'               => 'upload',
					'option_category'    => 'basic_option',
					'upload_button_text' => esc_attr__( 'Upload an image', 'et_builder' ),
					'choose_text'        => esc_attr__( 'Choose a Background Image', 'et_builder' ),
					'update_text'        => esc_attr__( 'Set As Background', 'et_builder' ),
					'description'        => esc_html__( 'If defined, this image will be used as the background for this module. To remove a background image, simply delete the URL from the settings field.', 'et_builder' ),
					'tab_slug'           => 'general',
					'toggle_slug'        => 'background',
					'sub_toggle'         => 'column_%s',
				),
				'background_color_%s' => array(
					'label'        => esc_html__( 'Column %s Background Color', 'et_builder' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'tab_slug'     => 'general',
					'toggle_slug'  => 'background',
					'sub_toggle'   => 'column_%s',
				),
				'parallax_%s' => array(
					'label'           => esc_html__( 'Column %s Parallax Effect', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default'         => 'off',
					'affects'         => array(
						'parallax_method_%s',
						'background_size_%s',
						'background_position_%s',
						'background_repeat_%s',
						'background_blend_%s',
					),
					'description'     => esc_html__( 'Here you can choose whether or not use parallax effect for the featured image', 'et_builder' ),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'parallax_method_%s' => array(
					'label'           => esc_html__( 'Column %s Parallax Method', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'CSS', 'et_builder' ),
						'on'  => esc_html__( 'True Parallax', 'et_builder' ),
					),
					'default'         => 'on',
					'depends_show_if' => 'on',
					'depends_on'      => array(
						'parallax_%s',
					),
					'description'     => esc_html__( 'Here you can choose which parallax method to use for the featured image', 'et_builder' ),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_size_%s' => array(
					'label'           => esc_html__( 'Column %s Background Image Size', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options'         => array(
						'cover'   => esc_html__( 'Cover', 'et_builder' ),
						'contain' => esc_html__( 'Fit', 'et_builder' ),
						'initial' => esc_html__( 'Actual Size', 'et_builder' ),
					),
					'default'         => 'cover',
					'depends_on'      => array(
						'parallax_%s',
					),
					'depends_show_if' => 'off',
					'toggle_slug'     => 'background',
				),
				'background_position_%s' => array(
					'label'           => esc_html__( 'Column %s Background Image Position', 'et_builder' ),
					'type'            => 'select',
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
					'default'         => 'center',
					'depends_on'      => array(
						'parallax_%s',
					),
					'depends_show_if' => 'off',
					'toggle_slug'     => 'background',
				),
				'background_repeat_%s' => array(
					'label'           => esc_html__( 'Column %s Background Image Repeat', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options'         => array(
						'repeat'    => esc_html__( 'Repeat', 'et_builder' ),
						'repeat-x'  => esc_html__( 'Repeat X (horizontal)', 'et_builder' ),
						'repeat-y'  => esc_html__( 'Repeat Y (vertical)', 'et_builder' ),
						'space'     => esc_html__( 'Space', 'et_builder' ),
						'round'     => esc_html__( 'Round', 'et_builder' ),
						'no-repeat' => esc_html__( 'No Repeat', 'et_builder' ),
					),
					'default'         => 'repeat',
					'depends_on'      => array(
						'parallax_%s',
					),
					'depends_show_if' => 'off',
					'toggle_slug'     => 'background',
				),
				'background_blend_%s' => array(
					'label'           => esc_html__( 'Column %s Background Image Blend', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'layout',
					'options' => array(
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
					'default'         => 'normal',
					'depends_on'      => array(
						'parallax_%s',
					),
					'depends_show_if' => 'off',
					'toggle_slug'     => 'background',
				),
				'use_background_color_gradient_%s' => array(
					'label'           => esc_html__( 'Column %s Use Background Color Gradient', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'No', 'et_builder' ),
						'on'  => esc_html__( 'Yes', 'et_builder' ),
					),
					'default'         => 'off',
					'affects'         => array(
						'background_color_gradient_start_%s',
						'background_color_gradient_end_%s',
						'background_color_gradient_start_position_%s',
						'background_color_gradient_end_position_%s',
						'background_color_gradient_type_%s',
						'background_color_gradient_overlays_image_%s'
					),
					'description'     => '',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_start_%s' => array(
					'label'           => esc_html__( 'Column %s Gradient Start', 'et_builder' ),
					'type'            => 'color-alpha',
					'option_category' => 'configuration',
					'description'     => '',
					'depends_show_if' => 'on',
					'default'         => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
					'depends_on'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_end_%s' => array(
					'label'           => esc_html__( 'Column %s Gradient End', 'et_builder' ),
					'type'            => 'color-alpha',
					'option_category' => 'configuration',
					'description'     => '',
					'depends_show_if' => 'on',
					'default'         => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
					'depends_on'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_type_%s' => array(
					'label'           => esc_html__( 'Column %s Gradient Type', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'configuration',
					'options'         => array(
						'linear' => esc_html__( 'Linear', 'et_builder' ),
						'radial' => esc_html__( 'Radial', 'et_builder' ),
					),
					'affects'         => array(
						'background_color_gradient_direction_%s',
						'background_color_gradient_direction_radial_%s',
					),
					'default'         => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
					'description'     => '',
					'depends_show_if' => 'on',
					'depends_on'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_direction_%s' => array(
					'label'           => esc_html__( 'Column %s Gradient Direction', 'et_builder' ),
					'type'            => 'range',
					'option_category' => 'configuration',
					'range_settings'  => array(
						'min'  => 1,
						'max'  => 360,
						'step' => 1,
					),
					'default'         => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
					'validate_unit'   => true,
					'fixed_unit'      => 'deg',
					'fixed_range'     => true,
					'depends_show_if' => 'linear',
					'depends_on'      => array(
						'background_color_gradient_type_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_direction_radial_%s' => array(
					'label'           => esc_html__( 'Column %s Radial Direction', 'et_builder' ),
					'type'            => 'select',
					'option_category' => 'configuration',
					'options'         => array(
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
					'default'         => '',
					'description'     => '',
					'depends_show_if' => 'radial',
					'depends_on'      => array(
						'background_color_gradient_type_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_start_position_%s' => array(
					'label'           => esc_html__( 'Column %s Start Position', 'et_builder' ),
					'type'            => 'range',
					'option_category' => 'configuration',
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'default'         => intval( ET_Global_Settings::get_value( 'all_background_gradient_start_position' ) ),
					'validate_unit'   => true,
					'fixed_unit'      => '%',
					'fixed_range'     => true,
					'depends_show_if' => 'on',
					'depends_on'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_end_position_%s' => array(
					'label'           => esc_html__( 'Column %s End Position', 'et_builder' ),
					'type'            => 'range',
					'option_category' => 'configuration',
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'default'         => intval( ET_Global_Settings::get_value( 'all_background_gradient_end_position' ) ),
					'validate_unit'   => true,
					'fixed_unit'      => '%',
					'fixed_range'     => true,
					'depends_show_if' => 'on',
					'depends_on'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_color_gradient_overlays_image_%s' => array(
					'label'           => esc_html__( 'Column %s Place Gradient Above Background Image', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'No', 'et_builder' ),
						'on'  => esc_html__( 'Yes', 'et_builder' ),

					'default'         => intval( ET_Global_Settings::get_value( 'all_background_gradient_overlays_image' ) ) ),
					'description'     => '',
					'depends_show_if' => 'on',
					'depends_on'      => array(
						'use_background_color_gradient_%s',
					),
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),

				'background_video_mp4_%s' => array(
					'label'              => esc_html__( 'Column %s Background Video MP4', 'et_builder' ),
					'type'               => 'upload',
					'option_category'    => 'basic_option',
					'data_type'          => 'video',
					'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
					'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'et_builder' ),
					'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
					'tab_slug'           => 'general',
					'toggle_slug'        => 'background',
					'sub_toggle'         => 'column_%s',
				),
				'background_video_webm_%s' => array(
					'label'              => esc_html__( 'Column %s Background Video Webm', 'et_builder' ),
					'type'               => 'upload',
					'option_category'    => 'basic_option',
					'data_type'          => 'video',
					'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
					'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'et_builder' ),
					'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
					'tab_slug'           => 'general',
					'toggle_slug'        => 'background',
					'sub_toggle'         => 'column_%s',
				),
				'background_video_width_%s' => array(
					'label'           => esc_html__( 'Column %s Background Video Width', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'tab_slug'        => 'general',
					'sub_toggle'      => 'column_%s',
				),
				'background_video_height_%s' => array(
					'label'           => esc_html__( 'Column %s Background Video Height', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'allow_player_pause_%s' => array(
					'label'           => esc_html__( 'Column %s Pause Video When Another Video Plays', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'No', 'et_builder' ),
						'on'  => esc_html__( 'Yes', 'et_builder' ),
					),
					'default'         => 'off',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'background_video_pause_outside_viewport_%s' => array(
					'label'           => esc_html__( 'Column %s Pause Video While Not In View', 'et_builder' ),
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => esc_html__( 'No', 'et_builder' ),
						'on'  => esc_html__( 'Yes', 'et_builder' ),
					),
					'default'         => 'on',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'background',
					'sub_toggle'      => 'column_%s',
				),
				'__video_background_%s' => array(
					'type'                => 'computed',
					'computed_callback'   => array( 'ET_Builder_Column', 'get_column_video_background' ),
					'computed_depends_on' => array(
						'background_video_mp4_%s',
						'background_video_webm_%s',
						'background_video_width_%s',
						'background_video_height_%s',
					),
				),
			),
			'advanced'                => array(
				'padding_%s'          => array(
					'label'           => esc_html__( 'Column %s Custom Padding', 'et_builder' ),
					'type'            => 'custom_padding',
					'mobile_options'  => true,
					'option_category' => 'layout',
					'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'et_builder' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'margin_padding',
					'sub_toggle'      => 'column_%s',
				),
			),
			'css'                     => array(
				'module_id_%s'        => array(
					'label'           => esc_html__( 'Column %s CSS ID', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'sub_toggle'      => 'column_%s',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'module_class_%s'     => array(
					'label'           => esc_html__( 'Column %s CSS Class', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'sub_toggle'      => 'column_%s',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'custom_css_before_%s'=> array(
					'label'           => esc_html__( 'Column %s before', 'et_builder' ),
					'no_space_before_selector' => true,
					'selector'        => ':before',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'custom_css',
					'sub_toggle'      => 'column_%s',
				),
				'custom_css_main_%s'  => array(
					'label'           => esc_html__( 'Column %s Main Element', 'et_builder' ),
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'custom_css',
					'sub_toggle'      => 'column_%s',
				),
				'custom_css_after_%s' => array(
					'label'           => esc_html__( 'Column %s After', 'et_builder' ),
					'no_space_before_selector' => true,
					'selector'        => ':after',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'custom_css',
					'sub_toggle'      => 'column_%s',
				),

			),
		),
		'knownShortcodeWrappers'           => et_fb_known_shortcode_wrappers(),
		'customizer'                   => array(
			'tablet' => array(
				'sectionHeight' => et_get_option( 'tablet_section_height' ),
			),
			'phone' => array(
				'sectionHeight' => et_get_option( 'phone_section_height' ),
			),
		),
		'acceptableCSSStringValues'    => et_builder_get_acceptable_css_string_values( 'all' ),
		'abTesting' => et_builder_ab_options( $post->ID ),
		'customModuleCredits' => ET_Builder_Element::get_custom_modules_credits( $post_type ),
	);

	$moduolesI10n = ET_Builder_Element::get_modules_i10n( $post_type );
	$aditionalI10n = array(
		'audio'    => array(
			'meta' => _x( 'by <strong>%1$s</strong>', 'Audio Module meta information', 'et_builder' ),
		),
		'background' => array(
			'label'       => __( 'Background', 'et_builder' ),
			'description' => '',
		),
		'column' => array(
			'backgroundColor' => esc_html__( 'Column %s Background', 'et_builder' ),
		),
		'contactForm' => array(
			'thankYou' => esc_html__( 'Thanks for contacting us', 'et_builder' ),
			'submit'   => esc_attr__( 'Submit', 'et_builder' ),
		),
		'contactFormItem' => array(
			'noOptions'     => esc_html__( 'No options added.', 'et_builder' ),
			'selectDefault' => esc_html__( '-- Please Select --', 'et_builder' ),
		),
		'countdownTimer' => array(
			'dayFull'     => esc_html__( 'Day(s)', 'et_builder' ),
			'dayShort'    => esc_html__( 'Day', 'et_builder' ),
			'hourFull'    => esc_html__( 'Hour(s)', 'et_builder' ),
			'hourShort'   => esc_html__( 'Hrs', 'et_builder' ),
			'minuteFull'  => esc_html__( 'Minute(s)', 'et_builder' ),
			'minuteShort' => esc_html__( 'Min', 'et_builder' ),
			'secondFull'  => esc_html__( 'Second(s)', 'et_builder' ),
			'secondShort' => esc_html__( 'Sec', 'et_builder' ),
		),
		'signup' => array(
			'firstName'    => esc_attr__( 'First Name', 'et_builder' ),
			'lastName'     => esc_attr__( 'Last Name', 'et_builder' ),
			'name'         => esc_attr__( 'Name', 'et_builder' ),
			'email'        => esc_attr__( 'Email', 'et_builder' ),
		),
		'filterablePortfolio' => array(
			'all' => esc_html__( 'All', 'et_builder' ),
		),
		'login' => array(
			'loginAs'         => sprintf( esc_html__( 'Login as %s', 'et_builder' ), $current_user->display_name ),
			'login'           => esc_html__( 'Login', 'et_builder' ),
			'logout'          => esc_html__( 'Log out', 'et_builder' ),
			'forgotPassword'  => esc_html__( 'Forgot your password?', 'et_builder' ),
			'username'        => esc_html__( 'Username', 'et_builder' ),
			'password'        => esc_html__( 'Password', 'et_builder' ),
			'note_autofill'   => esc_attr__( 'Note: this field is used to disable browser autofill during the form editing in VB', 'et_builder' ),
		),
		'postTitle' => array(
			'by' => esc_html__( 'by ', 'et_builder' ),
		),
		'search' => array(
			'submitButtonText' => esc_html__( 'Search', 'et_builder' ),
			'searchfor' => esc_html__( 'Search for:', 'et_builder' ),
		),
		'fullwidthPostSlider' => array(
			'by' => esc_html__( 'by ', 'et_builder' ),
		),
		'socialFollow' => array(
			'follow' => esc_html__( 'Follow', 'et_builder' ),
		),
		'items' => array(
			'newItemDefaultText' => esc_html__( 'New Item', 'et_builder' ),
		),
	);

	// Prepare VB help videos list.
	$help_videos = array_merge( array(
		'et_pb_default' => array(
			array(
				'id'   => esc_html__( 'T-Oe01_J62c', 'et_builder' ),
				'name' => esc_html__( 'An introduction to the Divi Builder', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( '9eqXcrLcnoc', 'et_builder' ),
				'name' => esc_html__( 'Jump-starting your page with pre-made layouts', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'exLLvnS5pR8', 'et_builder' ),
				'name' => esc_html__( 'Saving and loading layouts from the Divi Library', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( '3kmJ_mMVB1w', 'et_builder' ),
				'name' => esc_html__( 'Getting creative with Sections', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'R9ds7bEaHE8', 'et_builder' ),
				'name' => esc_html__( 'Organizing your content with Rows', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( '1iqjhnHVA9Y', 'et_builder' ),
				'name' => esc_html__( 'Using Design settings to customize your page', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'MVWpwKJR8eE', 'et_builder' ),
				'name' => esc_html__( 'Using the builders Right Click controls', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'PBmijAL4twA', 'et_builder' ),
				'name' => esc_html__( 'Importing and exporting Divi Builder layouts', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'pklyz3vcjEs', 'et_builder' ),
				'name' => esc_html__( 'Become a power use with keyboard shortcuts', 'et_builder' ),
			),
		),
		'et_pb_add_section' => array(
			array(
				'id'   => esc_html__( '3kmJ_mMVB1w', 'et_builder' ),
				'name' => esc_html__( 'An introduction to Sections', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( '1iqjhnHVA9Y', 'et_builder' ),
				'name' => esc_html__( 'Design Settings and Advanced Section Settings', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'boNZZ0MYU0E', 'et_builder' ),
				'name' => esc_html__( 'Saving and loading from the library', 'et_builder' ),
			),
		),
		'et_pb_add_row' => array(
			array(
				'id'   => esc_html__( 'R9ds7bEaHE8', 'et_builder' ),
				'name' => esc_html__( 'An introduction to Rows', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( '1iqjhnHVA9Y', 'et_builder' ),
				'name' => esc_html__( 'Design Settings and Advanced Row Settings', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'boNZZ0MYU0E', 'et_builder' ),
				'name' => esc_html__( 'Saving and loading from the library', 'et_builder' ),
			),
		),
		'et_pb_add_module' => array(
			array(
				'id'   => esc_html__( 'FkQuawiGWUw', 'et_builder' ),
				'name' => esc_html__( 'An introduction to Modules', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( '1iqjhnHVA9Y', 'et_builder' ),
				'name' => esc_html__( 'Design Settings and Advanced Module Settings', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'boNZZ0MYU0E', 'et_builder' ),
				'name' => esc_html__( 'Saving and loading from the library', 'et_builder' ),
			),
		),
		'et_pb_default_layouts' => array(
			array(
				'id'   => esc_html__( '9eqXcrLcnoc', 'et_builder' ),
				'name' => esc_html__( 'Using pre-made layouts', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'boNZZ0MYU0E', 'et_builder' ),
				'name' => esc_html__( ' Saving and loading from the library', 'et_builder' ),
			),
		),
		'et_pb_portability' => array(
			array(
				'id'   => esc_html__( 'PBmijAL4twA', 'et_builder' ),
				'name' => esc_html__( 'Importing and exporting layouts', 'et_builder' ),
			),
		),
		'et_pb_history' => array(
			array(
				'id'   => esc_html__( 'FkQuawiGWUw', 'et_builder' ),
				'name' => esc_html__( 'Managing your editing history', 'et_builder' ),
			),
		),
		'et_pb_save_to_library' => array(
			array(
				'id'   => esc_html__( 'boNZZ0MYU0E', 'et_builder' ),
				'name' => esc_html__( 'Saving and loading from the library', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'TQnPBXzTSGY', 'et_builder' ),
				'name' => esc_html__( 'Global modules, rows and sections', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'tarDcDjE86w', 'et_builder' ),
				'name' => esc_html__( 'Using Selective Sync', 'et_builder' ),
			),
			array(
				'id'   => esc_html__( 'PBmijAL4twA', 'et_builder' ),
				'name' => esc_html__( ' Importing and exporting items from the library', 'et_builder' ),
			),
		),
		'et_pb_page_settings' => array(
			array(
				'id'   => esc_html__( 'FkQuawiGWUw', 'et_builder' ),
				'name' => esc_html__( 'An introduction to Page Settings', 'et_builder' ),
			),
		),
	), ET_Builder_Element::get_help_videos() );

	// Internationalization.
	$helpers['i18n'] = array(
		'modules'                      => array_merge( $moduolesI10n, $aditionalI10n ),
		'saveButtonText'               => esc_attr__( 'Save', 'et_builder' ),
		'saveDraftButtonText'          => esc_attr__( 'Save Draft', 'et_builder' ),
		'publishButtonText'            => ( is_page() && ! current_user_can( 'publish_pages' ) ) || ( ! is_page() && ! current_user_can( 'publish_posts' ) ) ? esc_attr__( 'Submit', 'et_builder' ) : esc_attr__( 'Publish', 'et_builder' ),
		'controls'                     => array(
			'tinymce'                  => array(
				'visual'               => esc_html__( 'Visual', 'et_builder' ),
				'text'                 => esc_html__( 'Text', 'et_builder' ),
			),
			'moduleItem'               => array(
				'addNew'               => esc_html__( 'Add New Item', 'et_builder' ),
			),
			'upload'                   => array(
				'buttonText'           => esc_html__( 'Upload', 'et_builder' ),
				'addImage'             => esc_html__( 'Add Image', 'et_builder' ),
				'addVideo'             => esc_html__( 'Add Video', 'et_builder' ),
			),
			'insertMedia'              => array(
				'buttonText'           => esc_html__( 'Add Media', 'et_builder' ),
				'modalTitleText'       => esc_html__( 'Insert Media', 'et_builder' ),
			),
			'inputMargin'              => array(
				'top'                  => esc_html__( 'Top', 'et_builder' ),
				'right'                => esc_html__( 'Right', 'et_builder' ),
				'bottom'               => esc_html__( 'Bottom', 'et_builder' ),
				'left'                 => esc_html__( 'Left', 'et_builder' ),
			),
			'colorpicker'              => array(
				'clear'                => esc_html__( 'Clear', 'et_builder' ),
				'select'               => esc_html__( 'Select', 'et_builder' ),
			),
			'colorManager'             => array(
				'saved'                => esc_html__( 'Saved', 'et_builder' ),
				'recent'               => esc_html__( 'Recent', 'et_builder' ),
			),
			'uploadGallery'            => array(
				'uploadButtonText'     => esc_html__( 'Update Gallery', 'et_builder'),
				'addImages'            => esc_html__( 'Add Gallery Images', 'et_builder'),
			),
			'centerMap'                => array(
				'updateMapButtonText'  => esc_html__( 'Find', 'et_builder'),
				'geoCodeError'         => esc_html__( 'Geocode was not successful for the following reason', 'et_builder' ),
				'geoCodeError_2'       => esc_html__( 'Geocoder failed due to', 'et_builder' ),
				'noResults'            => esc_html__( 'No results found', 'et_builder' ),
				'mapPinAddressInvalid' => esc_html__( 'Invalid Pin and address data. Please try again.', 'et_builder' ),
			),
			'tabs'                     => array(
				'general'              => esc_html__( 'Content', 'et_builder' ),
				'design'               => esc_html__( 'Design', 'et_builder' ),
				'advanced'             => esc_html__( 'Design', 'et_builder' ),
				'css'                  => esc_html__( 'Advanced', 'et_builder' ),
			),
			'additionalButton'         => array(
				'changeApiKey'         => esc_html__( 'Change API Key', 'et_builder' ),
				'generateImageUrlFromVideo' => esc_html__( 'Generate From Video', 'et_builder' ),
			),
			'conditionalLogic'         => array(
				'checked'              => esc_html__( 'checked', 'et_builder' ),
				'unchecked'            => esc_html__( 'not checked', 'et_builder' ),
				'is'                   => esc_html__( 'equals', 'et_builder' ),
				'isNot'                => esc_html__( 'does not equal', 'et_builder' ),
				'isGreater'            => esc_html__( 'is greater than', 'et_builder' ),
				'isLess'               => esc_html__( 'is less than', 'et_builder' ),
				'contains'             => esc_html__( 'contains', 'et_builder' ),
				'doesNotContain'       => esc_html__( 'does not contain', 'et_builder' ),
				'isEmpty'              => esc_html__( 'is empty', 'et_builder' ),
				'isNotEmpty'           => esc_html__( 'is not empty', 'et_builder' ),
			),
			'selectAnimation'          => array(
				'none'   => esc_html__( 'None', 'et_builder' ),
				'fade'   => esc_html__( 'Fade', 'et_builder' ),
				'slide'  => esc_html__( 'Slide', 'et_builder' ),
				'bounce' => esc_html__( 'Bounce', 'et_builder' ),
				'zoom'   => esc_html__( 'Zoom', 'et_builder' ),
				'flip'   => esc_html__( 'Flip', 'et_builder' ),
				'fold'   => esc_html__( 'Fold', 'et_builder' ),
				'roll'   => esc_html__( 'Roll', 'et_builder' ),
			),
			'cssText'                  => esc_html__( 'CSS', 'et_builder'),
			'background'               => array(
				'addColor'       => esc_html__( 'Add Background Color', 'et_builder' ),
				'addGradient'    => esc_html__( 'Add Background Gradient', 'et_builder' ),
				'addImage'       => esc_html__( 'Add Background Image', 'et_builder' ),
				'addVideo'       => esc_html__( 'Add Background Video', 'et_builder' ),
				'color'          => esc_html__( 'Background Color', 'et_builder' ),
				'gradient'       => esc_html__( 'Background Gradient', 'et_builder' ),
				'gradientColors' => esc_html__( 'Background Gradient Colors', 'et_builder' ),
				'image'          => esc_html__( 'Background Image', 'et_builder' ),
				'video'          => esc_html__( 'Background Video', 'et_builder' ),
			),
			'responsiveTabs' => array(
				'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
				'phone'   => esc_html__( 'Smartphone', 'et_builder' ),
			),
		),
		'rightClickMenuItems' => array(
			'undo'            => esc_html__( 'Undo', 'et_builder' ),
			'redo'            => esc_html__( 'Redo', 'et_builder' ),
			'lock'            => esc_html__( 'Lock', 'et_builder' ),
			'unlock'          => esc_html__( 'Unlock', 'et_builder' ),
			'copy'            => esc_html__( 'Copy', 'et_builder' ),
			'paste'           => esc_html__( 'Paste', 'et_builder' ),
			'reset'           => esc_html__( 'Reset', 'et_builder' ),
			'styles'          => esc_html__( 'Styles', 'et_builder' ),
			'copyStyle'       => esc_html__( 'Copy Style', 'et_builder' ),
			'pasteStyle'      => esc_html__( 'Paste Style', 'et_builder' ),
			'disable'         => esc_html__( 'Disable', 'et_builder' ),
			'enable'          => esc_html__( 'Enable', 'et_builder' ),
			'save'            => esc_html__( 'Save to Library', 'et_builder' ),
			'startABTesting'  => esc_html__( 'Split Test', 'et_builder' ),
			'endABTesting'    => esc_html__( 'End Split Test', 'et_builder' ),
			'moduleType'      => array(
				'module'      => esc_html__( 'Module', 'et_builder' ),
				'row'         => esc_html__( 'Row', 'et_builder' ),
				'section'     => esc_html__( 'Section', 'et_builder' ),
			),
			'disableGlobal'   => esc_html__( 'Disable Global', 'et_builder' ),
			'collapse'        => esc_html__( 'Collapse', 'et_builder' ),
			'expand'          => esc_html__( 'Expand', 'et_builder' ),
			'stylesModified'  => esc_html__( 'View Modified Style', 'et_builder' ),
			'toggle'          => esc_html__( 'Toggle', 'et_builder' ),
			'tab'             => esc_html__( 'Tab', 'et_builder' ),
			'option'          => esc_html__( 'Option', 'et_builder' ),
			'item'            => esc_html__( 'Item', 'et_builder' ),
			'go_to_option'    => esc_html__( 'Go To Option', 'et_builder' ),
		),
		'tooltips'            => array(
			'insertModule'     => esc_html__( 'Insert Module', 'et_builder' ),
			'insertColumn'     => esc_html__( 'Insert Columns', 'et_builder' ),
			'insertSection'    => esc_html__( 'Insert Section', 'et_builder' ),
			'insertRow'        => esc_html__( 'Insert Row', 'et_builder' ),
			'newModule'        => esc_html__( 'New Module', 'et_builder' ),
			'newRow'           => esc_html__( 'New Row', 'et_builder' ),
			'newSection'       => esc_html__( 'New Section', 'et_builder' ),
			'addFromLibrary'   => esc_html__( 'Add From Library', 'et_builder' ),
			'addToLibrary'     => esc_html__( 'Add to Library', 'et_builder' ),
			'loading'          => esc_html__( 'loading...', 'et_builder' ),
			'regular'          => esc_html__( 'Regular', 'et_builder' ),
			'fullwidth'        => esc_html__( 'Fullwidth', 'et_builder' ),
			'specialty'        => esc_html__( 'Specialty', 'et_builder' ),
			'changeRow'        => esc_html__( 'Choose Layout', 'et_builder' ),
			'clearLayout'      => esc_html__( 'Clear Layout', 'et_builder' ),
			'clearLayoutText'  => esc_html__( 'All of your current page content will be lost. Do you wish to proceed?', 'et_builder' ),
			'yes'              => esc_html__( 'Yes', 'et_builder' ),
			'loadLayout'       => esc_html__( 'Load From Library', 'et_builder' ),
			'layoutDetails'    => esc_html__( 'Layout Details', 'et_builder' ),
			'layoutName'       => esc_html__( 'Layout Name', 'et_builder' ),
			'replaceLayout'    => esc_html__( 'Replace existing content.', 'et_builder' ),
			'search'           => esc_html__( 'Search', 'et_builder' ) . '...',
			'portability'      => esc_html__( 'Portability', 'et_builder' ),
			'export'           => esc_html__( 'Export', 'et_builder' ),
			'import'           => esc_html__( 'Import', 'et_builder' ),
			'exportText'       => esc_html__( 'Exporting your Divi Builder Layout will create a JSON file that can be imported into a different website.', 'et_builder' ),
			'exportName'       => esc_html__( 'Export File Name', 'et_builder' ),
			'exportButton'     => esc_html__( 'Export Divi Builder Layout', 'et_builder' ),
			'importText'       => esc_html__( 'Importing a previously-exported Divi Builder Layout file will overwrite all content currently on this page.', 'et_builder' ),
			'importField'      => esc_html__( 'Select File To Import', 'et_builder' ),
			'importBackUp'     => esc_html__( 'Download backup before importing', 'et_builder' ),
			'importButton'     => esc_html__( 'Import Divi Builder Layout', 'et_builder' ),
			'noFile'           => esc_html__( 'No File Selected', 'et_builder' ),
			'chooseFile'       => esc_html__( 'Choose File', 'et_builder' ),
			'importOptions'    => esc_html__( 'Options', 'et_builder' ),
		),
		'saveModuleLibraryAttrs'        => array(
			'general'               => esc_html__( 'Include General Settings', 'et_builder' ),
			'advanced'              => esc_html__( 'Include Advanced Design Settings', 'et_builder' ),
			'css'                   => esc_html__( 'Include Custom CSS', 'et_builder' ),
			'selectCategoriesText'  => esc_html__( 'Select category(ies) for new template or type a new name ( optional )', 'et_builder' ),
			'templateName'          => esc_html__( 'Layout Name', 'et_builder' ),
			'selectiveError'        => esc_html__( 'Please select at least 1 tab to save', 'et_builder' ),
			'globalTitle'           => esc_html__( 'Save as Global', 'et_builder' ),
			'globalText'            => esc_html__( 'Make this a global item', 'et_builder' ),
			'createCatText'         => esc_html__( 'Create New Category', 'et_builder' ),
			'addToCatText'          => esc_html__( 'Add To Categories', 'et_builder' ),
			'descriptionText'       => esc_html__( 'Here you can add the current item to your Divi Library for later use.', 'et_builder' ),
			'descriptionTextLayout' => esc_html__( 'Save your current page to the Divi Library for later use.', 'et_builder' ),
			'saveText'              => esc_html__( 'Save to Library', 'et_builder' ),
			'allCategoriesText'     => esc_html__( 'All Categories', 'et_builder' ),
		),
		'alertModal' => array(
			'buttonCancelLabel'  => esc_html__( 'Cancel', 'et_builder' ),
			'buttonProceedLabel' => esc_html__( 'Proceed', 'et_builder' ),
		),
		'modals' => array(
			'defaultTitle'   => esc_html__( 'Modal Title', 'et_builder' ),
			'tabItemTitles'  => array(
				'general' => esc_html__( 'General', 'et_builder' ),
				'design'  => esc_html__( 'Design', 'et_builder' ),
				'css'     => esc_html__( 'CSS', 'et_builder' ),
			),
			'moduleSettings' => array(
				'title' => esc_html__( '%s Settings', 'et_builder' ),
				'noSupport' => esc_html__( 'This option is not previewable in the Visual Builder. It will only take effect once you exit the Visual Builder', 'et_builder' ),
			),
			'pageSettings'   => array(
				'title'   => ET_Builder_Settings::get_title(),
				'tabs'    => ET_Builder_Settings::get_tabs(),
				'toggles' => ET_Builder_Settings::get_toggles(),
			),
			'searchOptions'         => esc_html__( 'Search Options', 'et_builder' ),
			'filter'                => esc_html__( 'Filter', 'et_builder' ),
			'show_only'             => esc_html__( 'Show Only', 'et_builder' ),
			'filterNotice'          => esc_html__( 'No options exist for this search query. Click here to clear your search filters.', 'et_builder' ),
			'filterNoticeClickable' => esc_html__( 'Click here', 'et_builder' ),
		),
		'selectControl' => array(
			'typeToSearch' => esc_html__( 'Start Typing', 'et_builder' ),
			'subgroups'    => array(
				'recent'   => esc_html__( 'Recent', 'et_builder' ),
				'uploaded' => esc_html__( 'Custom Fonts', 'et_builder' ),
			),
		),
		'history' => array(
			'modal' => array(
				'title' => esc_html__( 'Editing History', 'et_builder' ),
				'tabs' => array(
					'states' => esc_html__( 'History States', 'et_builder' ),
				),
			),
			'meta' => et_pb_history_localization(),
		),
		'help' => array(
			'modal' => array(
				'title' 				=> esc_html__( 'Divi Builder Helper', 'et_builder' ),
				'tabs'					=> array(
					'gettingStarted' 	=> esc_html__( 'Video Tutorials', 'et_builder' ),
					'shortcut' 			=> esc_html__( 'Keyboard Shortcuts', 'et_builder' ),
				),
			),
			'shortcuts' 				=> et_builder_get_shortcuts('fb'),
			'button' 					=> esc_html__( 'Help', 'et_builder' ),
		),
		'abTesting' => array_merge( et_builder_ab_labels(), array(
			'reportTitle'      => esc_html__( 'Split Testing Statistics', 'et_builder' ),
			'reportTabNavs' => array(
				'clicks'          => esc_html__( 'Clicks', 'et_builder' ),
				'reads'           => esc_html__( 'Reads', 'et_builder' ),
				'bounces'         => esc_html__( 'Bounces', 'et_builder' ),
				'engagements'     => esc_html__( 'Goal Engagement', 'et_builder' ),
				'conversions'     => esc_html__( 'Conversions', 'et_builder' ),
				'shortcode_conversions' => esc_html__( 'Shortcode Conversions', 'et_builder' ),
			),
			'reportFilterTime' => array(
				'day'   => esc_html__( 'Last 24 Hours', 'et_builder' ),
				'week'  => esc_html__( 'Last 7 Days', 'et_builder' ),
				'month' => esc_html__( 'Last Month', 'et_builder' ),
				'all'   => esc_html__( 'All Time', 'et_builder' ),
			),
			'reportTotal'          => esc_html__( 'Total', 'et_builder' ),
			'reportSummaryTitle'   => esc_html__( 'Summary & Data', 'et_builder' ),
			'reportRefreshTooltip' => esc_html__( 'Refresh Split Test Data', 'et_builder' ),
			'reportEndTestButton'  => esc_html__( 'End Split Test & Pick Winner', 'et_builder' ),
		) ),
		'fonts' => array(
			'fontWeight'     => esc_html__( 'Font Weight', 'et_builder' ),
			'fontStyle'      => esc_html__( 'Font Style', 'et_builder' ),
			'delete'         => esc_html__( 'Delete', 'et_builder' ),
			'deleteConfirm'  => esc_html__( 'Are You Sure Want to Delete', 'et_builder' ),
			'confirmAction'  => esc_html__( 'Are You Sure?', 'et_builder' ),
			'cancel'         => esc_html__( 'Cancel', 'et_builder' ),
			'upload'         => esc_html__( 'Upload', 'et_builder' ),
			'font'           => esc_html__( 'Font', 'et_builder' ),
			'chooseFile'     => esc_html__( 'Choose Font Files', 'et_builder' ),
			'supportedFiles' => esc_html__( 'Supported File Formats', 'et_builder' ),
			'fileError'      => esc_html__( 'Unsupported File Format', 'et_builder' ),
			'noFile'         => esc_html__( 'Drag Files Here', 'et_builder' ),
			'fontName'       => esc_html__( 'Name Your Font', 'et_builder' ),
			'fontNameLabel'  => esc_html__( 'Font Name', 'et_builder' ),
			'selectedFiles'  => esc_html__( 'Selected Font Files', 'et_builder' ),
			'weightsSupport' => esc_html__( 'Supported Font Weights', 'et_builder' ),
			'weightsHelp'    => esc_html__( 'Choose the font weights supported by your font. Select "All" if you don\'t know this information or if your font includes all weights.', 'et_builder' ),
			'noFilesError'   => esc_html__( 'Please Select At Least One File', 'et_builder' ),
			'searchFonts'    => esc_html__( 'Search Fonts', 'et_builder' ),
			'underline'      => esc_html__( 'Underline', 'et_builder' ),
			'strikethrough'  => esc_html__( 'Strikethrough', 'et_builder' ),
			'color'          => esc_html__( 'Color', 'et_builder' ),
			'style'          => esc_html__( 'Style', 'et_builder' ),
			'all'            => esc_html__( 'All', 'et_builder' ),

		),
		'app' => array(
			'modal' => array(
				'title'  => esc_html__( 'Builder Settings', 'et_builder' ),
				'labels' => array(
					'toolbar'            => esc_html__( 'Customize Builder Settings Toolbar', 'et_builder' ),
					'view_mode'          => esc_html__( 'Builder Default Interaction Mode', 'et_builder' ),
					'history'            => esc_html__( 'History State Interval', 'et_builder' ),
					'modal_position'     => esc_html__( 'Settings Modal Default Position', 'et_builder' ),
					'animation'          => esc_html__( 'Builder Interface Animations', 'et_builder' ),
					'disabled_modules'   => esc_html__( 'Show Disabled Modules At 50% Opacity', 'et_builder' ),
					'group_settings'     => esc_html__( 'Group Settings Into Closed Toggles', 'et_builder' ),
					'dummy_content'      => esc_html__( 'Add Placeholder Content To New Modules', 'et_builder' ),
					'page_creation_flow' => esc_html__( 'Page Creation Flow', 'et_builder' ),
				),
				'view_mode_select' => array(
                    '0' => esc_html__( 'Hover Mode' , 'et_builder' ),
                    '1' => esc_html__( 'Click Mode' , 'et_builder' ),
                    '2' => esc_html__( 'Grid Mode' , 'et_builder' ),
				),
				'history_intervals_select' => array(
                    '0' => esc_html__( 'After Every Action' , 'et_builder' ),
                    '1' => esc_html__( 'After Every 10th Action' , 'et_builder' ),
                    '2' => esc_html__( 'After Every 20th Action' , 'et_builder' ),
                    '3' => esc_html__( 'After Every 30th Action' , 'et_builder' ),
                    '4' => esc_html__( 'After Every 40th Action' , 'et_builder' ),
				),
				'modal_default_select' => array(
					'0' => esc_html__( 'Last Used Position', 'et_builder' ),
					'1' => esc_html__( 'Floating Minimum Size', 'et_builder' ),
					'2' => esc_html__( 'Fullscreen', 'et_builder' ),
					'3' => esc_html__( 'Fixed Left Sidebar', 'et_builder' ),
					'4' => esc_html__( 'Fixed Right Sidebar', 'et_builder' ),
					'5' => esc_html__( 'Fixed Bottom Panel', 'et_builder' ),
					// TODO, disabled until further notice (Issue #3930 & #5859)
					// '6' => esc_html__( 'Fixed Top Panel', 'et_builder' ),
				),
				'builder_animation_toggle' => array(
					'on'   => esc_html__( 'On', 'et_builder' ),
					'off'  => esc_html__( 'Off', 'et_builder' ),
				),
				'hide_disabled_module_toggle' => array(
					'on'   => esc_html__( 'On', 'et_builder' ),
					'off'  => esc_html__( 'Off', 'et_builder' ),
				),
				'display_modal_settings' => array(
					'on'   => esc_html__( 'On', 'et_builder' ),
					'off'  => esc_html__( 'Off', 'et_builder' ),
				),
				'enable_dummy_content' => array(
					'on'   => esc_html__( 'On', 'et_builder' ),
					'off'  => esc_html__( 'Off', 'et_builder' ),
				),
				'page_creation_flow_select' => array(
					'0' => esc_html__( 'Give Me A Choice', 'et_builder' ),
					'1' => esc_html__( 'Build From Scratch', 'et_builder' ),
					'2' => esc_html__( 'Load Premade Layout', 'et_builder' ),
					'3' => esc_html__( 'Clone Existing Page', 'et_builder' ),
				),
			),
		),
		'video' => array(
			'active'  => esc_html__( 'Video Overlay is Currently Active.', 'et_builder' ),
			'offline' => esc_html__( 'Unable to Establish Internet Connection.', 'et_builder' ),
		),

		/**
		 * Filters the builder's video tutorials.
		 * Can be used for add video tutorials for custom 3rd-party modules.
		 *
		 * @param string[] $help_videos {
		 *     Videos to module relations array
		 *
		 *     @type string[] $module_slug {
		 *          Module slugs array
		 *
		 *          @type string[] $sort_order {
		 *              Video sort order
		 *
		 *              @type string $id Youtube video ID
		 *              @type string $name Localized video title
		 *          }
		 *     }
		 * }
		 */
		'videos' => apply_filters( 'et_fb_help_videos', $help_videos ),

		'sortable' => array(
			'has_no_ab_permission'                     => esc_html__( 'You do not have permission to edit the module, row or section in this split test.', 'et_builder' ),
			'cannot_move_goal_into_subject'            => esc_html__( 'A split testing goal cannot be moved inside of a split testing subject. To perform this action you must first end your split test.', 'et_builder' ),
			'cannot_move_subject_into_goal'            => esc_html__( 'A split testing subject cannot be moved inside of a split testing goal. To perform this action you must first end your split test.', 'et_builder' ),
			'cannot_move_row_goal_out_from_subject'    => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
			'section_only_row_dragged_away'            => esc_html__( 'The section should have at least one row.', 'et_builder' ),
			'global_module_alert'                      => esc_html__( 'You cannot add global modules into global sections or rows', 'et_builder' ),
			'cannot_move_module_goal_out_from_subject' => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
			'stop_dropping_3_col_row'                  => esc_html__( '3 column row can\'t be used in this column.', 'et_builder' ),
		),
		'tooltip' => array(
			'pageSettingsBar' => array(
				'responsive' => array(
					'wireframe'    => esc_html__( 'Wireframe View', 'et_builder' ),
					'zoom'    => esc_html__( 'Zoom Out', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop View', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet View', 'et_builder' ),
					'phone'   => esc_html__( 'Phone View', 'et_builder' ),
				),
				'eventMode' => array(
					'click' => esc_html__( 'Click Mode', 'et_builder' ),
					'hover' => esc_html__( 'Hover Mode', 'et_builder' ),
					'grid'  => esc_html__( 'Grid Mode', 'et_builder' ),
				),
				'main' => array(
					'loadLibrary'       => esc_html__( 'Load From Library', 'et_builder' ),
					'saveToLibrary'     => esc_html__( 'Save To Library', 'et_builder' ),
					'clearLayout'       => esc_html__( 'Clear Layout', 'et_builder' ),
					'pageSettingsModal' => esc_html__( 'Page Settings', 'et_builder' ),
					'history'           => esc_html__( 'Editing History', 'et_builder' ),
					'portability'       => esc_html__( 'Portability', 'et_builder' ),
					'open'              => esc_html__( 'Expand Settings', 'et_builder' ),
					'close'             => esc_html__( 'Collapse Settings', 'et_builder' ),
				),
				'save' => array(
					'saveDraft' => esc_html__( 'Save as Draft', 'et_builder' ),
					'save'      => esc_html__( 'Save', 'et_builder' ),
					'publish'   => esc_html__( 'Publish', 'et_builder' ),
				)
			),
			'modal' => array(
				'expandModal'   => esc_html__( 'Expand Modal', 'et_builder' ),
				'contractModal' => esc_html__( 'Contract Modal', 'et_builder' ),
				'resize'        => esc_html__( 'Resize Modal', 'et_builder' ),
				'snapModal'     => esc_html__( 'Snap to Left', 'et_builder' ),
				'separateModal' => esc_html__( 'Separate Modal', 'et_builder' ),
				'redo'          => esc_html__( 'Redo', 'et_builder' ),
				'undo'          => esc_html__( 'Undo', 'et_builder' ),
				'cancel'        => esc_html__( 'Discard All Changes', 'et_builder' ),
				'save'          => esc_html__( 'Save Changes', 'et_builder' ),
			),
			'inlineEditor' => array(
				'back'             => esc_html__( 'Go Back', 'et_builder' ),
				'increaseFontSize' => esc_html__( 'Decrease Font Size', 'et_builder' ),
				'decreaseFontSize' => esc_html__( 'Increase Font Size', 'et_builder' ),
				'bold'             => esc_html__( 'Bold Text', 'et_builder' ),
				'italic'           => esc_html__( 'Italic Text', 'et_builder' ),
				'underline'        => esc_html__( 'Underline Text', 'et_builder' ),
				'link'             => esc_html__( 'Insert Link', 'et_builder' ),
				'quote'            => esc_html__( 'Insert Quote', 'et_builder' ),
				'alignment'        => esc_html__( 'Text Alignment', 'et_builder' ),
				'centerText'       => esc_html__( 'Center Text', 'et_builder' ),
				'rightText'        => esc_html__( 'Right Text', 'et_builder' ),
				'leftText'         => esc_html__( 'Left Text', 'et_builder' ),
				'justifyText'      => esc_html__( 'Justify Text', 'et_builder' ),
				'list'             => esc_html__( 'List Settings', 'et_builder' ),
				'indent'           => esc_html__( 'Indent List', 'et_builder' ),
				'undent'           => esc_html__( 'Undent List', 'et_builder' ),
				'orderedList'      => esc_html__( 'Insert Ordered List', 'et_builder' ),
				'unOrderedList'    => esc_html__( 'Insert Unordered List', 'et_builder' ),
				'text'             => esc_html__( 'Text Settings', 'et_builder' ),
				'textColor'        => esc_html__( 'Text Color', 'et_builder' ),
				'heading' => array(
					'one'   => esc_html__( 'Insert Heading One', 'et_builder' ),
					'two'   => esc_html__( 'Insert Heading Two', 'et_builder' ),
					'three' => esc_html__( 'Insert Heading Three', 'et_builder' ),
					'four'  => esc_html__( 'Insert Heading Four', 'et_builder' ),
				),
			),
			'section' => array(
				'tab' => array(
					'move'         => esc_html__( 'Move Section', 'et_builder' ),
					'settings'     => esc_html__( 'Section Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Section', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Section To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Section', 'et_builder' ),
					'exit'         => esc_html__( 'Exit Section', 'et_builder' ),
				),
				'addButton' => esc_html__( 'Add New Section', 'et_builder' ),
			),
			'row' => array(
				'tab' => array(
					'move'         => esc_html__( 'Move Row', 'et_builder' ),
					'settings'     => esc_html__( 'Row Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Row', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Row To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Row', 'et_builder' ),
					'exit'         => esc_html__( 'Exit Row', 'et_builder' ),
					'update'       => esc_html__( 'Change Column Structure', 'et_builder' ),
				),
				'addButton' => esc_html__( 'Add New Row', 'et_builder' ),
				'chooseColumn' => esc_html__( 'Choose Column Structure', 'et_builder' ),
			),
			'module' => array(
				'tab' => array(
					'move'         => esc_html__( 'Move Module', 'et_builder' ),
					'settings'     => esc_html__( 'Module Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Module', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Module To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Module', 'et_builder' ),
					'exit'         => esc_html__( 'Exit Module', 'et_builder' ),
				),
				'addButton' => esc_html__( 'Add New Module', 'et_builder' ),
			),
		),
		'unsavedConfirmation' => esc_html__( 'Unsaved changes will be lost if you leave the Divi Builder at this time.', 'et_builder' ),
		'libraryLoadError'    => esc_html__( 'Error loading Library items from server. Please refresh the page and try again.', 'et_builder' ),
		'productTourText'     => array(),
		'errorBoundaries'     => array(
			'noDisplay' => esc_html__( 'could not be displayed', 'et_builder' ),
			'hideError' => esc_html__( 'hide error message', 'et_builder' ),
			'showError' => esc_html__( 'show error message', 'et_builder' ),
			'component' => esc_html__( 'Component', 'et_builder' ),
		),
		/**
		 * @todo update vbSupport['modalSupportNotices']['off'] and vbSupport['modalSupportNotices']['partial'] once the documentation page is ready
		 */
		'vbSupport' => array(
			'modalSupportNotices' => array(
				'off' => sprintf(
					esc_html__( 'This third party module is not fully compatible with the latest version of the Divi Builder. You can still edit the module, but a preview will not be rendered in the builder. You can contact the developer of the module to encourage them to update it. <a href="%1$s" target="_blank">Click here</a> for more info.', 'et_builder' ),
					'https://www.elegantthemes.com/documentation/developers/divi-module/compatibility-levels/'
				),
				'partial' => sprintf(
					esc_html__( 'This third party module is not fully compatible with the latest version of the Divi Builder. You can still edit the module, but it will take longer to update on the page. You can contact the developer of the module to encourage them to update it. <a href="%1$s" target="_blank">Click here</a> for more info.', 'et_builder' ),
					'https://www.elegantthemes.com/documentation/developers/divi-module/compatibility-levels/'
				),
			),
			'unsupportedFieldType' => esc_html__( 'The above custom field is not fully supported and has been rendered as a standard input.' ),
		),
	);

	// Add strings from i18n directory. Note: We don't handle subdirectories, but we should in the future.
	$i18n_files = glob( ET_BUILDER_DIR . 'frontend-builder/i18n/*.php' );

	foreach ( $i18n_files as $file ) {
		$key = basename( $file, '.php' );

		$helpers['i18n'][ $key ] = require $file;
	}

	// Pass helpers via localization.
	wp_localize_script( 'et-frontend-builder', 'ETBuilderBackend', $helpers );
}

if ( ! function_exists( 'et_fb_fix_plugin_conflicts' ) ) :
function et_fb_fix_plugin_conflicts() {
	// Disable Autoptimize plugin
	remove_action( 'init', 'autoptimize_start_buffering', -1 );
	remove_action( 'template_redirect', 'autoptimize_start_buffering', 2 );
}
endif;

/**
 * Retrieves all WP taxonomies for Visual Builder
 *
 * @return array
 */
if ( ! function_exists( 'et_fb_get_taxonomy_terms' ) ) :
function et_fb_get_taxonomy_terms() {
	$result = array();

	$taxonomies = get_taxonomies();
	foreach ( $taxonomies as $taxonomy => $name ) {
		$terms = get_terms( $name, array( 'hide_empty' => false ) );
		if ( $terms ) {
			$result[ $name ] = $terms;
		}
	}

	return $result;
}
endif;

/**
 * Retrieves all WP taxonomies labels for Visual Builder
 *
 * @return array
 */
if ( ! function_exists( 'et_fb_get_taxonomy_labels' ) ) :
function et_fb_get_taxonomy_labels() {
	$result = array();

	foreach ( get_taxonomies() as $tax => $name ) {
		$taxonomy = get_taxonomy( $name );
		if ( $taxonomy ) {
			$result[ $name ] = get_taxonomy_labels( $taxonomy );
		}
	}

	return $result;
}
endif;
