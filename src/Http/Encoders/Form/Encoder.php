<?php

namespace WordPressPluginFramework\Http\Encoders\Form;

use WordPressPluginFramework\{
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use stdClass;

readonly class Encoder implements EncoderInterface
{
	public function __construct(
		protected MediaTypeInterface $contentType = new MediaType('application', 'x-www-form-urlencoded'),
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

		return http_build_query($body, '', '&', PHP_QUERY_RFC1738);
	}

	public function encodesBody(mixed $body): bool
	{
		if (
			!is_array($body) &&
			!$body instanceof stdClass
		) {
			return false;
		}

		foreach ($body as $value) {
			if (
				is_null($value) ||
				is_scalar($value)
			) {
				continue;
			}

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
		if ($subtype !== 'x-www-form-urlencoded') {
			return false;
		}

		return true;
	}
}
