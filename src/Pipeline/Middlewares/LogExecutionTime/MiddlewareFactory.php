<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\LogExecutionTime;

use WordPressPluginFramework\{
	Loggers\LoggerInterface,
	Pipeline\Middlewares\MiddlewareInterface,
};

readonly class MiddlewareFactory implements MiddlewareFactoryInterface
{
	public function __construct(
		protected LoggerInterface $logger,
	) {
	}

	public function create(): MiddlewareInterface
	{
		return new Middleware($this->logger);
	}
}