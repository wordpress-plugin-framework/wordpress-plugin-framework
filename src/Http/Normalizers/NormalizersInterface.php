<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Normalizers;

interface NormalizersInterface
{
	public function get(mixed $unnormalized): ?NormalizerInterface;
	public function normalize(mixed $unnormalized): mixed;
}
