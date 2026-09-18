<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\Int;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\AbstractComparator;

readonly class Comparator extends AbstractComparator
{
	protected function normalize(mixed $value): ?int
	{
		if (is_int($value)) {
			return $value;
		}

		return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	}

	protected function compareNormalized(mixed $a, mixed $b): ?int
	{
		return $a <=> $b;
	}
}
