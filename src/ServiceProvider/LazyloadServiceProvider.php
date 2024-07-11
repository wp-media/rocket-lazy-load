<?php
/**
 * Service Provider for the lazyload library
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\Container\AbstractServiceProvider;

/**
 * Adds the lazyload library to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class LazyloadServiceProvider extends AbstractServiceProvider {

	public function define() {
		$this->register_service( \RocketLazyLoadPlugin\Dependencies\RocketLazyload\Assets::class );
		$this->register_service( \RocketLazyLoadPlugin\Dependencies\RocketLazyload\Image::class );
		$this->register_service( \RocketLazyLoadPlugin\Dependencies\RocketLazyload\Iframe::class );
	}
}
