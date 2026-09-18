<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Accessor;

use Countable;
use Hoo\WordPressPluginFramework\Http;
use IteratorAggregate;
use stdClass;

interface BodyInterface extends Http\Message\Body\BodyInterface, IteratorAggregate, Countable
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
