<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface EncoderInterface
{
	public function contentType(): MediaTypeInterface;
	public function withContentType(MediaTypeInterface $contentType): static;

	public function encode(mixed $body): string;

	public function encodesBody(mixed $body): bool;
	public function encodesContentType(MediaTypeInterface $contentType): bool;
}
