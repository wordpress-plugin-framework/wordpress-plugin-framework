<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Collection\Item\Key;

interface KeyInterface
{
	public function __invoke(): int|string;
}