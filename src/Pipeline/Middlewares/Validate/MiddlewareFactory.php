<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use WordPressPluginFramework\{
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorsBuilderInterface,
};

readonly class MiddlewareFactory implements MiddlewareFactoryInterface
{
	public function __construct(
		protected ValidatorsBuilderInterface $validatorsBuilder,
	) {
	}

	public function create(Closure $validatorsBuilderClosure): MiddlewareInterface
	{
		$validators = $this->buildValidators($validatorsBuilderClosure);

		return new Middleware($validators);
	}

	protected function buildValidators(Closure $closure): array
	{
		$validatorsBuilder = $closure($this->validatorsBuilder);
		if (!$validatorsBuilder instanceof ValidatorsBuilderInterface) {
			throw new MiddlewareFactoryException('closure must return validators builder instance');
		}

		return $validatorsBuilder->build();
	}
}
