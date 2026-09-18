<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\BackedEnum;

use BackedEnum;
use WordPressPluginFramework\{
	Localization\Translator\TranslatorInterface,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleException,
};

readonly class Rule extends AbstractRule
{
	public function __construct(
		TranslatorInterface $translator,
		protected string $class,
		?string $message = null,
	) {
		parent::__construct($translator, $message);

		if (!is_subclass_of($this->class, BackedEnum::class)) {
			throw new RuleException("Class {$this->class} must be a BackedEnum.");
		}
	}

	protected function normalize(mixed $value): ?BackedEnum
	{
		if (
			!is_int($value) &&
			!is_string($value)
		) {
			return null;
		}

		return $this->class::tryFrom($value);
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be a BackedEnum');
	}
}
