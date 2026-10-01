<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Body\Encoders;

use WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface EncoderInterface
{
	public function contentType(): MediaTypeInterface;
	public function withContentType(MediaTypeInterface $contentType): static;

	public function encode(mixed $body): string;

	public function encodesBody(mixed $body): bool;
	public function encodesContentType(MediaTypeInterface $contentType): bool;
}
