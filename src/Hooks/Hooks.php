<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Hooks;

readonly class Hooks implements HookInterface
{
	public function __construct(
		protected array $hooks,
	) {
	}

	public function __invoke(): void
	{
		foreach ($this->hooks as $hook) {
			$hook();
		}
	}
}
