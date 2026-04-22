<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\Render;

class Render {
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
	public function __construct( string $template_path ) {
		$this->template_path = $template_path;
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
	public function render_template( $template, array $data = [] ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		$template_path = $this->template_path . $template . '.php';

		if ( ! is_readable( $template_path ) ) {
			return;
		}

		include $template_path;
	}
}
