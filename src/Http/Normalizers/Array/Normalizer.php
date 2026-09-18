<?php

namespace WordPressPluginFramework\Http\Normalizers\Array;

use WordPressPluginFramework\{
	Http\Normalizers\NormalizerException,
	Http\Normalizers\NormalizerInterface,
	Http\Normalizers\NormalizersInterface,
};

readonly class Normalizer implements NormalizerInterface
{
	public function __construct(
		protected NormalizersInterface $normalizers,
	) {
	}

	public function normalize(mixed $unnormalized): array
	{
		if (!$this->normalizes($unnormalized)) {
			throw new NormalizerException('does not normalize');
		}

		$normalized = [];

		foreach ($unnormalized as $key => $value) {
			$normalized[$key] = $this->normalizers->normalize($value);
		}

		return $normalized;
	}

	public function normalizes(mixed $unnormalized): bool
	{
		if (!is_array($unnormalized)) {
			return false;
		}

		return true;
	}
}
