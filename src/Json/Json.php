<?php

namespace WordPressPluginFramework\Json;

use Throwable;

readonly class Json implements JsonInterface
{
	public function decode(string $string): mixed
	{
		try {
			return json_decode($string, true, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			throw new JsonException($throwable->getMessage());
		}
	}

	public function encode(mixed $mixed): string
	{
		try {
			return json_encode($mixed, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			throw new JsonException($throwable->getMessage());
		}
	}
}