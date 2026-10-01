<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Value;

readonly class Value
{
	public function __construct(
		public mixed $value,
	) {
	}
}
