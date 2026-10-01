<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Host;

interface HostFactoryInterface
{
	public function create(string $host): HostInterface;
}
