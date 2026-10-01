<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Collection\Item;

interface ItemInterface
{
	public function key(): Key\KeyInterface;
}