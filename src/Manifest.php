<?php

declare(strict_types=1);

namespace Devly\WP\Assets;

use Devly\WP\Assets\Exceptions\ItemNotFoundInManifest;
use Devly\WP\Assets\Exceptions\JsonManifestNotFound;
use Devly\WP\Assets\Exceptions\ManifestParseError;

use function array_key_exists;
use function array_keys;
use function array_values;
use function file_get_contents;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function realpath;
use function strpos;

use const DIRECTORY_SEPARATOR;
use const JSON_ERROR_NONE;

class Manifest
{
    private string $path;

    /** @var array<string, string> */
    private array $manifest;

    /**
     * @throws JsonManifestNotFound
     * @throws ManifestParseError
     */
    public function __construct(string $path)
    {
        $realpath = realpath($path);
        if ($realpath === false) {
            throw new JsonManifestNotFound($path);
        }

        $this->path     = $realpath;
        $this->manifest = $this->parseManifest($realpath);
    }

    /**
     * @return array<string, string>
     *
     * @throws ManifestParseError
     */
    private function parseManifest(string $path): array
    {
        $manifest = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ManifestParseError($this->path(), json_last_error_msg());
        }

        return $manifest;
    }

    /**
     * Get the json manifest file path.
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Get an item from the manifest.
     *
     * @throws ItemNotFoundInManifest
     */
    public function get(string $path): string
    {
        if (strpos($path, '/') !== 0) {
            $path = DIRECTORY_SEPARATOR . $path;
        }

        if (! $this->has($path)) {
            throw new ItemNotFoundInManifest($path, $this->path());
        }

        return $this->manifest[$path];
    }

    /**
     * Check whether the manifest has a specific key.
     */
    public function has(string $path): bool
    {
        return array_key_exists($path, $this->manifest);
    }

    /**
     * Get manifest keys.
     *
     * @return string[]
     */
    public function keys(): array
    {
        return array_keys($this->manifest);
    }

    /**
     * Get manifest values.
     *
     * @return string[]
     */
    public function values(): array
    {
        return array_values($this->manifest);
    }
}
