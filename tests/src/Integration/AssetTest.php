<?php

declare(strict_types=1);

namespace Devly\Tests\WP\Assets\Integration;

use Devly\WP\Assets\Asset;
use WP_UnitTestCase_Base;

use function dirname;

class AssetTest extends WP_UnitTestCase_Base
{
    public function testEnqueueStylesheet(): void
    {
        $asset = new Asset(
            dirname(__DIR__) . '/lib/css/style.css',
            'http://example.com/lib/css/style.css',
            'theme-style'
        );

        $asset->enqueue();

        do_action('wp_enqueue_scripts');

        $this->assertTrue(wp_style_is('theme-style'));
    }

    public function testEnqueueScripts(): void
    {
        $asset = new Asset(
            dirname(__DIR__) . '/lib/js/scripts.js',
            'http://example.com/lib/css/scripts.js',
            'theme-scripts'
        );

        $asset->enqueue(['jquery']);

        do_action('wp_enqueue_scripts');

        $this->assertTrue(wp_script_is('jquery'));
        $this->assertTrue(wp_script_is('theme-scripts'));
    }
}
