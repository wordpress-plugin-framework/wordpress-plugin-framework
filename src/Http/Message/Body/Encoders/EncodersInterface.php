<?php

namespace WordPressPluginFramework\Http\Encoders;

use Countable;
use Closure;
use IteratorAggregate;

interface EncodersInterface extends IteratorAggregate, Countable
{
	public function __invoke(): array;

	public function contentTypes(): array;

	public function first(): EncoderInterface;
	public function last(): EncoderInterface;

	public function filter(Closure $closure): static;
	public function map(Closure $closure): static;
	public function sort(Closure $closure): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
