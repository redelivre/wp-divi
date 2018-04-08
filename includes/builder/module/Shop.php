<?php

class ET_Builder_Module_Shop extends ET_Builder_Module_Type_PostBased {
	function init() {
		$this->name       = esc_html__( 'Shop', 'et_builder' );
		$this->slug       = 'et_pb_shop';
		$this->fb_support = true;

		$this->whitelisted_fields = array(
			'type',
			'posts_number',
			'columns_number',
			'include_categories',
			'orderby',
			'admin_label',
			'module_id',
			'module_class',
			'sale_badge_color',
			'icon_hover_color',
			'hover_overlay_color',
			'hover_icon',
			'box_shadow_style_image',
			'box_shadow_horizontal_image',
			'box_shadow_vertical_image',
			'box_shadow_blur_image',
			'box_shadow_spread_image',
			'box_shadow_color_image',
			'box_shadow_position_image',
			'show_pagination',
		);

		$this->fields_defaults = array(
			'type'            => array( 'recent' ),
			'posts_number'    => array( '12', 'add_default_setting' ),
			'columns_number'  => array( '0' ),
			'orderby'         => array( 'menu_order' ),
			'show_pagination' => array( 'off' ),
		);

		$this->main_css_element = '%%order_class%%.et_pb_shop';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'elements' => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'badge'   => esc_html__( 'Sale Badge', 'et_builder' ),
					'image'   => esc_html__( 'Image', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce ul.products li.product h3, {$this->main_css_element} .woocommerce ul.products li.product h1, {$this->main_css_element} .woocommerce ul.products li.product h2, {$this->main_css_element} .woocommerce ul.products li.product h4, {$this->main_css_element} .woocommerce ul.products li.product h5, {$this->main_css_element} .woocommerce ul.products li.product h6",
						'important' => 'plugin_only',
					),
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce ul.products li.product .price, {$this->main_css_element} .woocommerce ul.products li.product .price .amount",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
			'background' => array(),
			'custom_margin_padding' => array(
				'css' => array(
					'main' => '%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'max_width' => array(),
			'text'      => array(
				'css' => array(
					'text_shadow' => array(
						// Title
						"{$this->main_css_element} .woocommerce ul.products h3, {$this->main_css_element} .woocommerce ul.products  h1, {$this->main_css_element} .woocommerce ul.products  h2, {$this->main_css_element} .woocommerce ul.products  h4, {$this->main_css_element} .woocommerce ul.products  h5, {$this->main_css_element} .woocommerce ul.products  h6",
						// Price
						"{$this->main_css_element} .woocommerce ul.products .price, {$this->main_css_element} .woocommerce ul.products .price .amount",

					),
				),
			),
			'filters' => array(
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image' => array(
				'css' => array(
					'main' => '%%order_class%% .et_shop_image',
				),
			),
		);

		$this->custom_css_options = array(
			'product' => array(
				'label'    => esc_html__( 'Product', 'et_builder' ),
				'selector' => 'li.product',
			),
			'onsale' => array(
				'label'    => esc_html__( 'Onsale', 'et_builder' ),
				'selector' => 'li.product .onsale',
			),
			'image' => array(
				'label'    => esc_html__( 'Image', 'et_builder' ),
				'selector' => '.et_shop_image',
			),
			'overlay' => array(
				'label'    => esc_html__( 'Overlay', 'et_builder' ),
				'selector' => '.et_overlay',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => $this->get_title_selector(),
			),
			'rating' => array(
				'label'    => esc_html__( 'Rating', 'et_builder' ),
				'selector' => '.star-rating',
			),
			'price' => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => 'li.product .price',
			),
			'price_old' => array(
				'label'    => esc_html__( 'Old Price', 'et_builder' ),
				'selector' => 'li.product .price del span.amount',
			),
		);
	}

	protected function _add_remove_pagination_callbacks( $verb, $shortcode_type ) {
		if ( 'add' !== $verb && 'remove' !== $verb ) {
			ET_Core_Logger::error( 'Invalid argument!' );
			return;
		} else if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$toggle_action = $verb . '_action';
		$toggle_filter = $verb . '_filter';

		$toggle_action( 'pre_get_posts', array( $this, 'add_paged_param' ) );
		
		$toggle_filter( 'woocommerce_shortcode_products_query', array( $this, 'shortcode_products_query_cb' ), 10 );

		$toggle_action( 'woocommerce_shortcode_after_' . $shortcode_type . '_loop', array( __CLASS__, 'add_pagination' ), 10 );

		// reset et_pb_shop_pages when removing pagintaion to avoid conflicts with other shop modules on page.
		if ( 'remove' === $verb ) {
			$GLOBALS['et_pb_shop_pages'] = 0;
		}
	}

	/**
	 * Add the paged param to a product shortcode query.
	 *
	 * @param WP_Query $query
	 */
	public function add_paged_param( $query ) {
		$is_product_query = self::is_product_query( $query );

		if ( ! $is_product_query || is_archive() || is_post_type_archive() ) {
			return;
		}

		$paged = $this->get_paged_var();

		$query->is_paged                    = true;
		$query->query['paged']              = $paged;
		$query->query_vars['paged']         = $paged;

		$query->query['posts_per_page']      = (int) $this->shortcode_atts['posts_number'];
		$query->query_vars['posts_per_page'] = (int) $this->shortcode_atts['posts_number'];

		$query->query['no_found_rows']      = false;
		$query->query_vars['no_found_rows'] = false;
	}

	/**
	 * Add pagination to the shortcode after loop end
	 *
	 * @param array $atts
	 */
	public static function add_pagination( $atts ) {		
		$query_var = is_front_page() ? 'page' : 'paged';
		$paged     = get_query_var( $query_var ) ? get_query_var( $query_var ) : 1;

		// no need to display pagination if all the products appear on 1 page.
		if ( ! isset( $GLOBALS['et_pb_shop_pages'] ) || $GLOBALS['et_pb_shop_pages'] < 1 ) {
			return;
		}
		?>
		<nav class="woocommerce-pagination">
			<?php
			echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
				'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
				'format'    => '',
				'add_args'  => false,
				'current'   => max( 1, $paged ),
				'total'     => $GLOBALS['et_pb_shop_pages'],
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type'      => 'list',
				'end_size'  => 3,
				'mid_size'  => 3,
			) ) );
			?>
		</nav>
		<?php
	}

	function get_fields() {
		$fields = array(
			'type' => array(
				'label'           => esc_html__( 'Type', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'recent'  => esc_html__( 'Recent Products', 'et_builder' ),
					'featured' => esc_html__( 'Featured Products', 'et_builder' ),
					'sale' => esc_html__( 'Sale Products', 'et_builder' ),
					'best_selling' => esc_html__( 'Best Selling Products', 'et_builder' ),
					'top_rated' => esc_html__( 'Top Rated Products', 'et_builder' ),
					'product_category' => esc_html__( 'Product Category', 'et_builder' ),
				),
				'affects'        => array(
					'include_categories',
				),
				'description'      => esc_html__( 'Choose which type of products you would like to display.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__shop',
				),
			),
			'posts_number' => array(
				'label'             => esc_html__( 'Product Count', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Define the number of products that should be displayed per page.', 'et_builder' ),
				'computed_affects'  => array(
					'__shop',
				),
				'toggle_slug'       => 'main_content',
			),
			'show_pagination' => array(
				'label'            => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default'          => 'off',
				'description'      => esc_html__( 'Turn pagination on and off.', 'et_builder' ),
				'computed_affects' => array(
					'__shop',
				),
				'toggle_slug'      => 'elements',
			),
			'include_categories'   => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'type'             => 'basic_option',
				'renderer'         => 'et_builder_include_categories_shop_option',
				'renderer_options' => array(
					'use_terms'    => true,
					'term_name'    => 'product_cat',
				),
				'depends_show_if'  => 'product_category',
				'description'      => esc_html__( 'Choose which categories you would like to include.', 'et_builder' ),
				'taxonomy_name'    => 'product_category',
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__shop',
				),
			),
			'columns_number' => array(
				'label'             => esc_html__( 'Columns Number', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'0' => esc_html__( 'default', 'et_builder' ),
					'6' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '6' ) ),
					'5' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '5' ) ),
					'4' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '4' ) ),
					'3' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '3' ) ),
					'2' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '2' ) ),
					'1' => esc_html__( '1 Column', 'et_builder' ),
				),
				'description'       => esc_html__( 'Choose how many columns to display.', 'et_builder' ),
				'computed_affects'  => array(
					'__shop',
				),
				'toggle_slug'       => 'main_content',
			),
			'orderby' => array(
				'label'             => esc_html__( 'Order By', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'menu_order'  => esc_html__( 'Default Sorting', 'et_builder' ),
					'popularity' => esc_html__( 'Sort By Popularity', 'et_builder' ),
					'rating' => esc_html__( 'Sort By Rating', 'et_builder' ),
					'date' => esc_html__( 'Sort By Date: Oldest To Newest', 'et_builder' ),
					'date-desc' => esc_html__( 'Sort By Date: Newest To Oldest', 'et_builder' ),
					'price' => esc_html__( 'Sort By Price: Low To High', 'et_builder' ),
					'price-desc' => esc_html__( 'Sort By Price: High To Low', 'et_builder' ),
				),
				'description'       => esc_html__( 'Choose how your products should be ordered.', 'et_builder' ),
				'computed_affects'  => array(
					'__shop',
				),
				'toggle_slug'       => 'main_content',
			),
			'sale_badge_color' => array(
				'label'             => esc_html__( 'Sale Badge Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'badge',
			),
			'icon_hover_color' => array(
				'label'             => esc_html__( 'Icon Hover Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'visibility',
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug' => 'admin_label',
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'__shop' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Shop', 'get_shop_html' ),
				'computed_depends_on' => array(
					'type',
					'include_categories',
					'posts_number',
					'orderby',
					'columns_number',
					'show_pagination',
					'__page',
				),
				'computed_minimum' => array(
					'posts_number',
					'show_pagination',
					'__page',
				),
			),
			'__page' => array(
				'type'              => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Shop', 'get_shop_html' ),
				'computed_depends_on' => array(
					'type',
					'include_categories',
					'posts_number',
					'orderby',
					'columns_number',
					'show_pagination',
				),
				'computed_affects'  => array(
					'__shop',
				),
			),
		);

		$fields = array_merge( $fields, ET_Builder_Module_Fields_Factory::get( 'BoxShadow' )->get_fields( array(
			'suffix'          => '_image',
			'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
			'option_category' => 'layout',
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'image',
		) ) );

		return $fields;
	}

	/**
	 * Get paged var
	 */
	public function get_paged_var() {
		if ( isset( $this->shortcode_atts['__page'] ) ) {
			// For the VB
			$paged = $this->shortcode_atts['__page'];
		} else {
			$query_var = is_front_page() ? 'page' : 'paged';
			$paged     = get_query_var( $query_var );
		}

		return $paged ? $paged : 1;
	}

	function add_product_class_name( $classes ) {
		$classes[] = 'product';

		return $classes;
	}

	function get_shop( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		foreach ( $args as $arg => $value ) {
			$this->shortcode_atts[ $arg ] = $value;
		}

		$type                 = $this->shortcode_atts['type'];
		$include_category_ids = explode ( ",", $this->shortcode_atts['include_categories'] );
		$posts_number         = $this->shortcode_atts['posts_number'];
		$orderby              = $this->shortcode_atts['orderby'];
		$columns              = $this->shortcode_atts['columns_number'];
		$pagination           = 'on' === $this->shortcode_atts['show_pagination'];

		$product_categories = array();
		$all_shop_categories = et_builder_get_shop_categories();
		if ( is_array( $all_shop_categories ) && ! empty( $all_shop_categories ) ) {
			foreach ( $all_shop_categories as $category ) {
				if ( is_object( $category ) && is_a($category, 'WP_Term') ) {
					if ( in_array( $category->term_id, $include_category_ids ) ) {
						$product_categories[] = $category->slug;
					}
				}
			}
		}

		$woocommerce_shortcodes_types = array(
			'recent'           => 'recent_products',
			'featured'         => 'featured_products',
			'sale'             => 'sale_products',
			'best_selling'     => 'best_selling_products',
			'top_rated'        => 'top_rated_products',
			'product_category' => 'product_category',
		);

		if ( $pagination ) {
			$this->_add_remove_pagination_callbacks( 'add', $woocommerce_shortcodes_types[$type] );
		}

		/**
		 * Actually, orderby parameter used by WooCommerce shortcode is equal to orderby parameter used by WP_Query
		 * Hence customize WooCommerce' product query via modify_woocommerce_shortcode_products_query method
		 * @see http://docs.woothemes.com/document/woocommerce-shortcodes/#section-5
		 */
		$modify_woocommerce_query = 'best_selling' !== $type && in_array( $orderby, array( 'menu_order', 'price', 'price-desc', 'date', 'date-desc', 'rating', 'popularity' ) );

		if ( $modify_woocommerce_query ) {
			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'modify_woocommerce_shortcode_products_query' ), 10, 2 );
		}

		do_action( 'et_pb_shop_before_print_shop' );

		// https://github.com/woocommerce/woocommerce/issues/17769
		$post = $GLOBALS['post'];

		$shop = do_shortcode(
			sprintf( '[%1$s per_page="%2$s" orderby="%3$s" columns="%4$s" category="%5$s"]',
				esc_html( $woocommerce_shortcodes_types[ $type ] ),
				esc_attr( $posts_number ),
				esc_attr( $orderby ),
				esc_attr( $columns ),
				esc_attr( implode ( ",", $product_categories ) )
			)
		);

		// https://github.com/woocommerce/woocommerce/issues/17769
		$GLOBALS['post'] = $post;

		do_action( 'et_pb_shop_after_print_shop' );

		if ( $pagination ) {
			$this->_add_remove_pagination_callbacks( 'remove', $woocommerce_shortcodes_types[$type] );
		}

		/**
		 * Remove modify_woocommerce_shortcode_products_query method after being used
		 */
		if ( $modify_woocommerce_query ) {
			remove_filter( 'woocommerce_shortcode_products_query', array( $this, 'modify_woocommerce_shortcode_products_query' ) );

			if ( function_exists( 'WC' ) ) {
				WC()->query->remove_ordering_args(); // remove args added by woocommerce to avoid errors in sql queries performed afterwards
			}
		}

		if ( '<div class="woocommerce columns-0"></div>' === $shop ) {
			$shop = self::get_no_results_template();
		}

		return $shop;
	}

	/**
	 * Get shop HTML for shop module
	 *
	 * @param array   arguments that affect shop output
	 * @param array   passed conditional tag for update process
	 * @param array   passed current page params
	 * @return string HTML markup for shop module
	 */
	static function get_shop_html( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$shop = new self();

		do_action( 'et_pb_get_shop_html_before' );

		$shop->shortcode_atts = $args;

		// Force product loop to have 'product' class name. It appears that 'product' class disappears
		// when $this->get_shop() is being called for update / from admin-ajax.php
		add_filter( 'post_class', array( $shop, 'add_product_class_name' ) );

		// Get product HTML
		$output = $shop->get_shop();

		// Remove 'product' class addition to product loop's post class
		remove_filter( 'post_class', array( $shop, 'add_product_class_name' ) );

		do_action( 'et_pb_get_shop_html_after' );

		return $output;
	}


	// WooCommerce changed the title tag from h3 to h2 in 3.0.0
	function get_title_selector() {
		$title_selector = 'li.product h3';

		if ( class_exists( 'WooCommerce' ) ) {
			global $woocommerce;

			if ( version_compare( $woocommerce->version, '3.0.0', '>=' ) ) {
				$title_selector = 'li.product h2';
			}
		}

		return $title_selector;
	}

	/**
	 * Whether or not the provided query is for products.
	 *
	 * @param WP_Query $query
	 *
	 * @return bool
	 */
	public static function is_product_query( $query ) {
		if ( ! isset( $query->query['post_type'] ) || ! empty( $query->query['p'] ) ) {
			return false;
		}

		if ( isset( $query->query['composite_component'] ) ) {
			return false;
		}

		$post_type = $query->query['post_type'];

		if ( 'product' === $post_type ) {
			return true;
		}

		if ( is_array( $post_type ) && in_array( 'product', $post_type ) ) {
			return true;
		}

		return false;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$type                    = $this->shortcode_atts['type'];
		$include_categories      = $this->shortcode_atts['include_categories'];
		$posts_number            = $this->shortcode_atts['posts_number'];
		$orderby                 = $this->shortcode_atts['orderby'];
		$columns                 = $this->shortcode_atts['columns_number'];
		$sale_badge_color        = $this->shortcode_atts['sale_badge_color'];
		$icon_hover_color        = $this->shortcode_atts['icon_hover_color'];
		$hover_overlay_color     = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon              = $this->shortcode_atts['hover_icon'];

		$module_class              = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		if ( '' !== $sale_badge_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% span.onsale',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $sale_badge_color )
				),
			) );
		}

		if ( '' !== $icon_hover_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $icon_hover_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .et_overlay',
				'declaration' => sprintf(
					'background-color: %1$s !important;
					border-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_options ) && array_key_exists( 'css', $this->advanced_options['image'] ) ) {
			$module_class .= $this->generate_css_filters(
				$function_name,
				'child_',
				self::$data_utils->array_get( $this->advanced_options['image']['css'], 'main', '%%order_class%%' )
			);
		}

		$data_icon = '' !== $hover_icon
			? sprintf(
				' data-icon="%1$s"',
				esc_attr( et_pb_process_font_icon( $hover_icon ) )
			)
			: '';

		$output = sprintf(
			'<div%2$s class="et_pb_module et_pb_shop%3$s%4$s%6$s%8$s%10$s"%5$s>
				%9$s
				%7$s
				%1$s
			</div>',
			$this->get_shop(),
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'0' === $columns ? ' et_pb_shop_grid' : '',
			$data_icon,
			'' !== $video_background ? ' et_pb_section_video et_pb_preload' : '',
			$video_background,
			'' !== $parallax_image_background ? ' et_pb_section_parallax' : '',
			$parallax_image_background,
			$this->get_text_orientation_classname()
		);

		return $output;
	}

	/**
	 * Products shortcode query args.
	 *
	 * @param array  $query_args
	 *
	 * @return array
	 */
	public function shortcode_products_query_cb( $query_args ) {
		$query_args['paged'] = $this->get_paged_var();

		$products   = new WP_Query( $query_args );
		
		// save the number of pages to global var so it can be used to render pagination
		$GLOBALS['et_pb_shop_pages'] = $products->max_num_pages;

		return $query_args;
	}

	/**
	 * Modifying WooCommerce' product query filter based on $orderby value given
	 * @see WC_Query->get_catalog_ordering_args()
	 */
	function modify_woocommerce_shortcode_products_query( $args, $atts ) {

		if ( function_exists( 'WC' ) ) {
			// Default to ascending order
			$orderby = $this->shortcode_atts['orderby'];
			$order   = 'ASC';

			// Switch to descending order if orderby is 'price-desc' or 'date-desc'
			if ( in_array( $orderby, array( 'price-desc', 'date-desc' ) ) ) {
				$order = 'DESC';
			}

			// Supported orderby arguments (as defined by WC_Query->get_catalog_ordering_args() ): rand | date | price | popularity | rating | title
			$orderby = in_array( $orderby, array( 'price-desc', 'date-desc' ) ) ? str_replace( '-desc', '', $orderby ) : $orderby;

			// Get arguments for the given non-native orderby
			$query_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

			// Confirm that returned argument isn't empty then merge returned argument with default argument
			if ( is_array( $query_args ) && ! empty( $query_args ) ) {
				$args = array_merge( $args, $query_args );
			}
		}

		return $args;
	}

	public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$selector  = sprintf( '.%1$s .et_shop_image', self::get_module_order_class( $function_name ) );

		self::set_style( $function_name, $boxShadow->get_style(
			$selector,
			$this->shortcode_atts,
			array( 'suffix' => '_image' )
		) );

		parent::process_box_shadow( $function_name );
	}

	protected function _add_additional_border_fields() {
		parent::_add_additional_border_fields();

		$suffix      = 'image';
		$tab_slug    = 'advanced';
		$toggle_slug = 'image';

		$this->_additional_fields_options = array_merge(
			$this->_additional_fields_options,
			ET_Builder_Module_Fields_Factory::get( 'Border' )->get_fields( array(
				'suffix'       => "_{$suffix}",
				'label_prefix' => esc_html__( 'Image', 'et_builder' ),
				'tab_slug'     => $tab_slug,
				'toggle_slug'  => $toggle_slug,
			) )
		);

		$this->advanced_options["border_{$suffix}"]["border_radii_{$suffix}"]  = $this->_additional_fields_options["border_radii_{$suffix}"];
		$this->advanced_options["border_{$suffix}"]["border_styles_{$suffix}"] = $this->_additional_fields_options["border_styles_{$suffix}"];

		$this->advanced_options["border_{$suffix}"]['css'] = array(
			'main' => array(
				'border_radii'  => "{$this->main_css_element} .et_shop_image > img",
				'border_styles' => "{$this->main_css_element} .et_shop_image > img",
			)
		);
	}

	function process_advanced_border_options( $function_name ) {
		parent::process_advanced_border_options( $function_name );

		$suffix = 'image';
		/**
		 * @var ET_Builder_Module_Field_Border $border_field
		 */
		$border_field = ET_Builder_Module_Fields_Factory::get( 'Border' );

		$css_selector = ! empty( $this->advanced_options["border_{$suffix}"]['css']['main']['border_radii'] ) ? $this->advanced_options["border_{$suffix}"]['css']['main']['border_radii'] : $this->main_css_element;
		self::set_style( $function_name, array(
			'selector'    => $css_selector,
			'declaration' => $border_field->get_radii_style( $this->shortcode_atts, $this->advanced_options, "_{$suffix}" ),
			'priority'    => $this->_style_priority,
		) );

		$css_selector = ! empty( $this->advanced_options["border_{$suffix}"]['css']['main']['border_styles'] ) ? $this->advanced_options["border_{$suffix}"]['css']['main']['border_styles'] : $this->main_css_element;
		self::set_style( $function_name, array(
			'selector'    => $css_selector,
			'declaration' => $border_field->get_borders_style( $this->shortcode_atts, $this->advanced_options, "_{$suffix}" ),
			'priority'    => $this->_style_priority,
		) );
	}
}

new ET_Builder_Module_Shop;
