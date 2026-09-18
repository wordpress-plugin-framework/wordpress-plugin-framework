<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Closure;
use Countable;
use IteratorAggregate;

interface EncodersInterface extends IteratorAggregate, Countable
{
	public function contentTypes(): array;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;

	public function first(): EncoderInterface;
	public function last(): EncoderInterface;

	public function filter(Closure $closure): static;
	public function map(Closure $closure): static;
	public function sort(Closure $closure): static;
}
