<?php

namespace WordPressPluginFramework\Http\Url\Path;

use ArrayIterator;
use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc5234,
	Preg\PregInterface,
};
use Traversable;

readonly class Path implements PathInterface
{
	protected const PCT_DECODED = '(?!' . Rfc3986::UNRESERVED . '|' . Rfc3986::SUB_DELIMS . '|:|\@)' . Rfc5234::OCTET;

	protected array $segments;

	public function __construct(
		protected PregInterface $preg,
		array $segments,
	) {
		$this->validate($segments);
		$this->segments = $this->normalize($segments);
	}

	public function has(int $index): bool
	{
		return isset($this->segments[$index]);
	}

	public function get(int $index): ?string
	{
		if (!$this->has($index)) {
			return null;
		}

		return $this->segments[$index];
	}

	public function first(): ?string
	{
		return $this->get(0);
	}

	public function last(): ?string
	{
		$index = $this->count() - 1;

		return $this->get($index);
	}

	public function with(int $index, string $segment): static
	{
		$segments = $this->segments;
		$segments[$index] = $segment;

		return new static($this->preg, $segments);
	}

	public function without(int $index): static
	{
		$segments = $this->segments;
		unset($segments[$index]);

		return new static($this->preg, $segments);
	}

	public function withFirst(string $segment): static
	{
		return $this->with(0, $segment);
	}

	public function withoutFirst(): static
	{
		return $this->without(0);
	}

	public function withLast(string $segment): static
	{
		$index = $this->count() - 1;

		return $this->with($index, $segment);
	}

	public function withoutLast(): static
	{
		$index = $this->count() - 1;

		return $this->without($index);
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
		return new ArrayIterator($this->segments);
	}

	public function count(): int
	{
		return count($this->segments);
	}

	public function __invoke(): array
	{
		return $this->segments;
	}

	public function __toString(): string
	{
		$path = '';

		foreach ($this->segments as $segment) {
			$path .= '/' . $this->preg->replaceCallback('@' . self::PCT_DECODED . '@', fn($match) => rawurlencode($match[0]), $segment);
		}

		return $path;
	}

	protected function validate(array $segments): void
	{
		foreach ($segments as $segment) {
			if (!is_string($segment)) {
				throw new PathException('segment must be a string');
			}

			if (
				$segment === '.' ||
				$segment === '..'
			) {
				throw new PathException('dot-segment cannot carry data');
			}
		}
	}

	protected function normalize(array $segments): array
	{
		return array_values($segments);
	}
}
