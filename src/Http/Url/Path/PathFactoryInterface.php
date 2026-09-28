<?php

namespace WordPressPluginFramework\Http\Url\Path;

interface PathFactoryInterface
{
	public function create(string $path): PathInterface;
}
