<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\String;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule;

readonly class Rule extends AbstractRule
{
	protected function normalize(mixed $value): ?string
	{
		return is_string($value) ? $value : null;
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be a string');
	}
}
