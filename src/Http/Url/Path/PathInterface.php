<?php

namespace WordPressPluginFramework\Http\Url\Path;

use Countable;
use IteratorAggregate;
use Stringable;

interface PathInterface extends IteratorAggregate, Countable, Stringable
{
	public function __invoke(): array;

	public function has(string $segment): bool;

	public function with(string $segment): static;
	public function without(string $segment): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
