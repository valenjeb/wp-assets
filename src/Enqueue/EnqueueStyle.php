<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Enqueue;

use function add_filter;
use function str_replace;
use function strpos;
use function wp_add_inline_style;
use function wp_enqueue_style;
use function wp_register_style;
use function wp_style_add_data;

class EnqueueStyle
{
    protected string $handle;

    private function __construct(string $handle)
    {
        $this->handle = $handle;
    }

    /**
     * Registers and enqueues a CSS stylesheet.
     *
     * @param string[] $dependencies
     */
    public static function enqueue(string $handle, string $src, array $dependencies = [], string $media = 'all'): self
    {
        wp_enqueue_style($handle, $src, $dependencies, null, $media);

        return new self($handle);
    }

    /**
     * Registers a CSS stylesheet to be enqueued later.
     *
     * @param string[] $dependencies
     */
    public static function register(string $handle, string $src, array $dependencies = [], string $media = 'all'): self
    {
        wp_register_style($handle, $src, $dependencies, null, $media);

        return new self($handle);
    }

    public function withCondition(string $condition): EnqueueStyle
    {
        return $this->addData('conditional', $condition);
    }

    public function asAlternate(): EnqueueStyle
    {
        return $this->addData('alt', true);
    }

    public function withTitle(string $title): EnqueueStyle
    {
        return $this->addData('title', $title);
    }

    /** @param mixed $value */
    private function addData(string $key, $value): EnqueueStyle
    {
        wp_style_add_data($this->handle, $key, $value);

        return $this;
    }

    public function inline(string $snippet): EnqueueStyle
    {
        wp_add_inline_style($this->handle, $snippet);

        return $this;
    }

    public function appendInline(string $snippet): EnqueueStyle
    {
        return $this->inline($snippet);
    }

    public function preload(): EnqueueStyle
    {
        $this->addFilter(static function ($html) {
            if (strpos($html, "rel='alternate stylesheet'") !== false) {
                return str_replace("rel='alternate stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='alternate stylesheet'\"", $html);
            }

            return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
        });

        return $this;
    }

    public function asCrossOrigin(): EnqueueStyle
    {
        $this->addFilter(static function ($html) {
            return str_replace('>', ' crossorigin>', $html);
        });

        return $this;
    }

    private function addFilter(callable $filter): void
    {
        add_filter('style_loader_tag', function (string $html, string $handle, string $href, string $media) use ($filter) {
            if (is_admin() || $handle !== $this->handle) {
                return $html;
            }

            return $filter($html, $handle, $href, $media);
        }, 10, 4);
    }
}
