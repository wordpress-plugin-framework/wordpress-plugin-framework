<?php

namespace WordPressPluginFramework\Loggers;

interface LoggerInterface
{
	public function info(string $message): void;
}