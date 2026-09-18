<?php

namespace WordPressPluginFramework\Hooks;

interface HookInterface
{
	public function __invoke(): void;
}
