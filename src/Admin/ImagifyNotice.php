<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\Admin;

class ImagifyNotice {
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
	public function __construct( $template_path ) {
		$this->template_path = $template_path;
	}

	/**
	 * Renders the Imagify notice
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function display_notice() {
		$this->render_template( 'imagify-notice' );
	}

	/**
	 * Renders the given template if it's readable.
	 *
	 * @since 2.0
	 *
	 * @param string $template Template name.
	 */
	protected function render_template( $template ) {
		$template_path = $this->template_path . $template . '.php';

		if ( ! is_readable( $template_path ) ) {
			return;
		}

		include $template_path;
	}
}
