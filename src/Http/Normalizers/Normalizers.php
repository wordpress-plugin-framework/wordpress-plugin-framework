<?php

namespace WordPressPluginFramework\Http\Normalizers;

readonly class Normalizers implements NormalizersInterface
{
	public function __construct(
		protected array $normalizers,
	) {
	}

	public function get(mixed $unnormalized): ?NormalizerInterface
	{
		foreach ($this->normalizers as $normalizer) {
			if ($normalizer->normalizes($unnormalized)) {
				return $normalizer;
			}
		}

		return null;
	}

	public function normalize(mixed $unnormalized): mixed
	{
		$normalizer = $this->get($unnormalized);

		return $normalizer === null ? $unnormalized : $normalizer->normalize($unnormalized);
	}
}
