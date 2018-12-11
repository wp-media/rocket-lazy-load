<?php
namespace RocketLazyLoadPlugin\Subscriber\ThirdParty;

use RocketLazyLoadPlugin\EventManagement\SubscriberInterface;

defined('ABSPATH') || die('Cheatin\' uh?');

/**
 * Manages compatibility with the AMP plugin
 *
 * @since 2.0
 * @author Remy Perona
 */
class AMPSubscriber implements SubscriberInterface
{
    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        $events = [];

        if ($this->isAmpEndpoint()) {
            $events['do_rocket_lazyload']         = 'returnFalse';
            $events['do_rocket_lazyload_iframes'] = 'returnFalse';
        }

        return $events;
    }

    /**
     * Checks if current page uses AMP
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return boolean
     */
    private function isAmpEndpoint()
    {
        if (function_exists('is_amp_endpoint') && \is_amp_endpoint()) {
            return true;
        }

        return false;
    }

    /**
     * Returns false
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function returnFalse()
    {
        \__return_false();
    }
}
