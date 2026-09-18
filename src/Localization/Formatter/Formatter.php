<?php

namespace WordPressPluginFramework\Localization\Formatter;

readonly class Formatter implements FormatterInterface
{
    public function number(float $number, int $decimals = 0): string
    {
        return number_format_i18n($number, $decimals);
    }
}
