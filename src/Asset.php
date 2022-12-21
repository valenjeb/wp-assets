<?php

declare(strict_types=1);

namespace Devly\WP\Assets;

use Devly\WP\Assets\Enqueue\Enqueuable;
use Mimey\MimeTypes;
use RuntimeException;

use function base64_encode;
use function class_exists;
use function file_exists;
use function file_get_contents;
use function get_headers;
use function pathinfo;
use function preg_match;
use function sprintf;

class Asset
{
    use Enqueuable;

    protected bool $remote;
    protected string $path;
    protected string $uri;
    protected string $name;
    protected string $extension;
    protected ?string $handle;
    protected bool $exists;
    /** @var false|string */
    private $contents;

    private string $dataUrl;

    private string $base64;

    /** @var false|string */
    private $mimeType;

    public function __construct(string $path, string $uri, ?string $handle = null)
    {
        $this->path   = $path;
        $this->uri    = $uri;
        $this->handle = $handle;

        $this->parsePathInfo();
    }

    protected function parsePathInfo(): void
    {
        $pathinfo        = pathinfo($this->path);
        $this->name      = (string) $pathinfo['filename'];
        $this->extension = (string) $pathinfo['extension'];
    }

    /**
     * Retrieves the file name
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get data URL of asset.
     *
     * @param string|null $mediatype MIME content type
     *
     * @throws RuntimeException If the Mimey\MimeTypes module not installed.
     */
    public function dataUrl(?string $mediatype = null): string
    {
        if (isset($this->dataUrl)) {
            return $this->dataUrl;
        }

        if (! $mediatype) {
            $mediatype = $this->contentType();
        }

        return $this->dataUrl = sprintf('data:%s;base64,%s', $mediatype, $this->base64());
    }

    /**
     * Get the MIME content type
     *
     * @throws RuntimeException If the Mimey\MimeTypes module not installed.
     */
    public function contentType(): string
    {
        if (! class_exists('Mimey\\MimeTypes')) {
            throw new RuntimeException(sprintf(
                'You must install Mimey\MimeTypes (ralouphie/mimey) extension before using %s::%s() method.',
                static::class,
                __METHOD__
            ));
        }

        if (! isset($this->mimeType)) {
            $mimes = new MimeTypes();

            $this->mimeType = $mimes->getMimeType($this->extension());
        }

        return $this->mimeType;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    /**
     * Base64-encoded contents
     */
    public function base64(): string
    {
        if (! isset($this->base64)) {
            $this->base64 = base64_encode($this->contents());
        }

        return $this->base64;
    }

    /**
     * Get the file contents
     */
    public function contents(): string
    {
        if (! $this->exists()) {
            return '';
        }

        if (! isset($this->contents)) {
            $this->contents = file_get_contents($this->path());
        }

        return $this->contents;
    }

    /**
     * Determine whether the file exists.
     */
    public function exists(): bool
    {
        if (isset($this->exists)) {
            return $this->exists;
        }

        if (! $this->isRemote()) {
            $this->exists = file_exists($this->path);
        } else {
            $this->exists = get_headers($this->path)[0] === 'HTTP/1.1 200 OK';
        }

        return $this->exists;
    }

    public function isRemote(): bool
    {
        if (! isset($this->remote)) {
            $this->remote = preg_match('/^(http(s)?:)?\/\//', $this->path) === 1;
        }

        return $this->remote;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function handle(): string
    {
        if (! isset($this->handle)) {
            $this->handle = $this->name();
        }

        return $this->handle;
    }

    public function setHandle(string $value): Asset
    {
        $this->handle = $value;

        return $this;
    }

    public function __toString(): string
    {
        return $this->uri();
    }
}
