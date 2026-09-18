<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\String;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\AbstractComparator;

readonly class Comparator extends AbstractComparator
{
	protected function normalize(mixed $value): ?string
	{
		if (is_string($value)) {
			return $value;
		}

		return is_scalar($value) ? (string) $value : null;
	}

	protected function compareNormalized(mixed $a, mixed $b): ?int
	{
		return strcmp($a, $b);
	}
}
