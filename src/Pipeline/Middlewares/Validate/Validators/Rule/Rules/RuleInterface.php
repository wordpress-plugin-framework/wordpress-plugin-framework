<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules;

use Closure;

interface RuleInterface
{
	public function break(mixed $value, Closure $closure): bool;
}
