<?php

namespace WordPressPluginFramework\Http\Encoders\Json;

use WordPressPluginFramework\{
	Http\Abnf\Rfc6838,
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use stdClass;
use Throwable;

readonly class Encoder implements EncoderInterface
{
	public function __construct(
		protected MediaTypeInterface $contentType = new MediaType('application', 'json'),
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

		try {
			return json_encode($body, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			throw new EncoderException($throwable->getMessage(), $throwable->getCode(), $throwable->getPrevious());
		}
	}

	public function encodesBody(mixed $body): bool
	{
		if (
			is_null($body) ||
			is_scalar($body)
		) {
			return true;
		}

		if (
			!is_array($body) &&
			!$body instanceof stdClass
		) {
			return false;
		}

		foreach ($body as $value) {
			if (!$this->encodesBody($value)) {
				return false;
			}
		}

		return true;
	}

	public function encodesContentType(MediaTypeInterface $contentType): bool
	{
		$type = $contentType->type();
		if ($type !== 'application') {
			return false;
		}

		$subtype = $contentType->subtype();
		if ($subtype !== 'json' && preg_match('/\A' . Rfc6838::RESTRICTED_NAME . '\+json\z/', $subtype) !== 1) {
			return false;
		}

		return true;
	}
}
