<?php

declare(strict_types=1);

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
		protected ValidatorInterface $expressionValidators,
		protected ValidatorInterface $ifStatementValidators,
		protected ValidatorInterface $elseStatementValidators,
	) {
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$messages = new MessageCollection();

		$this->expressionValidators->validate(
			$request,
			$messages->add(...),
		);

		if ($messages->isEmpty()) {
			$this->ifStatementValidators->validate($request, $closure);
		} else {
			$this->elseStatementValidators->validate($request, $closure);
		}
	}
}
