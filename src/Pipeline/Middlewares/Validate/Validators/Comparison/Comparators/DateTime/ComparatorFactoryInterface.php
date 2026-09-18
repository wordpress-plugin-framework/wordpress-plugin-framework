<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\DateTime;

use WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface;

interface ComparatorFactoryInterface
{
	public function create(): ComparatorInterface;
}
