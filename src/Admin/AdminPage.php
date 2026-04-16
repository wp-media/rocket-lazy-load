<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\Admin;

use WPMedia\Options\OptionArray;

class AdminPage {
	/**
	 * Plugin slug
	 *
	 * @var string
	 */
	private $slug = 'rocket_lazyload';

	/**
	 * OptionArray instance
	 *
	 * @var OptionArray
	 */
	private $options;

	/**
	 * Template path
	 *
	 * @var string
	 */
	private $template_path;

	/**
	 * Constructor
	 *
	 * @param string $template_path Template path.
	 */
	public function __construct( OptionArray $options, string $template_path ) {
		$this->options       = $options;
		$this->template_path = $template_path;
	}

	/**
	 * Registers plugin settings with WordPress
	 *
	 * @return void
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
