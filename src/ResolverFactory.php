<?php

declare(strict_types=1);

namespace Devly\WP\Assets;

use Devly\WP\Assets\Resolvers\EmptyResolver;
use Devly\WP\Assets\Resolvers\UrlResolver;
use Devly\WP\Assets\Version\EmptyVersionStrategy;
use Devly\WP\Assets\Version\JsonManifestVersionStrategy;
use Devly\WP\Assets\Version\StaticVersionStrategy;
use Devly\WP\Assets\Version\VersionStrategy;

class ResolverFactory
{
    /**
     * Create a Devly\WP\Assets\Resolvers\UrlResolver with an empty versioning strategy.
     */
    public static function create(string $path, string $uri): UrlResolver
    {
        return new UrlResolver(new Configurator($path, $uri), new EmptyVersionStrategy());
    }

    /**
     * Create a Devly\WP\Assets\Resolvers\UrlResolver with provided versioning strategy.
     */
    public static function createWith(string $path, string $uri, VersionStrategy $strategy): UrlResolver
    {
        return new UrlResolver(new Configurator($path, $uri), $strategy);
    }

    /**
     * Create a Devly\WP\Assets\Resolvers\UrlResolver with a static versioning strategy.
     */
    public static function createWithStaticVersionStrategy(
        string $path,
        string $uri,
        string $version,
        ?string $format = null
    ): UrlResolver {
        return new UrlResolver(new Configurator($path, $uri), new StaticVersionStrategy($version, $format));
    }

    /**
     * Create a Devly\WP\Assets\Resolvers\UrlResolver with a json manifest versioning strategy.
     */
    public static function createWithManifestVersionStrategy(
        string $path,
        string $uri,
        string $manifest
    ): UrlResolver {
        $manifest = new Manifest($manifest);

        return new UrlResolver(new Configurator($path, $uri), new JsonManifestVersionStrategy($manifest));
    }

    /**
     * Crete an empty resolver. Useful when bundling remote assets.
     */
    public static function createEmptyResolver(): EmptyResolver
    {
        return new EmptyResolver();
    }

    public static function createForRemoteAssets(): EmptyResolver
    {
        return new EmptyResolver();
    }
}
