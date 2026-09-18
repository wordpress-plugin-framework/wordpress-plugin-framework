<?php

namespace WordPressPluginFramework\Routes;

use Closure;

readonly class RoutesFactory implements RoutesFactoryInterface
{
	public function __construct(
		protected RoutesBuilderInterface $routesBuilder,
	) {
	}

	public function create(Closure $closure): RoutesInterface
	{
		return new Routes(
			$this->routes($closure),
		);
	}

	protected function routes(Closure $closure): array
	{
		$routesBuilder = $closure($this->routesBuilder);
		if (!$routesBuilder instanceof RoutesBuilderInterface) {
			throw new RoutesFactoryException('closure must return routes builder instance');
		}

		return $routesBuilder->build();
	}
}
