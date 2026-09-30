<?php

namespace WordPressPluginFramework\Http\Url\Port;

interface PortFactoryInterface
{
	public function create(string $port): PortInterface;
}
