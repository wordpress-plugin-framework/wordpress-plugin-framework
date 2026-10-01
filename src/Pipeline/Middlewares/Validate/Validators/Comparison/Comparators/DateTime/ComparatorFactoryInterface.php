<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\DateTime;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface;

interface ComparatorFactoryInterface
{
	public function create(): ComparatorInterface;
}
