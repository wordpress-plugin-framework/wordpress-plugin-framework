<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison;

use WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface,
	Pipeline\Middlewares\Validate\Validators\Comparison\Operator\Operator,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

interface ValidatorBuilderInterface
{
	public function withComparator(ComparatorInterface $comparator): static;
	public function withA(KeyValueInterface $a): static;
	public function withOperator(Operator $operator): static;
	public function withB(KeyValueInterface $b): static;

	public function body(string $key): static;
	public function query(string $key): static;
	public function header(string $name): static;

	public function equal(): static;
	public function notEqual(): static;
	public function lessThan(): static;
	public function greaterThan(): static;
	public function lessThanOrEqual(): static;
	public function greaterThanOrEqual(): static;

	public function toBody(string $key): static;
	public function toQuery(string $key): static;
	public function toHeader(string $name): static;

	public function build(): ValidatorInterface;
}
