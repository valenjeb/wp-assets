<?php

declare(strict_types=1);

namespace Devly\WP\Assets\WebFont;

use function implode;

class WebFontsLoader
{
    /** @var Family[] */
    private array $families        = [];
    private string $googleFontsUrl = 'https://fonts.googleapis.com/css2';
    /** @var Family[] */
    private array $googleFamilies = [];
    private string $display       = 'swap';

    public function enqueue(): void
    {
        $i = 1;
        foreach ($this->families as $family) {
            wp_enqueue_style($family->getName() ?? 'font-' . $i, $family->getUrl(), [], null);
            $i++;
        }

        if (empty($this->googleFamilies)) {
            return;
        }

        $fontsUrl = add_query_arg([
            'family' => implode('&family=', $this->googleFamilies),
            'display' => $this->display,
        ], $this->googleFontsUrl);

        wp_enqueue_style('google-fonts', wptt_get_webfont_url(esc_url_raw($fontsUrl)), [], null);
    }

    public function display(string $display): self
    {
        $this->display = $display;

        return $this;
    }

    public function addFontFamily(string $name): Family
    {
        $family           = new Family($name);
        $this->families[] = $family;

        return $family;
    }

    public function addGoogleFont(string $name): Family
    {
        $family                 = new Family($name);
        $this->googleFamilies[] = $family;

        return $family;
    }
}
