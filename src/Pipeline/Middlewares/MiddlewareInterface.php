<?php

namespace WordPressPluginFramework\Pipeline\Middlewares;

use Closure;
use WordPressPluginFramework\Http\Request\RequestInterface;

interface MiddlewareInterface
{
	public function __invoke(RequestInterface $request, Closure $closure): mixed;
}