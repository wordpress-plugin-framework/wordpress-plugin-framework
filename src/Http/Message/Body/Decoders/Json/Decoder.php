<?php

namespace WordPressPluginFramework\Http\Decoders\Json;

use WordPressPluginFramework\{
	Http\Abnf\Rfc6838,
	Http\Decoders\DecoderException,
	Http\Decoders\DecoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use Throwable;

readonly class Decoder implements DecoderInterface
{
	public function __construct(
		protected MediaTypeInterface $contentType = new MediaType('application', 'json'),
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
		try {
			return json_decode($body, false, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			throw new DecoderException($throwable->getMessage());
		}
	}

	public function decodesContentType(MediaTypeInterface $contentType): bool
	{
		$type = $contentType->type();
		if ($type !== 'application') {
			return false;
		}

		$subtype = $contentType->subtype();
		if (
			$subtype !== 'json' &&
			preg_match('/\A' . Rfc6838::RESTRICTED_NAME . '\+json\z/', $subtype) !== 1
		) {
			return false;
		}

		return true;
	}
}
