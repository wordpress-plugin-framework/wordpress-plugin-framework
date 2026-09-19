<?php

namespace WordPressPluginFramework\Http\Responses;

use ArrayIterator;
use Closure;
use WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Response\ResponseInterface,
};
use Traversable;

readonly class Responses implements ResponsesInterface
{
	public function __construct(
		protected array $responses = [],
	) {
		$this->validate($this->responses);
	}

	public function first(): ResponseInterface
	{
		$key = array_key_first($this->responses);
		if ($key === null) {
			throw new ResponsesException('collection is empty');
		}

		return $this->responses[$key];
	}

	public function last(): ResponseInterface
	{
		$key = array_key_last($this->responses);
		if ($key === null) {
			throw new ResponsesException('collection is empty');
		}

		return $this->responses[$key];
	}

	public function filter(Closure $closure): static
	{
		$responses = array_filter($this->responses, $closure);

		return new static($responses);
	}

	public function map(Closure $closure): static
	{
		$responses = array_map($closure, $this->responses);

		return new static($responses);
	}

	public function sort(Closure $closure): static
	{
		$responses = $this->responses;
		usort($responses, $closure);

		return new static($responses);
	}

	public function filterByAccept(AcceptInterface $accept): static
	{
		return $this->filter(fn(ResponseInterface $response) => $this->q($accept, $response) > 0);
	}

	public function sortByAccept(AcceptInterface $accept): static
	{
		return $this->sort(fn(ResponseInterface $a, ResponseInterface $b) => $this->q($accept, $b) <=> $this->q($accept, $a));
	}

	public function isEmpty(): bool
	{
		return $this->count() === 0;
	}

	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(
			array_values($this->responses),
		);
	}

	public function count(): int
	{
		return count($this->responses);
	}

	public function __invoke(): array
	{
		return array_values($this->responses);
	}

	protected function validate(array $responses): void
	{
		foreach ($responses as $response) {
			if (!$response instanceof ResponseInterface) {
				throw new ResponsesException('must provide response interface');
			}

			$contentType = $response->headers()->contentType();
			if ($contentType === null) {
				throw new ResponsesException('response without content-type is not negotiable');
			}
		}
	}

	protected function q(AcceptInterface $accept, ResponseInterface $response): float
	{
		$mediaType = $response->headers()->contentType();
		if ($mediaType === null) {
			throw new ResponsesException('cant get q w/o content-type');
		}

		return $accept->q($mediaType);
	}
}
