<?php

namespace WordPressPluginFramework\Database;

use WordPressPluginFramework\Database\Query\QueryInterface;
use wpdb;

readonly class Database implements DatabaseInterface
{
	public function __construct(
		protected Select\SelectInterface $select,
		protected wpdb $wpdb,
	) {
	}

	public function select(QueryInterface $query): array
	{
		return ($this->select)($query);
	}

	public function startTransaction(): void
	{
		$this->query('START TRANSACTION;');
	}

	public function commit(): void
	{
		$this->query('COMMIT;');
	}

	public function rollback(): void
	{
		$this->query('ROLLBACK;');
	}

	protected function query(string $query): void
	{
		$this->wpdb->query($query);

		if ($this->wpdb->last_error) {
			throw new DatabaseException($this->wpdb->last_error);
		}
	}
}