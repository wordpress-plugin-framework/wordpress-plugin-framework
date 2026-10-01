<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Path;

use Countable;
use IteratorAggregate;
use Stringable;

interface PathInterface extends IteratorAggregate, Countable, Stringable
{
	public function __invoke(): array;

	public function has(int $index): bool;
	public function get(int $index): ?string;

	public function first(): ?string;
	public function last(): ?string;

	public function with(int $index, string $segment): static;
	public function without(int $index): static;

	public function withFirst(string $segment): static;
	public function withoutFirst(): static;

	public function withLast(string $segment): static;
	public function withoutLast(): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
