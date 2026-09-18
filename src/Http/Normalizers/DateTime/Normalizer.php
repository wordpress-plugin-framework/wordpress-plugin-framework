<?php

namespace WordPressPluginFramework\Http\Normalizers\DateTime;

use WordPressPluginFramework\{
	Http\Normalizers\NormalizerException,
	Http\Normalizers\NormalizerInterface,
};
use DateTimeInterface;

readonly class Normalizer implements NormalizerInterface
{
	public function __construct(
		protected string $format,
	) {
	}

	public function normalize(mixed $unnormalized): string
	{
		if (!$this->normalizes($unnormalized)) {
			throw new NormalizerException('does not normalize');
		}

		return $unnormalized->format($this->format);
	}

	public function normalizes(mixed $unnormalized): bool
	{
		if (!$unnormalized instanceof DateTimeInterface) {
			return false;
		}

		return true;
	}
}
