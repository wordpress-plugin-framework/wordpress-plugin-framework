<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators;

use Closure;
use WordPressPluginFramework\{
    Pipeline\Middlewares\Validate\Validators\Condition\Validator as ConditionValidator,
    Pipeline\Middlewares\Validate\Validators\Rule\Validator as RuleValidator,
    Pipeline\Middlewares\Validate\Validators\Rule\Rules\RulesBuilderInterface,
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
        protected array $validators = [],
    ) {
    }

    public function withValidator(ValidatorInterface $validator): static
    {
        $validators = $this->validators;
        $validators[] = $validator;

        return new static($this->rulesBuilder, $this->dateTimeComparatorFactory, $this->comparisonValidatorBuilder, $validators);
    }

    public function body(string $key, Closure $rulesBuilderClosure): static
    {
        $rules = $this->rulesBuilder($rulesBuilderClosure)->build();

        $validator = new RuleValidator(
            new Body($key),
            $rules,
        );
        return $this->withValidator($validator);
    }

    public function query(string $key, Closure $rulesBuilderClosure): static
    {
        $rules = $this->rulesBuilder($rulesBuilderClosure)->build();

        $validator = new RuleValidator(
            new Query($key),
            $rules,
        );
        return $this->withValidator($validator);
    }

    public function header(string $name, Closure $rulesBuilderClosure): static
    {
        $rules = $this->rulesBuilder($rulesBuilderClosure)->build();

        $validator = new RuleValidator(
            new Header($name),
            $rules,
        );
        return $this->withValidator($validator);
    }

    public function condition(Closure $expressionValidatorsBuilderClosure, ?Closure $ifStatementValidatorsBuilderClosure = null, ?Closure $elseStatementValidatorsBuilderClosure = null): static
    {
        $expressionValidators = $this->validatorsBuilder($expressionValidatorsBuilderClosure)->build();
        $ifStatementValidators = $this->validatorsBuilder($ifStatementValidatorsBuilderClosure)->build();
        $elseStatementValidators = $this->validatorsBuilder($elseStatementValidatorsBuilderClosure)->build();

        $conditionValidator = new ConditionValidator($expressionValidators, $ifStatementValidators, $elseStatementValidators);
        return $this->withValidator($conditionValidator);
    }

    public function compareDateTimes(Closure $comparisonValidatorBuilderClosure): static
    {
        $comparator = $this->dateTimeComparatorFactory->create();

        $comparisonValidator = $this->comparisonValidatorBuilder($comparator, $comparisonValidatorBuilderClosure)->build();
        return $this->withValidator($comparisonValidator);
    }

    public function compareFloats(Closure $comparisonValidatorBuilderClosure): static
    {
        $comparator = new FloatComparator();

        $comparisonValidator = $this->comparisonValidatorBuilder($comparator, $comparisonValidatorBuilderClosure)->build();
        return $this->withValidator($comparisonValidator);
    }

    public function compareInts(Closure $comparisonValidatorBuilderClosure): static
    {
        $comparator = new IntComparator();

        $comparisonValidator = $this->comparisonValidatorBuilder($comparator, $comparisonValidatorBuilderClosure)->build();
        return $this->withValidator($comparisonValidator);
    }

    public function compareStrings(Closure $comparisonValidatorBuilderClosure): static
    {
        $comparator = new StringComparator();

        $comparisonValidator = $this->comparisonValidatorBuilder($comparator, $comparisonValidatorBuilderClosure)->build();
        return $this->withValidator($comparisonValidator);
    }

    public function build(): ValidatorInterface
    {
        return new Validators($this->validators);
    }

    protected function rulesBuilder(Closure $rulesBuilderClosure): RulesBuilderInterface
    {
        return $rulesBuilderClosure($this->rulesBuilder);
    }

    protected function validatorsBuilder(?Closure $validatorsBuilderClosure): ValidatorsBuilderInterface
    {
        $validatorsBuilder = new static($this->rulesBuilder, $this->dateTimeComparatorFactory, $this->comparisonValidatorBuilder, []);
        return $validatorsBuilderClosure === null ? null : $validatorsBuilderClosure($validatorsBuilder);
    }

    protected function comparisonValidatorBuilder(ComparatorInterface $comparator, Closure $comparisonValidatorBuilderClosure): ComparisonValidatorBuilderInterface
    {
        $comparisonValidatorBuilder = $this->comparisonValidatorBuilder->withComparator($comparator);
        return $comparisonValidatorBuilderClosure($comparisonValidatorBuilder);
    }
}
