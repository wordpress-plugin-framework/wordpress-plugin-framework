<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Transaction;

use Closure;
use WordPressPluginFramework\{
	Database\DatabaseInterface,
	Http\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewareInterface,
};
use Throwable;

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected DatabaseInterface $database,
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		$this->database->startTransaction();
		
		try {
			$return = $closure($request);

			$this->database->commit();
		} catch (Throwable $throwable) {
			$this->database->rollback();

			throw $throwable;
		}

		return $return;
	}
}