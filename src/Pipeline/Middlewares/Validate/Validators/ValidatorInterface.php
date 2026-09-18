<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators;

use Closure;
use WordPressPluginFramework\Http\Request\RequestInterface;

interface ValidatorInterface
{
	public function validate(RequestInterface $request, Closure $closure): void;
}
