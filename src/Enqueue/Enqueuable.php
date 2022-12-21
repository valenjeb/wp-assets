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
     * @param string[]    $dependencies
     * @param bool|string $mediaOrBool
     *
     * @return EnqueueScript|EnqueueStyle
     */
    public function enqueue(array $dependencies = [], $mediaOrBool = null)
    {
        switch ($this->extension()) {
            case 'css':
                return $this->enqueueStyle($dependencies, $mediaOrBool);

            case 'js':
                return $this->enqueueScript($dependencies, $mediaOrBool);

            default:
                throw new RuntimeException(sprintf('"%s" file type is not enqueuable.', $this->extension()));
        }
    }

    /** @param string[] $dependencies */
    protected function enqueueStyle(array $dependencies = [], ?string $media = 'all'): EnqueueStyle
    {
        return new EnqueueStyle($this->handle(), $this->uri(), $dependencies, $media ?? 'all');
    }

    /** @param string[] $dependencies */
    protected function enqueueScript(array $dependencies, ?bool $inFooter = true): EnqueueScript
    {
        return new EnqueueScript($this->handle(), $this->uri(), $dependencies, $inFooter ?? true);
    }
}
