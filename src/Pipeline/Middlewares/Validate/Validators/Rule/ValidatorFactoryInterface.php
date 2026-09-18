<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule;

use Closure;
use WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

interface ValidatorFactoryInterface
{
	public function create(KeyValueInterface $keyValue, Closure $closure): ValidatorInterface;
}