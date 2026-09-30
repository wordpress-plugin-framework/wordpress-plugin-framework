<?php

namespace WordPressPluginFramework\Preg;

interface PregInterface
{
	public function match(string $pattern, string $subject, int $flags = 0): ?array;
	public function matchAll(string $pattern, string $subject, int $flags = 0): array;
	public function replaceCallback(string $pattern, callable $callback, string $subject): string;
}
