<?php

declare(strict_types=1);

namespace Devly\WP\Assets;

use Devly\WP\Assets\Resolvers\Resolver;
use Devly\WP\Assets\Version\VersionStrategy;

class BundleFactory
{
    protected string $name;
    protected ?string $path;
    protected ?string $uri;
    /** @var array|string[] */
    protected array $bundle;
    private Resolver $resolver;
    private VersionStrategy $strategy;
    private ?string $manifest;

    /** @param string[] $bundle */
    private function __construct(string $name, array $bundle = [], ?string $path = null, ?string $uri = null)
    {
        $this->name   = $name;
        $this->path   = $path;
        $this->uri    = $uri;
        $this->bundle = $bundle;
    }

    /** @param string[] $bundle */
    public static function create(
        string $name,
        array $bundle = [],
        ?string $path = null,
        ?string $uri = null
    ): BundleFactory {
        return new self($name, $bundle, $path, $uri);
    }

    public function setResolver(Resolver $resolver): BundleFactory
    {
        $this->resolver = $resolver;

        return $this;
    }

    public function setStrategy(VersionStrategy $strategy): BundleFactory
    {
        $this->strategy = $strategy;

        return $this;
    }

    public function build(): Bundle
    {
        if (! empty($this->resolver)) {
            $resolver = $this->resolver;
        } else {
            if ($this->path && $this->uri) {
                if (! empty($this->strategy)) {
                    $resolver = ResolverFactory::createWith($this->path, $this->uri, $this->strategy);
                } else {
                    if (! empty($this->manifest)) {
                        $resolver = ResolverFactory::createWithManifestVersionStrategy(
                            $this->path,
                            $this->uri,
                            $this->manifest
                        );
                    } else {
                        $resolver = ResolverFactory::create($this->path, $this->uri);
                    }
                }
            } else {
                $resolver = ResolverFactory::createForRemoteAssets();
            }
        }

        return new Bundle($this->name, $resolver, $this->bundle);
    }

    public function setPath(?string $path): BundleFactory
    {
        $this->path = $path;

        return $this;
    }

    public function setUri(?string $uri): BundleFactory
    {
        $this->uri = $uri;

        return $this;
    }

    /** @param string[] $bundle */
    public function setBundleContents(array $bundle): BundleFactory
    {
        $this->bundle = $bundle;

        return $this;
    }

    public function setManifest(string $path): BundleFactory
    {
        $this->manifest = $path;

        return $this;
    }
}
