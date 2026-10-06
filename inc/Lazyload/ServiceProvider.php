<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\Lazyload;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Assets;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Image;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Iframe;
use RocketLazyLoadPlugin\ServiceProvider\ServiceProviderSubscribersInterface;
use WPMedia\Options\OptionArray;

class ServiceProvider extends AbstractServiceProvider implements ServiceProviderSubscribersInterface {
	/**
	 * Services provided by this provider
	 *
	 * @var array
	 */
	protected $provides = [
		Assets::class,
		Image::class,
		Iframe::class,
		Subscriber::class,
	];

	/**
	 * Subscribers provided by this provider
	 *
	 * @return array
	 */
	public function get_subscribers(): array {
		return [
			Subscriber::class,
		];
	}

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
	 * Registers the provided classes
	 *
	 * @return void
	 */
	public function register(): void {
		$this->getContainer()->add( Assets::class );
		$this->getContainer()->add( Image::class );
		$this->getContainer()->add( Iframe::class );

		$this->getContainer()->addShared( Subscriber::class )
			->addArguments(
				[
					Assets::class,
					Image::class,
					Iframe::class,
					OptionArray::class,
				]
			);
	}
}
