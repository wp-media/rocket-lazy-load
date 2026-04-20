<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin\ServiceProvider;

interface ServiceProviderSubscribersInterface {
	/**
	 * Subscribers provided by this provider
	 *
	 * @return array
	 */
	public function get_subscribers(): array;
}
