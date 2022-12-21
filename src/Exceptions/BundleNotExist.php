<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Exceptions;

use RuntimeException;

use function sprintf;

class BundleNotExist extends RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Bundle "%s" does not exist.', $name));
    }
}
