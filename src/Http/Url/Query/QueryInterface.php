<?php

namespace WordPressPluginFramework\Http\Url\Query;

use Countable;
use IteratorAggregate;
use stdClass;
use Stringable;

interface QueryInterface extends IteratorAggregate, Countable, Stringable
{
	public function __invoke(): array|stdClass;

	public function values(string $key): array;

	public function has(string $key): bool;
	public function get(string $key): string|int|float|bool|null|array|stdClass;

	public function with(string $key, string|int|float|bool|null|array|stdClass $value): static;
	public function without(string $key): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
