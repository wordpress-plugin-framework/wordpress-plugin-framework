<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Closure;
use Countable;
use Hoo\WordPressPluginFramework\{
    Http\Message\Headers\Accept\AcceptInterface,
    Http\Response\ResponseInterface,
};
use IteratorAggregate;

interface ResponsesInterface extends IteratorAggregate, Countable
{
    public function isEmpty(): bool;
    public function isNotEmpty(): bool;

    public function first(): ResponseInterface;
    public function last(): ResponseInterface;

    public function filter(Closure $closure): static;
    public function sort(Closure $closure): static;

    public function filterByAccept(AcceptInterface $accept): static;
    public function sortByAccept(AcceptInterface $accept): static;
}
