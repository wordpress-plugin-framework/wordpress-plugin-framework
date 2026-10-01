<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Database;

use WordPressPluginFramework\Database\Query\QueryInterface;

interface DatabaseInterface
{
	public function select(QueryInterface $query): array;

	public function startTransaction(): void;
	public function commit(): void;
	public function rollback(): void;
}