<?php

namespace WordPressPluginFramework\Routes;

interface RoutesInterface
{
	public function __invoke(): void;

	public function up(): void;
	public function down(): void;
}
