<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\KeyValue\Header;

use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\KeyValue\KeyValueInterface,
};

readonly class KeyValue implements KeyValueInterface
{
	public function __construct(
		protected string $key,
	) {
	}

	public function key(): string
	{
		return $this->key;
	}

	public function values(RequestInterface $request): ?array
	{
		$headers = $request->headers();
		if ($headers === null) {
			return null;
		}

		return [
			$this->key => $headers->header($this->key),
		];
	}

	public function value(RequestInterface $request): mixed
	{
		return $request->header($this->key);
	}
}
