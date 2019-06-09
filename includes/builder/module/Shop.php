<?php

class ET_Builder_Module_Shop extends ET_Builder_Module_Type_PostBased {
	function init() {
		$this->name       = esc_html__( 'Shop', 'et_builder' );
		$this->plural     = esc_html__( 'Shops', 'et_builder' );
		$this->slug       = 'et_pb_shop';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_shop';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'elements' => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'image'   => esc_html__( 'Image', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
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
				'sale_badge' => array(
					'label'           => esc_html__( 'Sale Badge', 'et_builder' ),
					'css'             => array(
						'main'      => "{$this->main_css_element} .woocommerce ul.products li.product .onsale",
						'important' => array( 'line-height', 'font', 'text-shadow' ),
					),
					'hide_text_align' => true,
					'line_height'     => array(
						'default' => '1.3em',
					),
					'font_size'       => array(
						'default' => '20px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
				),
				'sale_price' => array(
					'label'           => esc_html__( 'Sale Price', 'et_builder' ),
					'css'             => array(
						'main'    => "{$this->main_css_element} .woocommerce ul.products li.product .price ins .amount",
					),
					'hide_text_align' => true,
					'font'            => array(
						'default' => '|700|||||||',
					),
					'line_height'     => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
			'borders'               => array(
				'default' => array(),
				'image' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_shop_image > img",
							'border_styles' => "{$this->main_css_element} .et_shop_image > img",
						),
					),
					'label_prefix' => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'            => array(
				'default' => array(),
				'image'   => array(
					'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'css'             => array(
						'main'         => '%%order_class%% .et_shop_image',
						'overlay' => 'inset',
					),
					'default_on_fronts'  => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main' => '%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'                  => array(
				'css' => array(
					'text_shadow' => implode(', ', array(
						// Title
						"{$this->main_css_element} .woocommerce ul.products h3",
						"{$this->main_css_element} .woocommerce ul.products  h1",
						"{$this->main_css_element} .woocommerce ul.products  h2",
						"{$this->main_css_element} .woocommerce ul.products  h4",
						"{$this->main_css_element} .woocommerce ul.products  h5",
						"{$this->main_css_element} .woocommerce ul.products  h6",
						// Price
						"{$this->main_css_element} .woocommerce ul.products .price",
						"{$this->main_css_element} .woocommerce ul.products .price .amount"

					) ),
				),
			),
			'filters'               => array(
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'                 => array(
				'css' => array(
					'main' => '%%order_class%% .et_shop_image',
				),
			),
			'button'                => false,
		);

		$this->custom_css_fields = array(
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

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'O5RCEYP-qKI' ),
				'name' => esc_html__( 'An introduction to the Shop module', 'et_builder' ),
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

		$query->query['posts_per_page']      = (int) $this->props['posts_number'];
		$query->query_vars['posts_per_page'] = (int) $this->props['posts_number'];

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
				'label'           => esc_html__( 'Product Type', 'et_builder' ),
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
				'default_on_front' => 'recent',
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
				'default'           => '12',
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
				'label'            => esc_html__( 'Included Categories', 'et_builder' ),
				'type'             => 'categories',
				'meta_categories'  => array(
					'all'     => esc_html__( 'All Categories', 'et_builder' ),
					'current' => esc_html__( 'Current Category', 'et_builder' ),
				),
				'renderer_options' => array(
					'use_terms'    => true,
					'term_name'    => 'product_cat',
				),
				'depends_show_if'  => 'product_category',
				'description'      => esc_html__( 'Choose which categories you would like to include.', 'et_builder' ),
				'taxonomy_name'    => 'product_cat',
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__shop',
				),
			),
			'columns_number' => array(
				'label'             => esc_html__( 'Column Layout', 'et_builder' ),
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
				'default_on_front'  => '0',
				'description'       => esc_html__( 'Choose how many columns to display.', 'et_builder' ),
				'computed_affects'  => array(
					'__shop',
				),
				'toggle_slug'       => 'main_content',
			),
			'orderby' => array(
				'label'             => esc_html__( 'Order', 'et_builder' ),
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
				'default_on_front' => 'menu_order',
				'description'       => esc_html__( 'Choose how your products should be ordered.', 'et_builder' ),
				'computed_affects'  => array(
					'__shop',
				),
				'toggle_slug'       => 'main_content',
			),
			'sale_badge_color' => array(
				'label'             => esc_html__( 'Sale Badge Color', 'et_builder' ),
				'description'       => esc_html__( 'Pick a color to use for the sales bade that appears on products that are on sale.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'sale_badge',
				'hover'             => 'tabs',
				'mobile_options'    => true,
			),
			'icon_hover_color' => array(
				'label'             => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'description'       => esc_html__( 'Pick a color to use for the icon that appears when hovering over a product.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'mobile_options'    => true,
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Overlay Background Color', 'et_builder' ),
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'mobile_options'    => true,
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Overlay Icon', 'et_builder' ),
				'description'         => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
				'mobile_options'      => true,
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
				'computed_minimum'    => array(
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

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['sale_badge_color'] = array( 'background-color' => '%%order_class%% span.onsale' );

		return $fields;
	}

	/**
	 * Get paged var
	 */
	public function get_paged_var() {
		if ( ! empty( $this->props['__page'] ) ) {
			// For the VB
			$paged = $this->props['__page'];
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
			$this->props[ $arg ] = $value;
		}

		$post_id                 = isset( $current_page['id'] ) ? (int) $current_page['id'] : 0;
		$type                    = $this->props['type'];
		$posts_number            = $this->props['posts_number'];
		$orderby                 = $this->props['orderby'];
		$order                   = 'ASC'; // Default to ascending order
		$columns                 = $this->props['columns_number'];
		$pagination              = 'on' === $this->props['show_pagination'];
		$product_categories      = array();

		if ('product_category' === $type) {
			$all_shop_categories     = et_builder_get_shop_categories();
			$all_shop_categories_map = array();
			$raw_product_categories  = self::filter_meta_categories( $this->props['include_categories'], $post_id, 'product_cat' );

			foreach ( $all_shop_categories as $term ) {
				if ( is_object( $term ) && is_a( $term, 'WP_Term' ) ) {
					$all_shop_categories_map[ $term->term_id ] = $term->slug;
				}
			}

			$product_categories = array_values( $all_shop_categories_map );

			if ( ! empty( $raw_product_categories ) ) {
				$product_categories = array_intersect_key(
					$all_shop_categories_map,
					array_flip( $raw_product_categories )
				);
			}
		}

		if ( in_array( $orderby, array( 'price-desc', 'date-desc' ) ) ) {
			// Supported orderby arguments (as defined by WC_Query->get_catalog_ordering_args() ):
			//   rand | date | price | popularity | rating | title
			$orderby = str_replace( '-desc', '', $orderby );
			// Switch to descending order if orderby is 'price-desc' or 'date-desc'
			$order = 'DESC';
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

		do_action( 'et_pb_shop_before_print_shop' );

		// https://github.com/woocommerce/woocommerce/issues/17769
		$post = $GLOBALS['post'];

		$shop = do_shortcode(
			sprintf( '[%1$s per_page="%2$s" orderby="%3$s" columns="%4$s" %5$s order="%6$s"]',
				esc_html( $woocommerce_shortcodes_types[ $type ] ),
				esc_attr( $posts_number ),
				esc_attr( $orderby ),
				esc_attr( $columns ),
				! empty( $product_categories ) ? sprintf( 'category="%s"', esc_attr( implode( ',', $product_categories ) ) ) : '',
				esc_attr( $order )
			)
		);

		// https://github.com/woocommerce/woocommerce/issues/17769
		$GLOBALS['post'] = $post; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

		do_action( 'et_pb_shop_after_print_shop' );

		if ( $pagination ) {
			$this->_add_remove_pagination_callbacks( 'remove', $woocommerce_shortcodes_types[$type] );
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

		$shop->props = $args;

		// Force product loop to have 'product' class name. It appears that 'product' class disappears
		// when $this->get_shop() is being called for update / from admin-ajax.php
		add_filter( 'post_class', array( $shop, 'add_product_class_name' ) );

		// Get product HTML
		$output = $shop->get_shop( array(), array(), $current_page );

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

	function render( $attrs, $content = null, $render_slug ) {
		$type                    = $this->props['type'];
		$include_categories      = $this->props['include_categories'];
		$posts_number            = $this->props['posts_number'];
		$orderby                 = $this->props['orderby'];
		$columns                 = $this->props['columns_number'];

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$sale_badge_color_hover    = $this->get_hover_value( 'sale_badge_color' );
		$sale_badge_color_values   = et_pb_responsive_options()->get_property_values( $this->props, 'sale_badge_color' );
		$icon_hover_color_values   = et_pb_responsive_options()->get_property_values( $this->props, 'icon_hover_color' );
		$hover_overlay_color_value = et_pb_responsive_options()->get_property_values( $this->props, 'hover_overlay_color' );

		$hover_icon                = $this->props['hover_icon'];
		$hover_icon_values         = et_pb_responsive_options()->get_property_values( $this->props, 'hover_icon' );
		$hover_icon_tablet         = isset( $hover_icon_values['tablet'] ) ? $hover_icon_values['tablet'] : '';
		$hover_icon_phone          = isset( $hover_icon_values['phone'] ) ? $hover_icon_values['phone'] : '';

		// Sale Badge Color.
		et_pb_responsive_options()->generate_responsive_css( $sale_badge_color_values, '%%order_class%% span.onsale', 'background-color', $render_slug, ' !important;', 'color' );

		if ( et_builder_is_hover_enabled( 'sale_badge_color', $this->props ) ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => '%%order_class%%:hover span.onsale',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $sale_badge_color_hover )
				),
			) );
		}

		// Icon Hover Color.
		et_pb_responsive_options()->generate_responsive_css( $icon_hover_color_values, '%%order_class%% .et_overlay:before', 'color', $render_slug, ' !important;', 'color' );

		// Hover Overlay Color.
		et_pb_responsive_options()->generate_responsive_css( $hover_overlay_color_value, '%%order_class%% .et_overlay', array( 'background-color', 'border-color' ), $render_slug, ' !important;', 'color' );

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$this->add_classname( $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
			) );
		}

		$data_icon = '' !== $hover_icon
			? sprintf(
				' data-icon="%1$s"',
				esc_attr( et_pb_process_font_icon( $hover_icon ) )
			)
			: '';

		$data_icon_tablet = '' !== $hover_icon_tablet
			? sprintf(
				' data-icon-tablet="%1$s"',
				esc_attr( et_pb_process_font_icon( $hover_icon_tablet ) )
			)
			: '';

		$data_icon_phone = '' !== $hover_icon_phone
			? sprintf(
				' data-icon-phone="%1$s"',
				esc_attr( et_pb_process_font_icon( $hover_icon_phone ) )
			)
			: '';

		// Module classnames
		$this->add_classname( array(
			$this->get_text_orientation_classname(),
		) );

		if ( '0' === $columns ) {
			$this->add_classname( 'et_pb_shop_grid' );
		}

		$output = sprintf(
			'<div%2$s class="%3$s"%4$s%7$s%8$s>
				%6$s
				%5$s
				%1$s
			</div>',
			$this->get_shop( array(), array(), array( 'id' => $this->get_the_ID() ) ),
			$this->module_id(),
			$this->module_classname( $render_slug ),
			$data_icon,
			$video_background,
			$parallax_image_background,
			$data_icon_tablet,
			$data_icon_phone
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
}

new ET_Builder_Module_Shop;
