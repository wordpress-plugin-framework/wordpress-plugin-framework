<?php

namespace WordPressPluginFramework\Http\Responses;

use Countable;
use WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Response\ResponseInterface,
};
use Closure;
use IteratorAggregate;

interface ResponsesInterface extends IteratorAggregate, Countable
{
	public function __invoke(): array;

	public function first(): ResponseInterface;
	public function last(): ResponseInterface;

	public function filter(Closure $closure): static;
	public function map(Closure $closure): static;
	public function sort(Closure $closure): static;

	public function filterByAccept(AcceptInterface $accept): static;
	public function sortByAccept(AcceptInterface $accept): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
