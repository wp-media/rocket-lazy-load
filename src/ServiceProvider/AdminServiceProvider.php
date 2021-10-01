<?php
namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the admin page to the container
 *
 * @since 2.0
 */
class AdminServiceProvider extends AbstractServiceProvider
{
    /**
     * Data provided by the service provider
     *
     * @since 2.0
     *
     * @var array
     */
    protected $provides = [
        'RocketLazyLoadPlugin\Admin\AdminPage',
    ];

    /**
     * Registers the admin page in the container
     *
     * @since 2.0
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyLoadPlugin\Admin\AdminPage')
            ->addArgument($this->getContainer()->get('options'))
            ->addArgument($this->getContainer()->get('RocketLazyLoadPlugin\Options\OptionArray'))
            ->addArgument($this->getContainer()->get('template_path'));
    }
}
