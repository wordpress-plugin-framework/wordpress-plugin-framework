<?php

namespace WordPressPluginFramework\Cache;

use Closure;
use WordPressPluginFramework\Value\Value;

readonly class Cache implements CacheInterface
{
	public function __construct(
		protected int $ttl,
	) {
	}

	public function remember(string $key, Closure $closure, ?int $ttl = null): mixed
	{
		$value = get_transient($key);
		if ($value !== false) {
			if (!$value instanceof Value) {
				throw new CacheException('corrupted cache value');
			}

			return $value->value;
		}

		$value = new Value($closure());

		set_transient($key, $value, $ttl ?? $this->ttl);

		return $value->value;
	}

	public function forget(string $key): void
	{
		delete_transient($key);
	}
}