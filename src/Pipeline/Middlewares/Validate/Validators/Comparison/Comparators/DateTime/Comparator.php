<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\DateTime;

use DateTime;
use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\AbstractComparator;

readonly class Comparator extends AbstractComparator
{
	public function __construct(
		protected string $format,
	) {
	}

	protected function normalize(mixed $value): ?DateTime
	{
		if (!is_string($value)) {
			return null;
		}

		$dateTime = DateTime::createFromFormat("!{$this->format}", $value);
		if ($dateTime === false) {
			return null;
		}

		$lastErrors = DateTime::getLastErrors();
		if ($lastErrors !== false) {
			return null;
		}

		return $dateTime;
	}

	protected function compareNormalized(mixed $a, mixed $b): ?int
	{
		return $a <=> $b;
	}
}
