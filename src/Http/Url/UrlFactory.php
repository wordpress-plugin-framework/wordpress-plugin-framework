<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc5234,
	Http\Abnf\Rfc9110,
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

readonly class UrlFactory implements UrlFactoryInterface
{
	public function __construct(
		protected PregInterface $preg,
		protected HostFactoryInterface $hostFactory,
		protected PortFactoryInterface $portFactory,
		protected PathFactoryInterface $pathFactory,
		protected QueryFactoryInterface $queryFactory,
		protected FragmentFactoryInterface $fragmentFactory,
	) {
	}

	public function create(string $url): UrlInterface
	{
		$match = $this->preg->match('@' . Rfc3986::APPENDIX_B . '@s', $url, PREG_UNMATCHED_AS_NULL);
		if ($match['authority'] === null) {
			throw new UrlFactoryException('missing authority');
		}

		$match['authority'] = $this->preg->match('@\A' . Rfc9110::AUTHORITY . '\z@', $match['authority'], PREG_UNMATCHED_AS_NULL);
		if ($match['authority'] === null) {
			throw new UrlFactoryException('invalid authority');
		}

		if ($match['authority']['userinfo'] !== null) {
			throw new UrlFactoryException('userinfo is deprecated in http(s) urls');
		}

		$scheme = $this->createScheme($match['scheme']);
		$host = $this->createHost($match['authority']['host']);
		$port = $this->createPort($match['authority']['port']);
		$path = $this->createPath($match['path']);
		$query = $this->createQuery($match['query']);
		$fragment = $this->createFragment($match['fragment']);

		return new Url($this->preg, $scheme, $host, $port, $path, $query, $fragment);
	}

	protected function createScheme(?string $scheme): Scheme
	{
		if ($scheme === null) {
			throw new UrlFactoryException('missing scheme');
		}

		return Scheme::create($scheme);
	}

	protected function createHost(string $host): HostInterface
	{
		return $this->hostFactory->create($host);
	}

	protected function createPort(?string $port): ?PortInterface
	{
		if (
			$port === null ||
			$port === ''
		) {
			return null;
		}

		return $this->portFactory->create($port);
	}

	protected function createPath(string $path): PathInterface
	{
		$path = $this->preg->replaceCallback('@(?!/|' . Rfc3986::PCHAR . ')' . Rfc5234::OCTET . '@', fn($match) => rawurlencode($match[0]), $path);

		return $this->pathFactory->create($path);
	}

	protected function createQuery(?string $query): ?QueryInterface
	{
		if ($query === null) {
			return null;
		}

		$query = $this->preg->replaceCallback('@(?!' . Rfc3986::PCHAR . '|/|\?)' . Rfc5234::OCTET . '@', fn($match) => rawurlencode($match[0]), $query);

		return $this->queryFactory->createFromEncoded($query);
	}

	protected function createFragment(?string $fragment): ?FragmentInterface
	{
		if ($fragment === null) {
			return null;
		}

		$fragment = $this->preg->replaceCallback('@(?!' . Rfc3986::PCHAR . '|/|\?)' . Rfc5234::OCTET . '@', fn($match) => rawurlencode($match[0]), $fragment);

		return $this->fragmentFactory->create($fragment);
	}
}
