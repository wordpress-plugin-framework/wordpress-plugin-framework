<?php

namespace WordPressPluginFramework\Database\Select;

use WordPressPluginFramework\Database\DatabaseException;
use WordPressPluginFramework\Database\Query\QueryInterface;
use wpdb;

readonly class Select implements SelectInterface
{
	public function __construct(
		protected wpdb $wpdb,
	) {
		/*
		$this->query('SET SESSION group_concat_max_len = 1048576;');
		*/
	}

	public function __invoke(QueryInterface $query): array
	{
		$this->query($query);

		return array_map(get_object_vars(...), $this->wpdb->last_result);
	}

	protected function query(string $query): void
	{
		$this->wpdb->query($query);

		if ($this->wpdb->last_error) {
			throw new DatabaseException($this->wpdb->last_error);
		}
	}
}