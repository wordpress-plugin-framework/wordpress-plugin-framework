<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\Float;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\AbstractComparator;

readonly class Comparator extends AbstractComparator
{
	protected function normalize(mixed $value): ?float
	{
		if (is_float($value)) {
			return $value;
		}

		return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
	}

	protected function compareNormalized(mixed $a, mixed $b): ?int
	{
		return $a <=> $b;
	}
}
