<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules;

use Closure;

interface RulesBuilderInterface
{
	public function rules(): array;
	public function withRules(RuleInterface ...$rules): static;
	public function withoutRules(): static;

	public function withRule(RuleInterface $rule): static;

	public function array(): static;
	public function bool(): static;
	public function domain(): static;
	public function email(): static;
	public function enum(string $class): static;
	public function float(): static;
	public function int(): static;
	public function ip(): static;
	public function mac(): static;
	public function nullable(): static;
	public function regexp(string $regexp): static;
	public function string(): static;
	public function url(): static;

	public function build(): array;
}