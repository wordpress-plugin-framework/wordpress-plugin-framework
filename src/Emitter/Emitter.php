<?php

namespace WordPressPluginFramework\Emitter;

use WordPressPluginFramework\Http\Response\ResponseInterface;

readonly class Emitter implements EmitterInterface
{
	public function emit(ResponseInterface $response): void
	{
		http_response_code($response->statusCode());

		foreach ($response->headers() as $name => $value) {
			header("{$name}: {$value}");
		}

		echo $response->body();
	}
}
