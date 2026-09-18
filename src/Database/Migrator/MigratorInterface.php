<?php

namespace WordPressPluginFramework\Database\Migrator;

interface MigratorInterface
{
	public function up(): void;
	public function down(): void;
}
