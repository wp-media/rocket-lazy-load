<?php
namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the option array to the container
 *
 * @since 2.0
 */
class OptionServiceProvider extends AbstractServiceProvider
{
    /**
     * Data provided by the service provider
     *
     * @since 2.0
     *
     * @var array
     */
    protected $provides = [
        'RocketLazyLoadPlugin\Options\OptionArray',
    ];

    /**
     * Registers the option array in the container
     *
     * @since 2.0
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyLoadPlugin\Options\OptionArray')
            ->addArgument($this->getContainer()->get('options')->get('_options'));
    }
}
