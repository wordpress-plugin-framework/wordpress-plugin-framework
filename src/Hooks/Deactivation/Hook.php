<?php

namespace WordPressPluginFramework\Hooks\Deactivation;

use Closure;
use WordPressPluginFramework\Hooks\HookInterface;

readonly class Hook implements HookInterface
{
	public function __construct(
		protected string $file,
		protected Closure $closure,
	) {
	}

	public function __invoke(): void
	{
		register_deactivation_hook($this->file, $this->closure);
	}
}
