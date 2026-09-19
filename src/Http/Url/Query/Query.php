<?php

namespace WordPressPluginFramework\Http\Url\Query;

use ArrayIterator;
use WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Encoders\Query\EncoderInterface,
};
use stdClass;
use Traversable;

readonly class Query implements QueryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected EncoderInterface $encoder,
		protected array|stdClass $query,
	) {
	}

	public function values(string $key): array
	{
		return $this->accessor->values($this->query, $key);
	}

	public function has(string $key): bool
	{
		return $this->accessor->has($this->query, $key);
	}

	public function get(string $key): string|int|float|bool|null|array|stdClass
	{
		if (!$this->accessor->has($this->query, $key)) {
			return null;
		}

		return $this->accessor->get($this->query, $key);
	}

	public function with(string $key, string|int|float|bool|null|array|stdClass $value): static
	{
		$value = $this->accessor->with($this->query, $key, $value);

		return new static($this->accessor, $this->encoder, $value);
	}

	public function without(string $key): static
	{
		$value = $this->accessor->without($this->query, $key);

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
		return new ArrayIterator((array) $this->query);
	}

	public function count(): int
	{
		return count((array) $this->query);
	}

	public function __invoke(): array|stdClass
	{
		return $this->query;
	}

	public function __toString(): string
	{
		if ($this->isEmpty()) {
			return '';
		}

		return $this->encoder->encode($this->query);
	}
}
