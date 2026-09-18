<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Array;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule;

readonly class Rule extends AbstractRule
{
	protected function normalize(mixed $value): ?array
	{
		return is_array($value) ? $value : null;
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be an array');
	}
}
