<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Json;

interface JsonInterface
{
	public function decode(string $string): mixed;
	public function encode(mixed $mixed): string;
}