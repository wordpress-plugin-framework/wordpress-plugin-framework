<?php

namespace WordPressPluginFramework\Http\Accessor;

use stdClass;

interface AccessorInterface
{
	public function has(array|stdClass $data, string $path): bool;
	public function get(array|stdClass $data, string $path): string|int|float|bool|null|array|stdClass;
	public function values(array|stdClass $data, string $path): array;

	public function with(array|stdClass $data, string $path, string|int|float|bool|null|array|stdClass $value): array|stdClass;
	public function without(array|stdClass $data, string $path): array|stdClass;
}
