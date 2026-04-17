<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use RocketLazyLoadPlugin\Admin\ImagifyNotice;
use RocketLazyLoadPlugin\Subscriber\ImagifyNoticeSubscriber;

class ImagifyNoticeServiceProvider extends AbstractServiceProvider implements ServiceProviderSubscribersInterface {
	/**
	 * Services provided by this provider
	 *
	 * @var array
	 */
	protected $provides = [
		ImagifyNotice::class,
		ImagifyNoticeSubscriber::class,
	];

	/**
	 * Subscribers provided by this provider
	 *
	 * @return array
	 */
	public function get_subscribers(): array {
		return [
			ImagifyNoticeSubscriber::class,
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
		$this->getContainer()->add( ImagifyNotice::class )
			->addArgument( $this->getContainer()->get( 'template_path' ) );

		$this->getContainer()->addShared( ImagifyNoticeSubscriber::class )
			->addArgument( ImagifyNotice::class );
	}
}
