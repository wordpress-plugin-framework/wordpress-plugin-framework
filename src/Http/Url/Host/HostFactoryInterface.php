<?php

namespace WordPressPluginFramework\Http\Url\Host;

interface HostFactoryInterface
{
	public function create(string $host): HostInterface;
}
