<?php
/**
 * Admin Page Class
 *
 * @package RocketLazyloadPlugin
 */

namespace RocketLazyLoadPlugin\Admin;

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

use RocketLazyLoadPlugin\Dependencies\LaunchpadFrameworkOptions\Interfaces\SettingsAwareInterface;
use RocketLazyLoadPlugin\Dependencies\LaunchpadFrameworkOptions\Traits\SettingsAwareTrait;


/**
 * Admin page configuration
 *
 * @since 2.0
 * @author Remy Perona
 */
class AdminPage implements SettingsAwareInterface {
	use SettingsAwareTrait;

	/**
	 * Plugin slug
	 *
	 * @since 2.0
	 * @author Remy Perona
	 *
	 * @var string
	 */
	private $slug = 'rocket_lazyload';

	/**
	 * Template path
	 *
	 * @since 2.0
	 * @author Remy Perona
	 *
	 * @var string
	 */
	private $template_path;

	/**
	 * Constructor
	 *
	 * @param string $template_path Template path.
	 *
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function __construct( string $template_path ) {
		$this->template_path = $template_path;
	}

	/**
	 * Registers plugin settings with WordPress
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function configure() {
		register_setting( $this->getSlug(), $this->getSlug() . '_options' );
	}

	/**
	 * Gets the settings page title
	 *
	 * @return string
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function getPageTitle() {
		return __( 'LazyLoad by WP Rocket', 'rocket-lazy-load' );
	}

	/**
	 * Gets the settings submenu title
	 *
	 * @return string
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function getMenuTitle() {
		return __( 'LazyLoad', 'rocket-lazy-load' );
	}

	/**
	 * Gets the plugin slug
	 *
	 * @return string
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * Gets the plugin required capability
	 *
	 * @return string
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function getCapability() {
		return 'manage_options';
	}

	/**
	 * Renders the admin page template
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	public function renderPage() {
		$this->renderTemplate( 'admin-page' );
	}

	/**
	 * Renders the given template if it's readable.
	 *
	 * @param string $template Template name.
	 *
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	protected function renderTemplate( $template ) {
		$template_path = $this->template_path . $template . '.php';

		if ( ! is_readable( $template_path ) ) {
			return;
		}

		include $template_path;
	}
}
