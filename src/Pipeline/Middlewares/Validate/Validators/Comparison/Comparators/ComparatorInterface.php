<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators;

interface ComparatorInterface
{
	public function compare(mixed $a, mixed $b): ?int;
}
