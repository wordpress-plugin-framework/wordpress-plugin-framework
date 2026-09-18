<?php

namespace WordPressPluginFramework\Exceptions\Interfaces;

interface HasStatusCodeInterface
{
	public function getStatusCode(): int;
}