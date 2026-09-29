<?php

namespace WordPressPluginFramework\Http\Url\Query\Decoders\SquareBrackets;

use WordPressPluginFramework\Http\Url\Query\Decoders\DecoderInterface;

readonly class Decoder implements DecoderInterface
{
	public function squareBrackets(): bool
	{
		return true;
	}

	public function decode(string $query): mixed
	{
		parse_str($query, $decoded);
		return $decoded;
	}
}
