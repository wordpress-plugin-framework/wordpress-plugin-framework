<?php

namespace WordPressPluginFramework\Pipeline\Middlewares;

use Closure;
use WordPressPluginFramework\{
	Pipeline\Middlewares\CurrentUserCan\Middleware as CurrentUserCanMiddleware,
	Pipeline\Middlewares\CurrentUserCan\Capability\Capability,
	Pipeline\Middlewares\LogExecutionTime\MiddlewareFactoryInterface as LogExecutionTimeMiddlewareFactoryInterface,
	Pipeline\Middlewares\Transaction\MiddlewareFactoryInterface as TransactionMiddlewareFactoryInterface,
	Pipeline\Middlewares\Validate\MiddlewareFactoryInterface as ValidateMiddlewareFactoryInterface,
	Pipeline\Middlewares\VerifyNonce\Middleware as VerifyNonceMiddleware,
};

readonly class MiddlewaresBuilder implements MiddlewaresBuilderInterface
{
	public function __construct(
		protected LogExecutionTimeMiddlewareFactoryInterface $logExecutionTimeMiddlewareFactory,
		protected TransactionMiddlewareFactoryInterface $transactionMiddlewareFactory,
		protected ValidateMiddlewareFactoryInterface $validateMiddlewareFactory,
		protected array $middlewares = [],
	) {
	}

	public function middlewares(): array
	{
		return $this->middlewares;
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static($this->logExecutionTimeMiddlewareFactory, $this->transactionMiddlewareFactory, $this->validateMiddlewareFactory, $middlewares);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->logExecutionTimeMiddlewareFactory, $this->transactionMiddlewareFactory, $this->validateMiddlewareFactory, []);
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return $this->withMiddlewares(
			...[
				...$this->middlewares,
				$middleware,
			],
		);
	}

	public function currentUserCan(Capability $capability): static
	{
		return $this->withMiddleware(
			new CurrentUserCanMiddleware($capability),
		);
	}

	public function logExecutionTime(): static
	{

		return $this->withMiddleware(
			$this->logExecutionTimeMiddlewareFactory->create(),
		);
	}

	public function transaction(): static
	{
		return $this->withMiddleware(
			$this->transactionMiddlewareFactory->create(),
		);
	}

	public function verifyNonce(string $name, string|int $action = -1): static
	{
		return $this->withMiddleware(
			new VerifyNonceMiddleware($name, $action),
		);
	}

	public function validate(Closure $closure): static
	{
		return $this->withMiddleware(
			$this->validateMiddlewareFactory->create($closure),
		);
	}

	public function build(): array
	{
		return $this->middlewares;
	}
}