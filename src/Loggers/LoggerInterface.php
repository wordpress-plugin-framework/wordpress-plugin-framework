<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Loggers;

interface LoggerInterface
{
	public function info(string $message): void;
}