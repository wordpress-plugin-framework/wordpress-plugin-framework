<?php

namespace WordPressPluginFramework\Http\Decoders\Form;

use WordPressPluginFramework\{
	Http\Decoders\DecoderException,
	Http\Decoders\DecoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class Decoder implements DecoderInterface
{
	public function __construct(
		protected MediaTypeInterface $contentType = new MediaType('application', 'x-www-form-urlencoded'),
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
		parse_str($body, $decoded);
		return $decoded;
	}

	public function decodesContentType(MediaTypeInterface $contentType): bool
	{
		$type = $contentType->type();
		if ($type !== 'application') {
			return false;
		}

		$subtype = $contentType->subtype();
		if ($subtype !== 'x-www-form-urlencoded') {
			return false;
		}

		return true;
	}
}
