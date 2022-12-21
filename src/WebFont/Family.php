<?php

declare(strict_types=1);

namespace Devly\WP\Assets\WebFont;

use function func_get_args;
use function implode;
use function str_replace;

class Family
{
    protected string $family;
    protected ?string $display = null;
    protected ?string $weight  = null;
    private ?string $name      = null;

    public function __construct(string $family)
    {
        $this->family = str_replace(' ', '+', $family);
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function weight(string $weight): self
    {
        $weight = func_get_args();

        $this->weight = implode(';', $weight);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->family . ($this->weight ? ':wght@' . $this->weight : '');
    }

    public function __toString(): string
    {
        return $this->getUrl();
    }
}
