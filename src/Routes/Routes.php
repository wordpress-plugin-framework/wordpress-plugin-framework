<?php

namespace WordPressPluginFramework\Routes;

readonly class Routes implements RoutesInterface
{
	public function __construct(
		protected array $routes,
	) {
	}

	public function __invoke(): void
	{
		foreach ($this->routes as $route) {
			$route();
		}
	}

	public function up(): void
	{
		foreach ($this->routes as $route) {
			$route->up();
		}

		flush_rewrite_rules();
	}

	public function down(): void
	{
		foreach ($this->routes as $route) {
			$route->down();
		}

		flush_rewrite_rules();
	}
}
