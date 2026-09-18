<?php

namespace WordPressPluginFramework\Collection\Item\Key;

interface KeyInterface
{
	public function __invoke(): int|string;
}