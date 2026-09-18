<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Int;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule;

readonly class Rule extends AbstractRule
{
	protected function normalize(mixed $value): ?int
	{
		return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be an int');
	}
}
