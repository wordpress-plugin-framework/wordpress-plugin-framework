<?php

namespace WordPressPluginFramework\Http\Url\Query;

interface QueryFactoryInterface
{
	public function create(mixed $query): QueryInterface;
	public function createFromEncoded(string $query): QueryInterface;
}
