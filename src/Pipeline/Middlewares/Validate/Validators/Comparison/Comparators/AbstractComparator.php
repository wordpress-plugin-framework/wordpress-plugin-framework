<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface;

abstract readonly class AbstractComparator implements ComparatorInterface
{
	public function compare(mixed $a, mixed $b): ?int
	{
		$a = $this->normalize($a);
		if ($a === null) {
			return null;
		}

		$b = $this->normalize($b);
		if ($b === null) {
			return null;
		}

		return $this->compareNormalized($a, $b);
	}

	abstract protected function normalize(mixed $value): mixed;
	abstract protected function compareNormalized(mixed $a, mixed $b): ?int;
}
