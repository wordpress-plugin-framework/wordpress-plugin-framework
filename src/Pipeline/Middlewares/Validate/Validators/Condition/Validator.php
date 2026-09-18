<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Condition;

use Closure;
use WordPressPluginFramework\{
	Collections\Message\Collection as MessageCollection,
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected array $expressionValidators,
		protected array $ifStatementValidators = [],
		protected array $elseStatementValidators = [],
	) {
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$messages = new MessageCollection();

		foreach ($this->expressionValidators as $expressionValidator) {
			$expressionValidator->validate(
				$request,
				$messages->add(...),
			);
		}

		if ($messages->isEmpty()) {
			foreach ($this->ifStatementValidators as $statementValidator) {
				$statementValidator->validate($request, $closure);
			}
		} else {
			foreach ($this->elseStatementValidators as $statementValidator) {
				$statementValidator->validate($request, $closure);
			}
		}
	}
}
