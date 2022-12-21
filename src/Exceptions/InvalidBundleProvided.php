<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Exceptions;

use RuntimeException;

use function sprintf;

class InvalidBundleProvided extends RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Error adding bundle "%s": Bundle was not provided.', $name));
    }
}
