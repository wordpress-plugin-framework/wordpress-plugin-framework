<?php

namespace WordPressPluginFramework\Cache;

use Closure;

interface CacheInterface
{
	public function remember(string $key, Closure $closure, ?int $ttl = null): mixed;
	public function forget(string $key): void;
}