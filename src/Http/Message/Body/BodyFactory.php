<?php

namespace WordPressPluginFramework\Http\Message\Body;

use WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Decoders\DecodersInterface,
	Http\Encoders\EncodersInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Normalizers\NormalizersInterface,
};
use stdClass;

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected MediaTypeFactoryInterface $mediaTypeFactory,
		protected DecodersInterface $decoders,
		protected EncodersInterface $encoders,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function create(mixed $body, MediaTypeInterface|string $contentType): BodyInterface
	{
		if ($body instanceof BodyInterface) {
			$body = $body();
		}

		$normalizedBody = $this->normalizers->normalize($body);

		$contentType = $this->contentType($contentType);

		$encodersByContentType = $this->encoders
			->filter(fn($encoder) => $encoder->encodesContentType($contentType))
			->map(fn($encoder) => $encoder->withContentType($contentType));

		if ($encodersByContentType->isEmpty()) {
			throw new BodyFactoryException('no encoder for this content-type');
		}

		return $this->body($encodersByContentType, $body, $normalizedBody);
	}

	public function createFromEncoded(string $body, MediaTypeInterface|string $contentType): BodyInterface
	{
		$contentType = $this->contentType($contentType);

		$decoder = $this->decoders
			->filter(fn($decoder) => $decoder->decodesContentType($contentType))
			->map(fn($decoder) => $decoder->withContentType($contentType))
			->first();

		$decodedBody = $decoder->decode($body);

		return $this->create($decodedBody, $contentType);
	}

	protected function contentType(MediaTypeInterface|string $contentType): MediaTypeInterface
	{
		return $contentType instanceof MediaTypeInterface ? $contentType : $this->mediaTypeFactory->create($contentType);
	}

	protected function body(EncodersInterface $encoders, mixed $body, mixed $normalizedBody): BodyInterface
	{
		foreach ([
			$body,
			$normalizedBody,
		] as $body) {
			$encodersByBody = $encoders
				->filter(fn($encoder) => $encoder->encodesBody($body));

			if ($encodersByBody->isEmpty()) {
				continue;
			}

			$firstEncoderByBody = $encodersByBody->first();

			if (
				is_array($body) ||
				$body instanceof stdClass
			) {
				return new Accessor\Body($this->accessor, $firstEncoderByBody, $body);
			}

			return new Body($firstEncoderByBody, $body);
		}

		throw new BodyFactoryException('no encoders for this body');
	}
}
