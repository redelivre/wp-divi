<?php

class ET_Builder_Module_Posts_Navigation extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Post Navigation', 'et_builder' );
		$this->slug             = 'et_pb_post_nav';
		$this->fb_support       = true;
		$this->main_css_element = '.et_pb_posts_nav%%order_class%%';

		$this->defaults = array();

		$this->fields_defaults = array(
			'show_prev'          => array( 'on' ),
			'show_next'          => array( 'on' ),
		);

		$this->whitelisted_fields = array(
			'in_same_term',
			'taxonomy_name',
			'show_prev',
			'show_next',
			'prev_text',
			'next_text',
			'module_id',
			'module_class',
		);

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'et_builder' ),
					'categories'   => esc_html__( 'Categories', 'et_builder' ),
					'navigation'   => esc_html__( 'Navigation', 'et_builder' ),
				),
			),
		);

		$this->advanced_options = array(
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Links', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} span a, {$this->main_css_element} span a span",
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size' => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'hide_text_align' => true,
				),
			),
			'custom_margin_padding' => array(
				'css' => array(
					'main' => "{$this->main_css_element} span.nav-previous a, {$this->main_css_element} span.nav-next a",
				),
			),
			'background' => array(
				'css' => array(
					'main' => "{$this->main_css_element} a",
				),
			),
			'max_width' => array(),
			'filters' => array(),
		);

		$this->custom_css_options = array(
			'links' => array(
				'label'    => esc_html__( 'Links', 'et_builder' ),
				'selector' => 'span a',
			),
			'prev_link' => array(
				'label'    => esc_html__( 'Previous Link', 'et_builder' ),
				'selector' => 'span.nav-previous a',
			),
			'prev_link_arrow' => array(
				'label'    => esc_html__( 'Previous Link Arrow', 'et_builder' ),
				'selector' => 'span.nav-previous a span',
			),
			'next_link' => array(
				'label'    => esc_html__( 'Next Link', 'et_builder' ),
				'selector' => 'span.nav-next a',
			),
			'next_link_arrow' => array(
				'label'    => esc_html__( 'Next Link Arrow', 'et_builder' ),
				'selector' => 'span.nav-next a span',
			),
		);
	}

	function get_fields() {
		$fields = array(
			'in_same_term' => array(
				'label'           => esc_html__( 'In the same category', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => esc_html__( 'No', 'et_builder' ),
					'on'  => esc_html__( 'Yes', 'et_builder' ),
				),
				'affects'           => array(
					'taxonomy_name',
				),
				'description'      => esc_html__( 'Here you can define whether previous and next posts must be within the same taxonomy term as the current post', 'et_builder' ),
				'toggle_slug'      => 'categories',
				'computed_affects' => array(
					'__posts_navigation',
				),
			),
			'taxonomy_name' => array(
				'label'            => esc_html__( 'Custom Taxonomy Name', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'depends_show_if'  => 'on',
				'description'      => esc_html__( 'Leave blank if you\'re using this module on a Project or Post. Otherwise type the taxonomy name to make the \'In the Same Category\' option work correctly', 'et_builder' ),
				'toggle_slug'      => 'categories',
				'computed_affects' => array(
					'__posts_navigation',
				),
			),
			'show_prev' => array(
				'label'           => esc_html__( 'Show Previous Post Link', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'prev_text',
				),
				'toggle_slug'       => 'navigation',
				'description'       => esc_html__( 'Turn this on to show the previous post link', 'et_builder' ),
			),
			'show_next' => array(
				'label'           => esc_html__( 'Show Next Post Link', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'           => array(
					'next_text',
				),
				'toggle_slug'       => 'navigation',
				'description'       => esc_html__( 'Turn this on to show the next post link', 'et_builder' ),
			),
			'prev_text' => array(
				'label'           => esc_html__( 'Previous Link Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'on',
				'computed_affects' => array(
					'__posts_navigation',
				),
				'description'     => et_get_safe_localization( __( 'Define custom text for the previous link. You can use the <strong>%title</strong> variable to include the post title. Leave blank for default.', 'et_builder' ) ),
				'toggle_slug'     => 'main_content',
			),
			'next_text' => array(
				'label'           => esc_html__( 'Next Link Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'depends_show_if' => 'on',
				'computed_affects' => array(
					'__posts_navigation',
				),
				'description'     => et_get_safe_localization( __( 'Define custom text for the next link. You can use the <strong>%title</strong> variable to include the post title. Leave blank for default.', 'et_builder' ) ),
				'toggle_slug'     => 'main_content',
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
			'__posts_navigation' => array(
				'type' => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Posts_Navigation', 'get_posts_navigation' ),
				'computed_depends_on' => array(
					'in_same_term',
					'taxonomy_name',
					'prev_text',
					'next_text'
				),
			),
			'use_border_color' => array(
				'type' => 'skip',
			),
			'border_color'     => array(
				'type' => 'skip',
			),
			'border_width'     => array(
				'type' => 'skip',
			),
			'border_style'     => array(
				'type' => 'skip',
			),
		);
		return $fields;
	}

	/**
	 * Get prev and next post link data for frontend builder's post navigation module component
	 *
	 * @param int    post ID
	 * @param bool   show posts which uses same link only or not
	 * @param string excluded terms name
	 * @param string taxonomy name for in_same_terms
	 *
	 * @return string JSON encoded array of post's next and prev link
	 */
	static function get_posts_navigation( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $post;

		$defaults = array(
			'in_same_term'   => 'off',
			'taxonomy_name'  => 'category',
			'prev_text'      => '%title',
			'next_text'      => '%title',
		);

		$args = wp_parse_args( $args, $defaults );

		// taxonomy name overwrite if in_same_term option is set to off and no taxonomy name defined
		if ( '' === $args['taxonomy_name'] || 'on' !== $args['in_same_term'] ) {
			$is_singular_project   = isset( $conditional_tags['is_singular_project'] ) ? $conditional_tags['is_singular_project'] === 'true' : is_singular( 'project' );
			$args['taxonomy_name'] = $is_singular_project ? 'project_category' : 'category';
		}

		$in_same_term = ! $args['in_same_term'] || 'off' === $args['in_same_term'] ? false : true;

		if ( ! isset( $post ) && defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST['et_post_id'] ) ) {
			$post_id = sanitize_text_field( $_POST['et_post_id'] );
		} else if ( isset( $current_page['id'] ) ) {
			// Overwrite global $post value in this scope
			$post_id = intval( $current_page['id'] );
		} else if ( is_object( $post ) && isset( $post->ID ) ) {
			$post_id = $post->ID;
		} else {
			return array(
				'next' => '',
				'prev' => '',
			);
		}

		// Set current post as global $post
		$post = get_post( $post_id );

		// Get next post
		$next_post = get_next_post( $in_same_term, '', $args['taxonomy_name'] );

		$next = new stdClass();

		if ( ! empty( $next_post ) ) {

			$next_title = isset($next_post->post_title) ? esc_html( $next_post->post_title ) : esc_html__( 'Next Post' );

			$next_date = mysql2date( get_option( 'date_format' ), $next_post->post_date );
			$next_permalink = isset($next_post->ID) ? esc_url( get_the_permalink( $next_post->ID ) ) : '';

			$next_processed_title = '' === $args['next_text'] ? '%title' : $args['next_text'];

			// process Wordpress' wildcards
			$next_processed_title = str_replace( '%title', $next_title, $next_processed_title );
			$next_processed_title = str_replace( '%date', $next_date, $next_processed_title );
			$next_processed_title = str_replace( '%link', $next_permalink, $next_processed_title );

			$next->title = $next_processed_title;
			$next->id = isset($next_post->ID) ? intval( $next_post->ID ) : '';
			$next->permalink = $next_permalink;
		}

		// Get prev post
		$prev_post = get_previous_post( $in_same_term, '', $args['taxonomy_name'] );

		$prev = new stdClass();

		if ( ! empty( $prev_post ) ) {

			$prev_title = isset($prev_post->post_title) ? esc_html( $prev_post->post_title ) : esc_html__( 'Previous Post' );

			$prev_date = mysql2date( get_option( 'date_format' ), $prev_post->post_date );

			$prev_permalink = isset($prev_post->ID) ? esc_url( get_the_permalink( $prev_post->ID ) ) : '';

			$prev_processed_title = '' === $args['prev_text'] ? '%title' : $args['prev_text'];

			// process Wordpress' wildcards
			$prev_processed_title = str_replace( '%title', $prev_title, $prev_processed_title );
			$prev_processed_title = str_replace( '%date', $prev_date, $prev_processed_title );
			$prev_processed_title = str_replace( '%link', $prev_permalink, $prev_processed_title );

			$prev->title = $prev_processed_title;
			$prev->id = isset($prev_post->ID) ? intval( $prev_post->ID ) : '';
			$prev->permalink = $prev_permalink;
		}

		// Formatting returned value
		$posts_navigation = array(
			'next' => $next,
			'prev' => $prev,
		);

		return $posts_navigation;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id     = $this->shortcode_atts['module_id'];
		$module_class  = $this->shortcode_atts['module_class'];
		$in_same_term  = $this->shortcode_atts['in_same_term'];
		$taxonomy_name = $this->shortcode_atts['taxonomy_name'];
		$show_prev     = $this->shortcode_atts['show_prev'];
		$show_next     = $this->shortcode_atts['show_next'];
		$prev_text     = $this->shortcode_atts['prev_text'];
		$next_text     = $this->shortcode_atts['next_text'];

		// do not output anything if both prev and next links are disabled
		if ( 'on' !== $show_prev && 'on' !== $show_next ) {
			return;
		}

		$module_class              = ET_Builder_Element::add_module_order_class( $module_class, $function_name );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$posts_navigation = self::get_posts_navigation( array(
			'in_same_term'  => $in_same_term,
			'taxonomy_name' => $taxonomy_name,
			'prev_text'     => $prev_text,
			'next_text'     => $next_text,
		) );

		ob_start();

		$background_classname = array();

		if ( '' !== $video_background ) {
			$background_classname[] = 'et_pb_section_video';
			$background_classname[] = 'et_pb_preload';

		}

		if ( '' !== $parallax_image_background ) {
			$background_classname[] = 'et_pb_section_parallax';
		}

		$background_class_attr = empty( $background_classname ) ? '' : sprintf( ' class="%s"', esc_attr( implode( ' ', $background_classname ) ) );

		if ( 'on' === $show_prev && ! empty( $posts_navigation['prev']->permalink ) ) {
			$prev_link_text = '' !== $prev_text ? $prev_text : $posts_navigation['prev']->title;
			?>
				<span class="nav-previous">
					<a href="<?php echo esc_url( $posts_navigation['prev']->permalink ); ?>" rel="prev"<?php echo $background_class_attr; ?>>
						<?php
							echo $parallax_image_background;
							echo $video_background;
						?>
						<span class="meta-nav">&larr; </span><span class="nav-label"><?php echo esc_html( $posts_navigation['prev']->title ); ?></span>
					</a>
				</span>
			<?php
		}

		if ( 'on' === $show_next && ! empty( $posts_navigation['next']->permalink ) ) {
			$next_link_text = '' !== $next_text ? $next_text : $posts_navigation['next']->title;
			?>
				<span class="nav-next">
					<a href="<?php echo esc_url( $posts_navigation['next']->permalink ); ?>" rel="next"<?php echo $background_class_attr; ?>>
						<?php
							echo $parallax_image_background;
							echo $video_background;
						?>
						<span class="nav-label"><?php echo esc_html( $posts_navigation['next']->title ); ?></span><span class="meta-nav"> &rarr;</span>
					</a>
				</span>
			<?php
		}

		$page_links = ob_get_contents();

		ob_end_clean();

		$output = sprintf(
			'<div class="et_pb_posts_nav et_pb_module nav-single%2$s"%1$s>
				%3$s
			</div>',
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( ltrim( $module_class ) ) ) : '' ),
			$page_links
		);

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		/**
		 * @var ET_Builder_Module_Field_BoxShadow $boxShadow
		 */
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$selector = sprintf( '.%1$s .nav-previous, .%1$s .nav-next', self::get_module_order_class( $function_name ) );
		self::set_style( $function_name, $boxShadow->get_style(
			$selector,
			$this->shortcode_atts,
			array( 'important' => true )
		) );
	}

	protected function _add_additional_border_fields() {
		parent::_add_additional_border_fields();

		$this->advanced_options['border']['css'] = array(
			'main' => array(
				'border_radii'  => "{$this->main_css_element} span.nav-previous a, {$this->main_css_element} span.nav-next a",
				'border_styles' => "{$this->main_css_element} span.nav-previous a, {$this->main_css_element} span.nav-next a",
			)
		);
	}


}

new ET_Builder_Module_Posts_Navigation;
