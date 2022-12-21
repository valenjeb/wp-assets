<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Exceptions;

use RuntimeException;

use function sprintf;

class JsonManifestNotFound extends RuntimeException
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf('Asset manifest file "%s" does not exist.', $path));
    }
}
