<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\Subscriber;

use RocketLazyLoadPlugin\Admin\AdminPage;
use RocketLazyLoadPlugin\Config;
use WPMedia\EventManager\SubscriberInterface;

class AdminPageSubscriber implements SubscriberInterface {
	/**
	 * AdminPage instance
	 *
	 * @var AdminPage
	 */
	private $page;

	/**
	 * Plugin basename
	 *
	 * @var string
	 */
	private static $plugin_basename;

	/**
	 * Constructor
	 *
	 * @param AdminPage $page            AdminPage instance.
	 * @param string    $plugin_basename Plugin basename.
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
	public static function get_subscribed_events(): array {
		return [
			'admin_init'            => 'configure',
			'admin_menu'            => 'addAdminPage',
			'plugin_action_links_' . self::$plugin_basename => 'addPluginPageLink',
			'admin_enqueue_scripts' => 'enqueueAdminStyle',
		];
	}

	/**
	 * Registers the plugin settings in WordPress
	 *
	 * @return void
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
	 * @since 2.0
	 */
	public function enqueueAdminStyle( $hook_suffix ) {
		if ( 'settings_page_rocket_lazyload' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'rocket-lazyload', Config::get( 'assets_url' ) . 'css/admin.css', null, Config::get( 'version' ) );
	}
}
