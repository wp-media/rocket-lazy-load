<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/**
 * Add Rocket LazyLoad settings page to Settings menu.
 *
 * @since 1.1
 * @return void
 */
function rocket_lazyload_add_menu() {
	add_options_page( __( 'LazyLoad by WP Rocket', 'rocket-lazy-load' ), __( 'LazyLoad', 'rocket-lazy-load' ), 'manage_options', 'rocket-lazyload', 'rocket_lazyload_options_output' );
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

	$options = array(
		'images' => array(
			'label' => __( 'Images', 'rocket-lazyload' ),
		),
		'iframes' => array(
			'label' => __( 'Iframes &amp; Videos', 'rocket-lazyload' ),
		),
		'youtube' => array(
			'label' => __( 'Replace Youtube videos by thumbnail', 'rocket-lazyload' ),
		),
	);

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
				<p class="rocket-lazyload-title"><img src="<?php echo ROCKET_LL_ASSETS_URL; ?>img/logo.png" srcset="<?php echo ROCKET_LL_ASSETS_URL; ?>img/logo@2x.png 2x" alt="<?php echo esc_attr( get_admin_page_title() ); ?>" width="216" height="59"></p>
				<p class="rocket-lazyload-subtitle"><?php _e( 'Settings', 'rocket-lazyload' ); ?></p>
			</div>
			<?php $rocket_lazyload_rate_url = 'https://wordpress.org/support/plugin/rocket-lazy-load/reviews/?rate=5#postform'; ?>
			<p class="rocket-lazyload-rate-us">
				<?php
				// Translators: %1$s is a <strong> tag, %2$s is </strong><br>, %3$s is the complete link tag to Rocket Lazy Load review form, %4$s is the closing </a> tag.
				printf( __( '%1$sDo you like this plugin?%2$s Please take a few seconds to %3$srate it on WordPress.org%4$s!', 'rocket-lazyload' ), '<strong>', '</strong><br>', '<a href="' . $rocket_lazyload_rate_url . '">', '</a>' );
				?>
				<br>
				<a class="stars" href="<?php echo $rocket_lazyload_rate_url; ?>"><?php echo str_repeat( '<span class="dashicons dashicons-star-filled"></span>', 5 ); ?></a>
			</p>
		</div>
		<div class="rocket-lazyload-body">
			<form action="options.php" class="rocket-lazyload-form" method="post">
				<fieldset>
					<legend class="screen-reader-text"><?php _e( 'Lazyload', 'rocket-lazyload' ); ?></legend>
					<p><?php _e( 'LazyLoad displays images, iframes and videos on a page only when they are visible to the user.', 'rocket-lazyload' ); ?></p>
					<p><?php _e( 'This mechanism reduces the number of HTTP requests and improves the loading time.', 'rocket-lazyload' ); ?></p>
					<ul class="rocket-lazyload-options">
						<?php foreach ( $options as $slug => $infos ) { ?>

						<li class="rocket-lazyload-option">
							<input type="checkbox" value="1" id="lazyload-<?php echo $slug; ?>" name="rocket_lazyload_options[<?php echo $slug; ?>]" <?php checked( rocket_lazyload_get_option( $slug, 0 ), 1 ); ?> aria-labelledby="describe-lazyload-<?php echo $slug; ?>">
							<label for="lazyload-<?php echo $slug; ?>">
								<span id="describe-lazyload-<?php echo $slug; ?>" class="rocket-lazyload-label-description"><?php echo $infos['label']; ?></span>
							</label>
						</li>

						<?php } ?>

					</ul>
				</fieldset>
			<?php settings_fields( 'rocket_lazyload' ); ?>
			
			<?php if ( ! is_plugin_active( 'wp-rocket/wp-rocket.php' ) ) { ?>
			<div class="rocket-lazyload-upgrade">

				<div class="rocket-lazyload-upgrade-cta">
					<p class="rocket-lazyload-subtitle"><?php _e( 'We recommend for you', 'rocket-lazyload' ); ?></p>
					<p class="rocket-lazyload-bigtext">
						<?php _e( 'Go Premium with', 'rocket-lazyload' ); ?>
						<img class="rocket-lazyload-rocket-logo" src="<?php echo ROCKET_LL_ASSETS_URL; ?>img/wprocket.png" srcset="<?php echo ROCKET_LL_ASSETS_URL; ?>img/wprocket@2x.png" width="232" height="63" alt="WP Rocket">
					</p>
					
					<div class="rocket-lazyload-cta-block">
						<?php $promo = __( 'Get %s OFF%s Now', 'rocket-lazyload' ); ?>
						<?php /*<span class="rocket-lazyload-cta-promo">
							<?php printf( $promo, '<strong>20%', '</strong>' ); ?>
						</span>*/ ?>
						<a class="button button-primary" href="https://wp-rocket.me/?utm_source=wp_plugin&utm_medium=rocket_lazyload"><?php _e( 'Get WP&nbsp;Rocket Now!', 'rocket-lazyload' ); ?></a>
					</div>
				</div><!-- .rocket-lazyload-upgrade-cta -->

				<div class="rocket-lazyload-upgrade-arguments">
					<ul>
						<li class="rll-upgrade-item"><?php printf( __( '%sMultiple new features%s to further improve your load time', 'rocket-lazyload' ), '<strong>', '</strong>' ) ?></li>
						<li class="rll-upgrade-item"><?php printf( __( 'All you need to %simprove your Google PageSpeed%s score', 'rocket-lazyload' ), '<strong>', '</strong>' ) ?></li>
						<li class="rll-upgrade-item"><?php printf( __( '%sBoost your SEO%s by preloading your cache page for Google’s bots', 'rocket-lazyload' ), '<strong>', '</strong>' ) ?></li>
						<li class="rll-upgrade-item"><?php printf( __( 'Watch your conversion rise with the %s100%% WooCommerce compatibility%s', 'rocket-lazyload' ), '<strong>', '</strong>' ) ?></li>
						<li class="rll-upgrade-item"><?php printf( __( 'Minimal configuration, %sImmediate results%s', 'rocket-lazyload' ), '<strong>', '</strong>' ) ?></li>
						<li class="rll-upgrade-item"><?php printf( __( 'Set up takes %s5 minutes flat%s', 'rocket-lazyload' ), '<strong>', '</strong>' ) ?></li>
						<li class="rll-upgrade-item"><?php printf( __( '%s24/7 support%s', 'rocket-lazyload' ), '<strong>', '</strong>' ) ?></li>
					</ul>
				</div><!-- .rocket-lazyload-upgrade-arguments -->
				
			</div><!-- .rocket-lazyload-upgrade -->
			<?php } ?>

			<p class="submit">
				<button type="submit" class="button button-primary">
					<span class="text"><?php _e( 'Save changes', 'rocket-lazyload' ); ?></span>
					<span class="icon">✓</span>
				</button>
			</p>
			</form>
		</div>
	</div>
<?php
}

