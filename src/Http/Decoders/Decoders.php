<?php

namespace WordPressPluginFramework\Http\Decoders;

use ArrayIterator;
use Closure;
use WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use Traversable;

readonly class Decoders implements DecodersInterface
{
	public function __construct(
		protected array $decoders,
	) {
		$this->validate($this->decoders);
	}

	public function with(DecoderInterface $decoder): static
	{
		$decoders = $this->decoders;
		$decoders[] = $decoder;

		return new static($decoders);
	}

	public function first(): DecoderInterface
	{
		$key = array_key_first($this->decoders);
		if ($key === null) {
			throw new DecodersException('no decoder found');
		}

		return $this->decoders[$key];
	}

	public function last(): DecoderInterface
	{
		$key = array_key_last($this->decoders);
		if ($key === null) {
			throw new DecodersException('no decoder found');
		}

		return $this->decoders[$key];
	}

	public function filter(Closure $closure): static
	{
		$decoders = array_filter($this->decoders, $closure);

		return new static($decoders);
	}

	public function map(Closure $closure): static
	{
		$decoders = array_map($closure, $this->decoders);

		return new static($decoders);
	}

	public function sort(Closure $closure): static
	{
		$decoders = $this->decoders;
		usort($decoders, $closure);

		return new static($decoders);
	}

	public function filterByContentType(MediaTypeInterface $contentType): static
	{
		return $this->filter(fn($decoder) => $decoder->decodesContentType($contentType));
	}

	public function mapContentType(MediaTypeInterface $contentType): static
	{
		return $this->map(fn($decoder) => $decoder->withContentType($contentType));
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(
			array_values($this->decoders),
		);
	}

	public function isEmpty(): bool
	{
		return $this->count() === 0;
	}

	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	public function count(): int
	{
		return count($this->decoders);
	}

	protected function validate(array $decoders): void
	{
		foreach ($decoders as $decoder) {
			if (!$decoder instanceof DecoderInterface) {
				throw new DecodersException('must provide decoder interface');
			}
		}
	}
}
