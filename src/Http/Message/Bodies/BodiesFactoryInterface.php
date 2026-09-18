<?php

namespace WordPressPluginFramework\Http\Message\Bodies;

use WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface BodiesFactoryInterface
{
	public function create(mixed $body, MediaTypeInterface|string ...$contentTypes): BodiesInterface;
}
