<?php

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};
use Closure;
use Stringable;

interface UrlInterface extends Stringable
{
	public function scheme(): Scheme;
	public function withScheme(Scheme $scheme): static;

	public function host(): string;
	public function withHost(string $host): static;

	public function port(): ?int;
	public function withPort(int $port): static;
	public function withoutPort(): static;

	public function path(): string;
	public function withPath(string $path): static;

	public function query(): ?QueryInterface;
	public function withQuery(QueryInterface|Closure $query): static;
	public function withoutQuery(): static;
}
