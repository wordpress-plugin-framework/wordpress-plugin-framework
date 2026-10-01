<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Body\Encoders\View;

use WordPressPluginFramework\{
	Http\Message\Body\Encoders\EncoderException,
	Http\Message\Body\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Renderer\RendererInterface,
	View\ViewInterface,
};

readonly class Encoder implements EncoderInterface
{
	public function __construct(
		protected RendererInterface $renderer,
		protected MediaTypeInterface $contentType,
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
		return new static($this->renderer, $contentType);
	}

	public function encode(mixed $body): string
	{
		if (!$this->encodesBody($body)) {
			throw new EncoderException('does not encode');
		}

		return $this->renderer->render($body);
	}

	public function encodesBody(mixed $body): bool
	{
		if (!$body instanceof ViewInterface) {
			return false;
		}

		return true;
	}

	public function encodesContentType(MediaTypeInterface $contentType): bool
	{
		return $contentType->type() === 'text' && $contentType->subtype() === 'html';
	}
}
