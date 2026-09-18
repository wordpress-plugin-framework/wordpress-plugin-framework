<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept;

use WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\Precedence\Precedence,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class Accept implements AcceptInterface
{
	public function __construct(
		protected array $mediaRanges,
	) {
	}

	public function mediaRanges(): array
	{
		return $this->mediaRanges;
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

	public function __toString(): string
	{
		return implode(', ', $this->mediaRanges);
	}
}
