<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Headers;

interface HeadersFactoryInterface
{
	public function create(array $headers = []): HeadersInterface;
}
