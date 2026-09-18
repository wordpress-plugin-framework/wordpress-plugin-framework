<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Transaction;

use WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface;
}