<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Regexp;

use Closure;
use WordPressPluginFramework\{
	Localization\Translator\TranslatorInterface,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule
};

readonly class Rule extends AbstractRule
{
	protected const BREAK = false;

	public function __construct(
		TranslatorInterface $translator,
		protected string $regexp,
		?string $message = null,
	) {
		parent::__construct($translator, $message);
	}
	
	protected function normalize(mixed $value): ?string
	{
		return filter_var($value, FILTER_VALIDATE_REGEXP, [
			'options' => [
				'regexp' => $this->regexp,
			],
			'flags' => FILTER_NULL_ON_FAILURE,
		]);
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be a regexp');
	}
}
