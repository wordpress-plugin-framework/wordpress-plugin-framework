<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline\Middlewares;

use Closure;
use WordPressPluginFramework\Http\Request\RequestInterface;

interface MiddlewareInterface
{
	public function __invoke(RequestInterface $request, Closure $closure): mixed;
}