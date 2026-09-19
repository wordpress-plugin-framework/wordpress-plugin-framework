<?php

namespace WordPressPluginFramework\Http\Message\Bodies;

use Countable;
use WordPressPluginFramework\Http\Message\Body\BodyInterface;
use Closure;
use IteratorAggregate;

interface BodiesInterface extends IteratorAggregate, Countable
{
	public function __invoke(): array;

	public function first(): BodyInterface;
	public function last(): BodyInterface;

	public function filter(Closure $closure): static;
	public function map(Closure $closure): static;
	public function sort(Closure $closure): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
