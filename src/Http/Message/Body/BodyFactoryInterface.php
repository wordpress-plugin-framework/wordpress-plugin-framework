<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface BodyFactoryInterface
{
	public function create(mixed $body, MediaTypeInterface|string $contentType): BodyInterface;
	public function createFromEncoded(string $body, MediaTypeInterface|string $contentType): BodyInterface;
}
