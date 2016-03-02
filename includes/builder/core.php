<?php

function et_builder_register_layouts(){
	$labels = array(
		'name'               => esc_html_x( 'Layouts', 'Layout type general name', 'et_builder' ),
		'singular_name'      => esc_html_x( 'Layout', 'Layout type singular name', 'et_builder' ),
		'add_new'            => esc_html_x( 'Add New', 'Layout item', 'et_builder' ),
		'add_new_item'       => esc_html__( 'Add New Layout', 'et_builder' ),
		'edit_item'          => esc_html__( 'Edit Layout', 'et_builder' ),
		'new_item'           => esc_html__( 'New Layout', 'et_builder' ),
		'all_items'          => esc_html__( 'All Layouts', 'et_builder' ),
		'view_item'          => esc_html__( 'View Layout', 'et_builder' ),
		'search_items'       => esc_html__( 'Search Layouts', 'et_builder' ),
		'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
		'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => false,
		'publicly_queryable' => false,
		'can_export'         => true,
		'query_var'          => false,
		'has_archive'        => false,
		'capability_type'    => 'post',
		'map_meta_cap'       => true,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'revisions' ),
	);

	// Cannot use is_et_pb_preview() because it's too early
	if ( isset( $_GET['et_pb_preview'] ) && ( isset( $_GET['et_pb_preview_nonce'] ) && wp_verify_nonce( $_GET['et_pb_preview_nonce'], 'et_pb_preview_nonce' ) ) ) {
		$args['publicly_queryable'] = true;
	}

	if ( ! defined( 'ET_BUILDER_LAYOUT_POST_TYPE' ) ) {
		define( 'ET_BUILDER_LAYOUT_POST_TYPE', 'et_pb_layout' );
	}

	register_post_type( ET_BUILDER_LAYOUT_POST_TYPE, apply_filters( 'et_pb_layout_args', $args ) );

	$labels = array(
		'name'              => esc_html__( 'Scope', 'et_builder' )
	);

	register_taxonomy( 'scope', array( 'et_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Layout Type', 'et_builder' )
	);

	register_taxonomy( 'layout_type', array( 'et_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Module Width', 'et_builder' )
	);

	register_taxonomy( 'module_width', array( 'et_pb_layout' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );

	$labels = array(
		'name'              => esc_html__( 'Category', 'et_builder' )
	);

	register_taxonomy( 'layout_category', array( 'et_pb_layout' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_nav_menus' => false,
	) );
}
et_builder_register_layouts();

function et_pb_video_get_oembed_thumbnail() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$video_url = esc_url( $_POST['et_video_url'] );
	if ( false !== wp_oembed_get( $video_url ) ) {
		// Get image thumbnail
		add_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
		// Save thumbnail
		$image_src = wp_oembed_get( $video_url );
		// Set back to normal
		remove_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
		if ( '' === $image_src ) {
			die( -1 );
		}
		echo esc_url( $image_src );
	} else {
		die( -1 );
	}
	die();
}
add_action( 'wp_ajax_et_pb_video_get_oembed_thumbnail', 'et_pb_video_get_oembed_thumbnail' );

if ( ! function_exists( 'et_pb_video_oembed_data_parse' ) ) :
function et_pb_video_oembed_data_parse( $return, $data, $url ) {
	if ( isset( $data->thumbnail_url ) ) {
		return esc_url( str_replace( array('https://', 'http://'), '//', $data->thumbnail_url ), array('http') );
	} else {
		return false;
	}
}
endif;

function et_pb_add_widget_area(){
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );

	$number = $et_pb_widgets ? intval( $et_pb_widgets['number'] ) + 1 : 1;

	$et_widget_area_name = sanitize_text_field( $_POST['et_widget_area_name'] );
	$et_pb_widgets['areas']['et_pb_widget_area_' . $number] = $et_widget_area_name;
	$et_pb_widgets['number'] = $number;

	set_theme_mod( 'et_pb_widgets', $et_pb_widgets );

	et_pb_force_regenerate_templates();

	printf( et_get_safe_localization( __( '<strong>%1$s</strong> widget area has been created. You can create more areas, once you finish update the page to see all the areas.', 'et_builder' ) ),
		esc_html( $et_widget_area_name )
	);

	die();
}
add_action( 'wp_ajax_et_pb_add_widget_area', 'et_pb_add_widget_area' );

function et_pb_remove_widget_area(){
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );

	$et_widget_area_name = sanitize_text_field( $_POST['et_widget_area_name'] );
	unset( $et_pb_widgets['areas'][ $et_widget_area_name ] );

	set_theme_mod( 'et_pb_widgets', $et_pb_widgets );

	et_pb_force_regenerate_templates();

	die( esc_html( $et_widget_area_name ) );
}
add_action( 'wp_ajax_et_pb_remove_widget_area', 'et_pb_remove_widget_area' );

function et_pb_current_user_can_lock() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$permission = et_pb_is_allowed( 'lock_module' );
	$permission = json_encode( $permission );

	die( $permission );
}
add_action( 'wp_ajax_et_pb_current_user_can_lock', 'et_pb_current_user_can_lock' );

function et_builder_get_builder_post_types() {
	return apply_filters( 'et_builder_post_types', array(
		'page',
		'project',
		'et_pb_layout',
		'post',
	) );
}

/**
 * Check whether the specified capability allowed for current user
 * @return bool
 */
function et_pb_is_allowed( $capability, $role = '' ) {
	$saved_capabilities = et_pb_get_role_settings();
	$role = '' === $role ? et_pb_get_current_user_role() : $role;

	if ( ! empty( $saved_capabilities[ $role ][ $capability ] ) ) {
		$verdict = 'off' === $saved_capabilities[ $role ][ $capability ] ? false : true;
	} else {
		return true;
	}

	return $verdict;
}

/**
 * Gets the array of role settings
 * @return string
 */
function et_pb_get_role_settings() {
	global $et_pb_role_settings;

	// if we don't have saved global variable, then get the value from WPDB
	$et_pb_role_settings = isset( $et_pb_role_settings ) ? $et_pb_role_settings : get_option( 'et_pb_role_settings', array() );

	return $et_pb_role_settings;
}

/**
 * Determines the current user role
 * @return string
 */
function et_pb_get_current_user_role() {
	$current_user = wp_get_current_user();
	$user_roles = $current_user->roles;

	$role = ! empty( $user_roles ) ? $user_roles[0] : '';

	return $role;
}

function et_pb_show_all_layouts_built_for_post_type( $post_type ) {
	$similar_post_types = array(
		'post',
		'page',
		'project',
	);

	if ( in_array( $post_type, $similar_post_types ) ) {
		return $similar_post_types;
	}

	return $post_type;
}
add_filter( 'et_pb_show_all_layouts_built_for_post_type', 'et_pb_show_all_layouts_built_for_post_type' );

function et_pb_show_all_layouts() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	printf( '
		<label for="et_pb_load_layout_replace">
			<input name="et_pb_load_layout_replace" type="checkbox" id="et_pb_load_layout_replace" %2$s/>
			%1$s
		</label>',
		esc_html__( 'Replace the existing content with loaded layout', 'et_builder' ),
		checked( get_theme_mod( 'et_pb_replace_content', 'on' ), 'on', false )
	);

	$post_type = ! empty( $_POST['et_layouts_built_for_post_type'] ) ? sanitize_text_field( $_POST['et_layouts_built_for_post_type'] ) : 'post';
	$layouts_type = ! empty( $_POST['et_load_layouts_type'] ) ? sanitize_text_field( $_POST['et_load_layouts_type'] ) : 'predefined';

	$predefined_operator = 'predefined' === $layouts_type ? 'EXISTS' : 'NOT EXISTS';

	$post_type = apply_filters( 'et_pb_show_all_layouts_built_for_post_type', $post_type, $layouts_type );

	$query_args = array(
		'meta_query'      => array(
			'relation' => 'AND',
			array(
				'key'     => '_et_pb_predefined_layout',
				'value'   => 'on',
				'compare' => $predefined_operator,
			),
			array(
				'key'     => '_et_pb_built_for_post_type',
				'value'   => $post_type,
				'compare' => 'IN',
			),
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    => array( 'section', 'row', 'module', 'fullwidth_section', 'specialty_section', 'fullwidth_module' ),
				'operator' => 'NOT IN',
			),
		),
		'post_type'       => ET_BUILDER_LAYOUT_POST_TYPE,
		'posts_per_page'  => '-1',
	);

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) :

		echo '<ul class="et-pb-all-modules et-pb-load-layouts">';

		while ( $query->have_posts() ) : $query->the_post();

			printf( '<li class="et_pb_text" data-layout_id="%2$s">%1$s<span class="et_pb_layout_buttons"><a href="#" class="button-primary et_pb_layout_button_load">%3$s</a>%4$s</span></li>',
				esc_html( get_the_title() ),
				esc_attr( get_the_ID() ),
				esc_html__( 'Load', 'et_builder' ),
				'predefined' !== $layouts_type ?
					sprintf( '<a href="#" class="button et_pb_layout_button_delete">%1$s</a>',
						esc_html__( 'Delete', 'et_builder' )
					)
					: ''
			);

		endwhile;

		echo '</ul>';
	endif;

	wp_reset_postdata();

	die();
}
add_action( 'wp_ajax_et_pb_show_all_layouts', 'et_pb_show_all_layouts' );

function et_pb_get_saved_templates() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$templates_data = array();

	$layout_type = ! empty( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : 'layout';
	$module_width = ! empty( $_POST['et_module_width'] ) && 'module' === $layout_type ? sanitize_text_field( $_POST['et_module_width'] ) : '';
	$additional_condition = '' !== $module_width ?
		array(
				'taxonomy' => 'module_width',
				'field'    => 'slug',
				'terms'    =>  $module_width,
			) : '';
	$is_global = ! empty( $_POST['et_is_global'] ) ? sanitize_text_field( $_POST['et_is_global'] ) : 'false';
	$global_operator = 'global' === $is_global ? 'IN' : 'NOT IN';

	$meta_query = array();
	$specialty_query = ! empty( $_POST['et_specialty_columns'] ) && 'row' === $layout_type ? sanitize_text_field( $_POST['et_specialty_columns'] ) : '0';

	if ( '0' !== $specialty_query ) {
		$columns_val = '3' === $specialty_query ? array( '4_4', '1_2,1_2', '1_3,1_3,1_3' ) : array( '4_4', '1_2,1_2' );
		$meta_query[] = array(
			'key'     => '_et_pb_row_layout',
			'value'   => $columns_val,
			'compare' => 'IN',
		);
	}

	$post_type = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';
	$post_type = apply_filters( 'et_pb_show_all_layouts_built_for_post_type', $post_type, $layout_type );
	$meta_query[] = array(
		'key'     => '_et_pb_built_for_post_type',
		'value'   => $post_type,
		'compare' => 'IN',
	);


	$query = new WP_Query( array(
		'tax_query' => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    =>  $layout_type,
			),
			array(
				'taxonomy' => 'scope',
				'field'    => 'slug',
				'terms'    => array( 'global' ),
				'operator' => $global_operator,
			),
			$additional_condition,
		),
		'post_type'       => ET_BUILDER_LAYOUT_POST_TYPE,
		'posts_per_page'  => '-1',
		'meta_query'      => $meta_query,
	) );

	wp_reset_postdata();

	if ( ! empty ( $query->posts ) ) {
		foreach( $query->posts as $single_post ) {

			if ( 'module' === $layout_type ) {
				$module_type = get_post_meta( $single_post->ID, '_et_pb_module_type', true );
			} else {
				$module_type = '';
			}

			// add only modules allowed for current user
			if ( '' === $module_type || et_pb_is_allowed( $module_type ) ) {
				$categories = wp_get_post_terms( $single_post->ID, 'layout_category' );
				$categories_processed = array();

				if ( ! empty( $categories ) ) {
					foreach( $categories as $category_data ) {
						$categories_processed[] = esc_html( $category_data->slug );
					}
				}

				$templates_data[] = array(
					'ID'          => $single_post->ID,
					'title'       => esc_html( $single_post->post_title ),
					'shortcode'   => $single_post->post_content,
					'is_global'   => $is_global,
					'layout_type' => $layout_type,
					'module_type' => $module_type,
					'categories'  => $categories_processed,
				);
			}
		}
	}
	if ( empty( $templates_data ) ) {
		$templates_data = array( 'error' => esc_html__( 'You have not saved any items to your Divi Library yet. Once an item has been saved to your library, it will appear here for easy use.', 'et_builder' ) );
	}

	$json_templates = json_encode( $templates_data );

	die( $json_templates );
}
add_action( 'wp_ajax_et_pb_get_saved_templates', 'et_pb_get_saved_templates' );

function et_pb_add_template_meta() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die(-1);

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = ! empty( $_POST['et_post_id'] ) ? sanitize_text_field( $_POST['et_post_id'] ) : '';
	$value = ! empty( $_POST['et_meta_value'] ) ? sanitize_text_field( $_POST['et_meta_value'] ) : '';
	$custom_field = ! empty( $_POST['et_custom_field'] ) ? sanitize_text_field( $_POST['et_custom_field'] ) : '';

	if ( '' !== $post_id ){
		update_post_meta( $post_id, $custom_field, $value );
	}
}
add_action( 'wp_ajax_et_pb_add_template_meta', 'et_pb_add_template_meta' );

if ( ! function_exists( 'et_pb_add_new_layout' ) ) {
	function et_pb_add_new_layout() {
		if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

		if ( ! current_user_can( 'edit_posts' ) ) {
			die( -1 );
		}

		$fields_data = isset( $_POST['et_layout_options'] ) ? $_POST['et_layout_options'] : '';

		if ( '' === $fields_data ) {
			die();
		}

		$fields_data_json = str_replace( '\\', '',  $fields_data );
		$fields_data_array = json_decode( $fields_data_json, true );
		$processed_data_array = array();

		// prepare array with fields data in convenient format
		if ( ! empty( $fields_data_array ) ) {
			foreach ( $fields_data_array as $index => $field_data ) {
				$processed_data_array[ $field_data['field_id'] ] = $field_data['field_val'];
			}
		}

		$processed_data_array = apply_filters( 'et_pb_new_layout_data_from_form', $processed_data_array, $fields_data_array );

		if ( empty( $processed_data_array ) ) {
			die();
		}

		$args = array(
			'layout_type'          => ! empty( $processed_data_array['new_template_type'] ) ? sanitize_text_field( $processed_data_array['new_template_type'] ) : 'layout',
			'layout_selected_cats' => ! empty( $processed_data_array['selected_cats'] ) ? sanitize_text_field( $processed_data_array['selected_cats'] ) : '',
			'built_for_post_type'  => ! empty( $processed_data_array['et_builder_layout_built_for_post_type'] ) ? sanitize_text_field( $processed_data_array['et_builder_layout_built_for_post_type'] ) : 'page',
			'layout_new_cat'       => ! empty( $processed_data_array['et_pb_new_cat_name'] ) ? sanitize_text_field( $processed_data_array['et_pb_new_cat_name'] ) : '',
			'columns_layout'       => ! empty( $processed_data_array['et_columns_layout'] ) ? sanitize_text_field( $processed_data_array['et_columns_layout'] ) : '0',
			'module_type'          => ! empty( $processed_data_array['et_module_type'] ) ? sanitize_text_field( $processed_data_array['et_module_type'] ) : 'et_pb_unknown',
			'layout_scope'         => ! empty( $processed_data_array['et_pb_template_global'] ) ? sanitize_text_field( $processed_data_array['et_pb_template_global'] ) : 'not_global',
			'module_width'         => 'regular',
			'layout_content'       => ! empty( $processed_data_array['template_shortcode'] ) ? $processed_data_array['template_shortcode'] : '',
			'layout_name'          => ! empty( $processed_data_array['et_pb_new_template_name'] ) ? sanitize_text_field( $processed_data_array['et_pb_new_template_name'] ) : '',
		);

		// construct the initial shortcode for new layout
		switch ( $args['layout_type'] ) {
			case 'row' :
				$args['layout_content'] = '[et_pb_row template_type="row"][/et_pb_row]';
				break;
			case 'section' :
				$args['layout_content'] = '[et_pb_section template_type="section"][et_pb_row][/et_pb_row][/et_pb_section]';
				break;
			case 'module' :
				$args['layout_content'] = sprintf( '[et_pb_module_placeholder selected_tabs="%1$s"]', ! empty( $processed_data_array['selected_tabs'] ) ? $processed_data_array['selected_tabs'] : 'all' );
				break;
			case 'fullwidth_module' :
				$args['layout_content'] = sprintf( '[et_pb_fullwidth_module_placeholder selected_tabs="%1$s"]', ! empty( $processed_data_array['selected_tabs'] ) ? $processed_data_array['selected_tabs'] : 'all' );
				$args['module_width'] = 'fullwidth';
				$args['layout_type'] = 'module';
				break;
			case 'fullwidth_section' :
				$args['layout_content'] = '[et_pb_section template_type="section" fullwidth="on"][/et_pb_section]';
				$args['layout_type'] = 'section';
				break;
			case 'specialty_section' :
				$args['layout_content'] = '[et_pb_section template_type="section" specialty="on" skip_module="true" specialty_placeholder="true"][/et_pb_section]';
				$args['layout_type'] = 'section';
				break;
		}

		$new_layout_meta = et_pb_submit_layout( apply_filters( 'et_pb_new_layout_args', $args ) );
		die( $new_layout_meta );
	}
}
add_action( 'wp_ajax_et_pb_add_new_layout', 'et_pb_add_new_layout' );

if ( ! function_exists( 'et_pb_submit_layout' ) ) {
	function et_pb_submit_layout( $args ) {
		if ( empty( $args ) ) {
			return;
		}

		$layout_cats_processed = array();

		if ( '' !== $args['layout_selected_cats'] ) {
			$layout_cats_array = explode( ',', $args['layout_selected_cats'] );
			$layout_cats_processed = array_map( 'intval', $layout_cats_array );
		}

		$meta = array();

		if ( 'row' === $args['layout_type'] && '0' !== $args['columns_layout'] ) {
			$meta = array_merge( $meta, array( '_et_pb_row_layout' => $args['columns_layout'] ) );
		}

		if ( 'module' === $args['layout_type'] ) {
			$meta = array_merge( $meta, array( '_et_pb_module_type' => $args['module_type'] ) );
		}

		//et_layouts_built_for_post_type
		$meta = array_merge( $meta, array( '_et_pb_built_for_post_type' => $args['built_for_post_type'] ) );

		$tax_input = array(
			'scope'           => $args['layout_scope'],
			'layout_type'     => $args['layout_type'],
			'module_width'    => $args['module_width'],
			'layout_category' => $layout_cats_processed,
		);

		$new_layout_id = et_pb_create_layout( $args['layout_name'], $args['layout_content'], $meta, $tax_input, $args['layout_new_cat'] );
		$new_post_data['post_id'] = $new_layout_id;

		$new_post_data['edit_link'] = htmlspecialchars_decode( get_edit_post_link( $new_layout_id ) );
		$json_post_data = json_encode( $new_post_data );

		return $json_post_data;
	}
}

if ( ! function_exists( 'et_pb_create_layout' ) ) :
function et_pb_create_layout( $name, $content, $meta = array(), $tax_input = array(), $new_category = '' ) {
	$layout = array(
		'post_title'   => sanitize_text_field( $name ),
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => ET_BUILDER_LAYOUT_POST_TYPE,
	);

	$layout_id = wp_insert_post( $layout );

	if ( !empty( $meta ) ) {
		foreach ( $meta as $meta_key => $meta_value ) {
			add_post_meta( $layout_id, $meta_key, sanitize_text_field( $meta_value ) );
		}
	}
	if ( '' !== $new_category ) {
		$new_term_id = wp_insert_term( $new_category, 'layout_category' );
		$tax_input['layout_category'][] = (int) $new_term_id['term_id'];
	}

	if ( ! empty( $tax_input ) ) {
		foreach( $tax_input as $taxonomy => $terms ) {
			wp_set_post_terms( $layout_id, $terms, $taxonomy );
		}
	}

	return $layout_id;
}
endif;

function et_pb_save_layout() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	if ( empty( $_POST['et_layout_name'] ) ) {
		die();
	}

	$args = array(
		'layout_type'          => isset( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : 'layout',
		'layout_selected_cats' => isset( $_POST['et_layout_cats'] ) ? sanitize_text_field( $_POST['et_layout_cats'] ) : '',
		'built_for_post_type'  => isset( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'page',
		'layout_new_cat'       => isset( $_POST['et_layout_new_cat'] ) ? sanitize_text_field( $_POST['et_layout_new_cat'] ) : '',
		'columns_layout'       => isset( $_POST['et_columns_layout'] ) ? sanitize_text_field( $_POST['et_columns_layout'] ) : '0',
		'module_type'          => isset( $_POST['et_module_type'] ) ? sanitize_text_field( $_POST['et_module_type'] ) : 'et_pb_unknown',
		'layout_scope'         => isset( $_POST['et_layout_scope'] ) ? sanitize_text_field( $_POST['et_layout_scope'] ) : 'not_global',
		'module_width'         => isset( $_POST['et_module_width'] ) ? sanitize_text_field( $_POST['et_module_width'] ) : 'regular',
		'layout_content'       => isset( $_POST['et_layout_content'] ) ? $_POST['et_layout_content'] : '',
		'layout_name'          => isset( $_POST['et_layout_name'] ) ? sanitize_text_field( $_POST['et_layout_name'] ) : '',
	);

	$new_layout_meta = et_pb_submit_layout( $args );
	die( $new_layout_meta );
}
add_action( 'wp_ajax_et_pb_save_layout', 'et_pb_save_layout' );

function et_pb_get_global_module() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = isset( $_POST['et_global_id'] ) ? $_POST['et_global_id'] : '';

	if ( '' !== $post_id ) {
		$query = new WP_Query( array(
			'p'         => (int) $post_id,
			'post_type' => ET_BUILDER_LAYOUT_POST_TYPE
		) );

		wp_reset_postdata();

		if ( !empty( $query->post ) ) {
			$global_shortcode['shortcode'] = $query->post->post_content;
		}
	}

	if ( empty( $global_shortcode ) ) {
		$global_shortcode['error'] = 'nothing';
	}

	$json_post_data = json_encode( $global_shortcode );

	die( $json_post_data );
}
add_action( 'wp_ajax_et_pb_get_global_module', 'et_pb_get_global_module' );

function et_pb_update_layout() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = isset( $_POST['et_template_post_id'] ) ? $_POST['et_template_post_id'] : '';
	$new_content = isset( $_POST['et_layout_content'] ) ? et_pb_builder_post_content_capability_check( $_POST['et_layout_content'] ) : '';

	if ( '' !== $post_id ) {
		$update = array(
			'ID'           => $post_id,
			'post_content' => $new_content,
		);

		wp_update_post( $update );
	}

	die();
}
add_action( 'wp_ajax_et_pb_update_layout', 'et_pb_update_layout' );

function et_pb_builder_post_content_capability_check( $content) {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		$content = preg_replace_callback('/\[et_pb_code .*\](.*)\[\/et_pb_code\]/mis', '_et_pb_sanitize_code_module_content_regex', $content );
		$content = preg_replace_callback('/\[et_pb_fullwidth_code .*\](.*)\[\/et_pb_fullwidth_code\]/mis', '_et_pb_sanitize_code_module_content_regex', $content );
	}

	return $content;
}
add_filter( 'content_save_pre', 'et_pb_builder_post_content_capability_check' );

function et_pb_load_layout() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) die( -1 );

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$layout_id = (int) $_POST['et_layout_id'];

	if ( '' === $layout_id ) die( -1 );

	$replace_content = isset( $_POST['et_replace_content'] ) && 'on' === $_POST['et_replace_content'] ? 'on' : 'off';

	set_theme_mod( 'et_pb_replace_content', $replace_content );

	$layout = get_post( $layout_id );

	if ( $layout )
		echo $layout->post_content;

	die();
}
add_action( 'wp_ajax_et_pb_load_layout', 'et_pb_load_layout' );

function et_pb_delete_layout() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_others_posts' ) ) {
		die( -1 );
	}

	$layout_id = (int) $_POST['et_layout_id'];

	if ( '' === $layout_id ) die( -1 );

	wp_delete_post( $layout_id );

	die();
}
add_action( 'wp_ajax_et_pb_delete_layout', 'et_pb_delete_layout' );

function et_pb_get_backbone_templates() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_type = sanitize_text_field( $_POST['et_post_type'] );
	$start_from = isset( $_POST['et_templates_start_from'] ) ? sanitize_text_field( $_POST['et_templates_start_from'] ) : 0;
	$amount = ET_BUILDER_AJAX_TEMPLATES_AMOUNT;

	// get the portion of templates
	$result = json_encode( ET_Builder_Element::output_templates( $post_type, $start_from, $amount ) );

	die( $result );
}
add_action( 'wp_ajax_et_pb_get_backbone_templates', 'et_pb_get_backbone_templates' );

function et_pb_get_backbone_template() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$module_slugs = json_decode( str_replace( '\\', '', sanitize_text_field( $_POST['et_modules_slugs'] ) ) );
	$post_type   = sanitize_text_field( $_POST['et_post_type'] );

	// get the portion of templates for specified slugs
	$result = json_encode( ET_Builder_Element::get_modules_templates( $post_type, $module_slugs->missing_modules_array ) );

	die( $result );
}
add_action( 'wp_ajax_et_pb_get_backbone_template', 'et_pb_get_backbone_template' );

function et_pb_submit_subscribe_form() {
	if ( ! wp_verify_nonce( $_POST['et_frontend_nonce'], 'et_frontend_nonce' ) ) die( json_encode( array( 'error' => esc_html__( 'Configuration error', 'et_builder' ) ) ) );

	$service = sanitize_text_field( $_POST['et_service'] );

	$list_id = sanitize_text_field( $_POST['et_list_id'] );

	$email = sanitize_email( $_POST['et_email'] );

	$firstname = sanitize_text_field( $_POST['et_firstname'] );

	if ( '' === $firstname ) die( json_encode( array( 'error' => esc_html__( 'Please enter first name', 'et_builder' ) ) ) );

	if ( ! is_email( $email ) ) die( json_encode( array( 'error' => esc_html__( 'Incorrect email', 'et_builder' ) ) ) );

	if ( '' == $list_id ) die( json_encode( array( 'error' => esc_html__( 'Configuration error: List is not defined', 'et_builder' ) ) ) );

	$success_message = sprintf(
		'<h2 class="et_pb_subscribed">%s</h2>',
		esc_html__( 'Subscribed - look for the confirmation email!', 'et_builder' )
	);

	switch ( $service ) {
		case 'mailchimp' :
			$lastname = sanitize_text_field( $_POST['et_lastname'] );
			$email = array( 'email' => $email );

			if ( ! class_exists( 'MailChimp_Divi' ) )
				require_once( ET_BUILDER_DIR . 'subscription/mailchimp/mailchimp.php' );

			if ( et_is_builder_plugin_active() ) {
				$mailchimp_api_option = get_option( 'et_pb_builder_options' );
				$mailchimp_api_key = isset( $mailchimp_api_option['newsletter_main_mailchimp_key'] ) ? $mailchimp_api_option['newsletter_main_mailchimp_key'] : '';
			} else {
				$mailchimp_api_key = et_get_option( 'divi_mailchimp_api_key' );
			}

			if ( '' === $mailchimp_api_key ) die( json_encode( array( 'error' => esc_html__( 'Configuration error: api key is not defined', 'et_builder' ) ) ) );
				$mailchimp = new MailChimp_Divi( $mailchimp_api_key );

				$subscribe_args = array(
					'id'         => $list_id,
					'email'      => $email,
					'merge_vars' => array(
						'FNAME'  => $firstname,
						'LNAME'  => $lastname,
					),
				);

				$retval =  $mailchimp->call('lists/subscribe', $subscribe_args );

				if ( 200 !== wp_remote_retrieve_response_code( $retval ) ) {
					if ( '214' === wp_remote_retrieve_header( $retval, 'x-mailchimp-api-error-code' ) ) {
						$mailchimp_message = json_decode( wp_remote_retrieve_body( $retval ), true );
						$error_message = isset( $mailchimp_message['error'] ) ? $mailchimp_message['error'] : wp_remote_retrieve_body( $retval );
						$result = json_encode( array( 'success' => esc_html( $error_message ) ) );
					} else {
						$result = json_encode( array( 'success' => esc_html( wp_remote_retrieve_response_message( $retval ) ) ) );
					}
				} else {
					$result = json_encode( array( 'success' => $success_message ) );
				}

			die( $result );
			break;
		case 'aweber' :
			if ( ! class_exists( 'AWeberAPI' ) ) {
				require_once( ET_BUILDER_DIR . 'subscription/aweber/aweber_api.php' );
			}

			$account = et_pb_get_aweber_account();

			if ( ! $account ) {
				die( json_encode( array( 'error' => esc_html__( 'Aweber: Wrong configuration data', 'et_builder' ) ) ) );
			}

			try {
				$list_url = "/accounts/{$account->id}/lists/{$list_id}";
				$list = $account->loadFromUrl( $list_url );

				$new_subscriber = $list->subscribers->create(
					array(
						'email' => $email,
						'name'  => $firstname,
					)
				);

				die( json_encode( array( 'success' => $success_message ) ) );
			} catch ( Exception $exc ) {
				die( json_encode( array( 'error' => esc_html( $exc->message ) ) ) );
			}

			break;
	}

	die();
}
add_action( 'wp_ajax_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );
add_action( 'wp_ajax_nopriv_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );

/*
 * Is Builder plugin active?
 *
 * @return bool  True - if the plugin is active
 */
if ( ! function_exists( 'et_is_builder_plugin_active' ) ) :
function et_is_builder_plugin_active() {
	return (bool) defined( 'ET_BUILDER_PLUGIN_ACTIVE' );
}
endif;

if ( ! function_exists( 'et_pb_get_aweber_account' ) ) :
function et_pb_get_aweber_account() {
	if ( ! class_exists( 'AWeberAPI' ) ) {
		require_once( ET_BUILDER_DIR . 'subscription/aweber/aweber_api.php' );
	}
	if ( et_is_builder_plugin_active() ) {
		$aweber_api_option = get_option( 'et_pb_builder_options' );
		$consumer_key = isset( $aweber_api_option['aweber_consumer_key'] ) ? $aweber_api_option['aweber_consumer_key'] : '';
		$consumer_secret = isset( $aweber_api_option['aweber_consumer_secret'] ) ? $aweber_api_option['aweber_consumer_secret'] : '';
		$access_key = isset( $aweber_api_option['aweber_access_key'] ) ? $aweber_api_option['aweber_access_key'] : '';
		$access_secret = isset( $aweber_api_option['aweber_access_secret'] ) ? $aweber_api_option['aweber_access_secret'] : '';
	} else {
		$consumer_key = et_get_option( 'divi_aweber_consumer_key' );
		$consumer_secret = et_get_option( 'divi_aweber_consumer_secret' );
		$access_key = et_get_option( 'divi_aweber_access_key' );
		$access_secret = et_get_option( 'divi_aweber_access_secret' );
	}

	if ( ! empty( $consumer_key ) && ! empty( $consumer_secret ) && ! empty( $access_key ) && ! empty( $access_secret ) ) {
		try {
			// Aweber requires curl extension to be enabled
			if ( ! function_exists( 'curl_init' ) ) {
				return false;
			}

			$aweber = new AWeberAPI( $consumer_key, $consumer_secret );

			if ( ! $aweber ) {
				return false;
			}

			$account = $aweber->getAccount( $access_key, $access_secret );
		} catch ( Exception $exc ) {
			return false;
		}
	} else {
		return false;
	}

	return $account;
}
endif;

function et_aweber_submit_authorization_code() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( esc_html__( 'Nonce failed.', 'et_builder' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	$et_authorization_code = sanitize_text_field( $_POST['et_authorization_code'] );

	if ( '' === $et_authorization_code ) {
		die( esc_html__( 'Authorization code is empty.', 'et_builder' ) );
	}

	if ( ! class_exists( 'AWeberAPI' ) ) {
		require_once( ET_BUILDER_DIR . 'subscription/aweber/aweber_api.php' );
	}

	try {
		$auth = AWeberAPI::getDataFromAweberID( $et_authorization_code );

		if ( ! ( is_array( $auth ) && 4 === count( $auth ) ) ) {
			die ( esc_html__( 'Authorization code is invalid. Try regenerating it and paste in the new code.', 'et_builder' ) );
		}

		list( $consumer_key, $consumer_secret, $access_key, $access_secret ) = $auth;

		et_update_option( 'divi_aweber_consumer_key', $consumer_key );
		et_update_option( 'divi_aweber_consumer_secret', $consumer_secret );
		et_update_option( 'divi_aweber_access_key', $access_key );
		et_update_option( 'divi_aweber_access_secret', $access_secret );

		die( 'success' );
	} catch ( AWeberAPIException $exc ) {
		printf(
			'<p>%4$s.</p>
			<ul>
				<li>%5$s: %1$s</li>
				<li>%6$s: %2$s</li>
				<li>%7$s: %3$s</li>
			</ul>',
			esc_html( $exc->type ),
			esc_html( $exc->message ),
			esc_html( $exc->documentation_url ),
			esc_html__( 'Aweber API Exception', 'et_builder' ),
			esc_html__( 'Type', 'et_builder' ),
			esc_html__( 'Message', 'et_builder' ),
			esc_html__( 'Documentation', 'et_builder' )
		);
	}

	die();
}
add_action( 'wp_ajax_et_aweber_submit_authorization_code', 'et_aweber_submit_authorization_code' );

function et_aweber_remove_connection() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( esc_html__( 'Nonce failed', 'et_builder' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	et_delete_option( 'divi_aweber_consumer_key' );
	et_delete_option( 'divi_aweber_consumer_secret' );
	et_delete_option( 'divi_aweber_access_key' );
	et_delete_option( 'divi_aweber_access_secret' );

	die( 'success' );
}
add_action( 'wp_ajax_et_aweber_remove_connection', 'et_aweber_remove_connection' );

/**
 * Saves the Role Settings into WP database
 * @return void
 */
function et_pb_save_role_settings() {
	if ( ! wp_verify_nonce( $_POST['et_pb_save_roles_nonce'] , 'et_roles_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	// handle received data and convert json string to array
	$data_json = str_replace( '\\', '' ,  $_POST['et_pb_options_all'] );
	$data = json_decode( $data_json, true );
	$processed_options = array();

	// convert settings string for each role into array and save it into et_pb_role_settings option
	if ( ! empty( $data ) ) {
		foreach( $data as $role => $settings ) {
			parse_str( $data[ $role ], $processed_options[ $role ] );
		}
	}

	update_option( 'et_pb_role_settings', $processed_options );

	die();
}
add_action( 'wp_ajax_et_pb_save_role_settings', 'et_pb_save_role_settings' );

function et_pb_execute_content_shortcodes() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$unprocessed_data = $_POST['et_pb_unprocessed_data'];

	echo do_shortcode( $unprocessed_data );
}
add_action( 'wp_ajax_et_pb_execute_content_shortcodes', 'et_pb_execute_content_shortcodes' );

/**
 * Update et_pb_cache_notice option to indicate that Cache Notice was closed for current version of theme
 */
function et_pb_hide_cache_notice() {
	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	update_option(
		'et_pb_cache_notice',
		array(
			ET_BUILDER_VERSION => 'ignore',
		)
	);
}
add_action( 'wp_ajax_et_pb_hide_cache_notice', 'et_pb_hide_cache_notice' );

if ( ! function_exists( 'et_pb_register_posttypes' ) ) :
function et_pb_register_posttypes() {
	$labels = array(
		'name'               => esc_html__( 'Projects', 'et_builder' ),
		'singular_name'      => esc_html__( 'Project', 'et_builder' ),
		'add_new'            => esc_html__( 'Add New', 'et_builder' ),
		'add_new_item'       => esc_html__( 'Add New Project', 'et_builder' ),
		'edit_item'          => esc_html__( 'Edit Project', 'et_builder' ),
		'new_item'           => esc_html__( 'New Project', 'et_builder' ),
		'all_items'          => esc_html__( 'All Projects', 'et_builder' ),
		'view_item'          => esc_html__( 'View Project', 'et_builder' ),
		'search_items'       => esc_html__( 'Search Projects', 'et_builder' ),
		'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
		'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'can_export'         => true,
		'show_in_nav_menus'  => true,
		'query_var'          => true,
		'has_archive'        => true,
		'rewrite'            => apply_filters( 'et_project_posttype_rewrite_args', array(
			'feeds'      => true,
			'slug'       => 'project',
			'with_front' => false,
		) ),
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
	);

	register_post_type( 'project', apply_filters( 'et_project_posttype_args', $args ) );

	$labels = array(
		'name'              => esc_html__( 'Project Categories', 'et_builder' ),
		'singular_name'     => esc_html__( 'Project Category', 'et_builder' ),
		'search_items'      => esc_html__( 'Search Categories', 'et_builder' ),
		'all_items'         => esc_html__( 'All Categories', 'et_builder' ),
		'parent_item'       => esc_html__( 'Parent Category', 'et_builder' ),
		'parent_item_colon' => esc_html__( 'Parent Category:', 'et_builder' ),
		'edit_item'         => esc_html__( 'Edit Category', 'et_builder' ),
		'update_item'       => esc_html__( 'Update Category', 'et_builder' ),
		'add_new_item'      => esc_html__( 'Add New Category', 'et_builder' ),
		'new_item_name'     => esc_html__( 'New Category Name', 'et_builder' ),
		'menu_name'         => esc_html__( 'Categories', 'et_builder' ),
	);

	register_taxonomy( 'project_category', array( 'project' ), array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );

	$labels = array(
		'name'              => esc_html__( 'Project Tags', 'et_builder' ),
		'singular_name'     => esc_html__( 'Project Tag', 'et_builder' ),
		'search_items'      => esc_html__( 'Search Tags', 'et_builder' ),
		'all_items'         => esc_html__( 'All Tags', 'et_builder' ),
		'parent_item'       => esc_html__( 'Parent Tag', 'et_builder' ),
		'parent_item_colon' => esc_html__( 'Parent Tag:', 'et_builder' ),
		'edit_item'         => esc_html__( 'Edit Tag', 'et_builder' ),
		'update_item'       => esc_html__( 'Update Tag', 'et_builder' ),
		'add_new_item'      => esc_html__( 'Add New Tag', 'et_builder' ),
		'new_item_name'     => esc_html__( 'New Tag Name', 'et_builder' ),
		'menu_name'         => esc_html__( 'Tags', 'et_builder' ),
	);

	register_taxonomy( 'project_tag', array( 'project' ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );
}
endif;

function et_admin_backbone_templates_being_loaded() {
	if ( ! is_admin() ) {
		return false;
	}

	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return false;
	}

	if ( ! isset( $_POST['action'] ) || 'et_pb_get_backbone_templates' !== $_POST['action'] ) {
		return false;
	}

	if ( ! wp_verify_nonce( $_POST['et_admin_load_nonce'], 'et_admin_load_nonce' ) ) {
		return false;
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	return true;
}

function et_pb_force_regenerate_templates() {
	// add option to indicate that templates cache should be updated in case of term added/removed/updated
	et_update_option( 'et_pb_clear_templates_cache', 'on' );
}

add_action( 'created_term', 'et_pb_force_regenerate_templates' );
add_action( 'edited_term', 'et_pb_force_regenerate_templates' );
add_action( 'delete_term', 'et_pb_force_regenerate_templates' );