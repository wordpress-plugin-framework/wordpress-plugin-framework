<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Port;

interface PortFactoryInterface
{
	public function create(string $port): PortInterface;
}
