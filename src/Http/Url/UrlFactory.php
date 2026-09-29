<?php

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc9110,
	Http\Url\Host\HostFactoryInterface,
	Http\Url\Path\PathFactoryInterface,
	Http\Url\PercentEncoder\PercentEncoderInterface,
	Http\Url\Query\QueryFactoryInterface,
	Http\Url\Query\QueryInterface,
	Http\Url\Scheme\Scheme,
};

readonly class UrlFactory implements UrlFactoryInterface
{
	public function __construct(
		protected PercentEncoderInterface $percentEncoder,
		protected HostFactoryInterface $hostFactory,
		protected PathFactoryInterface $pathFactory,
		protected QueryFactoryInterface $queryFactory,
	) {
	}

	public function create(string $url): UrlInterface
	{
		if (preg_match('@' . Rfc3986::APPENDIX_B . '@s', $url, $match, PREG_UNMATCHED_AS_NULL) === false) {
			throw new UrlFactoryException('url is not checkable');
		}

		if ($match['scheme'] === null) {
			throw new UrlFactoryException('missing scheme');
		}

		if ($match['authority'] === null) {
			throw new UrlFactoryException('missing authority');
		}

		if ($match['fragment'] !== null) {
			throw new UrlFactoryException('fragment is not part of http(s) urls');
		}

		$authority = $this->authority($match['authority']);

		$scheme = Scheme::create($match['scheme']);

		$host = $this->hostFactory->create($authority['host']);
		$encodedPath = $this->percentEncoder->encodePath($match['path']);
		$path = $this->pathFactory->create($encodedPath);
		$query = $match['query'] === null ? null : $this->queryFactory->create($match['query']);

		return new Url($scheme, $host, $authority['port'], $path, $query);
	}

	protected function authority(string $authority): array
	{
		if (preg_match('@\A' . Rfc9110::AUTHORITY . '\z@', $authority, $match, PREG_UNMATCHED_AS_NULL) !== 1) {
			throw new UrlFactoryException('invalid authority');
		}

		if ($match['userinfo'] !== null) {
			throw new UrlFactoryException('userinfo is deprecated in http(s) urls');
		}

		if ($match['host'] === '') {
			throw new UrlFactoryException('empty host identifier');
		}

		return $match;
	}
}
