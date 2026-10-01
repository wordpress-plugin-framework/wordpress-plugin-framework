<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Query\Encoders;

interface EncoderInterface
{
	public function squareBrackets(): bool;

	public function encode(mixed $query): string;
	public function encodesQuery(mixed $query): bool;
}
