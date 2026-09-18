<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\LogExecutionTime;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Loggers\LoggerInterface,
	Pipeline\Middlewares\MiddlewareInterface,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected LoggerInterface $logger,
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		$startTime = microtime(true);

		$return = $closure($request);
		
		$stopTime = microtime(true);

		$this->logger->info(
			sprintf(
				'Execution time: %d ms',
				($stopTime - $startTime) * 1000,
			)
		);

		return $return;
	}
}