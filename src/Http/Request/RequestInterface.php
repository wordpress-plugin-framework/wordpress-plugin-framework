<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Request;

use WordPressPluginFramework\{
	Http\Message\MessageInterface,
	Http\Method\Method,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};

interface RequestInterface extends MessageInterface
{
	public function uuid(): UuidInterface;

	public function method(): Method;
	public function withMethod(Method $method): static;

	public function url(): UrlInterface;
	public function withUrl(UrlInterface $url): static;
}
