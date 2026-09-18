<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\CurrentUserCan;

use Closure;
use WordPressPluginFramework\{
	Http\Exceptions\Forbidden\Exception as ForbiddenException,
	Http\Request\RequestInterface,
	Pipeline\Middlewares\CurrentUserCan\Capability\Capability,
	Pipeline\Middlewares\MiddlewareInterface,
};

readonly class Middleware implements MiddlewareInterface
{
	public function __construct(
		protected Capability $capability,
	) {
	}

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		if (!current_user_can($this->capability->value)) {
			throw new ForbiddenException('can not', 'current_user_can_error');
		}

		return $closure($request);
	}
}