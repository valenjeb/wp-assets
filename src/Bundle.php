<?php

declare(strict_types=1);

namespace Devly\WP\Assets;

use Devly\WP\Assets\Exceptions\AttemptToOverwriteExistingItem;
use Devly\WP\Assets\Exceptions\HandleNotFoundInBundle;
use Devly\WP\Assets\Resolvers\Resolver;

use function array_merge;
use function call_user_func;
use function is_string;
use function preg_match;

class Bundle
{
    protected string $name;

    protected Resolver $resolver;
    /** @var array<string, array<string, Asset>> */
    protected array $bundle;

    /**
     * @param array<string|int, string> $bundle [handle(string|int)=>asset] if no handle is provided,
     *                                          it will be generated from file name automatically.
     *
     * @throws AttemptToOverwriteExistingItem
     */
    public function __construct(string $name, Resolver $resolver, array $bundle = [])
    {
        $this->name     = $name;
        $this->resolver = $resolver;
        $this->bundle   = [
            'css' => [],
            'js' => [],
        ];

        $this->init($bundle);
    }

    /** @param array<string|int, string> $bundle */
    private function init(array $bundle): void
    {
        foreach ($bundle as $handle => $asset) {
            $this->add($asset, is_string($handle) ? $handle : null);
        }
    }

    /** @throws AttemptToOverwriteExistingItem */
    public function add(string $asset, ?string $handle = null): void
    {
        if ($this->isRemote($asset)) {
            $asset = new Asset($asset, $asset);
        } else {
            $asset = new Asset($this->resolver->getPath($asset), $this->resolver->getUrl($asset));
        }

        if (! $this->isValidHandle($handle)) {
            $handle = $asset->name();
        }

        $asset->setHandle($this->name . '-' . $handle);

        if (isset($this->bundle[$asset->extension()][$handle])) {
            throw new AttemptToOverwriteExistingItem($handle, $this->name);
        }

        $this->bundle[$asset->extension()][$handle] = $asset;
    }

    /** @return Asset[]|array<string, array<string, Asset>> */
    public function all(?string $group = null): array
    {
        if (! $group) {
            return $this->bundle;
        }

        return $this->bundle[$group] ?? [];
    }

    /**
     * @return Asset|Asset[]
     *
     * @throws HandleNotFoundInBundle
     */
    public function css(?string $handle = null)
    {
        $assets = $this->all('css');

        if (! $handle) {
            return $assets;
        }

        if (! isset($assets[$handle])) {
            throw new HandleNotFoundInBundle($handle, $this->name);
        }

        return $assets[$handle];
    }

    /**
     * @return Asset|Asset[]
     *
     * @throws HandleNotFoundInBundle
     */
    public function js(?string $handle = null)
    {
        $assets = $this->all('js');

        if (! $handle) {
            return $assets;
        }

        if (! isset($assets[$handle])) {
            throw new HandleNotFoundInBundle($handle, $this->name);
        }

        return $assets[$handle];
    }

    public function each(callable $callback, ?string $group = null): Bundle
    {
        $bundle = [];

        if ($group) {
            $bundle = $this->all($group);
        } else {
            foreach ($this->all() as $group) {
                $bundle = array_merge($bundle, $group);
            }
        }

        if (empty($bundle)) {
            return $this;
        }

        foreach ($bundle as $asset) {
            call_user_func($callback, $asset);
        }

        return $this;
    }

    /** @param string[] $dependencies */
    public function enqueueStyles(array $dependencies = [], string $media = 'all', bool $preload = false): Bundle
    {
        $this->each(static function (Asset $asset) use ($dependencies, $media, $preload): void {
            $enqueuable = $asset->enqueue($dependencies, $media);

            if ($preload !== true) {
                return;
            }

            $enqueuable->preload();
        }, 'css');

        return $this;
    }

    /** @param string[] $dependencies */
    public function enqueueScripts(
        array $dependencies = [],
        bool $inFooter = true,
        bool $async = false,
        bool $defer = false
    ): Bundle {
        $this->each(static function (Asset $asset) use ($dependencies, $inFooter, $async, $defer): void {
            $enqueuable = $asset->enqueue($dependencies, $inFooter);
            if ($async === true) {
                $enqueuable->async();
            }

            if ($defer !== true) {
                return;
            }

            $enqueuable->defer();
        }, 'js');

        return $this;
    }

    public function enqueue(): Bundle
    {
        return $this->enqueueStyles()->enqueueScripts();
    }

    /**
     * Get an asset from the bundle.
     *
     * @throws HandleNotFoundInBundle
     */
    public function get(string $key, ?string $group = null): ?Asset
    {
        if ($group) {
            $assets = $this->all($group);

            return $assets[$key] ?? null;
        }

        try {
            return $this->css($key);
        } catch (HandleNotFoundInBundle $e) {
            return $this->js($key);
        }
    }

    private function isRemote(string $asset): bool
    {
        return preg_match('/^(http(s)?:)?\/\//', $asset) === 1;
    }

    /** @param string|int $handle */
    private function isValidHandle($handle): bool
    {
        return is_string($handle);
    }
}
