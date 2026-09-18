<?php

namespace WordPressPluginFramework\Localization\Formatter;

interface FormatterInterface
{
    public function number(float $number, int $decimals = 0): string;
}
