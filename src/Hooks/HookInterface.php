<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Hooks;

interface HookInterface
{
	public function __invoke(): void;
}
