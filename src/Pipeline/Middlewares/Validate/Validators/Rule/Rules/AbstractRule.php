<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules;

use Closure;
use WordPressPluginFramework\{
	Localization\Translator\TranslatorInterface,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface,
};

abstract readonly class AbstractRule implements RuleInterface
{
	protected const BREAK = null;

	public function __construct(
		protected TranslatorInterface $translator,
		protected ?string $message = null,
	) {
	}

	public function break(mixed $value, Closure $closure): bool
	{
		$break = $this->normalize($value) === null;
		if ($break) {
			$closure(
				$this->message ?? $this->message()
			);
		}

		return static::BREAK ?? $break;
	}

	abstract protected function normalize(mixed $value): mixed;
	abstract protected function message(): string;
}
