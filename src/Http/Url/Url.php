<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Url\Fragment\FragmentInterface,
	Http\Url\Host\HostInterface,
	Http\Url\Path\PathInterface,
	Http\Url\Port\PortInterface,
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
	Preg\PregInterface,
};

readonly class Url implements UrlInterface
{
	protected ?PortInterface $port;

	public function __construct(
		protected PregInterface $preg,
		protected Scheme $scheme,
		protected HostInterface $host,
		?PortInterface $port,
		protected PathInterface $path,
		protected ?QueryInterface $query,
		protected ?FragmentInterface $fragment,
	) {
		$this->port = $this->normalizePort($port);
	}

	public function scheme(): Scheme
	{
		return $this->scheme;
	}

	public function withScheme(Scheme $scheme): static
	{
		return new static($this->preg, $scheme, $this->host, $this->port, $this->path, $this->query, $this->fragment);
	}

	public function host(): HostInterface
	{
		return $this->host;
	}

	public function withHost(HostInterface $host): static
	{
		return new static($this->preg, $this->scheme, $host, $this->port, $this->path, $this->query, $this->fragment);
	}

	public function port(): ?PortInterface
	{
		return $this->port;
	}

	public function withPort(PortInterface $port): static
	{
		return new static($this->preg, $this->scheme, $this->host, $port, $this->path, $this->query, $this->fragment);
	}

	public function withoutPort(): static
	{
		return new static($this->preg, $this->scheme, $this->host, null, $this->path, $this->query, $this->fragment);
	}

	public function path(): PathInterface
	{
		return $this->path;
	}

	public function withPath(PathInterface $path): static
	{
		return new static($this->preg, $this->scheme, $this->host, $this->port, $path, $this->query, $this->fragment);
	}

	public function query(): ?QueryInterface
	{
		return $this->query;
	}

	public function withQuery(QueryInterface $query): static
	{
		return new static($this->preg, $this->scheme, $this->host, $this->port, $this->path, $query, $this->fragment);
	}

	public function withoutQuery(): static
	{
		return new static($this->preg, $this->scheme, $this->host, $this->port, $this->path, null, $this->fragment);
	}

	public function fragment(): ?FragmentInterface
	{
		return $this->fragment;
	}

	public function withFragment(FragmentInterface $fragment): static
	{
		return new static($this->preg, $this->scheme, $this->host, $this->port, $this->path, $this->query, $fragment);
	}

	public function withoutFragment(): static
	{
		return new static($this->preg, $this->scheme, $this->host, $this->port, $this->path, $this->query, null);
	}

	public function __toString(): string
	{
		$url = "{$this->scheme->value}://{$this->host}";

		if ($this->port !== null) {
			$url .= ":{$this->port}";
		}

		$url .= $this->path;

		if ($this->query !== null) {
			$url .= "?{$this->query}";
		}

		if ($this->fragment !== null) {
			$url .= "#{$this->fragment}";
		}

		return $url;
	}

	protected function normalizePort(?PortInterface $port): ?PortInterface
	{
		if ($port === null) {
			return null;
		}

		return $this->scheme->port() === $port() ? null : $port;
	}
}
