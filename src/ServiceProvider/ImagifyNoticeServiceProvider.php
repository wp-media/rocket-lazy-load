<?php
/**
 * Service Provider for the imagify notice class
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\Container\AbstractServiceProvider;
use RocketLazyLoadPlugin\Dependencies\League\Container\Definition\DefinitionInterface;


/**
 * Adds the Imagify notice to the container
 */
class ImagifyNoticeServiceProvider extends AbstractServiceProvider {

	public function define() {
		$this->register_service( \RocketLazyLoadPlugin\Admin\ImagifyNotice::class )
		     ->set_definition( function ( DefinitionInterface $instance ) {
			     $instance->addArguments( [
				     'template_path',
			     ] );
		     } );
	}
}
