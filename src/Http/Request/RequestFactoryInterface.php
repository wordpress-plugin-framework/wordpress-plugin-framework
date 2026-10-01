<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Request;

interface RequestFactoryInterface
{
	public function create(): RequestInterface;
}
