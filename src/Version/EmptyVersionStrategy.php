<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Version;

class EmptyVersionStrategy implements VersionStrategy
{
    public function __construct()
    {
    }

    public function applyVersion(string $path): string
    {
        return $path;
    }
}
