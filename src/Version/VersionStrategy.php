<?php

declare(strict_types=1);

namespace Devly\WP\Assets\Version;

use Devly\WP\Assets\Exceptions\UnableToApplyVersion;

interface VersionStrategy
{
    /** @throws UnableToApplyVersion */
    public function applyVersion(string $path): string;
}
