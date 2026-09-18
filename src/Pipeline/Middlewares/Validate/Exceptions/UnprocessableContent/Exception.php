<?php

namespace WordPressPluginFramework\Pipeline\Middlewares\Validate\Exceptions\UnprocessableContent;

use WordPressPluginFramework\{
	Exceptions\Interfaces\HasMessagesInterface,
	Http\Exceptions\UnprocessableContent\Exception as UnprocessableContentException,
};

class Exception extends UnprocessableContentException implements HasMessagesInterface
{
	public function __construct(
		string $message,
		string $code,
		protected array $messages,
	) {
		parent::__construct(
			$message,
			$code,
		);
	}

	public function getMessages(): array
	{
		return $this->messages;
	}
}