<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Exceptions;

use RuntimeException;

use function sprintf;

class ItemNotFoundInManifest extends RuntimeException
{
    public function __construct(string $path, string $manifest)
    {
        parent::__construct(sprintf('Asset "%s" not found in manifest "%s".', $path, $manifest));
    }
}
