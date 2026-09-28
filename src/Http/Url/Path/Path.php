<?php

namespace WordPressPluginFramework\Http\Url\Path;

use ArrayIterator;
use WordPressPluginFramework\Http\Abnf\Rfc3986;
use Traversable;

readonly class Path implements PathInterface
{
	protected array $segments;

	public function __construct(array $segments)
	{
		$this->validate($segments);
		$this->segments = array_values($segments);
	}

	public function has(string $segment): bool
	{
		return in_array($segment, $this->segments, true);
	}

	public function with(string $segment): static
	{
		$segments = $this->segments;
		$segments[] = $segment;

		return new static($segments);
	}

	public function without(string $segment): static
	{
		$segments = [];

		foreach ($this->segments as $kept) {
			if ($kept === $segment) {
				continue;
			}

			$segments[] = $kept;
		}

		return new static($segments);
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
			$encoded = $this->encode($segment);
			$path .= '/' . $encoded;
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

	protected function encode(string $segment): string
	{
		$pattern = '@(?!' . Rfc3986::UNRESERVED . '|' . Rfc3986::SUB_DELIMS . '|:|\@)(.)@s';

		$encoded = preg_replace_callback($pattern, $this->pctEncoded(...), $segment);
		if ($encoded === null) {
			throw new PathException('segment is not encodable');
		}

		return $encoded;
	}

	protected function pctEncoded(array $match): string
	{
		$hex = bin2hex($match[1]);

		return '%' . strtoupper($hex);
	}
}
