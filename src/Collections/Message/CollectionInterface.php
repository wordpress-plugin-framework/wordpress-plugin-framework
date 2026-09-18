<?php

namespace WordPressPluginFramework\Collections\Message;

use Countable;
use IteratorAggregate;

interface CollectionInterface extends IteratorAggregate, Countable
{
	public function all(): array;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;

	public function toArray(): array;
}
