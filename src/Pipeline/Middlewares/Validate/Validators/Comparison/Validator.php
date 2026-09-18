<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
	Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface,
	Pipeline\Middlewares\Validate\Validators\Comparison\Operator\Operator,
	Pipeline\Middlewares\Validate\Validators\ValidatorInterface,
};

readonly class Validator implements ValidatorInterface
{
	public function __construct(
		protected ComparatorInterface $comparator,
		protected KeyValueInterface $a,
		protected Operator $operator,
		protected KeyValueInterface $b,
	) {
	}

	public function validate(RequestInterface $request, Closure $closure): void
	{
		$comparison = $this->comparator->compare(
			$this->a->value($request),
			$this->b->value($request),
		);

		if ($comparison === null) {
			$closure($this->a->key(), "is not comparable to {$this->b->key()}");
		} else {
			if (!$this->operator->result($comparison)) {
				$closure($this->a->key(), "{$this->operator->message()} {$this->b->key()}");
			}
		}
	}
}
