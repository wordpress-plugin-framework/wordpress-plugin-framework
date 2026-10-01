<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url;

interface UrlFactoryInterface
{
	public function create(string $url): UrlInterface;
}
