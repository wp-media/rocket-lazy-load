<?php
/**
 * Service Provider for the admin page classes
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Admin\AdminPage;
use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\Container\AbstractServiceProvider;
use RocketLazyLoadPlugin\Dependencies\League\Container\Definition\DefinitionInterface;

/**
 * Adds the admin page to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class AdminServiceProvider extends AbstractServiceProvider
{

	public function define()
	{
		error_log(__METHOD__);
		$this->register_service( AdminPage::class)
		     ->set_definition(function (DefinitionInterface $instance) {
			     $instance->addArguments( [
				     'template_path'
			     ] );
            });
	}
}