<?php

namespace WordPressPluginFramework\Http\Normalizers;

interface NormalizersInterface
{
	public function get(mixed $unnormalized): ?NormalizerInterface;
	public function normalize(mixed $unnormalized): mixed;
}
