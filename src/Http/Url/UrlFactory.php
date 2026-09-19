<?php

namespace WordPressPluginFramework\Http\Url;

use WordPressPluginFramework\Http\Url\Query\QueryFactoryInterface;

readonly class UrlFactory implements UrlFactoryInterface
{
	public function __construct(
		protected QueryFactoryInterface $queryFactory,
	) {
	}

	public function create(string $url): UrlInterface
	{
		$url = parse_url($url);
		if (!is_array($url)) {
			throw new UrlFactoryException('seriously damaged url');
		}

		$scheme = Scheme\Scheme::create($url['scheme'] ?? '');
		$query = array_key_exists('query', $url) ? $this->queryFactory->createFromEncoded($url['query']) : null;

		return new Url($scheme, $url['host'] ?? '', $url['port'] ?? null, $url['path'] ?? '', $query);
	}
}
