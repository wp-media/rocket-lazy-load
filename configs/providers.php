<?php

defined( 'ABSPATH' ) || exit;

return [
	\RocketLazyLoadPlugin\Render\ServiceProvider::class,
	\RocketLazyLoadPlugin\Admin\ServiceProvider::class,
	\RocketLazyLoadPlugin\Notices\ServiceProvider::class,
	\RocketLazyLoadPlugin\Lazyload\ServiceProvider::class,
	\RocketLazyLoadPlugin\ThirdParty\ServiceProvider::class,
];
