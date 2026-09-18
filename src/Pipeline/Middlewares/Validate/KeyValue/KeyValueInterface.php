<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValue;

use WordPressPluginFramework\Http\Request\RequestInterface;

interface KeyValueInterface
{
	public function key(): string;
	public function values(RequestInterface $request): ?array;
	public function value(RequestInterface $request): mixed;
}
