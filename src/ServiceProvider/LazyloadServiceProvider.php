<?php
namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the lazyload library to the container
 *
 * @since 2.0
 */
class LazyloadServiceProvider extends AbstractServiceProvider
{
    /**
     * Data provided by the service provider
     *
     * @since 2.0
     *
     * @var array
     */
    protected $provides = [
        'RocketLazyLoadPlugin\Dependencies\RocketLazyload\Assets',
        'RocketLazyLoadPlugin\Dependencies\RocketLazyload\Image',
        'RocketLazyLoadPlugin\Dependencies\RocketLazyload\Iframe',
    ];

    /**
     * Registers the lazyload library in the container
     *
     * @since 2.0
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Assets');
        $this->getContainer()->add('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Image');
        $this->getContainer()->add('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Iframe');
    }
}
