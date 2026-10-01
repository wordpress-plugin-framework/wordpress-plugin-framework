<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline;

use Closure;
use WordPressPluginFramework\Pipeline\{
	Middlewares\MiddlewareInterface,
	Middlewares\CurrentUserCan\Capability\Capability,
};

interface PipelineBuilderInterface
{
	public function withMiddleware(MiddlewareInterface $middleware): static;

	public function currentUserCan(Capability $capability): static;
	public function logExecutionTime(): static;
	public function transaction(): static;
	public function verifyNonce(string $name, string|int $action = -1): static;
	public function validate(Closure $closure): static;

	public function build(): PipelineInterface;
}