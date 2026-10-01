<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Url\Host\HostInterface,
	Http\Url\Path\PathInterface,
	Http\Url\Port\PortInterface,
	Http\Url\Scheme\Scheme,
};

interface UrlBuilderInterface
{
	public function scheme(Scheme|string $scheme): static;
	public function host(HostInterface|string $host): static;
	public function port(PortInterface|string $port): static;
	public function path(PathInterface|string $path): static;
	public function query(mixed $query): static;

	public function build(): UrlInterface;
}
