<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept;

use ArrayIterator;
use WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\Precedence\Precedence,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use Traversable;

readonly class Accept implements AcceptInterface
{
	public function __construct(
		protected array $mediaRanges,
	) {
	}

	public function q(MediaTypeInterface $mediaType): float
	{
		foreach (Precedence::cases() as $precedence) {
			foreach ($this->mediaRanges as $mediaRange) {
				if ($precedence === $mediaRange->precedence($mediaType)) {
					return $mediaRange->q() ?? '1';
				}
			}
		}

		return 0;
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
		return new ArrayIterator(
			array_values($this->mediaRanges),
		);
	}

	public function count(): int
	{
		return count($this->mediaRanges);
	}

	public function __invoke(): array
	{
		return $this->mediaRanges;
	}

	public function __toString(): string
	{
		return implode(', ', $this->mediaRanges);
	}
}
