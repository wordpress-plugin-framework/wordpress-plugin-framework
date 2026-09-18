<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Bool;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule;

readonly class Rule extends AbstractRule
{
	protected function normalize(mixed $value): ?bool
	{
		return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be a bool');
	}
}
