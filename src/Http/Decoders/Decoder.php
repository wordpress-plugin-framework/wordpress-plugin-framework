<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class Decoder implements DecoderInterface
{
	public function __construct(
		protected MediaTypeInterface $contentType = new MediaType('application', 'octet-stream'),
	) {
		if (!$this->decodesContentType($contentType)) {
			throw new DecoderException('does not decode this media type');
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

	public function decode(string $body): mixed
	{
		return $body;
	}

	public function decodesContentType(MediaTypeInterface $contentType): bool
	{
		return true;
	}
}
