<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\Notices;

use RocketLazyLoadPlugin\Render\Render;

class ImagifyNotice {
	/**
	 * Render instance
	 *
	 * @var Render
	 */
	private $render;

	/**
	 * Constructor
	 *
	 * @param Render $render Render instance.
	 */
	public function __construct( Render $render ) {
		$this->render = $render;
	}

	/**
	 * Renders the Imagify notice
	 *
	 * @since 2.0
	 *
	 * @return void
	 */
	public function display_notice() {
		$this->render->render_template( 'imagify-notice' );
	}
}
