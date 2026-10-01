<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

interface MediaTypeFactoryInterface
{
	public function create(string $contentType): MediaTypeInterface;
}
