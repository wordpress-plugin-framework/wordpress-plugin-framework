<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Routes;

use Closure;
use WordPressPluginFramework\Http\Method\Method;

interface RoutesBuilderInterface
{
	public function withRoute(RouteInterface $route): static;

	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresClosure = null): static;
	public function feed(string $name, Closure $closure, ?Closure $middlewaresClosure = null): static;
	public function rest(string $routeNamespace, string $route, Closure $closure, Method $method, ?Closure $middlewaresClosure = null): static;

	public function build(): RouteInterface;
}
