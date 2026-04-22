<?php

defined( 'ABSPATH' ) || exit;

return [
	\RocketLazyLoadPlugin\Render\ServiceProvider::class,
	\RocketLazyLoadPlugin\ServiceProvider\AdminServiceProvider::class,
	\RocketLazyLoadPlugin\ServiceProvider\ImagifyNoticeServiceProvider::class,
	\RocketLazyLoadPlugin\ServiceProvider\LazyloadServiceProvider::class,
];
