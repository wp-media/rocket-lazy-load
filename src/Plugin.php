<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin;

use RocketLazyLoadPlugin\Dependencies\League\Container\Argument\Literal\ArrayArgument;
use RocketLazyLoadPlugin\Dependencies\League\Container\Argument\Literal\StringArgument;
use RocketLazyLoadPlugin\Dependencies\League\Container\Container;
use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\ServiceProviderInterface;
use RocketLazyLoadPlugin\ServiceProvider\ServiceProviderSubscribersInterface;
use WPMedia\EventManager\EventManager;
use WPMedia\EventManager\PluginApiManager;
use WPMedia\EventManager\SubscriberInterface;
use WPMedia\Options\OptionArray;
use WPMedia\Options\Options;

class Plugin {
	/**
	 * Container instance
	 *
	 * @var Container
	 */
	private $container;

	/**
	 * Is plugin loaded
	 *
	 * @var bool
	 */
	private $loaded = false;

	/**
	 * Array of service providers
	 *
	 * @var array<ServiceProviderInterface>
	 */
	private $providers;

	/**
	 * Constructor
	 *
	 * @param array $providers Array of service providers.
	 */
	public function __construct( array $providers ) {
		$this->providers = $providers;
	}

	/**
	 * Load the plugin
	 *
	 * @return void
	 */
	public function load(): void {
		if ( $this->loaded ) {
			return;
		}

		$this->loaded = true;

		$this->container = new Container();

		$this->container->add( 'template_path', new StringArgument( Config::get( 'path' ) . 'views/' ) );
		$this->container->add( 'plugin_basename', new StringArgument( Config::get( 'basename' ) ) );

		$this->container->add(
			Options::class,
			function () {
				return new Options( 'rocket_lazyload' );
			}
		);

		$this->container->add( OptionArray::class )
			->addArguments(
				[
					new ArrayArgument( $this->container->get( Options::class )->get( '_options', [] ) ),
					new StringArgument( 'rocket_lazyload' ),
				]
			);

		$this->container->add(
			EventManager::class,
			function () {
				return new EventManager( new PluginApiManager() );
			}
		);

		foreach ( $this->providers as $provider ) {
			$provider_instance = new $provider();

			$this->container->addServiceProvider( $provider_instance );

			if ( $provider_instance instanceof ServiceProviderSubscribersInterface ) {
				$this->load_subscribers( $provider_instance );
			}
		}
	}

	/**
	 * Load list of event subscribers from service provider.
	 *
	 * @param ServiceProviderSubscribersInterface $service_provider Instance of service provider.
	 *
	 * @return void
	 */
	private function load_subscribers( ServiceProviderSubscribersInterface $service_provider ) {
		if ( empty( $service_provider->get_subscribers() ) ) {
			return;
		}

		foreach ( $service_provider->get_subscribers() as $subscriber ) {
			$subscriber_object = $this->container->get( $subscriber );

			if ( $subscriber_object instanceof SubscriberInterface ) {
				$this->container->get( EventManager::class )->add_subscriber( $subscriber_object );
			}
		}
	}
}
