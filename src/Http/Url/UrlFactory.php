<?php

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc9110,
	Http\Url\Host\HostFactoryInterface,
	Http\Url\Path\PathFactoryInterface,
	Http\Url\Query\QueryFactoryInterface,
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};

readonly class UrlFactory implements UrlFactoryInterface
{
	public function __construct(
		protected HostFactoryInterface $hostFactory,
		protected PathFactoryInterface $pathFactory,
		protected QueryFactoryInterface $queryFactory,
	) {
	}

	public function create(string $url): UrlInterface
	{
		$components = $this->components($url);

		if ($components['scheme'] === null) {
			throw new UrlFactoryException('missing scheme');
		}

		if ($components['authority'] === null) {
			throw new UrlFactoryException('missing authority');
		}

		if ($components['fragment'] !== null) {
			throw new UrlFactoryException('fragment is not part of http(s) urls');
		}

		$scheme = Scheme::create($components['scheme']);
		$authority = $this->authority($components['authority']);
		$host = $this->hostFactory->create($authority['host']);
		$path = $this->pathFactory->create($components['path']);
		$query = $this->query($components['query']);

		return new Url($scheme, $host, $authority['port'], $path, $query);
	}

	protected function components(string $url): array
	{
		$matched = preg_match('@' . Rfc3986::APPENDIX_B . '@s', $url, $match, PREG_UNMATCHED_AS_NULL);
		if ($matched === false) {
			throw new UrlFactoryException('url is not checkable');
		}

		return $match;
	}

	protected function authority(string $authority): array
	{
		$matched = preg_match('@\A' . Rfc9110::AUTHORITY . '\z@J', $authority, $match, PREG_UNMATCHED_AS_NULL);
		if ($matched === false) {
			throw new UrlFactoryException('authority is not checkable');
		}

		if ($matched !== 1) {
			throw new UrlFactoryException('invalid authority');
		}

		if ($match['userinfo'] !== null) {
			throw new UrlFactoryException('userinfo is deprecated in http(s) urls');
		}

		return $match;
	}

	protected function query(?string $query): ?QueryInterface
	{
		if ($query === null) {
			return null;
		}

		return $this->queryFactory->createFromEncoded($query);
	}
}
