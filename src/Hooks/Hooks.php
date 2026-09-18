<?php

namespace WordPressPluginFramework\Hooks;

readonly class Hooks implements HooksInterface
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
