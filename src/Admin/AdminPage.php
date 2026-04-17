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
	 * @param OptionArray $options Option array instance.
	 * @param string      $template_path Template path.
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
		register_setting( $this->get_slug(), $this->get_slug() . '_options' );
	}

	/**
	 * Gets the settings page title
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public function get_page_title() {
		return __( 'LazyLoad by WP Rocket', 'rocket-lazy-load' );
	}

	/**
	 * Gets the settings submenu title
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public function get_menu_title() {
		return __( 'LazyLoad', 'rocket-lazy-load' );
	}

	/**
	 * Gets the plugin slug
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Gets the plugin required capability
	 *
	 * @return string
	 *
	 * @since 2.0
	 */
	public function get_capability() {
		return 'manage_options';
	}

	/**
	 * Renders the admin page template
	 *
	 * @return void
	 *
	 * @since 2.0
	 */
	public function render_page() {
		$data = [
			'images'  => [
				'label' => __( 'Images', 'rocket-lazy-load' ),
				'value' => $this->options->get( 'images' ),
			],
			'iframes' => [
				'label' => __( 'Iframes &amp; Videos', 'rocket-lazy-load' ),
				'value' => $this->options->get( 'iframes' ),
			],
			'youtube' => [
				'label' => __( 'Replace Youtube videos by thumbnail', 'rocket-lazy-load' ),
				'value' => $this->options->get( 'youtube' ),
			],
		];

		$this->render_template( 'admin-page', $data );
	}

	/**
	 * Renders the given template if it's readable.
	 *
	 * @since 2.0
	 *
	 * @param string $template Template name.
	 * @param array  $data     Data to pass to the template.
	 *
	 * @return void
	 */
	protected function render_template( $template, $data = [] ): void {
		$template_path = $this->template_path . $template . '.php';

		if ( ! is_readable( $template_path ) ) {
			return;
		}

		include $template_path;
	}
}
