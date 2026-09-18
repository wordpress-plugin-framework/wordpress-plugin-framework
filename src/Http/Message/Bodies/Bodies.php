<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Bodies;

use ArrayIterator;
use Closure;
use Hoo\WordPressPluginFramework\Http\Message\Body\BodyInterface;
use Traversable;

readonly class Bodies implements BodiesInterface
{
	public function __construct(
		protected array $bodies = [],
	) {
		$this->validate($this->bodies);
	}

	public function first(): BodyInterface
	{
		$key = array_key_first($this->bodies);
		if ($key === null) {
			throw new BodiesException('collection is empty');
		}

		return $this->bodies[$key];
	}

	public function last(): BodyInterface
	{
		$key = array_key_last($this->bodies);
		if ($key === null) {
			throw new BodiesException('collection is empty');
		}

		return $this->bodies[$key];
	}

	public function filter(Closure $closure): static
	{
		$bodies = array_filter($this->bodies, $closure);

		return new static($bodies);
	}

	public function sort(Closure $closure): static
	{
		$bodies = $this->bodies;
		usort($bodies, $closure);

		return new static($bodies);
	}


	public function getIterator(): Traversable
	{
		return new ArrayIterator(
			array_values($this->bodies),
		);
	}

	public function count(): int
	{
		return count($this->bodies);
	}

	public function isEmpty(): bool
	{
		return $this->count() === 0;
	}

	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	protected function validate(array $bodies): void
	{
		foreach ($bodies as $body) {
			if (!$body instanceof BodyInterface) {
				throw new BodiesException('must provide body interface');
			}
		}
	}
}
