<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Hooks\Activation;

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
		register_activation_hook($this->file, $this->closure);
	}
}
