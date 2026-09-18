<?php

namespace WordPressPluginFramework\Http\Response;

use WordPressPluginFramework\{
	Http\Message\MessageInterface,
	Uuid\UuidInterface,
};

interface ResponseInterface extends MessageInterface
{
	public function uuid(): UuidInterface;

	public function statusCode(): int;
	public function withStatusCode(int $statusCode): static;
}
