<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Nullable;

use Closure;
use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function break(mixed $value, Closure $closure): bool
	{
		return $value === null;
	}
}
