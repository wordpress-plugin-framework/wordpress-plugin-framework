<?php

namespace WordPressPluginFramework\Http\Decoders;

use Closure;
use Countable;
use WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use IteratorAggregate;

interface DecodersInterface extends IteratorAggregate, Countable
{
	public function with(DecoderInterface $decoder): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;

	public function first(): DecoderInterface;
	public function last(): DecoderInterface;

	public function filter(Closure $closure): static;
	public function map(Closure $closure): static;
	public function sort(Closure $closure): static;

	public function filterByContentType(MediaTypeInterface $contentType): static;
	public function mapContentType(MediaTypeInterface $contentType): static;
}
