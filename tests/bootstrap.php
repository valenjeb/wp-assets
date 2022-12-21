<?php

/**
 * PHPUnit bootstrap file.
 */

declare(strict_types=1);

$testDir = getenv('WP_TESTS_DIR');

if (! $testDir) {
    $testDir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$phpunitPolyfillsPath = getenv('WP_TESTS_PHPUNIT_POLYFILLS_PATH');
if ($phpunitPolyfillsPath !== false) {
    define('WP_TESTS_PHPUNIT_POLYFILLS_PATH', $phpunitPolyfillsPath);
}

if (! file_exists($testDir . '/includes/functions.php')) {
    $message = sprintf(
        'Could not find %s/includes/functions.php, have you run bin/install-wp-tests.sh ?' . PHP_EOL,
        $testDir
    );

    echo $testDir; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

    exit(1);
}

// Give access to tests_add_filter() function.
require_once $testDir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin(): void // phpcs:ignore Squiz.Functions.GlobalFunction.Found
{
    require dirname(__FILE__, 2) . '/wp-assets.php';
}

tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment.
require $testDir . '/includes/bootstrap.php';
