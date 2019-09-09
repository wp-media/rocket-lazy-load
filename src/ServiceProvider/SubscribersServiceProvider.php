<?php
/**
 * Service Provider for the plugin subscribers
 *
 * @package RocketLazyload
 */

namespace RocketLazyLoadPlugin\ServiceProvider;

use RocketLazyLoadPlugin\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Adds the subscribers to the container
 *
 * @since 2.0
 * @author Remy Perona
 */
class SubscribersServiceProvider extends AbstractServiceProvider
{
    /**
     * Data provided by the service provider
     *
     * @since 2.0
     * @author Remy Perona
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
     * @author Remy Perona
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->share('RocketLazyLoadPlugin\Subscriber\ThirdParty\AMPSubscriber');

        $this->getContainer()->share('RocketLazyLoadPlugin\Subscriber\AdminPageSubscriber')
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Admin\AdminPage'))
            ->withArgument($this->getContainer()->get('plugin_basename'));

        $this->getContainer()->share('RocketLazyLoadPlugin\Subscriber\ImagifyNoticeSubscriber')
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Admin\ImagifyNotice'));

        $this->getContainer()->share('RocketLazyLoadPlugin\Subscriber\LazyloadSubscriber')
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Options\OptionArray'))
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Assets'))
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Image'))
            ->withArgument($this->getContainer()->get('RocketLazyLoadPlugin\Dependencies\RocketLazyload\Iframe'));
    }
}
