<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\Subscriber\ThirdParty;

use WPMedia\EventManager\EventManagerAwareSubscriberInterface;
use WPMedia\EventManager\EventManager;

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
	public static function get_subscribed_events(): array {
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
	public function set_event_manager( EventManager $event_manager ): void {
		$this->event_manager = $event_manager;
	}

	/**
	 * Disable if on AMP page
	 *
	 * @return void
	 *
	 * @since 2.0.2
	 */
	public function disableIfAMP() {
		if ( $this->isAmpEndpoint() ) {
			$this->event_manager->add_listener( 'do_rocket_lazyload', '__return_false' );
			$this->event_manager->add_listener( 'do_rocket_lazyload_iframes', '__return_false' );
		}
	}

	/**
	 * Checks if current page uses AMP
	 *
	 * @return bool
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
