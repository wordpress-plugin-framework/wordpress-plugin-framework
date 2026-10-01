<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Body\Encoders;

use WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Preg\Preg,
};

readonly class Encoder implements EncoderInterface
{
	public function __construct(
		protected MediaTypeInterface $contentType = new MediaType(new Preg(), 'application', 'octet-stream'),
	) {
		if (!$this->encodesContentType($contentType)) {
			throw new EncoderException('does not encode this media type');
		}
	}

	public function contentType(): MediaTypeInterface
	{
		return $this->contentType;
	}

	public function withContentType(MediaTypeInterface $contentType): static
	{
		return new static($contentType);
	}

	public function encode(mixed $body): string
	{
		if (!$this->encodesBody($body)) {
			throw new EncoderException('does not encode');
		}

		return $body;
	}

	public function encodesBody(mixed $body): bool
	{
		if (!is_string($body)) {
			return false;
		}

		return true;
	}

	public function encodesContentType(MediaTypeInterface $contentType): bool
	{
		return true;
	}
}
