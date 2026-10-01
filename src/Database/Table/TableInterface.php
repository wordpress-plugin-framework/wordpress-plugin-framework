<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Database\Table;

interface TableInterface
{
	public function __invoke(string $table): string;
}
