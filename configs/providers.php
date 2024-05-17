<?php

defined( 'ABSPATH' ) || exit;
error_log('providers.php');
return [
	\RocketLazyLoadPlugin\Dependencies\LaunchpadFrameworkOptions\ServiceProvider::class,
	\RocketLazyLoadPlugin\ServiceProvider\SubscribersServiceProvider::class,
	\RocketLazyLoadPlugin\ServiceProvider\AdminServiceProvider::class,
	\RocketLazyLoadPlugin\ServiceProvider\ImagifyNoticeServiceProvider::class,
	\RocketLazyLoadPlugin\ServiceProvider\LazyloadServiceProvider::class,
	\RocketLazyLoadPlugin\ServiceProvider\OptionServiceProvider::class,
];