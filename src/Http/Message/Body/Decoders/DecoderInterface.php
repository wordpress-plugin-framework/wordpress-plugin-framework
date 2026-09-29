<?php

namespace WordPressPluginFramework\Http\Decoders;

use WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface DecoderInterface
{
	public function contentType(): MediaTypeInterface;
	public function withContentType(MediaTypeInterface $contentType): static;

	public function decode(string $body): mixed;

	public function decodesContentType(MediaTypeInterface $contentType): bool;
}
