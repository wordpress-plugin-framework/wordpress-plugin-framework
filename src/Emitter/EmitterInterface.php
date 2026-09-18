<?php

namespace WordPressPluginFramework\Emitter;

use WordPressPluginFramework\Http\Response\ResponseInterface;

interface EmitterInterface
{
	public function emit(ResponseInterface $response): void;
}
