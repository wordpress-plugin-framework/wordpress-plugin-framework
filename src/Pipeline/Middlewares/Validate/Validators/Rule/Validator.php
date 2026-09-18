<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected KeyValueInterface $keyValue,
		protected array $rules = [],
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
				foreach ($this->rules as $rule) {
					if ($rule->break($value, fn($message) => $closure($key, $message))) {
						break;
					}
				}
			}
		}
	}
}
