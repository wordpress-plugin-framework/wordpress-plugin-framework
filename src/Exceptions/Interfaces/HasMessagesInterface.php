<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Exceptions\Interfaces;

interface HasMessagesInterface
{
	public function getMessages(): array;
}