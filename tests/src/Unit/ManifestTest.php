<?php

declare(strict_types=1);

namespace Devly\Tests\WP\Assets\Unit;

use Devly\WP\Assets\Exceptions\ItemNotFoundInManifest;
use Devly\WP\Assets\Exceptions\JsonManifestNotFound;
use Devly\WP\Assets\Exceptions\ManifestParseError;
use Devly\WP\Assets\Manifest;
use PHPUnit\Framework\TestCase;

use function dirname;
use function realpath;
use function sprintf;

class ManifestTest extends TestCase
{
    private string $manifestPath;

    protected function setUp(): void
    {
        $this->manifestPath = dirname(__DIR__, 2) . '/lib/manifest.json';
    }

    public function testManifestMethods(): void
    {
        $manifest = new Manifest($this->manifestPath);

        $this->assertEquals($this->manifestPath, $manifest->path());
        $this->assertEquals('/js/scripts.js?id=12345', $manifest->get('/js/scripts.js'));
        $this->assertTrue($manifest->has('/js/scripts.js'));
        $this->assertFalse($manifest->has('/js/not-found.js'));
    }

    public function testManifestNotFound(): void
    {
        $this->expectException(JsonManifestNotFound::class);
        $this->expectExceptionMessage('Asset manifest file "./public/manifest.json" does not exist.');

        new Manifest('./public/manifest.json');
    }

    public function testManifestParseError(): void
    {
        $manifestPath = realpath(dirname(__DIR__, 2) . '/lib/error-manifest.json');
        $this->expectException(ManifestParseError::class);
        $this->expectExceptionMessage(sprintf(
            'Error parsing JSON from asset manifest file "%s": Syntax error.',
            $manifestPath
        ));

        new Manifest($manifestPath);
    }

    public function testItemNotFoundInManifest(): void
    {
        $manifestPath = realpath($this->manifestPath);

        $this->expectException(ItemNotFoundInManifest::class);
        $this->expectExceptionMessage(sprintf(
            'Asset "/js/not-found.js" not found in manifest "%s".',
            $manifestPath
        ));

        $manifest = new Manifest($manifestPath);

        $manifest->get('/js/not-found.js');
    }

    public function testGetManifestKeysAndValues(): void
    {
        $manifestPath = realpath($this->manifestPath);

        $manifest = new Manifest($manifestPath);

        $this->assertEquals([
            '/js/scripts.js',
            '/css/style.css',
            '/css/reset.css',
        ], $manifest->keys());

        $this->assertEquals([
            '/js/scripts.js?id=12345',
            '/css/style.12345.css',
            '/css/reset.css',
        ], $manifest->values());
    }
}
