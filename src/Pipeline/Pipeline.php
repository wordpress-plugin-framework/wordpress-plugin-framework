<?php

namespace WordPressPluginFramework\Pipeline;

use Closure;
use WordPressPluginFramework\Http\Request\RequestInterface;

readonly class Pipeline implements PipelineInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected array $middlewares,
	) {
	}

	public function __invoke(Closure $closure): mixed
	{
		return array_reduce(array_reverse($this->middlewares), fn($closure, $middleware) => fn($request) => $middleware($request, $closure), $closure)($this->request);
	}
}
