<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die('-1');
}

et_core_security_check( 'edit_posts', 'et_pb_preview_nonce', '', '_GET' );

$container_style = isset( $_POST['is_fb_preview'] ) ? 'max-width: none; padding: 0;' : '';
$post_id         = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;

if ( ! current_user_can( 'edit_post', $post_id ) ) {
	$post_id = 0;
}

$post = get_post( $post_id );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />

		<?php
		/**
		 * Fires in the head, before {@see wp_head()} is called. This action can be used to
		 * insert elements into the beginning of the head before any styles are scripts.
		 *
		 * @since 1.0
		 */
		do_action( 'et_head_meta' );

		$template_directory_uri = get_template_directory_uri();
		?>

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

		<script type="text/javascript">
			document.documentElement.className = 'js';
		</script>

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<div id="page-container">
			<div id="main-content">
				<div class="container" style="<?php echo esc_attr($container_style); ?>">
					<div id="<?php echo esc_attr( apply_filters( 'et_pb_preview_wrap_id', 'content' ) ); ?>">
					<div class="<?php echo esc_attr( apply_filters( 'et_pb_preview_wrap_class', 'entry-content post-content entry content' ) ); ?>">

					<?php
						if ( isset( $_POST['shortcode' ] ) ) {
							if ( $post ) {
								// Setup postdata so post-dependent data like dynamic content
								// can be resolved.
								setup_postdata( $post );
							}

							// process content for builder plugin
							if ( et_is_builder_plugin_active() ) {
								$content = do_shortcode( wp_unslash( $_POST['shortcode'] ) );
								$content = str_replace( ']]>', ']]&gt;', $content );

								$outer_class   = apply_filters( 'et_builder_outer_content_class', array( 'et-boc' ) );
								$outer_classes = implode( ' ', $outer_class );

								$outer_id      = apply_filters( 'et_builder_outer_content_id', 'et-boc' );

								$inner_class   = apply_filters( 'et_builder_inner_content_class', array( 'et_builder_inner_content' ) );
								$inner_classes = implode( ' ', $inner_class );

								$content = sprintf(
									'<div class="%2$s" id="%4$s">
										<div class="%3$s">
											%1$s
										</div>
									</div>',
									$content,
									esc_attr( $outer_classes ),
									esc_attr( $inner_classes ),
									esc_attr( $outer_id )
								);
							} else {
								$content = apply_filters( 'the_content', wp_unslash( $_POST['shortcode'] ) );
								$content = str_replace( ']]>', ']]&gt;', $content );
							}

							if ( $post ) {
								wp_reset_postdata();
							}

							echo et_core_intentionally_unescaped( $content, 'html' );
						} else {
							printf( '<p class="et-pb-preview-loading"><span>%1$s</span></p>', esc_html__( 'Loading preview...', 'et_builder' ) );
						}
					?>

					</div> <!-- .entry-content.post-content.entry -->
					</div> <!-- #content -->
					<div class="et_pb_modal_overlay link-disabled">
						<div class="et_pb_prompt_modal">
							<h3><?php esc_html_e( 'Link Disabled', 'et_builder' ); ?></h3>
							<p><?php esc_html_e( 'During preview, link to different page is disabled', 'et_builder' ); ?></p>

							<div class="et_pb_prompt_buttons">
								<a href="#" class="et_pb_prompt_proceed"><?php esc_html_e( 'Close', 'et_builder' ); ?></a>
							</div>
						</div><!-- .et_pb_prompt_modal -->
					</div><!-- .et_pb_modal_overlay -->
				</div><!-- .container -->
			</div><!-- #main-content -->
		</div> <!-- #page-container -->
		<?php wp_footer(); ?>
	</body>
</html>
