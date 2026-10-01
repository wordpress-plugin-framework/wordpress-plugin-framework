<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\PercentEncoder;

interface PercentEncoderInterface
{
	public function encodePath(string $path): string;

	public function encodeQuery(string $query): string;
}
