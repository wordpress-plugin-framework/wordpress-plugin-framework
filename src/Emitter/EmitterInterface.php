<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Emitter;

use WordPressPluginFramework\Http\Response\ResponseInterface;

interface EmitterInterface
{
	public function emit(ResponseInterface $response): void;
}
