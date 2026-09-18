<?php

namespace WordPressPluginFramework\Http\Exceptions;

use WordPressPluginFramework\Exceptions\Interfaces\HasStatusCodeInterface;

class Exception extends \Exception implements HasStatusCodeInterface
{
	public function __construct(
		string $message,
		string $code,
		protected int $statusCode,
	) {
		$this->message = $message;
		$this->code = $code;
	}

	public function getStatusCode(): int
	{
		return $this->statusCode;
	}
}