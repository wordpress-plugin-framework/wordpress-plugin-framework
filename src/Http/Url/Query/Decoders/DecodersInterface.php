<?php

namespace WordPressPluginFramework\Http\Url\Query\Decoders;

use Countable;
use Closure;
use IteratorAggregate;

interface DecodersInterface extends IteratorAggregate, Countable
{
	public function __invoke(): array;

	public function first(): DecoderInterface;
	public function last(): DecoderInterface;

	public function filter(Closure $closure): static;
	public function map(Closure $closure): static;
	public function sort(Closure $closure): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
