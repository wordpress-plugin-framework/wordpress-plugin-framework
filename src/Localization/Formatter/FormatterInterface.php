<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Localization\Formatter;

interface FormatterInterface
{
    public function number(float $number, int $decimals = 0): string;
}
