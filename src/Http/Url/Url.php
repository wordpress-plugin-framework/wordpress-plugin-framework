<?php

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Url\Host\HostInterface,
	Http\Url\Path\PathInterface,
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};

readonly class Url implements UrlInterface
{
	protected ?string $port;

	public function __construct(
		protected Scheme $scheme,
		protected HostInterface $host,
		?string $port,
		protected PathInterface $path,
		protected ?QueryInterface $query = null,
	) {
		$this->validatePort($port);
		$this->port = $this->normalizePort($port);
	}

	public function scheme(): Scheme
	{
		return $this->scheme;
	}

	public function withScheme(Scheme $scheme): static
	{
		return new static($scheme, $this->host, $this->port, $this->path, $this->query);
	}

	public function host(): HostInterface
	{
		return $this->host;
	}

	public function withHost(HostInterface $host): static
	{
		return new static($this->scheme, $host, $this->port, $this->path, $this->query);
	}

	public function port(): ?string
	{
		return $this->port;
	}

	public function withPort(string $port): static
	{
		return new static($this->scheme, $this->host, $port, $this->path, $this->query);
	}

	public function withoutPort(): static
	{
		return new static($this->scheme, $this->host, null, $this->path, $this->query);
	}

	public function path(): PathInterface
	{
		return $this->path;
	}

	public function withPath(PathInterface $path): static
	{
		return new static($this->scheme, $this->host, $this->port, $path, $this->query);
	}

	public function query(): ?QueryInterface
	{
		return $this->query;
	}

	public function withQuery(QueryInterface $query): static
	{
		return new static($this->scheme, $this->host, $this->port, $this->path, $query);
	}

	public function withoutQuery(): static
	{
		return new static($this->scheme, $this->host, $this->port, $this->path, null);
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

		return $url;
	}

	protected function validatePort(?string $port): void
	{
		if ($port === null) {
			return;
		}

		if (!preg_match('@\A' . Rfc3986::PORT . '\z@', $port)) {
			throw new UrlException('invalid port');
		}
	}

	protected function normalizePort(?string $port): ?string
	{
		if ($port === null) {
			return null;
		}

		if ($port === '') {
			return null;
		}

		$value = (int) $port;

		return $this->scheme->port() === $value ? null : $port;
	}
}
