<?php

declare(strict_types=1);

namespace Devly\Tests\WP\Assets\Unit;

use Devly\WP\Assets\Asset;
use Devly\WP\Assets\Bundle;
use Devly\WP\Assets\Exceptions\AttemptToOverwriteExistingItem;
use Devly\WP\Assets\ResolverFactory;
use PHPUnit\Framework\TestCase;

use function dirname;

class BundleTest extends TestCase
{
    private string $publicPath;
    private string $publicUri;
    private string $manifestPath;

    protected function setUp(): void
    {
        $this->publicPath   = dirname(__DIR__, 2) . '/lib';
        $this->publicUri    = 'https://example.com';
        $this->manifestPath = $this->publicPath . '/manifest.json';
    }

    public function testBundle(): void
    {
        $bundle = new Bundle(
            'theme',
            ResolverFactory::create($this->publicPath, $this->publicUri),
            [
                'scripts' => '/js/scripts.js',
                'style'   => '/css/style.css',
            ]
        );

        $bundle->each(function (Asset $asset): void {
            $this->assertEquals('css', $asset->extension());
            $this->assertTrue($asset->exists());
        }, 'css');
    }

    public function testBundleWithManifestVersionStrategy(): void
    {
        $resolver = ResolverFactory::createWithManifestVersionStrategy(
            $this->publicPath,
            $this->publicUri,
            $this->manifestPath
        );

        $bundle = new Bundle('theme', $resolver, [
            'scripts' => '/js/scripts.js',
            'style'   => '/css/style.css',
        ]);

        $bundle->each(function (Asset $asset): void {
            $this->assertEquals('css', $asset->extension());
            $this->assertEquals('https://example.com/css/style.12345.css', $asset->uri());
            $this->assertTrue($asset->exists());
        }, 'css');

        $bundle->each(function (Asset $asset): void {
            $this->assertEquals('js', $asset->extension());
            $this->assertEquals('https://example.com/js/scripts.js?id=12345', $asset->uri());
            $this->assertTrue($asset->exists());
        }, 'js');
    }

    public function testBundleWithStaticVersionStrategy(): void
    {
        $resolver = ResolverFactory::createWithStaticVersionStrategy(
            $this->publicPath,
            $this->publicUri,
            'v1',
            '%s?version=%s'
        );

        $bundle = new Bundle('theme', $resolver, [
            'scripts' => '/js/scripts.js',
            'style'   => '/css/style.css',
        ]);

        $bundle->each(function (Asset $asset): void {
            $this->assertEquals('css', $asset->extension());
            $this->assertEquals('https://example.com/css/style.css?version=v1', $asset->uri());
            $this->assertTrue($asset->exists());
        }, 'css');

        $bundle->each(function (Asset $asset): void {
            $this->assertEquals('js', $asset->extension());
            $this->assertEquals('https://example.com/js/scripts.js?version=v1', $asset->uri());
            $this->assertTrue($asset->exists());
        }, 'js');
    }

    public function testGetItemFromBundle(): void
    {
        $resolver = ResolverFactory::createWithManifestVersionStrategy(
            $this->publicPath,
            $this->publicUri,
            $this->manifestPath
        );

        $bundle = new Bundle('theme', $resolver, [
            'scripts' => '/js/scripts.js',
            'style'   => '/css/style.css',
        ]);

        $asset = $bundle->get('scripts');
        $this->assertInstanceOf(Asset::class, $asset);
    }

    public function testGetNotFoundItemFromBundle(): void
    {
        $resolver = ResolverFactory::createWithManifestVersionStrategy(
            $this->publicPath,
            $this->publicUri,
            $this->manifestPath
        );

        $bundle = new Bundle('theme', $resolver, ['scripts' => '/js/scripts.js']);

        $asset = $bundle->get('scripts');
        $this->assertInstanceOf(Asset::class, $asset);
    }

    public function testAutoGenerateHandles(): void
    {
        $bundle = new Bundle(
            'theme',
            ResolverFactory::create($this->publicPath, $this->publicUri),
            ['/js/scripts.js']
        );

        $asset = $bundle->get('scripts');
        $this->assertInstanceOf(Asset::class, $asset);
    }

    public function testAttemptToOverwriteExistingItem(): void
    {
        $this->expectException(AttemptToOverwriteExistingItem::class);
        $this->expectExceptionMessage('Handle "scripts" already exists in bundle "theme"');

        new Bundle(
            'theme',
            ResolverFactory::create($this->publicPath, $this->publicUri),
            ['/js/scripts.js', '/js/admin/scripts.js']
        );
    }

    public function testMixedWithRemote(): void
    {
        $bundle = new Bundle(
            'theme',
            ResolverFactory::create($this->publicPath, $this->publicUri),
            [
                'jquery'  => 'https://code.jquery.com/jquery-3.6.0.slim.js',
                'scripts' => '/js/admin/scripts.js',
            ]
        );

        $jquery = $bundle->get('jquery');
        $this->assertEquals('https://code.jquery.com/jquery-3.6.0.slim.js', $jquery->uri());
        $this->assertTrue($jquery->isRemote());

        $scripts = $bundle->get('scripts');
        $this->assertEquals('https://example.com/js/admin/scripts.js', $scripts->uri());
    }

    public function testRemoteOnly(): void
    {
        $bundle = new Bundle(
            'theme',
            ResolverFactory::createForRemoteAssets(),
            ['jquery' => 'https://code.jquery.com/jquery-3.6.0.slim.js']
        );

        $jquery = $bundle->get('jquery');
        $this->assertEquals('https://code.jquery.com/jquery-3.6.0.slim.js', $jquery->uri());
        $this->assertEquals('jquery-3.6.0.slim', $jquery->name());
        $this->assertTrue($jquery->exists());
    }
}
