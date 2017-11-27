<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Manage the dismissed boxes
 *
 * @since 2.4 Add a delete_transient on function name (box name)
 * @since 1.3.0 $args can replace $_GET when called internaly
 * @since 1.1.10
 *
 * @param array $args An array of query args.
 */
function rocket_lazyload_dismiss_boxes( $args ) {
	$args = empty( $args ) ? $_GET : $args;

	if ( isset( $args['box'], $args['_wpnonce'] ) ) {

		if ( ! wp_verify_nonce( $args['_wpnonce'], $args['action'] . '_' . $args['box'] ) ) {
			if ( defined( 'DOING_AJAX' ) ) {
				wp_send_json( array( 'error' => 1 ) );
			} else {
				wp_nonce_ays( '' );
			}
		}

		if ( '__rocket_lazyload_imagify_notice' === $args['box'] ) {
			update_option( 'rocket_lazyload_dismiss_imagify_notice', 0 );
		}

		global $current_user;
		$actual = get_user_meta( $current_user->ID, 'rocket_lazyload_boxes', true );
		$actual = array_merge( (array) $actual, array( $args['box'] ) );
		$actual = array_filter( $actual );
		$actual = array_unique( $actual );
		update_user_meta( $current_user->ID, 'rocket_lazyload_boxes', $actual );
		delete_transient( $args['box'] );

		if ( 'admin-post.php' === $GLOBALS['pagenow'] ) {
			if ( defined( 'DOING_AJAX' ) ) {
				wp_send_json( array( 'error' => 0 ) );
			} else {
				wp_safe_redirect( wp_get_referer() );
				die();
			}
		}
	}
}
add_action( 'wp_ajax_rocket_lazyload_ignore', 'rocket_lazyload_dismiss_boxes' );
add_action( 'admin_post_rocket_lazyload_ignore', 'rocket_lazyload_dismiss_boxes' );
