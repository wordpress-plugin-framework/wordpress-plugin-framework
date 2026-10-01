<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Fragment;

use Stringable;

interface FragmentInterface extends Stringable
{
	public function __invoke(): string;
}
