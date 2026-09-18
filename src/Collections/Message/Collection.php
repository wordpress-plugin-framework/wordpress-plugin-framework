<?php

namespace WordPressPluginFramework\Collections\Message;

use ArrayIterator;
use Traversable;

class Collection implements CollectionInterface
{
	public function __construct(
		protected array $messages = []
	) {
	}

	public function add(string $key, string $message): void
	{
		$this->messages[$key][] = $message;
	}

	public function remove(string $key): void
	{
		unset($this->messages[$key]);
	}

	public function all(): array
	{
		return $this->messages;
	}

	public function isEmpty(): bool
	{
		return !$this->isNotEmpty();
	}

	public function isNotEmpty(): bool
	{
		return $this->count() > 0;
	}

	public function toArray(): array {
		return $this->messages;
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->messages);
	}

	public function count(): int
	{
		return count($this->messages, COUNT_RECURSIVE) - count($this->messages);
	}
}
