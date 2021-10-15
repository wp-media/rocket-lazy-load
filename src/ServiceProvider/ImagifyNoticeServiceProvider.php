<?php
namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the Imagify notice to the container
 *
 * @since 2.0
 */
class ImagifyNoticeServiceProvider extends AbstractServiceProvider
{
    /**
     * Data provided by the service provider
     *
     * @since 2.0
     *
     * @var array
     */
    protected $provides = [
        'RocketLazyLoadPlugin\Admin\ImagifyNotice',
    ];

    /**
     * Registers the Imagify notice in the container
     *
     * @since 2.0
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add('RocketLazyLoadPlugin\Admin\ImagifyNotice')
            ->addArgument($this->getContainer()->get('template_path'));
    }
}
