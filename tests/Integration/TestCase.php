<?php
declare( strict_types=1 );

namespace RocketLazyLoadPlugin\Tests\Integration;

use ReflectionObject;
use WPMedia\PHPUnit\Integration\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
	protected $config;

	public static function set_up_before_class() {
		parent::set_up_before_class();
	}

	public static function tear_down_after_class() {
		parent::tear_down_after_class();
	}

	public function set_up() {
		parent::set_up();

		if ( empty( $this->config ) ) {
			$this->loadTestDataConfig();
		}
	}

	public function tear_down() {
		parent::tear_down();
	}

	public function configTestData() {
		if ( empty( $this->config ) ) {
			$this->loadTestDataConfig();
		}

		return isset( $this->config['test_data'] )
			? $this->config['test_data']
			: $this->config;
	}

	protected function loadTestDataConfig() {
		$obj      = new ReflectionObject( $this );
		$filename = $obj->getFileName();

		$this->config = $this->getTestData( dirname( $filename ), basename( $filename, '.php' ) );
	}
}
