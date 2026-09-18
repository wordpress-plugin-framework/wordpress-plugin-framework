<?php

namespace WordPressPluginFramework\Http\Exceptions\BadRequest;

use WordPressPluginFramework\Http;

class Exception extends Http\Exceptions\Exception
{
	public function __construct(
		string $message,
		string $code,
	) {
		parent::__construct($message, $code, 400);
	}
}