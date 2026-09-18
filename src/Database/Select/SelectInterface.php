<?php

namespace WordPressPluginFramework\Database\Select;

use WordPressPluginFramework\Database\Query\QueryInterface;

interface SelectInterface
{
	public function __invoke(QueryInterface $query): array;
}