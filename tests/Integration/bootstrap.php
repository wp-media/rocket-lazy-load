<?php
/**
 * Bootstraps the Rocket Lazyload Plugin integration tests
 *
 * @package RocketlazyloadPlugin\Tests\Integration
 */

if (version_compare(phpversion(), '5.6.0', '<')) {
    die('Rocket Lazyload Plugin Integration Tests require PHP 5.6 or higher.');
}

// Define testing constants.
define('RLL_PLUGIN_TESTS_ROOT', __DIR__);
define('RLL_PLUGIN_ROOT', dirname(dirname(__DIR__)));

/**
 * Gets the WP tests suite directory
 *
 * @return string
 */
function rllPluginGetWPTestsDir()
{
    $tests_dir = getenv('WP_TESTS_DIR');

    // Travis CI & Vagrant SSH tests directory.
    if (empty($tests_dir)) {
        $tests_dir = '/tmp/wordpress-tests-lib';
    }
    // If the tests' includes directory does not exist, try a relative path to Core tests directory.
    if (! file_exists($tests_dir . '/includes/')) {
        $tests_dir = '../../../../tests/phpunit';
    }
    // Check it again. If it doesn't exist, stop here and post a message as to why we stopped.
    if (! file_exists($tests_dir . '/includes/')) {
        trigger_error('Unable to run the integration tests, as the WordPress test suite could not be located.', E_USER_ERROR);  // @codingStandardsIgnoreLine.
    }
    // Strip off the trailing directory separator, if it exists.
    return rtrim($tests_dir, DIRECTORY_SEPARATOR);
}

$rocket_ll_tests_dir = rllPluginGetWPTestsDir();

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function rocket_lazyload_manually_load_plugin()
{
    require RLL_PLUGIN_ROOT . '/rocket-lazy-load.php';
}
tests_add_filter('muplugins_loaded', 'rocket_lazyload_manually_load_plugin');

require_once $rocket_ll_tests_dir . '/includes/bootstrap.php';

unset($rocket_ll_tests_dir);
