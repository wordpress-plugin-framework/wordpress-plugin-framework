<?php

namespace WordPressPluginFramework\Http\Url\Port;

use Stringable;

interface PortInterface extends Stringable
{
	public function __invoke(): int;
}
