<?php

namespace WordPressPluginFramework\Pipeline;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\CurrentUserCan\Middleware as CurrentUserCanMiddleware,
	Pipeline\Middlewares\CurrentUserCan\Capability\Capability,
	Pipeline\Middlewares\LogExecutionTime\MiddlewareFactoryInterface as LogExecutionTimeMiddlewareFactoryInterface,
	Pipeline\Middlewares\Transaction\MiddlewareFactoryInterface as TransactionMiddlewareFactoryInterface,
	Pipeline\Middlewares\Validate\MiddlewareFactoryInterface as ValidateMiddlewareFactoryInterface,
	Pipeline\Middlewares\VerifyNonce\Middleware as VerifyNonceMiddleware,
};

readonly class PipelineBuilder implements PipelineBuilderInterface
{
	public function __construct(
		protected LogExecutionTimeMiddlewareFactoryInterface $logExecutionTimeMiddlewareFactory,
		protected TransactionMiddlewareFactoryInterface $transactionMiddlewareFactory,
		protected ValidateMiddlewareFactoryInterface $validateMiddlewareFactory,
		protected RequestInterface $request,
		protected array $middlewares = [],
	) {
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		$middlewares = $this->middlewares;
		$middlewares[] = $middleware;

		return new static($this->logExecutionTimeMiddlewareFactory, $this->transactionMiddlewareFactory, $this->validateMiddlewareFactory, $middlewares);
	}

	public function currentUserCan(Capability $capability): static
	{
		$middleware = new CurrentUserCanMiddleware($capability);
		return $this->withMiddleware($middleware);
	}

	public function logExecutionTime(): static
	{
		$middleware = $this->logExecutionTimeMiddlewareFactory->create();
		return $this->withMiddleware($middleware);
	}

	public function transaction(): static
	{
		$middleware = $this->transactionMiddlewareFactory->create();
		return $this->withMiddleware($middleware);
	}

	public function verifyNonce(string $name, string|int $action = -1): static
	{
		$middleware = new VerifyNonceMiddleware($name, $action);
		return $this->withMiddleware($middleware);
	}

	public function validate(Closure $closure): static
	{
		$middleware = $this->validateMiddlewareFactory->create($closure);
		return $this->withMiddleware($middleware);
	}

	public function build(): PipelineInterface
	{
		return new Pipeline($this->request, $this->middlewares);
	}
}