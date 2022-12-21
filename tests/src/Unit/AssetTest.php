<?php

declare(strict_types=1);

namespace Devly\Tests\WP\Assets\Unit;

use Devly\WP\Assets\Asset;
use PHPUnit\Framework\TestCase;

use function dirname;
use function trim;

class AssetTest extends TestCase
{
    public function testLocalFile(): void
    {
        $path  = dirname(__DIR__, 2) . '/lib/js/scripts.js';
        $uri   = 'https://example.com/lib/js/scripts.js';
        $asset = new Asset($path, $uri);

        $this->assertEquals($uri, $asset->uri());
        $this->assertEquals($path, $asset->path());
        $this->assertEquals('scripts', $asset->name());
        $this->assertEquals('js', $asset->extension());
        $this->assertEquals('application/javascript', $asset->contentType());
        $this->assertEquals("console.log('foo');", trim($asset->contents()));
        $this->assertEquals('Y29uc29sZS5sb2coJ2ZvbycpOwo=', trim($asset->base64()));
        $this->assertEquals('data:application/javascript;base64,Y29uc29sZS5sb2coJ2ZvbycpOwo=', trim($asset->dataUrl()));
        $this->assertTrue($asset->exists());
        $this->assertFalse($asset->isRemote());

        $this->assertEquals('scripts', $asset->handle());
        $this->assertEquals('custom-handle', $asset->setHandle('custom-handle')->handle());
    }

    public function testRemoteFile(): void
    {
        $uri   = 'https://code.jquery.com/jquery-3.6.0.slim.js';
        $asset = new Asset($uri, $uri);
        $this->assertTrue($asset->exists());
        $this->assertTrue($asset->isRemote());
        $this->assertEquals('jquery-3.6.0.slim', $asset->handle());
    }
}
