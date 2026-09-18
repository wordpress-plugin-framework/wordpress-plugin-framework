<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\DateTime;

use DateTime;
use WordPressPluginFramework\{
	Localization\Translator\TranslatorInterface,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule
};

readonly class Rule extends AbstractRule
{
	public function __construct(
		TranslatorInterface $translator,
		protected string $format,
		?string $message = null,
	) {
		parent::__construct($translator, $message);
	}

	protected function normalize(mixed $value): ?DateTime
	{
		if (!is_string($value)) {
			return null;
		}

		$dateTime = DateTime::createFromFormat("!{$this->format}", $value);
		if ($dateTime === false) {
			return null;
		}

		$lastErrors = DateTime::getLastErrors();
		if ($lastErrors !== false) {
			return null;
		}

		return $dateTime;
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be a DateTime');
	}
}
