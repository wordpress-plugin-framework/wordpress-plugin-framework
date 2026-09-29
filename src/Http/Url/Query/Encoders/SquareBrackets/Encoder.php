<?php

namespace WordPressPluginFramework\Http\Url\Query\Encoders\SquareBrackets;

use WordPressPluginFramework\{
	Http\Message\Body\Encoders\EncoderException,
	Http\Url\Query\Encoders\EncoderInterface,
};
use stdClass;

readonly class Encoder implements EncoderInterface
{
	public function squareBrackets(): bool
	{
		return true;
	}

	public function encode(mixed $query): string
	{
		if (!$this->encodesQuery($query)) {
			throw new EncoderException('does not encode');
		}

		return http_build_query($query, '', '&', PHP_QUERY_RFC3986);
	}

	public function encodesQuery(mixed $query): bool
	{
		if (!is_array($query) && !$query instanceof stdClass) {
			return false;
		}

		return true;
	}
}
