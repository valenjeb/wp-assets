<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Resolvers;

interface Resolver
{
    public function getUrl(string $path): string;

    public function getPath(string $path): string;
}
