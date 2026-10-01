<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Collections\Message\Collection as MessageCollection,
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected ValidatorInterface $validator,
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		$messages = new MessageCollection();

		$this->validator->validate(
			$request,
			$messages->add(...),
		);

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
