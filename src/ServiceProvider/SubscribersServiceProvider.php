<?php
/**
 * Service Provider for the plugin subscribers
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Admin\ImagifyNotice;
use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\Container\AbstractServiceProvider;

use RocketLazyLoadPlugin\Dependencies\League\Container\Definition\DefinitionInterface;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Assets;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Iframe;
use RocketLazyLoadPlugin\Dependencies\RocketLazyload\Image;
use RocketLazyLoadPlugin\Subscriber\ImagifyNoticeSubscriber;
use RocketLazyLoadPlugin\Subscriber\LazyloadSubscriber;
use RocketLazyLoadPlugin\Subscriber\ThirdParty\AMPSubscriber;

class SubscribersServiceProvider extends AbstractServiceProvider {

	public function get_common_subscribers(): array {
		return [
			AMPSubscriber::class,
			ImagifyNoticeSubscriber::class,
			LazyloadSubscriber::class
		];
	}

	public function define() {
		$this->register_service( AMPSubscriber::class )
		     ->share();

		$this->register_service( ImagifyNoticeSubscriber::class )
		     ->share()
		     ->set_definition( function ( DefinitionInterface $instance ) {
			     $instance->addArgument( ImagifyNotice::class );
		     } );

		$this->register_service( LazyloadSubscriber::class )
		     ->share()
		     ->set_definition( function ( DefinitionInterface $instance ) {
			     $instance->addArguments( [
					     Assets::class,
					     Image::class,
					     Iframe::class
				     ]
			     );
		     }
		     );
	}

}
