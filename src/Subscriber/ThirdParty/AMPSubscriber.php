<?php

namespace RocketLazyLoadPlugin\Subscriber\ThirdParty;

use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\EventManagement\EventManagerAwareSubscriberInterface;
use RocketLazyLoadPlugin\Dependencies\LaunchpadCore\EventManagement\EventManager;

/**
 * Manages compatibility with the AMP plugin
 *
 * @since 2.0
 * @author Remy Perona
 */
class AMPSubscriber implements EventManagerAwareSubscriberInterface {

	/**
	 * EventManager instance
	 *
	 * @var EventManager
	 */
	protected $event_manager;

	/**
	 * Returns an array of events that this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function get_subscribed_events() {
		return [
			'wp' => 'disableIfAMP',
		];
	}

	/**
	 * Set the WordPress event manager for the subscriber.
	 *
	 * @param EventManager $event_manager EventManager instance.
	 *
	 * @return void
	 */
	public function set_event_manager( EventManager $event_manager ) {
		$this->event_manager = $event_manager;
	}

	/**
	 * Disable if on AMP page
	 *
	 * @return void
	 * @author Remy Perona
	 *
	 * @since 2.0.2
	 */
	public function disableIfAMP() {
		if ( $this->isAmpEndpoint() ) {
			$this->event_manager->add_callback( 'do_rocket_lazyload', '__return_false' );
			$this->event_manager->add_callback( 'do_rocket_lazyload_iframes', '__return_false' );
		}
	}

	/**
	 * Checks if current page uses AMP
	 *
	 * @return boolean
	 * @author Remy Perona
	 *
	 * @since 2.0
	 */
	private function isAmpEndpoint() {
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			return true;
		}

		return false;
	}
}
