<?php
/**
 * Plugin Name: Lazy Load - Optimize Images
 * Plugin URI: http://wordpress.org/plugins/rocket-lazy-load/
 * Description: The tiny Lazy Load script for WordPress without jQuery or others libraries.
 * Version: 2.4.0
 * Requires at least: 4.9
 * Requires PHP: 7.4
 * Author: WP Rocket
 * Author URI: https://wp-rocket.me
 * Text Domain: rocket-lazy-load
 * Domain Path: /languages
 *
 * @package RocketLazyloadPlugin
 *
 * Copyright 2015-2024 WP Media
 *
 * This program is free software; you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation; either version 2 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use RocketLazyLoadPlugin\Config;

defined( 'ABSPATH' ) || exit;

// Load autoloader.
if ( ! class_exists( Config::class ) && is_file( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

Config::init(
	[
		'version'     => '2.4.0',
		'wp_version'  => '4.9',
		'php_version' => '7.4',
		'basename'    => plugin_basename( __FILE__ ),
		'path'        => realpath( plugin_dir_path( __FILE__ ) ) . '/',
		'assets_url'  => plugin_dir_url( __FILE__ ) . 'assets/',
	]
);

use RocketLazyLoadPlugin\Plugin;

require Config::get( 'path' ) . 'includes/RocketLazyloadRequirementsCheck.php';

$rocket_lazyload_requirement_checks = new Rocket_Lazyload_Requirements_Check(
	[
		'plugin_name'    => 'Lazy Load by WP Rocket',
		'plugin_version' => Config::get( 'version' ),
		'wp_version'     => Config::get( 'wp_version' ),
		'php_version'    => Config::get( 'php_version' ),
	]
);

if ( $rocket_lazyload_requirement_checks->check() ) {
	$rll_providers = require Config::get( 'path' ) . 'configs/providers.php';

	$rll_plugin = new Plugin( $rll_providers );

	add_action( 'plugins_loaded', [ $rll_plugin, 'load' ] );
}

unset( $rocket_lazyload_requirement_checks );
