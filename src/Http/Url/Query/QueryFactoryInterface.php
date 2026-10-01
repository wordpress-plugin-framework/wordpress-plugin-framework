<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Query;

interface QueryFactoryInterface
{
	public function create(mixed $query, bool $squareBrackets = true): QueryInterface;
	public function createFromEncoded(string $query, bool $squareBrackets = true): QueryInterface;
}
