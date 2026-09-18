<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersInterface,
	Http\Method\Method,
	Http\Url\UrlInterface,
};

interface RequestBuilderInterface
{
	public function method(Method|string $method): static;
	public function url(UrlInterface|string $url): static;
	public function headers(HeadersInterface|array $headers): static;
	public function body(mixed $body, MediaTypeInterface|string $contentType): static;

	public function build(): RequestInterface;
}
