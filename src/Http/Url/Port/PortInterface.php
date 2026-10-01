<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Port;

use Stringable;

interface PortInterface extends Stringable
{
	public function __invoke(): int;
}
