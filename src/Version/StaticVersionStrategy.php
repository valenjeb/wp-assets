<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Version;

use function sprintf;

class StaticVersionStrategy implements VersionStrategy
{
    protected string $version;
    protected string $format;

    public function __construct(string $version, ?string $format = null)
    {
        $this->version = $version;
        $this->format  = $format ?? '%s?%s';
    }

    public function applyVersion(string $path): string
    {
        return sprintf($this->format, $path, $this->version);
    }
}
