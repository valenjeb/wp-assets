<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Enqueue;

use RuntimeException;

use function sprintf;

trait Enqueuable
{
    abstract public function handle(): string;

    abstract public function uri(): string;

    abstract public function extension(): string;

    /**
     * Registers and enqueues a script or a CSS stylesheet.
     *
     * @param string[]    $dependencies
     * @param bool|string $mediaOrBool
     *
     * @return EnqueueScript|EnqueueStyle
     */
    public function enqueue(array $dependencies = [], $mediaOrBool = null)
    {
        switch ($this->extension()) {
            case 'css':
                return EnqueueStyle::enqueue($this->handle(), $this->uri(), $dependencies, $mediaOrBool ?? 'all');

            case 'js':
                return EnqueueScript::enqueue($this->handle(), $this->uri(), $dependencies, $mediaOrBool ?? true);
            default:
                throw new RuntimeException(sprintf('"%s" file type is not enqueuable.', $this->extension()));
        }
    }

    /**
     * Registers a script or a CSS stylesheet to be enqueued later.
     *
     * @param string[]    $dependencies
     * @param bool|string $mediaOrBool
     *
     * @return EnqueueScript|EnqueueStyle
     */
    public function register(array $dependencies = [], $mediaOrBool = null)
    {
        switch ($this->extension()) {
            case 'css':
                return EnqueueStyle::register($this->handle(), $this->uri(), $dependencies, $mediaOrBool ?? 'all');

            case 'js':
                return EnqueueScript::register($this->handle(), $this->uri(), $dependencies, $mediaOrBool ?? true);

            default:
                throw new RuntimeException(sprintf('"%s" file type is not enqueuable.', $this->extension()));
        }
    }
}
