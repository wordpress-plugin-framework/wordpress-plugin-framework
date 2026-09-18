<?php

namespace WordPressPluginFramework\Pipeline\Middlewares;

use Closure;
use WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan\Capability\Capability;


interface MiddlewaresBuilderInterface
{
	public function middlewares(): array;
	public function withMiddlewares(MiddlewareInterface ...$middlewares): static;
	public function withoutMiddlewares(): static;

	public function withMiddleware(MiddlewareInterface $middleware): static;

	public function currentUserCan(Capability $capability): static;
	public function logExecutionTime(): static;
	public function transaction(): static;
	public function verifyNonce(string $name, string|int $action = -1): static;
	public function validate(Closure $closure): static;

	public function build(): array;
}