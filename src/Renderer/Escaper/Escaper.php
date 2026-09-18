<?php

namespace WordPressPluginFramework\Renderer\Escaper;

readonly class Escaper implements EscaperInterface
{
    public function attr(string $value): string
    {
        return esc_attr($value);
    }

    public function html(string $value): string
    {
        return esc_html($value);
    }

    public function js(string $value): string
    {
        return esc_js($value);
    }

    public function textarea(string $value): string
    {
        return esc_textarea($value);
    }

    public function url(string $value): string
    {
        return esc_url($value);
    }

    public function xml(string $value): string
    {
        return esc_xml($value);
    }
}