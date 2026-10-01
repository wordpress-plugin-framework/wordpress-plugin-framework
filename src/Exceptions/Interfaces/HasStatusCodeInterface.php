<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Exceptions\Interfaces;

interface HasStatusCodeInterface
{
	public function getStatusCode(): int;
}