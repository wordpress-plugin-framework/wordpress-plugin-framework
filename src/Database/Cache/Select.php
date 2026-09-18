<?php

namespace WordPressPluginFramework\Database\Cache;

use WordPressPluginFramework\Cache\CacheInterface;
use WordPressPluginFramework\Database\Select\SelectInterface;
use WordPressPluginFramework\Database\Query;

readonly class Select implements SelectInterface
{
	public function __construct(
		protected CacheInterface $cache,
		protected SelectInterface $select,
		protected string $salt,
	) {
	}

	public function __invoke(Query\QueryInterface $query): array
	{
		return $this->cache->remember(hash_hmac('sha256', $query, $this->salt), fn() => ($this->select)($query));
	}
}