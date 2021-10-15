<?php
namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the subscribers to the container
 *
 * @since 2.0
 */
class SubscribersServiceProvider extends AbstractServiceProvider
{
    /**
     * Data provided by the service provider
     *
     * @since 2.0
     *
     * @var array
     */
    protected $provides = [
        'RocketLazyLoadPlugin\Subscriber\ThirdParty\AMPSubscriber',
        'RocketLazyLoadPlugin\Subscriber\AdminPageSubscriber',
        'RocketLazyLoadPlugin\Subscriber\ImagifyNoticeSubscriber',
        'RocketLazyLoadPlugin\Subscriber\LazyloadSubscriber',
    ];

    /**
     * Registers the subscribers in the container
     *
     * @since 2.0
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->share('RocketLazyLoadPlugin\Subscriber\ThirdParty\AMPSubscriber');

        $this->getContainer()->share('RocketLazyLoadPlugin\Subscriber\AdminPageSubscriber')
            ->addArgument($this->getContainer()->get('RocketLazyLoadPlugin\Admin\AdminPage'))
            ->addArgument($this->getContainer()->get('plugin_basename'));

        $this->getContainer()->share('RocketLazyLoadPlugin\Subscriber\ImagifyNoticeSubscriber')
            ->addArgument($this->getContainer()->get('RocketLazyLoadPlugin\Admin\ImagifyNotice'));

        $this->getContainer()->share('RocketLazyLoadPlugin\Subscriber\LazyloadSubscriber')
            ->addArgument($this->getContainer()->get('RocketLazyLoadPlugin\Options\OptionArray'))
            ->addArgument($this->getContainer()->get('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Assets'))
            ->addArgument($this->getContainer()->get('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Image'))
            ->addArgument($this->getContainer()->get('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Iframe'));
    }
}
