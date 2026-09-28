<?php

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Url\Host\HostInterface,
	Http\Url\Path\PathInterface,
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};
use Stringable;

interface UrlInterface extends Stringable
{
	public function scheme(): Scheme;
	public function withScheme(Scheme $scheme): static;

	public function host(): HostInterface;
	public function withHost(HostInterface $host): static;

	public function port(): ?string;
	public function withPort(string $port): static;
	public function withoutPort(): static;

	public function path(): PathInterface;
	public function withPath(PathInterface $path): static;

	public function query(): ?QueryInterface;
	public function withQuery(QueryInterface $query): static;
	public function withoutQuery(): static;
}
