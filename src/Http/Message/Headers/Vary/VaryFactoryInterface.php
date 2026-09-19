<?php

namespace WordPressPluginFramework\Http\Message\Headers\Vary;

interface VaryFactoryInterface
{
	public function create(string $vary): VaryInterface;
}
