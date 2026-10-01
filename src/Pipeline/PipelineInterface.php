<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Pipeline;

use Closure;

interface PipelineInterface
{
	public function __invoke(Closure $closure): mixed;
}
