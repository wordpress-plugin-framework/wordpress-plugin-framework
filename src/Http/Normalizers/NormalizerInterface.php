<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Normalizers;

interface NormalizerInterface
{
	public function normalize(mixed $unnormalized): mixed;
	public function normalizes(mixed $unnormalized): bool;
}
