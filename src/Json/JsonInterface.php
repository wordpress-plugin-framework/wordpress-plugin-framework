<?php

namespace WordPressPluginFramework\Json;

interface JsonInterface
{
	public function decode(string $string): mixed;
	public function encode(mixed $mixed): string;
}