<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Add a message about Imagify on the Rocket LazyLoad options page.
 *
 * @author Geoffrey Crofte
 * @return  void
 * @since 1.4.5
 */
function rocket_lazyload_imagify_notice() {
	$current_screen = get_current_screen();

	// Add the notice only on the "WP Rocket" settings, "Media Library" & "Upload New Media" screens.
	if ( 'admin_notices' === current_filter() && ( isset( $current_screen ) && 'settings_page_rocket-lazyload' !== $current_screen->base ) ) {
		return;
	}

	$boxes = get_user_meta( $GLOBALS['current_user']->ID, 'rocket_lazyload_boxes', true );

	if ( defined( 'IMAGIFY_VERSION' ) || in_array( __FUNCTION__, (array) $boxes, true ) || 1 === get_option( 'rocket_lazyload_dismiss_imagify_notice' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$action_url = wp_nonce_url( add_query_arg(
		array(
			'action'       => 'install-plugin',
			'plugin'    => 'imagify',
		),
		admin_url( 'update.php' )
	), 'install-plugin_imagify' );

	$classes = ' install-now';
	$cta_txt = esc_html__( 'Install Imagify for Free', 'rocket-lazyload' );

	$dismiss_url = wp_nonce_url(
		admin_url( 'admin-post.php?action=rocket_lazyload_ignore&box=' . __FUNCTION__ ),
		'rocket_lazyload_ignore_' . __FUNCTION__
	);
	?>

	<div id="plugin-filter" class="updated plugin-card plugin-card-imagify rktll-imagify-notice">
		<a href="<?php echo $dismiss_url; ?>" class="rktll-cross"><span class="dashicons dashicons-no"></span></a>

		<p class="rktll-imagify-logo">
			<img src="<?php echo ROCKET_LL_ASSETS_URL ?>img/logo-imagify.png" srcset="<?php echo ROCKET_LL_ASSETS_URL ?>img/logo-imagify.svg 2x" alt="Imagify" width="150" height="18">
		</p>
		<p class="rktll-imagify-msg">
			<?php _e( 'Speed up your website and boost your SEO by reducing image file sizes without losing quality with Imagify.', 'rocket-lazyload' ); ?>
		</p>
		<p class="rktll-imagify-cta">
			<a data-slug="imagify" href="<?php echo $action_url; ?>" class="button button-primary<?php echo $classes; ?>"><?php echo $cta_txt; ?></a>
		</p>
	</div>

	<?php
}
add_action( 'admin_notices', 'rocket_lazyload_imagify_notice' );

/**
 * Add Script to remove Notice thanks to JS.
 * @since  1.4.5
 * @author Geoffrey Crofte
 * @return void
 */
function rocket_lazyload_notice_script() {
	echo <<<HTML
	<script>
	jQuery( document ).ready( function( $ ){
		$( '.rktll-cross' ).on( 'click', function( e ) {
			e.preventDefault();
			var url = $( this ).attr( 'href' ).replace( 'admin-post', 'admin-ajax' );
			$.get( url ).done( $( this ).parent().hide( 'slow' ) );
		});
	} );
	</script>
HTML;
} 
add_action( 'admin_footer', 'rocket_lazyload_notice_script' );
