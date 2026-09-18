<?php

namespace WordPressPluginFramework\Http\Message\Headers;

interface HeadersFactoryInterface
{
	public function create(array $headers = []): HeadersInterface;
}
