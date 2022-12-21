<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Exceptions;

use RuntimeException;

use function sprintf;

class UnableToApplyVersion extends RuntimeException
{
    public function __construct(string $error)
    {
        parent::__construct(sprintf('Unable to apply version: %s', $error));
    }
}
