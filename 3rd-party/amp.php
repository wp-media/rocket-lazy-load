<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Removes LazyLoad when on an AMP version of a post with the AMP for WordPress plugin from Auttomatic
 *
 * @since 1.2.2
 *
 * @author Remy Perona
 */
function rocket_lazyload_disable_on_amp() {
	if ( defined( 'AMP_QUERY_VAR' ) && function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		add_filter( 'do_rocket_lazyload', '__return_false' );
	}
}
add_action( 'wp', 'rocket_lazyload_disable_on_amp' );
