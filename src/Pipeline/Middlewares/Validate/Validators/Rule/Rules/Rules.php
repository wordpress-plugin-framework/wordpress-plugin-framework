<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules;

use Closure;

readonly class Rules implements RuleInterface
{
	public function __construct(
		protected array $rules = [],
	) {
	}

	public function with(RuleInterface $rule): static
	{
		$rules = $this->rules;
		$rules[] = $rule;

		return new static($rules);
	}

	public function break(mixed $value, Closure $closure): bool
	{
		foreach ($this->rules as $rule) {
			if ($rule->break($value, $closure)) {
				return true;
			}
		}

		return false;
	}
}
