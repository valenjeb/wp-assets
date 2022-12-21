<?php

declare(strict_types=1);

namespace Devly\WP\Assets;

use Devly\WP\Assets\Exceptions\BundleNotExist;
use Devly\WP\Assets\Exceptions\InvalidBundleProvided;

use function is_array;

class Manager
{
    /** @var array<string, Bundle> */
    protected array $bundles;

    /**
     * @param array<string, Bundle> $bundles
     *
     * @throws InvalidBundleProvided
     */
    public function __construct(array $bundles = [])
    {
        $this->add($bundles);
    }

    /**
     * @param string|array<string, Bundle> $name
     *
     * @throws InvalidBundleProvided If bundle was not provided.
     */
    public function add($name, ?Bundle $bundle = null): Manager
    {
        if (is_array($name)) {
            $bundles = $name;
            foreach ($bundles as $name => $bundle) {
                $this->add($name, $bundle);
            }
        } else {
            if ($bundle === null) {
                throw new InvalidBundleProvided($name);
            }

            $this->bundles[$name] = $bundle;
        }

        return $this;
    }

    /**
     * Get bundle by name.
     *
     * @throws BundleNotExist If bundle not found.
     */
    public function get(string $name): Bundle
    {
        if (! isset($this->bundles[$name])) {
            throw new BundleNotExist($name);
        }

        return $this->bundles[$name];
    }
}
