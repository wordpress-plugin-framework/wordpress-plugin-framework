<?php

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};
use Closure;

readonly class Url implements UrlInterface
{
	protected string $host;
	protected string $path;
	protected ?int $port;

	public function __construct(
		protected Scheme $scheme,
		string $host,
		?int $port,
		string $path,
		protected QueryInterface $query,
	) {
		$this->validateHost($host);
		$this->host = $this->normalizeHost($host);

		$this->validatePort($port);
		$this->port = $this->normalizePort($port);

		$this->validatePath($path);
		$this->path = $this->normalizePath($path);
	}

	public function scheme(): Scheme
	{
		return $this->scheme;
	}

	public function withScheme(Scheme $scheme): static
	{
		return new static($scheme, $this->host, $this->port, $this->path, $this->query);
	}

	public function host(): string
	{
		return $this->host;
	}

	public function withHost(string $host): static
	{
		return new static($this->scheme, $host, $this->port, $this->path, $this->query);
	}

	public function port(): ?int
	{
		return $this->port;
	}

	public function withPort(int $port): static
	{
		return new static($this->scheme, $this->host, $port, $this->path, $this->query);
	}

	public function withoutPort(): static
	{
		return new static($this->scheme, $this->host, null, $this->path, $this->query);
	}

	public function path(): string
	{
		return $this->path;
	}

	public function withPath(string $path): static
	{
		return new static($this->scheme, $this->host, $this->port, $path, $this->query);
	}

	public function query(): QueryInterface
	{
		return $this->query;
	}

	public function withQuery(QueryInterface|Closure $query): static
	{
		if ($query instanceof Closure) {
			$query = $query($this->query);
		}

		if (!$query instanceof QueryInterface) {
			throw new UrlException('must provide query interface');
		}

		return new static($this->scheme, $this->host, $this->port, $this->path, $query);
	}

	public function __toString(): string
	{
		$url = "{$this->scheme->value}://{$this->host}";

		if ($this->port !== null) {
			$url .= ":{$this->port}";
		}

		$url .= "{$this->path}{$this->query}";

		return $url;
	}

	protected function validateHost(string $host): void
	{
		if ($host === '') {
			throw new UrlException('host is mandatory');
		}
	}

	protected function normalizeHost(string $host): string
	{
		return strtolower($host);
	}

	protected function validatePort(?int $port): void
	{
		if (
			$port !== null &&
			(
				$port < 1 ||
				$port > 65535
			)
		) {
			throw new UrlException('port must be within range');
		}
	}

	protected function normalizePort(?int $port): ?int
	{
		if ($port === null) {
			return null;
		}

		return $this->scheme->port() === $port ? null : $port;
	}

	protected function validatePath(string $path): void
	{
		if (
			$path !== '' &&
			$path[0] !== '/'
		) {
			throw new UrlException('path must be empty or begin with "/" when authority is present');
		}
	}

	protected function normalizePath(string $path): string
	{
		if ($path === '') {
			return '';
		}

		$segments = explode('/', $path);

		$path = array_reduce($segments, function ($carry, $segment) {
			if ($segment === '.') {
				return $carry;
			}

			if ($segment === '..') {
				if (count($carry) > 1) {
					array_pop($carry);
				}

				return $carry;
			}

			array_push($carry, $segment);

			return $carry;
		}, []);

		$end = end($segments);
		if (
			$end === '.' ||
			$end === '..'
		) {
			array_push($path, '');
		}

		return implode('/', $path);
	}
}