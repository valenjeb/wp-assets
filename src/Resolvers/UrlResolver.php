<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Resolvers;

use Devly\WP\Assets\Configurator;
use Devly\WP\Assets\Version\VersionStrategy;

class UrlResolver implements Resolver
{
    protected Configurator $config;
    protected VersionStrategy $version;

    public function __construct(Configurator $config, VersionStrategy $version)
    {
        $this->config  = $config;
        $this->version = $version;
    }

    public function getUrl(string $path): string
    {
        return $this->config->getUri($this->version->applyVersion($path));
    }

    public function getPath(string $path): string
    {
        return $this->config->getPath($path);
    }
}
