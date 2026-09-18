<?php

namespace WordPressPluginFramework\Http\Message\Body\Accessor;

use ArrayIterator;
use WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use stdClass;
use Traversable;

readonly class Body implements BodyInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected EncoderInterface $encoder,
		protected array|stdClass $body,
	) {
	}

	public function mediaType(): MediaTypeInterface
	{
		return $this->encoder->contentType();
	}

	public function __invoke(): array|stdClass
	{
		return $this->body;
	}

	public function values(string $key): array
	{
		return $this->accessor->values($this->body, $key);
	}

	public function has(string $key): bool
	{
		return $this->accessor->has($this->body, $key);
	}

	public function get(string $key): string|int|float|bool|null|array|stdClass
	{
		return $this->accessor->get($this->body, $key);
	}

	public function with(string $key, string|int|float|bool|null|array|stdClass $value): static
	{
		$value = $this->accessor->with($this->body, $key, $value);

		return new static($this->accessor, $this->encoder, $value);
	}

	public function without(string $key): static
	{
		$value = $this->accessor->without($this->body, $key);

		return new static($this->accessor, $this->encoder, $value);
	}

	public function isEmpty(): bool
	{
		return $this->count() === 0;
	}

	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator((array) $this->body);
	}

	public function count(): int
	{
		return count((array) $this->body);
	}

	public function __toString(): string
	{
		return $this->encoder->encode($this->body);
	}
}
