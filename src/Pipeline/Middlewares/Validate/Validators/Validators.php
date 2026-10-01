<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators;

use Closure;
use WordPressPluginFramework\Http\Request\RequestInterface;

readonly class Validators implements ValidatorInterface
{
	public function __construct(
		protected array $validators = [],
	) {
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		foreach ($this->validators as $validator) {
			$validator->validate($request, $closure);
		}
	}
}
