<?php

namespace WordPressPluginFramework\Http\Message\Headers\Vary;

use Countable;
use IteratorAggregate;
use Stringable;

interface VaryInterface extends IteratorAggregate, Countable, Stringable
{
	public function __invoke(): array;

	public function has(string $fieldName): bool;

	public function with(string $fieldName): static;
	public function without(string $fieldName): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
