<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Database\Table;

readonly class Table implements TableInterface
{
	public function __construct(
		protected string $prefix,
	) {
	}

	public function __invoke(string $table): string
	{
		return "{$this->prefix}{$table}";
	}
}
