<?php

namespace WordPressPluginFramework\Http\Message\Bodies;

use Closure;
use Countable;
use WordPressPluginFramework\Http\Message\Body\BodyInterface;
use IteratorAggregate;

interface BodiesInterface extends IteratorAggregate, Countable
{
	public function first(): BodyInterface;
	public function last(): BodyInterface;

	public function filter(Closure $closure): static;
	public function sort(Closure $closure): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
