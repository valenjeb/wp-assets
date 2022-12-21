<?php

declare(strict_types=1);

namespace Devly\Tests\WP\Assets\Integration;

use Devly\WP\Assets\Bundle;
use Devly\WP\Assets\Resolvers\EmptyResolver;
use WP_UnitTestCase_Base;

class BundleTest extends WP_UnitTestCase_Base
{
    public function testEnqueueBundleItems(): void
    {
        $bundle = new Bundle('theme', new EmptyResolver(), [
            'theme-scripts' => '/js/scripts.js',
            'theme-style'   => '/css/style.css',
        ]);

        $bundle->enqueue();

        do_action('wp_enqueue_scripts');

        $this->assertTrue(wp_style_is('theme-style'));
        $this->assertTrue(wp_script_is('theme-scripts'));
    }
}
