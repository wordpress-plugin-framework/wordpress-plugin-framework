<?php

namespace WordPressPluginFramework\Http\Request;

interface RequestFactoryInterface
{
	public function create(): RequestInterface;
}
