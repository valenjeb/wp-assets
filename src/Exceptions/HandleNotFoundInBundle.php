<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Exceptions;

use RuntimeException;

use function sprintf;

class HandleNotFoundInBundle extends RuntimeException
{
    public function __construct(string $handle, string $bundle)
    {
        parent::__construct(sprintf('Handle "%s" not found in bundle "%s".', $handle, $bundle));
    }
}
