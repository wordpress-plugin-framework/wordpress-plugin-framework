<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\LogExecutionTime;

use WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface;
}