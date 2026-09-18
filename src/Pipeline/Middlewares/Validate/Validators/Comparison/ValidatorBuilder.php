<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison;

use WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\KeyValue\Body\KeyValue as Body,
	Pipeline\Middlewares\Validate\KeyValue\Query\KeyValue as Query,
	Pipeline\Middlewares\Validate\KeyValue\Header\KeyValue as Header,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface,
	Pipeline\Middlewares\Validate\Validators\Comparison\Operator\Operator,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};


readonly class ValidatorBuilder implements ValidatorBuilderInterface
{
	public function __construct(
		protected ?ComparatorInterface $comparator = null,
		protected ?KeyValueInterface $a = null,
		protected ?Operator $operator = null,
		protected ?KeyValueInterface $b = null,
	) {
	}

	public function comparator(): ?ComparatorInterface
	{
		return $this->comparator;
	}

	public function withComparator(ComparatorInterface $comparator): static
	{
		return new static($comparator, $this->a, $this->operator, $this->b);
	}

	public function withoutComparator(): static
	{
		return new static(null, $this->a, $this->operator, $this->b);
	}

	public function a(): ?KeyValueInterface
	{
		return $this->a;
	}

	public function withA(KeyValueInterface $a): static
	{
		return new static($this->comparator, $a, $this->operator, $this->b);
	}

	public function withoutA(): static
	{
		return new static($this->comparator, null, $this->operator, $this->b);
	}

	public function operator(): ?Operator
	{
		return $this->operator;
	}

	public function withOperator(Operator $operator): static
	{
		return new static($this->comparator, $this->a, $operator, $this->b);
	}

	public function withoutOperator(): static
	{
		return new static($this->comparator, $this->a, null, $this->b);
	}

	public function b(): ?KeyValueInterface
	{
		return $this->b;
	}

	public function withB(KeyValueInterface $b): static
	{
		return new static($this->comparator, $this->a, $this->operator, $b);
	}

	public function withoutB(): static
	{
		return new static($this->comparator, $this->a, $this->operator, null);
	}

	public function body(string $key): static
	{
		return $this->withA(
			new Body($key),
		);
	}

	public function query(string $key): static
	{
		return $this->withA(
			new Query($key),
		);
	}

	public function header(string $name): static
	{
		return $this->withA(
			new Header($name),
		);
	}

	public function equal(): static
	{
		return $this->withOperator(
			Operator::Equal,
		);
	}

	public function notEqual(): static
	{
		return $this->withOperator(
			Operator::NotEqual,
		);
	}

	public function lessThan(): static
	{
		return $this->withOperator(
			Operator::LessThan,
		);
	}

	public function greaterThan(): static
	{
		return $this->withOperator(
			Operator::GreaterThan,
		);
	}

	public function lessThanOrEqual(): static
	{
		return $this->withOperator(
			Operator::LessThanOrEqual,
		);
	}

	public function greaterThanOrEqual(): static
	{
		return $this->withOperator(
			Operator::GreaterThanOrEqual,
		);
	}

	public function toBody(string $key): static
	{
		return $this->withB(
			new Body($key),
		);
	}

	public function toQuery(string $key): static
	{
		return $this->withB(
			new Query($key),
		);
	}

	public function toHeader(string $name): static
	{
		return $this->withB(
			new Header($name),
		);
	}

	public function build(): ValidatorInterface
	{
		if (
			$this->comparator === null ||
			$this->a === null ||
			$this->operator === null ||
			$this->b === null
		) {
			throw new ValidatorBuilderException('comparison is incomplete');
		}

		return new Validator($this->comparator, $this->a, $this->operator, $this->b);
	}
}
