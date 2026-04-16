<?php
declare(strict_types=1);

namespace RocketLazyLoadPlugin;

class Config {
	/**
	 * The configuration container.
	 * @var array<string, mixed>|null
	 */
	private static ?array $container = null;

	/**
	 * Initialize the configuration container.
	 *
	 * @param array<string, mixed> $container
	 */
	public static function init( array $container ): void {
		if ( isset( self::$container ) ) {
			return;
		}

		self::$container = $container;
	}

	/**
	 * Get a configuration value by name.
	 *
	 * @param string $name The name of the configuration value.
	 *
	 * @return mixed
	 */
	public static function get( string $name ) {
		if ( ! isset( self::$container ) || ! array_key_exists( $name, self::$container ) ) {
			return null;
		}

		return self::$container[ $name ];
	}
}