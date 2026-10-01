<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Database\Migrator;

interface MigratorInterface
{
	public function up(): void;
	public function down(): void;
}
