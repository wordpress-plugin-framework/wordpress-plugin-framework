<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Fragment;

interface FragmentFactoryInterface
{
	public function create(string $fragment): FragmentInterface;
}
