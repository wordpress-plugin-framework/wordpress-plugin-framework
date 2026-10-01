<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected KeyValueInterface $keyValue,
		protected RuleInterface $rule,
	) {
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$values = $this->keyValue->values($request);
		if ($values === null) {
			$key = $this->keyValue->key();

			$closure($key, 'no content to validate');
		} else {
			foreach ($values as $key => $value) {
				$this->rule->break($value, fn($message) => $closure((string) $key, $message));
			}
		}
	}
}
