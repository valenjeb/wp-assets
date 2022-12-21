<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Version;

use Devly\WP\Assets\Exceptions\ItemNotFoundInManifest;
use Devly\WP\Assets\Exceptions\UnableToApplyVersion;
use Devly\WP\Assets\Manifest;

class JsonManifestVersionStrategy implements VersionStrategy
{
    protected Manifest $manifest;

    public function __construct(Manifest $manifest)
    {
        $this->manifest = $manifest;
    }

    public function applyVersion(string $path): string
    {
        try {
            return $this->manifest->get($path);
        } catch (ItemNotFoundInManifest $e) {
            throw new UnableToApplyVersion($e->getMessage());
        }
    }
}
