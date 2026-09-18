<?php

namespace WordPressPluginFramework\Http\Message\Headers\Parameters;

use Countable;
use IteratorAggregate;
use Stringable;

interface ParametersInterface extends IteratorAggregate, Countable, Stringable
{
	public function __invoke(): array;

	public function has(string $name): bool;

	public function get(string $name): ?string;

	public function with(string $name, string $value): static;

	public function without(string $name): static;
}
