<?php

namespace WordPressPluginFramework\Pipeline;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewaresBuilderInterface,
};

readonly class PipelineFactory implements PipelineFactoryInterface
{
	public function __construct(
		protected MiddlewaresBuilderInterface $middlewaresBuilder,
	) {
	}

	public function create(RequestInterface $request, ?Closure $middlewaresBuilderClosure = null): PipelineInterface
	{
		$middlewares = $this->tryBuildMiddlewares($middlewaresBuilderClosure);

		return new Pipeline($request, $middlewares);
	}

	protected function buildMiddlewares(Closure $closure): array
	{
		$middlewaresBuilder = $closure($this->middlewaresBuilder);
		if (!$middlewaresBuilder instanceof MiddlewaresBuilderInterface) {
			throw new PipelineFactoryException('closure must return middlewares builder instance');
		}

		return $middlewaresBuilder->build();
	}

	protected function tryBuildMiddlewares(?Closure $closure): array
	{
		if ($closure === null) {
			return [];
		}

		return $this->buildMiddlewares($closure);
	}
}
