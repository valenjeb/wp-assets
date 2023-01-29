<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Enqueue;

use function add_filter;
use function sprintf;
use function str_replace;
use function wp_add_inline_script;
use function wp_enqueue_script;
use function wp_register_script;
use function wp_localize_script;
use function wp_scripts;

class EnqueueScript
{
    protected string $handle;

    private function __construct(string $handle)
    {
        $this->handle = $handle;
    }

    /**
     * Registers and enqueues a script.
     *
     * @param string[] $dependencies
     */
    public static function enqueue(string $handle, string $src, array $dependencies = [], bool $inFooter = true): self
    {
        wp_enqueue_script($handle, $src, $dependencies, null, $inFooter);

        return new self($handle);
    }

    /**
     * Registers a new script to be enqueued later.
     *
     * @param string[] $dependencies
     */
    public static function register(string $handle, string $src, array $dependencies = [], bool $inFooter = true): self
    {
        wp_register_script($handle, $src, $dependencies, null, $inFooter);

        return new self($handle);
    }

    /**
     * @param array<string, mixed> $l10n
     *
     * @return $this
     */
    public function localize(string $name, array $l10n): EnqueueScript
    {
        wp_localize_script($this->handle, $name, $l10n);

        return $this;
    }

    public function withCondition(string $condition): EnqueueScript
    {
        wp_scripts()->add_data($this->handle, 'conditional', $condition);

        return $this;
    }

    public function inline(string $snippet, string $position = 'after'): EnqueueScript
    {
        if ($position !== 'after') {
            $position = 'before';
        }

        wp_add_inline_script($this->handle, $snippet, $position);

        return $this;
    }

    public function prependInline(string $snippet): EnqueueScript
    {
        return $this->inline($snippet, 'before');
    }

    public function appendInline(string $snippet): EnqueueScript
    {
        return $this->inline($snippet);
    }

    public function async(): EnqueueScript
    {
        return $this->addTag('async');
    }

    public function defer(): EnqueueScript
    {
        return $this->addTag('defer');
    }

    public function referrerPolicy(string $referrerpolicy): EnqueueScript
    {
        return $this->addTag('referrerpolicy', $referrerpolicy);
    }

    public function addTag(string $key, ?string $value = null): EnqueueScript
    {
        add_filter('script_loader_tag', function (string $tag, string $handle) use ($key, $value) {
            if (is_user_logged_in() || $handle !== $this->handle) {
                return $tag;
            }

            if ($value === null) {
                $string = $key;
            } else {
                $string = sprintf("%s='%s'", $key, str_replace('\'', '"', $value));
            }

            return str_replace(' src', sprintf(' %s src', $string), $tag);
        }, 10, 2);

        return $this;
    }
}
