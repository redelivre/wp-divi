<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Compatibility for Gutenberg
 *
 * @since 3.18 Added support for WP 5.0
 * @since 3.10.2
 */
class ET_Builder_Block_Editor_Integration {

	protected $gb_gallery_regexp = '/<ul class="wp-block-gallery[^"]*?">.*?<\/ul>/mis';

	public function __construct() {
		$this->init_hooks();
	}

	protected function _can_edit_post( $post ) {
		if ( function_exists( 'gutenberg_can_edit_post' ) ) {
			return gutenberg_can_edit_post( $post );
		}

		// In case WordPress is lower than version 5.0
		if ( ! function_exists( 'use_block_editor_for_post' ) ) {
			return false;
		}

		return  use_block_editor_for_post( $post );
	}

	protected function _can_edit_post_type( $type ) {
		if ( function_exists( 'gutenberg_can_edit_post_type' ) ) {
			return gutenberg_can_edit_post_type( $type );
		}

		// In case WordPress is lower than version 5.0
		if ( ! function_exists( 'use_block_editor_for_post_type' ) ) {
			return false;
		}

		return use_block_editor_for_post_type( $type );
	}

	protected function _is_block_editor_page() {
		if ( function_exists( 'is_gutenberg_page' ) ) {
			return is_gutenberg_page();
		}

		// In case WordPress is lower than version 5.0
		if ( ! function_exists( 'use_block_editor_for_post' ) ) {
			return false;
		}

		return use_block_editor_for_post( get_the_ID() );
	}

	/**
	 * Filter on map_meta_cap.
	 *
	 * @param array $caps Capabilities.
	 * @param string $cap Capability to check.
	 * @param string $user_id User ID.
	 * @param array $args Additional args.
	 *
	 * @return void
	 */
	public function map_meta_cap( $caps, $cap, $user_id, $args ) {
		// This only needs to run once,
		remove_filter( 'map_meta_cap', array( $this, 'map_meta_cap' ), 10 );
		if (
			// GB checks for 'edit_post' so do nothing in all other cases
			'edit_post' !== $cap ||
			// Ignore the case where Divi wasn't used to edit the post
			! et_pb_is_pagebuilder_used( $args[0] )
		) {
			return $caps;
		}
		// We need to add `do_not_allow` for superadmins
		$caps = array( 'do_not_allow' );

		return $caps;
	}

	/**
	 * Filter used to disable GB for certain post types.
	 *
	 * @param bool $can_edit
	 * @param string $post_type
	 *
	 * @return void
	 */
	public function gutenberg_can_edit_post_type( $can_edit, $post_type ) {
		// The tricky part here is that GB doesn't pass the post ID to this filter but only its type
		// but we need the ID to determine whether the post has been edited with Divi.
		// Since GB uses `current_user_can( 'edit_post', $post->ID )` right after call this filter,
		// We hook into `map_meta_cap` (which gets passed the ID) and do our checks there
		add_filter( 'map_meta_cap', array( $this, 'map_meta_cap' ), 10, 4 );

		return $can_edit;
	}

	/**
	 * Enqueue our GB compatibility bundle.
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets() {
		et_fb_enqueue_open_sans();
		et_fb_enqueue_bundle( 'et-builder-gutenberg', 'gutenberg.js', array( 'jquery' ) );
		et_fb_enqueue_bundle( 'et-builder-gutenberg', 'gutenberg.css', array() );
		$res = et_pb_is_pagebuilder_used();

		$post_type = get_post_type();
		$enabled_for_post_type = et_builder_enabled_for_post_type( $post_type );

		// Set helpers needed by our own Gutenberg bundle.
		wp_localize_script( 'et-builder-gutenberg', 'et_builder_gutenberg', array(
			'helpers' => array(
				'postID'             => get_the_ID(),
				'postType'           => $post_type,
				'is3rdPartyPostType' => et_builder_is_post_type_custom( $post_type ) ? 'yes' : 'no',
				'vbUrl'              => et_fb_get_vb_url(),
				'builderUsed'        => et_pb_is_pagebuilder_used(),
				'scriptDebug'        => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
				'canToggle'          => et_pb_is_allowed( 'divi_builder_control' ) && $enabled_for_post_type,
				'isEnabled'          => $enabled_for_post_type,
				'i18n'               => array(
					'placeholder' => array(
						'block'  => array(
							'title'       => esc_html__( 'Divi Builder', 'et_builder' ),
							'description' => esc_html__( 'The Divi Builder is activated on this page. To edit your page using the builder, click the Edit With The Divi Builder button.', 'et_builder' ),
						),
						'render' => array(
							'title'   => array(
								'new' => esc_html__( 'Build Your Layout Using Divi', 'et_builder' ),
								'old' => esc_html__( 'This Layout Is Built With Divi', 'et_builder' ),
							),
							'divi'    => array(
								'new' => esc_html__( 'Use Divi Builder', 'et_builder' ),
								'old' => esc_html__( 'Edit With The Divi Builder', 'et_builder' ),
							),
							'default' => esc_html__( 'Use Default Editor', 'et_builder' ),
						),
					),
				),
			),
		) );
	}

	/**
	 * Add new Divi page
	 *
	 * @return void
	 */
	public function add_new_button() {
		global $typenow;
		if ( ! $this->_can_edit_post_type( $typenow ) ) {
			return;
		}

		$edit = 'post-new.php';
		$edit .= 'post' !== $typenow ? "?post_type=$typenow" : '';

		// Create a nonce to auto activate VB on a new Auto Draft
		$url    = add_query_arg( 'et_fb_new_vb_nonce', wp_create_nonce( 'et_fb_new_vb_nonce' ), admin_url( $edit ) );
		$button = sprintf( '<a href="%s">%s</a>', esc_url( $url ), 'Divi' );
		?>
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', function() {
				var menu = document.querySelector('#split-page-title-action .dropdown');

				if (menu) {
					menu.insertAdjacentHTML('afterbegin', '<?php echo et_core_esc_previously( $button ); ?>');
					return;
				}

			});
		</script>
		<?php
	}

	/**
	 * This filter allows VB to be directly activated for Auto Drafts.
	 *
	 * @param object $post Auto Draft post.
	 *
	 * @return void
	 */
	public function auto_draft( $post ) {
		if ( ! wp_verify_nonce( $_GET['et_fb_new_vb_nonce'], 'et_fb_new_vb_nonce' ) ) {
			return;
		}

		// Save the draft
		wp_update_post( array(
			'ID'          => $post->ID,
			'post_status' => 'draft',
		) );

		// Add VB activation nonce
		$url = add_query_arg(
			'et_fb_activation_nonce',
			wp_create_nonce( 'et_fb_activation_nonce_' . $post->ID ),
			et_fb_prepare_ssl_link( get_permalink( $post ) )
		);

		// Set post meta to `off` or else `et_builder_set_content_activation` won't work...
		update_post_meta( $post->ID, '_et_pb_use_builder', 'off' );

		wp_redirect( $url );

		exit();
	}

	/**
	 * Add 'Edit With Divi Editor' links
	 *
	 * @param array $actions Currently defined actions for the row.
	 * @param object $post Current post object.
	 *
	 * @return void
	 */
	public function add_edit_link( $actions, $post ) {
		// Maybe change this with et_fb_current_user_can_save or equivalent

		if ( ! $this->_can_edit_post( $post ) || ! et_builder_enabled_for_post_type( $post->post_type )) {
			return $actions;
		}

		$post_id         = $post->ID;
		$is_divi_library = 'et_pb_layout' === get_post_type( $post_id );
		$edit_url        = $is_divi_library ? get_edit_post_link( $post_id, 'raw' ) : get_permalink( $post_id );

		if ( et_pb_is_pagebuilder_used( $post_id ) ) {
			$edit_url = et_fb_get_vb_url( $edit_url );
		} else {
			if ( ! et_pb_is_allowed( 'divi_builder_control' ) ) {
				// Do not add Divi activation link when user lacks `Toggle Divi Builder` capability.
				return $actions;
			}
			$edit_url = add_query_arg(
				array(
					'et_fb_activation_nonce' => wp_create_nonce( 'et_fb_activation_nonce_' . $post_id ),
				),
				$edit_url
			);
		}

		$edit_action = array(
			'divi' => sprintf(
				'<a href="%s" aria-label="%s">%s</a>',
				esc_url( $edit_url ),
				esc_attr( sprintf(
					__( 'Edit &#8220;%s&#8221; in Divi', 'et-builder' ),
					_draft_or_post_title( $post->ID )
				) ),
				esc_html__( 'Edit With Divi', 'et-builder' )
			),
		);

		$actions = array_merge( $actions, $edit_action );

		// I'm leaving this here in case we wanna change item position.
		// $edit_offset = array_search( 'edit', array_keys( $actions ), true );
		// $actions     = array_merge(
		// 	array_slice( $actions, 0, $edit_offset + 1 ),
		// 	$edit_action,
		// 	array_slice( $actions, $edit_offset + 1 )
		// );

		return $actions;

	}

	/**
	 * Add filters needed to show our extra row action.
	 *
	 * @return void
	 */
	public function add_edit_link_filters() {
		// For hierarchical post types.
		add_filter( 'page_row_actions', array( $this, 'add_edit_link' ), 10, 2 );
		// For non-hierarchical post types.
		add_filter( 'post_row_actions', array( $this, 'add_edit_link' ), 10, 2 );
	}

	/**
	 * Add 'Divi' to post states when builder is enabled for it.
	 *
	 * @param array $post_states Existing post states.
	 * @param object $post Current post object.
	 *
	 * @return array
	 */
	public function display_post_states( $post_states, $post ) {
		// Make sure that $post_states is an array. Third party plugin might modify $post_states and makes it null
		// which create various issue (i.e. Piklist + Having a page configured as a static page)
		if ( ! is_array( $post_states ) ) {
			$post_states = array();
		}

		if ( et_pb_is_pagebuilder_used( $post->ID ) ) {
			// Remove Gutenberg if existing
			$key = array_search( 'Gutenberg', $post_states );
			if ( false !== $key ) {
				unset( $post_states[ $key ] );
			}
			// GB devs didn't allow this to be translated so why should we ?
			$post_states[] = 'Divi';
		}

		return $post_states;
	}

	/**
	 * Ensures that Divi enabled CPTs support 'custom-fields'.
	 *
	 * @since 3.19.12
	 *
	 */
	public function ensure_post_type_supports() {
		$post_types = et_builder_get_builder_post_types();

		foreach ( $post_types as $post_type ) {
			if ( ! post_type_supports( $post_type, 'custom-fields' ) ) {
				add_post_type_support( $post_type, 'custom-fields' );
			}
		}
	}

	/**
	 * Alter update_post_metadata return value from during a REST API update
	 * when meta value isn't changed.
	 *
	 * @param mixed $result Previous result.
	 * @param int $object_id Post ID.
	 * @param string $meta_key Meta key.
	 * @param mixed $meta_value Meta value.
	 *
	 * @return mixed
	 */
	public function update_post_metadata( $result, $object_id, $meta_key, $meta_value ) {
		if ( ! in_array( $meta_key, array( '_et_pb_use_builder', '_et_pb_old_content' ) ) ) {
			// Only act if it's one of our metas
			return $result;
		}
		if ( $meta_value === get_metadata( 'post', $object_id, $meta_key, true ) ) {
			// Return true instead of false so silly WP REST API call won't die on us....
			return true;
		}

		return $result;
	}

	/**
	 * Remove empty Divi GB placeholder when processing shortcode.
	 *
	 * @param string $post_content Raw post content (shortcode).
	 *
	 * @return string
	 */
	public function et_fb_load_raw_post_content( $post_content ) {
		// Replace empty placeholder with no content so page creation will
		// still work in this case.
		return '<!-- wp:divi/placeholder /-->' === $post_content ? '' : $post_content;
	}

	/**
	 * Convert a single GB gallery to shortcode.
	 *
	 * @param string $content Post content
	 *
	 * @return string
	 */
	public function gb_gallery_to_shortcode( $gallery ) {

		$gallery   = is_array( $gallery ) ? $gallery[0] : $gallery;
		$ids       = preg_match_all( '/data-id="(\d+)"/i', $gallery, $matches ) ? $matches[1] : array();
		$columns   = preg_match( '/<ul class="wp-block-gallery columns-(\d)[^"]*?">/i', $gallery, $matches ) ? $matches[1] : 3;
		$shortcode = sprintf(
			'[gallery columns="%s" ids="%s"]',
			intval( $columns ),
			implode( ',', array_map( 'intval', $ids ) )
		);

		return $shortcode;
	}

	/**
	 * Convert all GB galleries to shortcodes.
	 *
	 * @param string $content Post content
	 *
	 * @return string
	 */
	public function gb_galleries_to_shortcodes( $content ) {
		return preg_replace_callback(
			$this->gb_gallery_regexp,
			array( $this, 'gb_gallery_to_shortcode' ),
			$content
		);
	}

	/**
	 * Check a specified post's content for GB gallery and, if present, return the first
	 *
	 * @param string $gallery Gallery data and srcs parsed from the expanded shortcode.
	 * @param int|WP_Post $post Post ID or object.
	 *
	 * @return string|array Gallery data and srcs parsed from the expanded shortcode.
	 */
	public function get_post_gallery( $gallery, $post ) {
		if ( $gallery ) {
			return $gallery;
		}

		$content = get_post_field( 'post_content', $post );
		if ( empty( $content ) ) {
			return $gallery;
		}

		if ( preg_match( $this->gb_gallery_regexp, $content, $matches ) ) {
			// Found a GB gallery
			if ( apply_filters( 'et_gb_gallery_to_shortcode', true ) ) {
				// Return as shortcode
				return do_shortcode( $this->gb_gallery_to_shortcode( $matches[0] ) );
			}
			// Return it as is
			return $matches[0];
		}

		return $gallery;
	}

	/**
	 * Delete first GB gallery in content
	 *
	 * @param string $content Content.
	 * @param bool $deleted Whether a gallery has been already deleted or not.;
	 * @return string
	 */
	public function et_delete_post_gallery( $content, $deleted ) {
		if ( $deleted ) {
			// If a gallery was already removed, do nothing
			return $content;
		}
		return preg_replace( $this->gb_gallery_regexp, '', $content, 1 );
	}

	/**
	 * Remove custom style from our metabox when GB is showing it.
	 *
	 * @param string $post_type Post type.
	 *
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {
		if ( $this->_is_block_editor_page() && function_exists( 'et_single_settings_meta_box' ) && et_builder_enabled_for_post_type( $post_type ) ) {
			// Change our metabox id so that no custom style is applied.
			remove_meta_box( 'et_settings_meta_box', $post_type, 'side' );
			add_meta_box(
				'et_settings_meta_box_gutenberg',
				esc_html__( 'Divi Page Settings', 'Divi' ),
				'et_single_settings_meta_box',
				$post_type,
				'side',
				'high'
			);
		}
	}

	/**
	 * Hook into REST API page call.
	 *
	 * @return void
	 */
	public function rest_insert_page() {
		add_filter( 'update_post_metadata', array( $this, 'update_post_metadata' ), 10, 4 );
	}

	/**
	 * Custom auth function for meta updates via REST API.
	 *
	 * @param boolean $allowed True if allowed to view the meta field by default, false if else.
	 * @param string $meta_key The meta key.
	 * @param int $id Post ID.
	 *
	 * @return bool
	 */
	public function meta_auth( $allowed, $meta_key, $id ) {
		return current_user_can( 'edit_post', $id );
	}

	/**
	 * Hook methods to WordPress
	 * Latest plugin version: 1.5
	 * @return void
	 */
	public function init_hooks() {
		if ( is_admin() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ), 4 );
			add_action( 'admin_print_scripts-edit.php', array( $this, 'add_new_button' ), 10 );
			add_action( 'admin_init', array( $this, 'add_edit_link_filters' ) );

			// Only need to add this filter is the nonce is present in the url request
			// nonce value will be checked in the filter itself.
			if ( isset( $_GET['et_fb_new_vb_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
				add_action( 'new_to_auto-draft', array( $this, 'auto_draft' ), 1 );
			}
			add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
		} else {
			// If "Enable Divi Gallery" option is enabled
			if ( apply_filters( 'et_gb_gallery_to_shortcode', false ) === true ) {
				// Converts GB galleries to shortcodes
				add_filter( 'the_content', array( $this, 'gb_galleries_to_shortcodes' ), 1 );
			}
			if ( apply_filters( 'et_gb_gallery_include_in_get_post_gallery', false ) === true ) {
				// Makes sure `get_post_gallery` returns a GB gallery if no shortcode is found
				add_filter( 'get_post_gallery', array( $this, 'get_post_gallery' ), 10, 2 );
			}
			// This filter gets called when Divi removes first gallery shortcode from
			// a gallery post (as in post format). We hook into that to ensure that the first GB gallery
			// is deleted if nothing else was
			add_filter( 'et_delete_post_gallery', array( $this, 'et_delete_post_gallery' ), 10, 2 );
			// Provide other code a simple way to access the conversion function via this custom filter
			add_filter( 'et_gb_galleries_to_shortcodes', array( $this, 'gb_galleries_to_shortcodes' ) );
		}

		add_filter( 'et_fb_load_raw_post_content', array( $this, 'et_fb_load_raw_post_content' ) );
		add_filter( 'init', array( $this, 'ensure_post_type_supports' ), 100 );

		// This is one of the most idiot things I had to do ever and its due to
		// a 10 month old-yet not fixed WP bug: https://core.trac.wordpress.org/ticket/42069
		// TLDR: when updating a post with meta via WP REST API, `update_metadata` should only
		// be called for metas whose value changed.
		// However, the equality check is fooled by values including characters that are
		// slashed or converted to entities, like " or <.
		// `update_metadata` is then called and returns `false` (because value didn't change) which results
		// in REST API page update to abort with a 500 error code....
		// To fix the issue, we hook into REST API page update and force `update_metadata` to return `true`
		// when value didn't change (only applied to our own meta keys)
		add_action( 'rest_insert_page', array( $this, 'rest_insert_page' ) );

		// Need to deal with our metabox styling when inside GB
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 1 );

		// To register the post metas is needed because we want to change their value within our custom GB blocks
		// Editing a post meta via REST API is allowed by default unless its key is protected (starts with `_`)
		// which is the case here so we also need to create a custom auth function.
		$auth = array( $this, 'meta_auth' );
		register_meta( 'post', '_et_pb_use_builder', array(
			'auth_callback' => $auth,
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
		) );
		register_meta( 'post', '_et_pb_old_content', array(
			'auth_callback' => $auth,
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
		) );
		// Looks like this isn't needed for now
		//add_filter( 'gutenberg_can_edit_post_type', array( $this, 'gutenberg_can_edit_post_type' ), 10, 2 );
	}
}


if ( et_core_is_gutenberg_active() ) {
	new ET_Builder_Block_Editor_Integration;
}
