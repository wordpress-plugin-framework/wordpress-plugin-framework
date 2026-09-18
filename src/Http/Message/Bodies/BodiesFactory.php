<?php

namespace WordPressPluginFramework\Http\Message\Bodies;

use WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Encoders\EncodersInterface,
	Http\Message\Body\Accessor,
	Http\Message\Body\Body,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Normalizers\NormalizersInterface,
};
use stdClass;

readonly class BodiesFactory implements BodiesFactoryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected MediaTypeFactoryInterface $mediaTypeFactory,
		protected EncodersInterface $encoders,
		protected NormalizersInterface $normalizers,
	) {
	}

	public function create(mixed $body, MediaTypeInterface|string ...$contentTypes): BodiesInterface
	{
		if ($body instanceof BodyInterface) {
			$body = $body();
		}

		$normalizedBody = $this->normalizers->normalize($body);

		$contentTypes = $contentTypes === [] ? $this->contentTypes($body, $normalizedBody) : array_map($this->contentType(...), $contentTypes);

		$bodies = [];

		foreach ($contentTypes as $contentType) {
			$encodersByContentType = $this->encoders
				->filter(fn($encoder) => $encoder->encodesContentType($contentType))
				->map(fn($encoder) => $encoder->withContentType($contentType));

			if ($encodersByContentType->isEmpty()) {
				throw new BodiesFactoryException('no encoder for this content-type');
			}

			$bodies[] = $this->body($encodersByContentType, $body, $normalizedBody);
		}

		return new Bodies($bodies);
	}

	protected function contentTypes(mixed $body, mixed $normalizedBody): array
	{
		$encodersByBody = $this->encoders
			->filter(fn($encoder) => $encoder->encodesBody($body) || $encoder->encodesBody($normalizedBody));

		if ($encodersByBody->isEmpty()) {
			throw new BodiesFactoryException('no encoders for this body');
		}

		return $encodersByBody->contentTypes();
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

		throw new BodiesFactoryException('no encoders for this body');
	}
}
