<?php

namespace WordPressPluginFramework\Routes;

use Closure;
use WordPressPluginFramework\{
	Http\Method\Method,
	Http\Response\ResponseFactoryInterface,
	Pipeline\PipelineFactoryInterface,
	Exceptions\Handler\HandlerInterface,
	Pipeline\Middlewares\MiddlewaresFactoryInterface,
};

readonly class RoutesBuilder implements RoutesBuilderInterface
{
	public function __construct(
		protected ResponseFactoryInterface $responseFactory,
		protected PipelineFactoryInterface $pipelineFactory,
		protected HandlerInterface $handler,
		protected MiddlewaresFactoryInterface $middlewaresFactory,
		protected array $routes = [],
	) {
	}

	public function withRoute(RouteInterface $route): static
	{
		$routes = $this->routes;
		$routes[] = $route;

		return new static($this->responseFactory, $this->pipelineFactory, $this->handler, $this->middlewaresFactory, $routes);
	}

	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$pipelineFactory = $this->pipelineFactory($middlewaresClosure);

		return $this->withRoute(
			new AdminAjax\Route($this->responseFactory, $pipelineFactory, $action, $closure)
		);
	}

	public function feed(string $name, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$pipelineFactory = $this->pipelineFactory($middlewaresClosure);

		return $this->withRoute(
			new Feed\Route($this->responseFactory, $pipelineFactory, $name, $closure)
		);
	}

	public function rest(string $routeNamespace, string $route, Closure $closure, Method $method, ?Closure $middlewaresClosure = null): static
	{
		$pipelineFactory = $this->pipelineFactory($middlewaresClosure);

		return $this->withRoute(
			new Rest\Route($this->responseFactory, $pipelineFactory, $routeNamespace, $route, $closure, $method)
		);
	}

	public function build(): RouteInterface
	{
		return new Routes($this->routes);
	}

	protected function pipelineFactory(?Closure $closure): PipelineFactoryInterface
	{
		return $this->pipelineFactory
			->withMiddlewares(
				$this->middlewaresFactory->tryCreate($closure),
			)
			->withHandler($this->handler);
	}
}
