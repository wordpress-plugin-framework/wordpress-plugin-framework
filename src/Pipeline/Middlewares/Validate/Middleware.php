<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareInterface,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected array $validators,
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		$messages = new MessageCollection();

		foreach ($this->validators as $validator) {
			$validator->validate(
				$request,
				$messages->add(...),
			);
		}

		if ($messages->isNotEmpty()) {
			throw new Exceptions\UnprocessableContent\Exception(
				'validation error',
				'',
				$messages->toArray()
			);
		}

		return $closure($request);
	}
}
