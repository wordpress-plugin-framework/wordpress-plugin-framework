<?php

namespace WordPressPluginFramework\Http\Normalizers\BackedEnum;

use WordPressPluginFramework\{
	Http\Normalizers\NormalizerException,
	Http\Normalizers\NormalizerInterface,
};
use BackedEnum;

readonly class Normalizer implements NormalizerInterface
{
	public function normalize(mixed $unnormalized): int|string
	{
		if (!$this->normalizes($unnormalized)) {
			throw new NormalizerException('does not normalize');
		}

		return $unnormalized->value;
	}

	public function normalizes(mixed $unnormalized): bool
	{
		if (!$unnormalized instanceof BackedEnum) {
			return false;
		}

		return true;
	}
}
