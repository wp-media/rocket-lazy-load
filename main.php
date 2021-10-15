<?php
defined( 'ABSPATH' ) || exit;

// Composer autoload.
if ( file_exists( ROCKET_LL_PATH . 'vendor/autoload.php' ) ) {
	require ROCKET_LL_PATH . 'vendor/autoload.php';
}

add_action( 'plugins_loaded', [ new RocketLazyLoadPlugin\Plugin(), 'load' ] );
