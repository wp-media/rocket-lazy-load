<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\Admin;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use RocketLazyLoadPlugin\Render\Render;
use RocketLazyLoadPlugin\ServiceProvider\ServiceProviderSubscribersInterface;
use WPMedia\Options\OptionArray;

class ServiceProvider extends AbstractServiceProvider implements ServiceProviderSubscribersInterface {
	/**
	 * Services provided by this provider
	 *
	 * @var array
	 */
	protected $provides = [
		AdminPage::class,
		Subscriber::class,
	];

	/**
	 * Check if the service provider provides a specific service.
	 *
	 * @param string $id The id of the service.
	 *
	 * @return bool
	 */
	public function provides( string $id ): bool {
		return in_array( $id, $this->provides, true );
	}

	/**
	 * Get the list of subscribers provided by this service provider.
	 *
	 * @return array
	 */
	public function get_subscribers(): array {
		return [
			Subscriber::class,
		];
	}

	/**
	 * Registers the provided classes
	 *
	 * @return void
	 */
	public function register(): void {
		$this->getContainer()->add( AdminPage::class )
			->addArguments(
				[
					OptionArray::class,
					Render::class,
				]
			);

		$this->getContainer()->add( Subscriber::class )
			->addArguments(
				[
					AdminPage::class,
					$this->getContainer()->get( 'plugin_basename' ),
				]
			);
	}
}
