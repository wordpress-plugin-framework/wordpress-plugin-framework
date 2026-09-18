<?php

namespace WordPressPluginFramework\Http\Decoders\Query;

readonly class Decoder implements DecoderInterface
{
	public function decode(string $query): mixed
	{
		parse_str($query, $decoded);
		return $decoded;
	}
}
