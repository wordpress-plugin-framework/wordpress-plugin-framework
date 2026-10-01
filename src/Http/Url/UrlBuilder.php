<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc5234,
	Http\Url\Fragment\FragmentFactoryInterface,
	Http\Url\Fragment\FragmentInterface,
	Http\Url\Host\HostFactoryInterface,
	Http\Url\Host\HostInterface,
	Http\Url\Path\PathFactoryInterface,
	Http\Url\Path\PathInterface,
	Http\Url\Port\PortFactoryInterface,
	Http\Url\Port\PortInterface,
	Http\Url\Query\QueryFactoryInterface,
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
	Preg\PregInterface,
};

readonly class UrlBuilder implements UrlBuilderInterface
{
	protected PathInterface $path;

	public function __construct(
		protected PregInterface $preg,
		protected HostFactoryInterface $hostFactory,
		protected PortFactoryInterface $portFactory,
		protected PathFactoryInterface $pathFactory,
		protected QueryFactoryInterface $queryFactory,
		protected FragmentFactoryInterface $fragmentFactory,
		protected ?Scheme $scheme = null,
		protected ?HostInterface $host = null,
		protected ?PortInterface $port = null,
		?PathInterface $path = null,
		protected ?QueryInterface $query = null,
		protected ?FragmentInterface $fragment = null,
	) {
		$this->path = $path ?? $this->pathFactory->create('');
	}

	public function scheme(Scheme|string $scheme): static
	{
		$scheme = $this->createScheme($scheme);
		return new static($this->preg, $this->hostFactory, $this->portFactory, $this->pathFactory, $this->queryFactory, $this->fragmentFactory, $scheme, $this->host, $this->port, $this->path, $this->query, $this->fragment);
	}

	public function host(HostInterface|string $host): static
	{
		$host = $this->createHost($host);
		return new static($this->preg, $this->hostFactory, $this->portFactory, $this->pathFactory, $this->queryFactory, $this->fragmentFactory, $this->scheme, $host, $this->port, $this->path, $this->query, $this->fragment);
	}

	public function port(PortInterface|string $port): static
	{
		$port = $this->createPort($port);
		return new static($this->preg, $this->hostFactory, $this->portFactory, $this->pathFactory, $this->queryFactory, $this->fragmentFactory, $this->scheme, $this->host, $port, $this->path, $this->query, $this->fragment);
	}

	public function path(PathInterface|string $path): static
	{
		$path = $this->createPath($path);
		return new static($this->preg, $this->hostFactory, $this->portFactory, $this->pathFactory, $this->queryFactory, $this->fragmentFactory, $this->scheme, $this->host, $this->port, $path, $this->query, $this->fragment);
	}

	public function query(mixed $query): static
	{
		$query = $this->createQuery($query);
		return new static($this->preg, $this->hostFactory, $this->portFactory, $this->pathFactory, $this->queryFactory, $this->fragmentFactory, $this->scheme, $this->host, $this->port, $this->path, $query, $this->fragment);
	}

	public function fragment(FragmentInterface|string $fragment): static
	{
		$fragment = $this->createFragment($fragment);
		return new static($this->preg, $this->hostFactory, $this->portFactory, $this->pathFactory, $this->queryFactory, $this->fragmentFactory, $this->scheme, $this->host, $this->port, $this->path, $this->query, $fragment);
	}

	public function build(): UrlInterface
	{
		if ($this->scheme === null) {
			throw new UrlBuilderException('scheme is mandatory');
		}

		if ($this->host === null) {
			throw new UrlBuilderException('host is mandatory');
		}

		return new Url($this->preg, $this->scheme, $this->host, $this->port, $this->path, $this->query, $this->fragment);
	}

	protected function createScheme(Scheme|string $scheme): Scheme
	{
		if ($scheme instanceof Scheme) {
			return $scheme;
		}

		return Scheme::create($scheme);
	}

	protected function createHost(HostInterface|string $host): HostInterface
	{
		if ($host instanceof HostInterface) {
			return $host;
		}

		return $this->hostFactory->create($host);
	}

	protected function createPort(PortInterface|string $port): ?PortInterface
	{
		if ($port instanceof PortInterface) {
			return $port;
		}

		if ($port === '') {
			return null;
		}

		return $this->portFactory->create($port);
	}

	protected function createPath(PathInterface|string $path): PathInterface
	{
		if ($path instanceof PathInterface) {
			return $path;
		}

		$path = $this->preg->replaceCallback('@(?!/|' . Rfc3986::PCHAR . ')' . Rfc5234::OCTET . '@', fn($match) => rawurlencode($match[0]), $path);

		return $this->pathFactory->create($path);
	}

	protected function createQuery(mixed $query): QueryInterface
	{
		return $this->queryFactory->create($query);
	}

	protected function createFragment(FragmentInterface|string $fragment): FragmentInterface
	{
		if ($fragment instanceof FragmentInterface) {
			return $fragment;
		}

		$fragment = $this->preg->replaceCallback('@(?!' . Rfc3986::PCHAR . '|/|\?)' . Rfc5234::OCTET . '@', fn($match) => rawurlencode($match[0]), $fragment);

		return $this->fragmentFactory->create($fragment);
	}
}
