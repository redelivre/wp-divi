<?php

class ET_Builder_Module_Fullwidth_Portfolio extends ET_Builder_Module_Type_PostBased {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Portfolio', 'et_builder' );
		$this->plural     = esc_html__( 'Fullwidth Portfolios', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_portfolio';
		$this->vb_support = 'on';
		$this->fullwidth  = true;

		// need to use global settings from the slider module
		$this->global_settings_slug = 'et_pb_portfolio';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'et_builder' ),
					'elements'     => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'   => esc_html__( 'Layout', 'et_builder' ),
					'overlay'  => esc_html__( 'Overlay', 'et_builder' ),
					'rotation' => esc_html__( 'Rotation', 'et_builder' ),
					'text'     => array(
						'title'    => esc_html__( 'Text', 'et_builder' ),
						'priority' => 49,
					),
					'image'   => esc_html__( 'Image', 'et_builder' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'      => array(
				'portfolio_header'   => array(
					'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .et_pb_portfolio_title",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
					'font_size' => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'title'   => array(
					'label'    => esc_html__( 'Portfolio Item Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} h3, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h2.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h3',
					),
				),
				'caption' => array(
					'label'    => esc_html__( 'Meta', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
						'text_align' => "{$this->main_css_element} .et_pb_portfolio_image p.post-meta",
					),
				),
			),
			'background' => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'    => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element}",
							'border_styles' => "{$this->main_css_element}",
						)
					),
				),
				'image' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_portfolio_image",
							'border_styles' => "{$this->main_css_element} .et_pb_portfolio_image",
						),
					),
					'label_prefix' => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow' => array(
				'default' => array(),
				'image'   => array(
					'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'css'             => array(
						'main'         => '%%order_class%% .et_pb_portfolio_image',
						'overlay' => 'inset',
					),
					'default_on_fronts'  => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'text'       => array(
				'use_background_layout' => true,
				'css' => array(
					'text_orientation' => '%%order_class%% h2, %%order_class%% .et_pb_portfolio_image h3, %%order_class%% .et_pb_portfolio_image p, %%order_class%% .et_pb_portfolio_title, %%order_class%% .et_pb_portfolio_image .et_pb_module_header',
				),
				'options' => array(
					'text_orientation'  => array(
						'default_on_front' => 'center',
					),
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover' => 'tabs',
					),
				),
			),
			'filters'    => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug' => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'      => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_portfolio_image',
				),
			),
			'button'     => false,
		);

		$this->custom_css_fields = array(
			'portfolio_title' => array(
				'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
				'selector' => '> h2',
			),
			'portfolio_item' => array(
				'label'    => esc_html__( 'Portfolio Item', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item',
			),
			'portfolio_overlay' => array(
				'label'    => esc_html__( 'Item Overlay', 'et_builder' ),
				'selector' => 'span.et_overlay',
			),
			'portfolio_item_title' => array(
				'label'    => esc_html__( 'Item Title', 'et_builder' ),
				'selector' => '.meta h3',
			),
			'portfolio_meta' => array(
				'label'    => esc_html__( 'Meta', 'et_builder' ),
				'selector' => '.meta p',
			),
			'portfolio_arrows' => array(
				'label'    => esc_html__( 'Navigation Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( 'Mug6LhcJQ5M' ),
				'name' => esc_html__( 'An introduction to the Fullwidth Portfolio module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => esc_html__( 'Portfolio Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Title displayed above the portfolio.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'fullwidth' => array(
				'label'             => esc_html__( 'Layout', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'on'  => esc_html__( 'Carousel', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'default_on_front'  => 'on',
				'affects'           => array(
					'auto',
				),
				'tab_slug'          => 'advanced',
				'toggle_slug'       => 'layout',
				'description'       => esc_html__( 'Choose your desired portfolio layout style.', 'et_builder' ),
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'type'             => 'categories',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select the categories that you would like to include in the feed.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'taxonomy_name'    => 'project_category',
				'toggle_slug'      => 'main_content',
			),
			'posts_number' => array(
				'label'            => esc_html__( 'Posts Number', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Control how many projects are displayed. Leave blank or use 0 to not limit the amount.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'toggle_slug'      => 'main_content',
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
			'show_date' => array(
				'label'             => esc_html__( 'Show Date', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front'  => 'on',
				'toggle_slug'       => 'elements',
				'description'       => esc_html__( 'Turn the date display on or off.', 'et_builder' ),
			),
			'zoom_icon_color' => array(
				'label'             => esc_html__( 'Zoom Icon Color', 'et_builder' ),
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
				'type'                => 'select_icon',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'overlay',
			),
			'__projects'          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Fullwidth_Portfolio', 'get_portfolio_item' ),
				'computed_depends_on' => array(
					'posts_number',
					'include_categories',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get portfolio objects for portfolio module
	 *
	 * @param array  arguments that affect et_pb_portfolio query
	 * @param array  passed conditional tag for update process
	 * @param array  passed current page params
	 * @return array portfolio item data
	 */
	static function get_portfolio_item( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'posts_number'       => '',
			'include_categories' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array(
			'post_type'   => 'project',
			'post_status' => 'publish',
		);

		if ( is_numeric( $args['posts_number'] ) && $args['posts_number'] > 0 ) {
			$query_args['posts_per_page'] = $args['posts_number'];
		} else {
			$query_args['nopaging'] = true;
		}

		$include_categories = self::filter_invalid_term_ids( explode( ',', $args['include_categories'] ), 'project_category' );

		if ( ! empty( $include_categories ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'project_category',
					'field'    => 'id',
					'terms'    => $include_categories,
					'operator' => 'IN'
				)
			);
		}

		// Get portfolio query
		$query = new WP_Query( $query_args );

		// Format portfolio output, add supplementary data
		$width  = (int) apply_filters( 'et_pb_portfolio_image_width', 510 );
		$height = (int) apply_filters( 'et_pb_portfolio_image_height', 382 );

		if( $query->post_count > 0 ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();

				// Get thumbnail
				$thumbnail   = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array( $width, $height ) );

				if ( isset( $thumbnail[2] ) && isset( $thumbnail[1] ) ) {
					$orientation = ( $thumbnail[2] > $thumbnail[1] ) ? 'portrait' : 'landscape';
				} else {
					$orientation = false;
				}

				// Append value to query post
				$query->posts[ $post_index ]->post_permalink             = get_permalink();
				$query->posts[ $post_index ]->post_thumbnail             = isset( $thumbnail[0] ) ? $thumbnail[0] : false;
				$query->posts[ $post_index ]->post_thumbnail_orientation = $orientation;
				$query->posts[ $post_index ]->post_date_readable         = get_the_date();
				$query->posts[ $post_index ]->post_class_name            = get_post_class( 'et_pb_portfolio_item et_pb_grid_item ' );

				$post_index++;
			}
		} else if ( wp_doing_ajax() || et_core_is_fb_enabled() ) {
			// This is for the VB
			$posts  = '<div class="et_pb_row et_pb_no_results">';
			$posts .= self::get_no_results_template();
			$posts .= '</div>';
			$query  = array( 'posts' => $posts );
		}

		wp_reset_postdata();

		return $query;
	}

	function render( $attrs, $content = null, $render_slug ) {
		$title                           = $this->_esc_attr( 'title' );
		$fullwidth                       = $this->props['fullwidth'];
		$include_categories              = $this->props['include_categories'];
		$posts_number                    = $this->props['posts_number'];
		$show_title                      = $this->props['show_title'];
		$show_date                       = $this->props['show_date'];
		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$auto                            = $this->props['auto'];
		$auto_speed                      = $this->props['auto_speed'];
		$zoom_icon_color                 = $this->props['zoom_icon_color'];
		$hover_overlay_color             = $this->props['hover_overlay_color'];
		$hover_icon                      = $this->props['hover_icon'];
		$header_level                    = $this->props['title_level'];
		$portfolio_header                = $this->props['portfolio_header_level'];

		$zoom_and_hover_selector = '.et_pb_fullwidth_portfolio%%order_class%% .et_pb_portfolio_image';

		if ( '' !== $zoom_icon_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => "{$zoom_and_hover_selector} .et_overlay:before",
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $zoom_icon_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $render_slug, array(
				'selector'    => "{$zoom_and_hover_selector} .et_overlay",
				'declaration' => sprintf(
					'background-color: %1$s;
					border-color: %1$s;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		$args = array();
		if ( is_numeric( $posts_number ) && $posts_number > 0 ) {
			$args['posts_per_page'] = $posts_number;
		} else {
			$args['nopaging'] = true;
		}

		if ( '' !== $include_categories ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'project_category',
					'field' => 'id',
					'terms' => explode( ',', $include_categories ),
					'operator' => 'IN'
				)
			);
		}

		$projects = self::get_portfolio_item( array(
			'posts_number'       => $posts_number,
			'include_categories' => $include_categories,
		) );

		ob_start();
		if( $projects->post_count > 0 ) {
			while ( $projects->have_posts() ) {
				$projects->the_post();
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_portfolio_item et_pb_grid_item ' ); ?>>
				<?php
					$thumb = '';

					$width = 510;
					$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

					$height = 382;
					$height = (int) apply_filters( 'et_pb_portfolio_image_height', $height );

					list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array( $width, $height ) );

					$orientation = ( $thumb_height > $thumb_width ) ? 'portrait' : 'landscape';

					if ( '' !== $thumb_src ) : ?>
						<div class="et_pb_portfolio_image <?php echo esc_attr( $orientation ); ?>">
							<img src="<?php echo esc_url( $thumb_src ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"/>
							<div class="meta">
								<a href="<?php esc_url( the_permalink() ); ?>">
								<?php
									$data_icon = '' !== $hover_icon
										? sprintf(
											' data-icon="%1$s"',
											esc_attr( et_pb_process_font_icon( $hover_icon ) )
										)
										: '';

									printf( '<span class="et_overlay%1$s"%2$s></span>',
										( '' !== $hover_icon ? ' et_pb_inline_icon' : '' ),
										et_core_esc_previously( $data_icon )
									);
								?>
									<?php if ( 'on' === $show_title ) : ?>
										<<?php echo et_pb_process_header_level( $header_level, 'h3' ) ?> class="et_pb_module_header"><?php the_title(); ?></<?php echo et_pb_process_header_level( $header_level, 'h3' ) ?>>
									<?php endif; ?>

									<?php if ( 'on' === $show_date ) : ?>
										<p class="post-meta"><?php echo get_the_date(); ?></p>
									<?php endif; ?>
								</a>
							</div>
						</div>
				<?php endif; ?>
				</div>
				<?php
			}
		}

		wp_reset_postdata();

		if ( ! $posts = ob_get_clean() ) {
			$posts  = '<div class="et_pb_row et_pb_no_results">';
			$posts .= self::get_no_results_template();
			$posts .= '</div>';
		}

		$video_background = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( isset( $this->advanced_fields['image']['css'] ) ) {
			$this->add_classname( $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
			) );
		}

		$portfolio_title = sprintf( '<%1$s class="et_pb_portfolio_title">%2$s</%1$s>', et_pb_process_header_level( $portfolio_header, 'h2' ), et_core_esc_previously( $title ) );

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

		// Module classnames
		$this->add_classname( "et_pb_bg_layout_{$background_layout}" );

		if ( 'on' === $fullwidth ) {
			$this->add_classname( 'et_pb_fullwidth_portfolio_carousel' );
		} else {
			$this->add_classname( array(
				'et_pb_fullwidth_portfolio_grid',
				'clearfix',
			) );
		}

		$output = sprintf(
			'<div%3$s class="%1$s" data-auto-rotate="%4$s" data-auto-rotate-speed="%5$s"%9$s%10$s>
				%8$s
				%7$s
				%6$s
				<div class="et_pb_portfolio_items clearfix" data-portfolio-columns="">
					%2$s
				</div><!-- .et_pb_portfolio_items -->
			</div> <!-- .et_pb_fullwidth_portfolio -->',
			$this->module_classname( $render_slug ),
			$posts,
			$this->module_id(),
			( '' !== $auto && in_array( $auto, array('on', 'off') ) ? esc_attr( $auto ) : 'off' ),
			( '' !== $auto_speed && is_numeric( $auto_speed ) ? esc_attr( $auto_speed ) : '7000' ), // #5
			( '' !== $title ? $portfolio_title : '' ),
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover ) // #10
		);

		return $output;
	}
}

new ET_Builder_Module_Fullwidth_Portfolio;
