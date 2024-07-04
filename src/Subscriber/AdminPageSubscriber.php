<?php

namespace RocketLazyLoadPlugin\Subscriber;

use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\EventManagement\ClassicSubscriberInterface;
use RocketLazyLoadPlugin\Admin\AdminPage;

/**
 * Admin Page Subscriber
 *
 * @since 2.0
 * @author Remy Perona
 */
class AdminPageSubscriber implements ClassicSubscriberInterface {

	/**
	 * AdminPage instance
	 *
	 * @since 2.0
	 * @author Remy Perona
	 *
	 * @var AdminPage
	 */
	private $page;

	/**
	 * Plugin basename
	 *
	 * @since 2.0
	 * @author Remy Perona
	 *
	 * @var string
	 */
	private static $plugin_basename;

	/**
	 * Constructor
	 *
	 * @param AdminPage $page AdminPage instance.
	 * @param string $plugin_basename Plugin basename.
	 *
	 * @since 2.0
	 * @author Remy Perona
	 *
	 */
	public function __construct( AdminPage $page, $plugin_basename ) {
		$this->page            = $page;
		self::$plugin_basename = $plugin_basename;
	}

	/**
	 * Returns an array of events that this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function get_subscribed_events(): array {
		return [
			'admin_init'                                    => 'configure',
			'admin_menu'                                    => 'addAdminPage',
			"plugin_action_links_" . self::$plugin_basename => 'addPluginPageLink',
			'admin_enqueue_scripts'                         => 'enqueueAdminStyle',
		];
	}

	/**
	 * Registers the plugin settings in WordPress
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function configure() {
		$this->page->configure();
	}

	/**
	 * Adds the admin page to the settings menu
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function addAdminPage() {
		add_options_page(
			$this->page->getPageTitle(),
			$this->page->getMenuTitle(),
			$this->page->getCapability(),
			$this->page->getSlug(),
			[ $this->page, 'renderPage' ]
		);
	}

	/**
	 * Adds a link to the plugin settings on the plugins page
	 *
	 * @param array $actions Actions for the plugin.
	 *
	 * @return array
	 * @since 2.0
	 * @author Remy Perona
	 *
	 */
	public function addPluginPageLink( $actions ) {
		array_unshift(
			$actions,
			sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'options-general.php?page=' . $this->page->getSlug() ),
				__( 'Settings', 'rocket-lazy-load' )
			)
		);

		return $actions;
	}

	/**
	 * Enqueue the css for the option page
	 *
	 * @param string $hook_suffix Current page hook.
	 *
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function enqueueAdminStyle( $hook_suffix ) {
		if ( 'settings_page_rocket_lazyload' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'rocket-lazyload', \ROCKET_LL_ASSETS_URL . 'css/admin.css', null, \ROCKET_LL_VERSION );
	}
}
