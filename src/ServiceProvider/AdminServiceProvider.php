<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Admin\AdminPage;
use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use RocketLazyLoadPlugin\Subscriber\AdminPageSubscriber;
use WPMedia\Options\OptionArray;

class AdminServiceProvider extends AbstractServiceProvider {
	/**
	 * Services provided by this provider
	 *
	 * @var array
	 */
	protected $provides = [
		AdminPage::class,
		AdminPageSubscriber::class,
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
			AdminPageSubscriber::class,
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
					$this->container->get( 'template_path' ),
				]
			);

		$this->getContainer()->add( AdminPageSubscriber::class )
			->addArguments(
				[
					AdminPage::class,
					$this->container->get( 'plugin_basename' ),
				]
			);
	}
}
