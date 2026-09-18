<?php

namespace WordPressPluginFramework\Collection;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

abstract class AbstractCollection implements IteratorAggregate, Countable
{
	protected array $items = [];

	public function has(Item\Key\KeyInterface $key): bool
	{
		return isset($this->items[$key()]);
	}

	public function get(Item\Key\KeyInterface $key): mixed
	{
		if (!$this->has($key)) {
			return null;
		}

		return $this->items[$key()];
	}

	public function first(): mixed
	{
		if (!$this->items) {
			return null;
		}

		$key = array_key_first($this->items);
		return $this->items[$key];
	}

	public function last(): mixed
	{
		if (!$this->items) {
			return null;
		}

		$key = array_key_last($this->items);
		return $this->items[$key];
	}

	public function shift(): mixed
	{
		return array_shift($this->items);
	}

	public function pop(): mixed
	{
		return array_pop($this->items);
	}

	public function remove(Item\Key\KeyInterface $key): void
	{
		if (!$this->has($key)) {
			return;
		}

		unset($this->items[$key()]);
	}

	public function merge(self $collection): self
	{
		foreach ($collection->items as $key => $item) {
			$this->items[$key] = $item;
		}

		return $this;
	}

	public function all(): array
	{
		return array_values($this->items);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->items));
	}

	public function count(): int
	{
		return count($this->items);
	}
}