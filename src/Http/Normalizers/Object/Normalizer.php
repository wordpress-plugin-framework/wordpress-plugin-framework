<?php

namespace WordPressPluginFramework\Http\Normalizers\Object;

use WordPressPluginFramework\{
	Http\Normalizers\NormalizerException,
	Http\Normalizers\NormalizerInterface,
	Http\Normalizers\NormalizersInterface,
};
use stdClass;

readonly class Normalizer implements NormalizerInterface
{
	public function __construct(
		protected NormalizersInterface $normalizers,
	) {
	}

	public function normalize(mixed $unnormalized): object
	{
		if (!$this->normalizes($unnormalized)) {
			throw new NormalizerException('does not normalize');
		}

		$normalized = new stdClass();

		foreach (get_object_vars($unnormalized) as $key => $value) {
			$normalized->{$key} = $this->normalizers->normalize($value);
		}

		return $normalized;
	}

	public function normalizes(mixed $unnormalized): bool
	{
		if (!is_object($unnormalized)) {
			return false;
		}

		return true;
	}
}
