<?php

namespace WordPressPluginFramework\Hooks;

use Closure;

interface HooksFactoryInterface
{
	public function create(Closure $hooksBuilderClosure): HooksInterface;
}
