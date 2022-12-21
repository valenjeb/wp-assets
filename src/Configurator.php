<?php

declare(strict_types=1);

namespace Devly\WP\Assets;

use function ltrim;
use function rtrim;

class Configurator
{
    protected string $path;
    protected string $uri;
    protected bool $debug;

    public function __construct(string $path, string $uri, bool $debug = false)
    {
        $this->path  = rtrim($path, '/');
        $this->uri   = rtrim($uri, '/');
        $this->debug = $debug;
    }

    public function getPath(?string $path = null): string
    {
        return $this->path . ($path ? '/' . ltrim($path, '/') : '');
    }

    public function getUri(?string $path = null): string
    {
        return $this->uri . ($path ? '/' . ltrim($path, '/') : '');
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }
}
