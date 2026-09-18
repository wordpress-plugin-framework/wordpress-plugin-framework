<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept;

use WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use Stringable;

interface AcceptInterface extends Stringable
{
	public function mediaRanges(): array;

	public function q(MediaTypeInterface $mediaType): float;
}
