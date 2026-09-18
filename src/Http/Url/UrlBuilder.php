<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

use Hoo\WordPressPluginFramework\{
	Http\Url\Query\QueryFactoryInterface,
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};

readonly class UrlBuilder implements UrlBuilderInterface
{
	protected ?QueryInterface $query;

	public function __construct(
		protected QueryFactoryInterface $queryFactory,
		protected ?Scheme $scheme = null,
		protected string $host = '',
		protected ?int $port = null,
		protected string $path = '',
		?QueryInterface $query = null,
	) {
		$this->query = $query ?? $this->queryFactory->create([]);
	}

	public function scheme(Scheme|string $scheme): static
	{
		$scheme = $this->createScheme($scheme);
		return new static($this->queryFactory, $scheme, $this->host, $this->port, $this->path, $this->query);
	}

	public function host(string $host): static
	{
		return new static($this->queryFactory, $this->scheme, $host, $this->port, $this->path, $this->query);
	}

	public function port(int $port): static
	{
		return new static($this->queryFactory, $this->scheme, $this->host, $port, $this->path, $this->query);
	}

	public function path(string $path): static
	{
		return new static($this->queryFactory, $this->scheme, $this->host, $this->port, $path, $this->query);
	}

	public function query(mixed $query): static
	{
		$query = $this->createQuery($query);
		return new static($this->queryFactory, $this->scheme, $this->host, $this->port, $this->path, $query);
	}

	public function build(): UrlInterface
	{
		if ($this->scheme === null) {
			throw new UrlBuilderException('scheme is mandatory');
		}

		if ($this->host === '') {
			throw new UrlBuilderException('host is mandatory');
		}

		return new Url($this->scheme, $this->host, $this->port, $this->path, $this->query);
	}

	protected function createScheme(Scheme|string $scheme): Scheme
	{
		if ($scheme instanceof Scheme) {
			return $scheme;
		}

		return Scheme::create($scheme);
	}

	protected function createQuery(mixed $query): QueryInterface
	{
		return $this->queryFactory->create($query);
	}
}
