<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
	public function create(Closure $validatorsBuilderClosure): MiddlewareInterface;
}
