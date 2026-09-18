<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Query;

use Hoo\WordPressPluginFramework\Http\Encoders\EncoderException;
use stdClass;

readonly class Encoder implements EncoderInterface
{
	public function encode(mixed $query): string
	{
		if (!$this->encodesType($query)) {
			throw new EncoderException('does not encode');
		}

		return http_build_query($query, '', '&', PHP_QUERY_RFC3986);
	}

	protected function encodesType(mixed $query): bool
	{
		if (!is_array($query) && !$query instanceof stdClass) {
			return false;
		}

		return true;
	}
}
