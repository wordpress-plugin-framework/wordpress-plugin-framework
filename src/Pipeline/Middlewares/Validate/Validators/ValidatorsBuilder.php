<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators;

use Closure;
use WordPressPluginFramework\{
    Pipeline\Middlewares\Validate\Validators\Condition\Validator as ConditionValidator,
    Pipeline\Middlewares\Validate\Validators\Rule\Validator as RuleValidator,
    Pipeline\Middlewares\Validate\Validators\Rule\Rules\RulesBuilderInterface,
    Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
    Pipeline\Middlewares\Validate\KeyValue\Body\KeyValue as Body,
    Pipeline\Middlewares\Validate\KeyValue\Query\KeyValue as Query,
    Pipeline\Middlewares\Validate\KeyValue\Header\KeyValue as Header,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\DateTime\ComparatorFactoryInterface as DateTimeComparatorFactoryInterface,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\Float\Comparator as FloatComparator,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\Int\Comparator as IntComparator,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\String\Comparator as StringComparator,
    Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface,
    Pipeline\Middlewares\Validate\Validators\Comparison\ValidatorBuilderInterface as ComparisonValidatorBuilderInterface,
};

readonly class ValidatorsBuilder implements ValidatorsBuilderInterface
{
    public function __construct(
        protected RulesBuilderInterface $rulesBuilder,
        protected DateTimeComparatorFactoryInterface $dateTimeComparatorFactory,
        protected ComparisonValidatorBuilderInterface $comparisonValidatorBuilder,
        protected Validators $validators = new Validators(),
    ) {
    }

    public function withValidators(ValidatorInterface ...$validators): static
    {
        return new static($this->rulesBuilder, $this->dateTimeComparatorFactory, $this->comparisonValidatorBuilder, new Validators($validators));
    }

    public function withoutValidators(): static
    {
        return new static($this->rulesBuilder, $this->dateTimeComparatorFactory, $this->comparisonValidatorBuilder, new Validators());
    }

    public function withValidator(ValidatorInterface $validator): static
    {
        return new static($this->rulesBuilder, $this->dateTimeComparatorFactory, $this->comparisonValidatorBuilder, $this->validators->with($validator));
    }

    public function body(string $key, Closure $closure): static
    {
        return $this->withRuleValidator(
            new Body($key),
            $closure,
        );
    }

    public function query(string $key, Closure $closure): static
    {
        return $this->withRuleValidator(
            new Query($key),
            $closure,
        );
    }

    public function header(string $name, Closure $closure): static
    {
        return $this->withRuleValidator(
            new Header($name),
            $closure,
        );
    }

    protected function withRuleValidator(KeyValueInterface $keyValue, Closure $closure): static
    {
        $rulesBuilder = $closure($this->rulesBuilder);
        if (!$rulesBuilder instanceof RulesBuilderInterface) {
            throw new ValidatorsBuilderException('closure must return rules builder instance');
        }

        return $this->withValidator(
            new RuleValidator($keyValue, $rulesBuilder->build()),
        );
    }

    public function condition(Closure $expressionValidatorsClosure, ?Closure $ifStatementValidatorsClosure = null, ?Closure $elseStatementValidatorsClosure = null): static
    {
        return $this->withValidator(
            new ConditionValidator(
                $this->buildValidators($expressionValidatorsClosure),
                $this->tryBuildValidators($ifStatementValidatorsClosure),
                $this->tryBuildValidators($elseStatementValidatorsClosure),
            ),
        );
    }

    public function compareDateTimes(Closure $closure): static
    {
        return $this->buildComparisonValidator(
            $closure,
            $this->dateTimeComparatorFactory->create(),
        );
    }

    public function compareFloats(Closure $closure): static
    {
        return $this->buildComparisonValidator(
            $closure,
            new FloatComparator(),
        );
    }

    public function compareInts(Closure $closure): static
    {
        return $this->buildComparisonValidator(
            $closure,
            new IntComparator(),
        );
    }

    public function compareStrings(Closure $closure): static
    {
        return $this->buildComparisonValidator(
            $closure,
            new StringComparator(),
        );
    }

    public function build(): ValidatorInterface
    {
        return $this->validators;
    }

    protected function buildValidators(Closure $validatorsBuilderClosure): ValidatorInterface
    {
        $validatorsBuilder = $validatorsBuilderClosure(
            $this->withoutValidators(),
        );
        if (!$validatorsBuilder instanceof ValidatorsBuilderInterface) {
            throw new ValidatorsBuilderException('not an instance of validators builder');
        }

        return $validatorsBuilder->build();
    }

    protected function tryBuildValidators(?Closure $validatorsBuilderClosure): ValidatorInterface
    {
        if ($validatorsBuilderClosure === null) {
            return new Validators();
        }

        return $this->buildValidators($validatorsBuilderClosure);
    }


    protected function buildComparisonValidator(Closure $comparisonValidatorBuilderClosure, ComparatorInterface $comparator): static
    {
        $comparisonValidatorBuilder = $comparisonValidatorBuilderClosure(
            $this->comparisonValidatorBuilder,
        );
        if (!$comparisonValidatorBuilder instanceof ComparisonValidatorBuilderInterface) {
            throw new ValidatorsBuilderException('not an instance of comparison builder');
        }

        return $this->withValidator(
            $comparisonValidatorBuilder
                ->withComparator($comparator)
                ->build()
        );
    }
}
