<?php
defined( 'ABSPATH' ) or	die( 'Cheatin\' uh?' );

/**
 * Add Rocket LazyLoad settings page to Settings menu.
 *
 * @since 1.1
 * @return void
 */
function rocket_lazyload_add_menu() {
	add_options_page( __( 'Rocket LazyLoad', 'rocket-lazy-load' ), __( 'Rocket LazyLoad', 'rocket-lazy-load' ), 'manage_options', 'rocket-lazyload', 'rocket_lazyload_options_output' );
}
add_action( 'admin_menu', 'rocket_lazyload_add_menu' );

/**
 * Enqueue the css for the option page
 *
 * @since 1.1
 * @author Remy Perona
 *
 * @param string $hook Current page hook.
 */
function rocket_lazyload_enqueue_scripts( $hook ) {
	if ( 'settings_page_rocket-lazyload' !== $hook ) {
		return;
	}

	wp_enqueue_style( 'rocket-lazyload', ROCKET_LL_ASSETS_URL . 'css/admin.css', null, ROCKET_LL_VERSION );
}
add_action( 'admin_enqueue_scripts', 'rocket_lazyload_enqueue_scripts' );


/**
 * Register Rocket LazyLoad settings.
 *
 * @since 1.1
 * @return void
 */
function rocket_lazyload_settings_init() {
	register_setting( 'rocket_lazyload', 'rocket_lazyload_options' );
}
add_action( 'admin_init', 'rocket_lazyload_settings_init' );


/**
 * Display Rocket LazyLoad settings.
 *
 * @since 1.1
 * @return void
 */
function rocket_lazyload_options_output() {
	global $wp_version;

	// check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	?>
	<div class="wrap rocket-lazyload-settings">
		<?php $heading_tag = version_compare( $wp_version, '4.3' ) >= 0 ? 'h1' : 'h2'; ?>
		<<?php echo $heading_tag; ?> class="screen-reader-text"><?php echo esc_html( get_admin_page_title() ); ?></<?php echo $heading_tag; ?>>
		<div class="rocket-lazyload-header">
			<div>
				<p class="rocket-lazyload-title"><?php echo esc_html( get_admin_page_title() ); ?></p>
				<p class="rocket-lazyload-subtitle"><?php _e( 'Settings', 'rocket-lazyload' ); ?></p>
			</div>
			<?php $rocket_lazyload_rate_url = 'https://wordpress.org/support/plugin/rocket-lazy-load/reviews/?rate=5#postform'; ?>
			<p class="rocket-lazyload-rate-us">
				<?php printf( __( '%1$sDo you like this plugin?%2$s Please take a few seconds to %3$srate it on WordPress.org%4$s!', 'rocket-lazyload' ), '<strong>', '</strong><br>', '<a href="' . $rocket_lazyload_rate_url . '">', '</a>' ); ?>
				<br>
				<a class="stars" href="<?php echo $rocket_lazyload_rate_url; ?>"><?php echo str_repeat( '<span class="dashicons dashicons-star-filled"></span>', 5 ); ?></a>
			</p>
		</div>
		<div class="rocket-lazyload-body">
			<form action="options.php" class="rocket-lazyload-form" method="post">
				<fieldset>
					<legend><?php _e( 'Lazyload', 'rocket-lazyload' ); ?></legend>
					<p><?php _e( 'LazyLoad displays images, iframes and videos on a page only when they are visible to the user.', 'rocket-lazyload' ); ?></p>
					<p><?php _e( 'This mechanism reduces the number of HTTP requests and improves the loading time.', 'rocket-lazyload' ); ?></p>
					<ul>
						<li class="rocket-lazyload-option">
							<input type="checkbox" value="1" id="lazyload-images" name="rocket_lazyload_options[images]" <?php checked( rocket_lazyload_get_option( 'images', 0 ), 1 ); ?> aria-describedby="describe-lazyload-images">
							<label for="lazyload-images"><span class="screen-reader-text"><?php _e( 'Images', 'rocket-lazyload' ); ?></span></label>
							<span id="describe-lazyload-images" class="rocket-lazyload-label-description"><?php _e( 'Images', 'rocket-lazyload' ); ?></span>
						</li>
						<li class="rocket-lazyload-option">
							<input type="checkbox" value="1" id="lazyload-iframes" name="rocket_lazyload_options[iframes]" <?php checked( rocket_lazyload_get_option( 'iframes', 0 ), 1 ); ?> aria-describedby="describe-lazyload-iframes">
							<label for="lazyload-iframes"><span class="screen-reader-text"><?php _e( 'Iframes & Videos', 'rocket-lazyload' ); ?></span></label>
							<span id="describe-lazyload-images" class="rocket-lazyload-label-description"><?php _e( 'Iframes & Videos', 'rocket-lazyload' ); ?></span>
						</li>
					</ul>
				</fieldset>
			<?php
			    settings_fields( 'rocket_lazyload' );
			    submit_button( __( '✓ Save changes', 'rocket-lazyload' ) );
			?>
			</form>
			<div class="rocket-lazyload-cross-sell">
				<h2 class="rocket-lazyload-cross-sell-title"><?php _e( 'Need To Boost Your Speed Even More?', 'rocket-lazyload' ); ?></h2>
				<div class="rocket-lazyload-ads">
					<a href="https://wp-rocket.me?utm_source=wp_plugin&utm_medium=rocket_lazyload"><img src="<?php echo ROCKET_LL_ASSETS_URL ?>img/wp-rocket@2x.jpg" alt="WP Rocket" width="393" height="180"></a>
					<?php if ( ! is_plugin_active( 'imagify/imagify.php' ) ) : ?>
					<a href="https://imagify.io?utm_source=wp_plugin&utm_medium=rocket_lazyload"><img src="<?php echo ROCKET_LL_ASSETS_URL ?>img/imagify@2x.jpg" alt="Imagify" width="393" height="180"></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
<?php }
