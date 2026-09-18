<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\{
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};

interface UrlBuilderInterface
{
	public function scheme(Scheme|string $scheme): static;
	public function host(string $host): static;
	public function port(int $port): static;
	public function path(string $path): static;
	public function query(mixed $query): static;

	public function build(): UrlInterface;
}
