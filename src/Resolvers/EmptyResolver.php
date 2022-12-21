<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Resolvers;

/**
 * Empty Resolver
 *
 * Empty resolver should be used when bundle includes remote assets only.
 */
class EmptyResolver implements Resolver
{
    public function getUrl(string $path): string
    {
        return $path;
    }

    public function getPath(string $path): string
    {
        return $path;
    }
}
