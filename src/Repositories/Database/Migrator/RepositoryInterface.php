<?php

namespace WordPressPluginFramework\Repositories\Database\Migrator;

interface RepositoryInterface
{
	public function has(string $name): bool;
	public function add(string $name): void;
	public function remove(string $name): void;
}
