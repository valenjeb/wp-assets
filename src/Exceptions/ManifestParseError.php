<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Exceptions;

use RuntimeException;

use function sprintf;

class ManifestParseError extends RuntimeException
{
    /**
     * @param string $path  Path to manifest file
     * @param string $error Json error message.
     */
    public function __construct(string $path, string $error)
    {
        parent::__construct(sprintf('Error parsing JSON from asset manifest file "%s": %s.', $path, $error));
    }
}
