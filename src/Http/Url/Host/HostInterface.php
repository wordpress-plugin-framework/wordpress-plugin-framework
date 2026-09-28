<?php

namespace WordPressPluginFramework\Http\Url\Host;

use Stringable;

interface HostInterface extends Stringable
{
	public function __invoke(): string;
}
