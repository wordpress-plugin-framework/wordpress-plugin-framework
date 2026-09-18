<?php

namespace WordPressPluginFramework\Database\Table;

interface TableInterface
{
	public function __invoke(string $table): string;
}
