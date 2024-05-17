<?php
/**
 * Service Provider for the plugin options
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\Container\AbstractServiceProvider;
use RocketLazyLoadPlugin\Dependencies\League\Container\Definition\DefinitionInterface;
use RocketLazyLoadPlugin\Options\OptionArray;


/**
 * Adds the option array to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class OptionServiceProvider extends AbstractServiceProvider
{
	public function define()
	{
		// TODO how to ->get('_options')
		$this->register_service( OptionArray::class)
		     ->set_definition(function (DefinitionInterface $instance) {
			     $instance->addArguments( [
				     'options',
			     ] );
		     });
	}
}
