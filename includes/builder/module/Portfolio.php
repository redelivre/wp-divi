<?php

class ET_Builder_Module_Portfolio extends ET_Builder_Module_Type_PostBased {
	function init() {
		$this->name       = esc_html__( 'Portfolio', 'et_builder' );
		$this->plural     = esc_html__( 'Portfolios', 'et_builder' );
		$this->slug       = 'et_pb_portfolio';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%% .et_pb_portfolio_item';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'  => esc_html__( 'Layout', 'et_builder' ),
					'overlay' => esc_html__( 'Overlay', 'et_builder' ),
					'image' => array(
						'title' => esc_html__( 'Image', 'et_builder' ),
					),
					'text'    => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'                 => array(
				'title'   => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main'      => "{$this->main_css_element} h2, {$this->main_css_element} h2 a, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h1.et_pb_module_header a, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header a, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header a, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header a, {$this->main_css_element} h6.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header a",
						'important' => 'all',
						'hover'     => "{$this->main_css_element} h2:hover, {$this->main_css_element} h2:hover a, {$this->main_css_element} h1.et_pb_module_header:hover, {$this->main_css_element} h1.et_pb_module_header:hover a, {$this->main_css_element} h3.et_pb_module_header:hover, {$this->main_css_element} h3.et_pb_module_header:hover a, {$this->main_css_element} h4.et_pb_module_header:hover, {$this->main_css_element} h4.et_pb_module_header:hover a, {$this->main_css_element} h5.et_pb_module_header:hover, {$this->main_css_element} h5.et_pb_module_header:hover a, {$this->main_css_element} h6.et_pb_module_header:hover, {$this->main_css_element} h6.et_pb_module_header:hover a",
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'caption' => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
						'hover' => "{$this->main_css_element} .post-meta a:hover",
					),
				),
				'pagination' => array(
					'label'    => esc_html__( 'Pagination', 'et_builder' ),
					'css'      => array(
						'main' => function_exists( 'wp_pagenavi' ) ? "%%order_class%% .wp-pagenavi a, %%order_class%% .wp-pagenavi span" : "%%order_class%% .pagination a",
						'important'  => function_exists( 'wp_pagenavi' ) ? 'all' : array(),
						'text_align' => '%%order_class%% .wp-pagenavi',
						'hover'      => function_exists( 'wp_pagenavi' ) ? "%%order_class%% .wp-pagenavi a:hover, %%order_class%% .wp-pagenavi span:hover" : "%%order_class%% .pagination a:hover",
					),
					'hide_text_align' => ! function_exists( 'wp_pagenavi' ),
					'text_align' => array(
						'options' => et_builder_get_text_orientation_options( array( 'justified' ), array() ),
					),
				),
			),
			'background'            => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'               => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => $this->main_css_element,
							'border_styles' => $this->main_css_element,
						),
					),
				),
				'image' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_portfolio_image",
							'border_styles' => "{$this->main_css_element} .et_portfolio_image",
						)
					),
					'label_prefix' => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'            => array(
				'default' => array(
					'css' => array(
						'overlay' => false,
					)
				),
				'image'   => array(
					'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'css'             => array(
						'main'    => '%%order_class%% .et_pb_portfolio_item .et_portfolio_image',
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
				'use_background_layout' => true,
				'options' => array(
					'background_layout' => array(
						'default' => 'light',
						'hover' => 'tabs',
					),
				),
				'css' => array(
					'main' => '%%order_class%% .et_pb_module_header, %%order_class%% .post-meta'
				)
			),
			'filters'               => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'                 => array(
				'css' => array(
					'main' => '%%order_class%% .et_portfolio_image',
				),
			),
			'button'                => false,
		);

		$this->custom_css_fields = array(
			'portfolio_image' => array(
				'label'    => esc_html__( 'Portfolio Image', 'et_builder' ),
				'selector' => '.et_portfolio_image',
			),
			'overlay' => array(
				'label'    => esc_html__( 'Overlay', 'et_builder' ),
				'selector' => '.et_overlay',
			),
			'overlay_icon' => array(
				'label'    => esc_html__( 'Overlay Icon', 'et_builder' ),
				'selector' => '.et_overlay:before',
			),
			'portfolio_title' => array(
				'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item h2',
			),
			'portfolio_post_meta' => array(
				'label'    => esc_html__( 'Portfolio Post Meta', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item .post-meta',
			),
			'pagination' => array(
				'label'    => esc_html__( 'Portfolio Pagination', 'et_builder' ),
				'selector' => function_exists( 'wp_pagenavi' ) ? '%%order_class%% .wp-pagenavi a, %%order_class%% .wp-pagenavi span' : '%%order_class%% .pagination a',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( '6NpHdiLciDU' ),
				'name' => esc_html__( 'An introduction to the Portfolio module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'fullwidth' => array(
				'label'           => esc_html__( 'Layout', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Fullwidth', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'affects' => array(
					'hover_icon',
					'zoom_icon_color',
					'hover_overlay_color',
				),
				'description'       => esc_html__( 'Choose your desired portfolio layout style.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
			),
			'posts_number' => array(
				'default'           => 10,
				'label'             => esc_html__( 'Post Count', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Define the number of projects that should be displayed per page.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'toggle_slug'       => 'main_content',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Included Categories', 'et_builder' ),
				'type'             => 'categories',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select the categories that you would like to include in the feed.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__projects',
				),
				'taxonomy_name' => 'project_category',
			),
			'show_title' => array(
				'label'           => esc_html__( 'Show Title', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turn project titles on or off.', 'et_builder' ),
			),
			'show_categories' => array(
				'label'           => esc_html__( 'Show Categories', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turn the category links on or off.', 'et_builder' ),
			),
			'show_pagination' => array(
				'label'           => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
			),
			'zoom_icon_color' => array(
				'label'             => esc_html__( 'Zoom Icon Color', 'et_builder' ),
				'description'       => esc_html__( 'Here you can define a custom color for the zoom icon.', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'mobile_options'    => true,
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'description'       => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'depends_show_if'   => 'off',
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'overlay',
				'mobile_options'    => true,
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'description'         => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'depends_show_if'     => 'off',
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'__projects'          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Portfolio', 'get_portfolio_item' ),
				'computed_depends_on' => array(
					'posts_number',
					'include_categories',
					'fullwidth',
					'__page',
				),
			),
			'__page'          => array(
				'type'              => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Portfolio', 'get_portfolio_item' ),
				'computed_affects'  => array(
					'__projects',
				),
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['max_width'] = array( 'max-width' => '%%order_class%%, %%order_class%% .et_pb_portfolio_item' );
		$fields['width']     = array( 'width' => '%%order_class%%, %%order_class%% .et_pb_portfolio_item' );

		return $fields;
	}

	/**
	 * Get portfolio objects for portfolio module
	 *
	 * @param array $args             arguments that affect et_pb_portfolio query
	 * @param array $conditional_tags conditional tag for update process
	 * @param array $current_page     current page params
	 *
	 * @return mixed portfolio item data
	 */
	static function get_portfolio_item( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $et_fb_processing_shortcode_object;

		$global_processing_original_value = $et_fb_processing_shortcode_object;

		$defaults = array(
			'posts_number'       => 10,
			'include_categories' => '',
			'fullwidth'          => 'on',
		);

		$args          = wp_parse_args( $args, $defaults );

		// Native conditional tag only works on page load. Data update needs $conditional_tags data
		$is_front_page = et_fb_conditional_tag( 'is_front_page', $conditional_tags );
		$is_search     = et_fb_conditional_tag( 'is_search', $conditional_tags );

		// Prepare query arguments
		$query_args    = array(
			'posts_per_page' => (int) $args['posts_number'],
			'post_type'      => 'project',
			'post_status'    => 'publish',
		);

		// Conditionally get paged data
		if ( defined( 'DOING_AJAX' ) && isset( $current_page[ 'paged'] ) ) {
			$et_paged = intval( $current_page[ 'paged' ] );
		} else {
			$et_paged = $is_front_page ? get_query_var( 'page' ) : get_query_var( 'paged' );
		}

		if ( $is_front_page ) {
			global $paged;
			$paged = $et_paged; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		}

		// support pagination in VB
		if ( isset( $args['__page'] ) ) {
			$et_paged = $args['__page'];
		}

		if ( ! is_search() ) {
			$query_args['paged'] = $et_paged;
		}

		// Passed categories parameter
		$include_categories = self::filter_invalid_term_ids( explode( ',', $args['include_categories'] ), 'project_category' );

		if ( ! empty( $include_categories ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'project_category',
					'field'    => 'id',
					'terms'    => $include_categories,
					'operator' => 'IN',
				)
			);
		}

		// Get portfolio query
		$query = new WP_Query( $query_args );

		// Format portfolio output, and add supplementary data
		$width     = 'on' === $args['fullwidth'] ?  1080 : 400;
		$width     = (int) apply_filters( 'et_pb_portfolio_image_width', $width );
		$height    = 'on' === $args['fullwidth'] ?  9999 : 284;
		$height    = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
		$classtext = 'on' === $args['fullwidth'] ? 'et_pb_post_main_image' : '';
		$titletext = get_the_title();

		// Loop portfolio item data and add supplementary data
		if ( $query->have_posts() ) {
			$post_index = 0;
			while( $query->have_posts() ) {
				$query->the_post();

				$categories = array();

				$categories_object = get_the_terms( get_the_ID(), 'project_category' );

				if ( ! empty( $categories_object ) ) {
					foreach ( $categories_object as $category ) {
						$categories[] = array(
							'id' => $category->term_id,
							'label' => $category->name,
							'permalink' => get_term_link( $category ),
						);
					}
				}

				// need to disable processnig to make sure get_thumbnail() doesn't generate errors
				$et_fb_processing_shortcode_object = false;

				// Get thumbnail
				$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				// Append value to query post
				$query->posts[ $post_index ]->post_permalink 	= get_permalink();
				$query->posts[ $post_index ]->post_thumbnail 	= print_thumbnail( $thumbnail['thumb'], $thumbnail['use_timthumb'], $titletext, $width, $height, '', false, true );
				$query->posts[ $post_index ]->post_categories 	= $categories;
				$query->posts[ $post_index ]->post_class_name 	= get_post_class( '', get_the_ID() );

				$post_index++;
			}

			$query->posts_next = array(
				'label' => esc_html__( '&laquo; Older Entries', 'et_builder' ),
				'url' => next_posts( $query->max_num_pages, false ),
			);

			$query->posts_prev = array(
				'label' => esc_html__( 'Next Entries &raquo;', 'et_builder' ),
				'url' => ( $et_paged > 1 ) ? previous_posts( false ) : '',
			);

			// Added wp_pagenavi support
			$query->wp_pagenavi = function_exists( 'wp_pagenavi' ) ? wp_pagenavi( array(
				'query' => $query,
				'echo' => false
			) ) : false;
		} else if ( wp_doing_ajax() || et_core_is_fb_enabled() ) {
			// This is for the VB
			$query = array( 'posts' => self::get_no_results_template() );
		}

		wp_reset_postdata();

		return $query;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$fullwidth                       = $this->props['fullwidth'];
		$posts_number                    = $this->props['posts_number'];
		$include_categories              = $this->props['include_categories'];
		$show_title                      = $this->props['show_title'];
		$show_categories                 = $this->props['show_categories'];
		$show_pagination                 = $this->props['show_pagination'];
		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$hover_icon                      = $this->props['hover_icon'];
		$header_level                    = $this->props['title_level'];
		$zoom_icon_color_values          = et_pb_responsive_options()->get_property_values( $this->props, 'zoom_icon_color' );
		$hover_overlay_color_values      = et_pb_responsive_options()->get_property_values( $this->props, 'hover_overlay_color' );

		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet        = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone         = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';

		global $paged;

		$processed_header_level = et_pb_process_header_level( $header_level, 'h2' );

		// Set inline style

		// Zoom Icon color.
		et_pb_responsive_options()->generate_responsive_css( $zoom_icon_color_values, '%%order_class%% .et_overlay:before', 'color', $render_slug, ' !important;', 'color' );

		// Hover Overlay color.
		et_pb_responsive_options()->generate_responsive_css( $hover_overlay_color_values, '%%order_class%% .et_overlay', array( 'background-color', 'border-color' ), $render_slug, '', 'color' );

		$container_is_closed = false;

		// Get loop data
		$portfolio = self::get_portfolio_item( array(
			'posts_number'       => $posts_number,
			'include_categories' => $include_categories,
			'fullwidth'          => $fullwidth,
		) );

		// setup overlay
		$overlay = '';
		if ( 'on' !== $fullwidth ) {
			$data_icon = '' !== $hover_icon
				? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $hover_icon ) )
				)
				: '';

			$overlay = sprintf( '<span class="et_overlay%1$s"%2$s></span>',
				( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
				$data_icon
			);
		}

		ob_start();

		if ( $portfolio->have_posts() ) {
			while( $portfolio->have_posts() ) {
				$portfolio->the_post();

				// Get $post data of current loop
				global $post;

				array_push( $post->post_class_name, 'et_pb_portfolio_item' );

				if ( 'on' !== $fullwidth ) {
					array_push( $post->post_class_name, 'et_pb_grid_item' );
				}

				?>
				<div id="post-<?php echo esc_attr( $post->ID ); ?>" class="<?php echo esc_attr( join( $post->post_class_name, ' ' ) ); ?>">

					<?php if ( '' !== $post->post_thumbnail ) { ?>
					<a href="<?php echo esc_url( $post->post_permalink ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
						<?php if ( 'on' === $fullwidth ) { ?>
							<span class="et_portfolio_image">
								<img src="<?php echo esc_url( $post->post_thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="1080" height="9999" />
							</span>
						<?php } else { ?>
							<span class="et_portfolio_image">
								<img src="<?php echo esc_url( $post->post_thumbnail ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="400" height="284" />
								<?php echo et_core_esc_previously( $overlay ); ?>
							</span>
						<?php } ?>
					</a>
					<?php } ?>

					<?php if ( 'on' === $show_title ) { ?>
						<<?php echo et_core_esc_previously( $processed_header_level ); ?> class="et_pb_module_header">
							<a href="<?php echo esc_url( $post->post_permalink ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
								<?php echo esc_html( get_the_title() ); ?>
							</a>
						</<?php echo et_core_esc_previously( $processed_header_level ); ?>>
					<?php } ?>


					<?php if ( 'on' === $show_categories && ! empty( $post->post_categories ) ) : ?>
						<p class="post-meta">
							<?php
								$category_index = 0;
								foreach( $post->post_categories as $category ) {
									$category_index++;
									$separator =  $category_index < count(  $post->post_categories ) ? ', ' : '';
									echo '<a href="'. esc_url( $category['permalink'] ) .'" title="' . esc_attr( $category['label'] ) . '">' . esc_html( $category['label'] ) . '</a>' . et_core_intentionally_unescaped( $separator, 'fixed_string' );
								}
							?>
						</p>
					<?php endif; ?>

				</div><!-- .et_pb_portfolio_item -->
				<?php
			}

			if ( 'on' === $show_pagination && ! is_search() ) {
				if ( function_exists( 'wp_pagenavi' ) ) {
					$pagination = wp_pagenavi( array( 'query' => $portfolio, 'echo' => false ) );
				} else {
					$next_posts_link_html = $prev_posts_link_html = '';

					if ( ! empty( $portfolio->posts_next['url'] ) ) {
						$next_posts_link_html = sprintf(
							'<div class="alignleft">
								<a href="%1$s">%2$s</a>
							</div>',
							esc_url( $portfolio->posts_next['url'] ),
							esc_html( $portfolio->posts_next['label'] )
						);
					}

					if ( ! empty( $portfolio->posts_prev['url'] ) ) {
						$prev_posts_link_html = sprintf(
							'<div class="alignright">
								<a href="%1$s">%2$s</a>
							</div>',
							esc_url( $portfolio->posts_prev['url'] ),
							esc_html( $portfolio->posts_prev['label'] )
						);
					}

					$pagination = sprintf(
						'<div class="pagination clearfix">
							%1$s
							%2$s
						</div>',
						$next_posts_link_html,
						$prev_posts_link_html
					);
				}
			}
		}

		// Reset post data
		wp_reset_postdata();

		if ( ! $posts = ob_get_clean() ) {
			$posts = self::get_no_results_template();
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$fullwidth = 'on' === $fullwidth;

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$this->add_classname( $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
			) );
		}

		// Module classnames
		$this->add_classname( array(
			$this->get_text_orientation_classname(),
			"et_pb_bg_layout_{$background_layout}",
		) );

		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}

		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		if ( ! $fullwidth ) {
			$this->add_classname( array(
				'et_pb_portfolio_grid',
				'clearfix',
			) );

			$this->remove_classname( $render_slug );
		}

		$data_background_layout       = '';
		$data_background_layout_hover = '';
		if ( $background_layout_hover_enabled ) {
			$data_background_layout = sprintf(
				' data-background-layout="%1$s"',
				esc_attr( $background_layout )
			);
			$data_background_layout_hover = sprintf(
				' data-background-layout-hover="%1$s"',
				esc_attr( $background_layout_hover )
			);
		}

		$output = sprintf(
			'<div%4$s class="%1$s"%10$s%11$s>
				<div class="et_pb_ajax_pagination_container">
					%6$s
					%5$s
					%7$s
						%2$s
					%8$s
					%9$s
				</div>
			%3$s',
			$this->module_classname( $render_slug ),
			$posts,
			( ! $container_is_closed ? '</div> <!-- .et_pb_portfolio -->' : '' ),
			$this->module_id(),
			$video_background, // #5
			$parallax_image_background,
			$fullwidth ? '' : '<div class="et_pb_portfolio_grid_items">',
			$fullwidth ? '' : '</div>',
			isset( $pagination ) ? $pagination : '',
			et_core_esc_previously( $data_background_layout ), // #10
			et_core_esc_previously( $data_background_layout_hover )
		);

		return $output;
	}
}

new ET_Builder_Module_Portfolio;
