<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Ip;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule;

readonly class Rule extends AbstractRule
{
	protected const BREAK = false;

	protected function normalize(mixed $value): ?string
	{
		return filter_var($value, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE);
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be an ip');
	}
}
