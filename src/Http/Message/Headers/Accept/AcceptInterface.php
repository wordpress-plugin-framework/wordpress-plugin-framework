<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept;

use Countable;
use WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use IteratorAggregate;
use Stringable;

interface AcceptInterface extends IteratorAggregate, Countable, Stringable
{
	public function __invoke(): array;

	public function q(MediaTypeInterface $mediaType): float;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
