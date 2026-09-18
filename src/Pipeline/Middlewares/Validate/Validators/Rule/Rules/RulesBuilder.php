<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules;

use WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Array\Rule as ArrayRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Bool\Rule as BoolRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Domain\Rule as DomainRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Email\Rule as EmailRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Enum\Rule as EnumRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Float\Rule as FloatRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Int\Rule as IntRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Ip\Rule as IpRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Mac\Rule as MacRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Nullable\Rule as NullableRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Regexp\Rule as RegexpRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\String\Rule as StringRule,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\Url\Rule as UrlRule,
};

readonly class RulesBuilder implements RulesBuilderInterface
{
	public function __construct(
		protected array $rules = [],
	) {
	}

	public function rules(): array
	{
		return $this->rules;
	}

	public function withRules(RuleInterface ...$rules): static
	{
		return new static($rules);
	}

	public function withoutRules(): static
	{
		return new static([]);
	}

	public function withRule(RuleInterface $rule): static
	{
		return $this->withRules(
			...[
				...$this->rules,
				$rule,
			],
		);
	}

	public function array(): static
	{
		return $this->withRule(
			new ArrayRule(),
		);
	}

	public function bool(): static
	{
		return $this->withRule(
			new BoolRule(),
		);
	}

	public function domain(): static
	{
		return $this->withRule(
			new DomainRule(),
		);
	}

	public function email(): static
	{
		return $this->withRule(
			new EmailRule(),
		);
	}

	public function enum(string $class): static
	{
		return $this->withRule(
			new EnumRule($class),
		);
	}

	public function float(): static
	{
		return $this->withRule(
			new FloatRule(),
		);
	}

	public function int(): static
	{
		return $this->withRule(
			new IntRule(),
		);
	}

	public function ip(): static
	{
		return $this->withRule(
			new IpRule(),
		);
	}

	public function mac(): static
	{
		return $this->withRule(
			new MacRule(),
		);
	}

	public function nullable(): static
	{
		return $this->withRule(
			new NullableRule(),
		);
	}

	public function regexp(string $regexp): static
	{
		return $this->withRule(
			new RegexpRule($regexp),
		);
	}

	public function string(): static
	{
		return $this->withRule(
			new StringRule(),
		);
	}

	public function url(): static
	{
		return $this->withRule(
			new UrlRule(),
		);
	}

	public function build(): array
	{
		return $this->rules;
	}
}