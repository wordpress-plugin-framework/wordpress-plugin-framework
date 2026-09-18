<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Operator;

enum Operator
{
	case Equal;
	case NotEqual;
	case LessThan;
	case GreaterThan;
	case LessThanOrEqual;
	case GreaterThanOrEqual;

	public function result(int $comparison): bool
	{
		return match ($this) {
			self::Equal => $comparison == 0,
			self::NotEqual => $comparison != 0,
			self::LessThan => $comparison < 0,
			self::GreaterThan => $comparison > 0,
			self::LessThanOrEqual => $comparison <= 0,
			self::GreaterThanOrEqual => $comparison >= 0,
		};
	}

	public function message(): string
	{
		return match ($this) {
			self::Equal => 'must be equal to',
			self::NotEqual => 'must be not equal to',
			self::LessThan => 'must be less than',
			self::GreaterThan => 'must be greater than',
			self::LessThanOrEqual => 'must be less than or equal to',
			self::GreaterThanOrEqual => 'must be greater than or equal to',
		};
	}
}
