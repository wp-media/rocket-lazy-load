<?php
/**
 * Admin Page view
 *
 * @package RocketLazyloadPlugin
 */

defined('ABSPATH') || die('Cheatin\' uh?');

global $wp_version;

$options = [
    'images'  => [
        'label' => __('Images', 'rocket-lazy-load'),
    ],
    'iframes' => [
        'label' => __('Iframes &amp; Videos', 'rocket-lazy-load'),
    ],
    'youtube' => [
        'label' => __('Replace Youtube videos by thumbnail', 'rocket-lazy-load'),
    ],
];
$notices = \RocketLazyLoadPlugin\Admin\Notices::get_instance();
$notices->echoNotices();
?>
<div class="wrap rocket-lazyload-settings">
    <?php $heading_tag = version_compare($wp_version, '4.3') >= 0 ? 'h1' : 'h2'; ?>
    <<?php echo $heading_tag; ?> class="screen-reader-text"><?php echo esc_html(get_admin_page_title()); ?></<?php echo $heading_tag; ?>>
    <div class="rocket-lazyload-header">
        <div>
            <p class="rocket-lazyload-title"><img src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/logo.png'); ?>" srcset="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/logo@2x.png'); ?> 2x" alt="<?php echo esc_attr(get_admin_page_title()); ?>" width="216" height="59"></p>
        </div>
        <?php $rocket_lazyload_rate_url = 'https://wordpress.org/support/plugin/rocket-lazy-load/reviews/?rate=5#postform'; ?>
        <p class="rocket-lazyload-rate-us">
            <?php
            // Translators: %1$s is a <strong> tag, %2$s is </strong><br>, %3$s is the complete link tag to Rocket Lazy Load review form, %4$s is the closing </a> tag.
            printf(__('%1$sDo you like this plugin?%2$s Please take a few seconds to %3$srate it on WordPress.org%4$s!', 'rocket-lazy-load'), '<strong>', '</strong><br>', '<a href="' . $rocket_lazyload_rate_url . '">', '</a>');
            ?>
            <br>
            <a class="stars" href="<?php echo $rocket_lazyload_rate_url; ?>"><?php echo str_repeat('<span class="dashicons dashicons-star-filled"></span>', 5); ?></a>
        </p>
    </div>
    <div class="rocket-lazyload-body">
		<div class="wrapper-nav">
			<h2 class="nav-tab-wrapper">
				<span class="nav-tab nav-tab-active" data-tab="general-settings"><?php esc_html_e( 'General settings', 'rocket-lazy-load' ); ?></span>
<?php if( !$this->plugin_cards['wp-rocket']->is_activated() ): ?>
				<span class="nav-tab" data-tab="more-optimization"><?php esc_html_e( 'More optimization', 'rocket-lazy-load' ); ?></span>
<?php endif; ?>
				<span class="nav-tab" data-tab="about-us" ><?php esc_html_e( 'About us', 'rocket-lazy-load' ); ?></span>
			</h2>
		</div>
		<div id="tab_general-settings" class="tab tab-active">
			<form action="options.php" class="rocket-lazyload-form" method="post">
				<fieldset>
					<legend class="screen-reader-text"><?php esc_html_e('Lazyload', 'rocket-lazy-load'); ?></legend>
					<p><?php esc_html_e('LazyLoad displays images, iframes and videos on a page only when they are visible to the user.', 'rocket-lazy-load'); ?></p>
					<p><?php esc_html_e('This mechanism reduces the number of HTTP requests and improves the loading time.', 'rocket-lazy-load'); ?></p>
					<ul class="rocket-lazyload-options">
						<?php foreach ($options as $slug => $infos) : ?>
							<li class="rocket-lazyload-option">
								<input type="checkbox" value="1" id="lazyload-<?php echo esc_attr($slug); ?>" name="rocket_lazyload_options[<?php echo esc_attr($slug); ?>]" <?php checked($this->option_array->get($slug, 0), 1); ?> aria-labelledby="describe-lazyload-<?php echo esc_attr($slug); ?>">
								<label for="lazyload-<?php echo esc_attr($slug); ?>">
									<span id="describe-lazyload-<?php echo esc_attr($slug); ?>" class="rocket-lazyload-label-description"><?php echo esc_html($infos['label']); ?></span>
								</label>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
				<?php settings_fields('rocket_lazyload'); ?>
				<p class="submit">
					<button type="submit" class="button button-primary">
						<span class="text"><?php esc_html_e('Save changes', 'rocket-lazy-load'); ?></span>
						<span class="icon">✓</span>
					</button>
				</p>
			</form>
		</div>
<?php if( !$this->plugin_cards['wp-rocket']->is_activated() ): ?>
        <div id="tab_more-optimization" class="tab">
            <div class="wrapper-content wrapper-intro">
                <div class="wrapper-left">
                    <div class="wrapper-img">
                        <img src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/logo-wprocket.svg'); ?>" alt="">
                    </div>
                    <div class="wrapper-txt">
                        <p>
                            <?php
                                printf(
                                    __( 'Looking for more optimization? %1$sThen you should use %2$sWP Rocket%3$s, and your site will be cached and optimized without you lifting a finger!', 'rocket-lazy-load' ),
                                    '<br>', '<strong>', '</strong>'
                                );
                            ?>
                    </div>
<?php 	if( 'installed' === $this->plugin_cards['wp-rocket']->get_status() ): ?>
					<a class="btn referer-link <?php echo esc_attr( $this->plugin_cards['wp-rocket']->get_status() ); ?>" href="<?php echo $this->plugin_cards['wp-rocket']->get_install_url(); ?>">
						<?php esc_html_e( 'Activate wp rocket', 'rocket-lazy-load' ); ?>
					</a>
<?php 	else: ?>
					<a href="https://wp-rocket.me/?utm_source=wp_plugin&utm_medium=rocket_heartbeat" class="btn" target="_blank">
						<?php esc_html_e( 'Get wp rocket', 'rocket-lazy-load' ); ?>
					</a>
<?php 	endif; ?>
                    <div class="wrapper-img"></div>
                </div>
                <div class="wrapper-right">
                    <div class="wrapper-right-img"></div>
                </div>
            </div>
            <div class="wrapper-content wrapper-numbers">
                <div class="top-part">
                    <?php
                        printf(
                            __( 'Recognized as the %1$smost powerful caching plugin%2$s by WordPress experts', 'rocket-lazy-load' ),
                            '<strong>', '</strong>'
                        );
                    ?>
                </div>
                <div class="bottom-part">
                    <ul>
                        <li>
                            <div class="visuel visuel-chiffre"></div>
                            <div class="txt">
                                <?php
                                    printf(
                                        __( 'Automatically apply more than %1$s80&#x25;%2$s of web performance best practices', 'rocket-lazy-load' ),
                                        '<strong>', '</strong>'
                                    );
                                ?>
                            </div>
                        </li>
                        <li>
                            <div class="visuel">
                                <img src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/noun_performance_1221123.svg'); ?>" alt="">
                            </div>
                            <div class="txt">
                                <?php
                                    printf(
                                        __( 'Help improve your %1$sGoogle PageSpeed%2$s score', 'rocket-lazy-load' ),
                                        '<strong>', '</strong>'
                                    );
                                ?>
                            </div>
                        </li>
                        <li>
                            <div class="visuel">
                                <img src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/noun_SEO_737036.svg'); ?>" alt="">
                            </div>
                            <div class="txt">
                                <?php
                                    printf(
                                        __( '%1$sBoost your SEO%2$s by preloading your pages and make them faster for Google\'s bots', 'rocket-lazy-load' ),
                                        '<strong>', '</strong>'
                                    );
                                ?>
                            </div>
                        </li>
                        <li>
                            <div class="visuel">
                                <img src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/noun_revenue_949180.svg'); ?>" alt="">
                            </div>
                            <div class="txt">
                                <?php
                                    printf(
                                        __( 'Improve %1$sconversions and revenue%2$s thanks to a stunning web performance', 'rocket-lazy-load' ),
                                        '<strong>', '</strong>'
                                    );
                                ?>
                            </div>
                        </li>
                        
                    </ul>
                </div>
            </div>
            <div class="wrapper-content wrapper-video">
                <div class="wrapper-iframe">
                    <script src="https://fast.wistia.com/embed/medias/s3jveyzr5h.json" async></script>
                    <script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
                    <div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">
                        <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
                            <div class="wistia_embed wistia_async_s3jveyzr5h videoFoam=true" style="height:100%;position:relative;width:100%">
                                <div class="wistia_swatch" style="height:100%;left:0;opacity:0;overflow:hidden;position:absolute;top:0;transition:opacity 200ms;width:100%;">
                                    <img src="https://fast.wistia.com/embed/medias/s3jveyzr5h/swatch"
                                        style="filter:blur(5px);height:100%;object-fit:contain;width:100%;" alt=""
                                        aria-hidden="true"
                                        onload="this.parentNode.style.opacity=1;"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrapper-content wrapper-contact">
                <div class="txt">
                    <?php
                        printf(
                            __( 'Forget complicated settings and headaches, and %1$senjoy the fastest speed results%2$s your site has ever had!', 'rocket-lazy-load' ),
                            '<strong>', '</strong>'
                        );
                    ?>
                </div>
                <div class="contact-btn">
<?php 	if( 'installed' === $this->plugin_cards['wp-rocket']->get_status() ): ?>
					<a class="btn referer-link <?php echo esc_attr( $this->plugin_cards['wp-rocket']->get_status() ); ?>" href="<?php echo $this->plugin_cards['wp-rocket']->get_install_url(); ?>">
						<?php esc_html_e( 'Activate wp rocket', 'rocket-lazy-load' ); ?>
					</a>
<?php 	else: ?>
					<a href="https://wp-rocket.me/?utm_source=wp_plugin&utm_medium=rocket_heartbeat" class="btn" target="_blank">
						<?php esc_html_e( 'Get wp rocket', 'rocket-lazy-load' ); ?>
					</a>
<?php 	endif; ?>
				</div>
            </div>
        </div>
<?php endif; ?>
		<div id="tab_about-us" class="tab">
			<div class="wrapper-top wrapper-info">
				<div class="top-img">
                    <img src="<?php echo esc_url(ROCKET_LL_ASSETS_URL . 'img/team.jpg'); ?>" alt="">
				</div>
				<div class="top-txt">
					<h2><?php esc_html_e( 'Welcome to WP Media!', 'rocket-lazy-load' ); ?></h2>
					<p><?php esc_html_e( 'Founded in 2014 in beautiful Lyon (France), WP Media is now a distributed company of more than 20 WordPress lovers living in the four corners of the world.', 'rocket-lazy-load' ); ?></p>
					<p><?php esc_html_e( 'We develop plugins that make the web a better place - faster, lighter, and easier to use.', 'rocket-lazy-load' ); ?></p>
					<p><?php esc_html_e( 'Check out our other plugins: we built them all to give a boost to the performance of your website!', 'rocket-lazy-load' ); ?></p>
				</div>
			</div>
            <div class="wrapper-bottom wrapper-link">
    			<?php $this->plugin_cards['wp-rocket']->helper(); ?>
    			<?php $this->plugin_cards['imagify']->helper(); ?>
    			<?php $this->plugin_cards['heartbeat-control']->helper(); ?>
		  </div>
        </div>
    </div>
</div>
