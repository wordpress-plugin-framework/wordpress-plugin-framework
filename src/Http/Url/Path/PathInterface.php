<?php

namespace WordPressPluginFramework\Http\Url\Path;

use Countable;
use IteratorAggregate;
use Stringable;

interface PathInterface extends IteratorAggregate, Countable, Stringable
{
	public function __invoke(): array;

	public function hasSegment(string $segment): bool;

	public function withSegment(string $segment): static;
	public function withoutSegment(string $segment): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
