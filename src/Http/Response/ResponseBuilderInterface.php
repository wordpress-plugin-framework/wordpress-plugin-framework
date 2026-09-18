<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersInterface,
};

interface ResponseBuilderInterface
{
	public function statusCode(int $statusCode): static;
	public function headers(HeadersInterface|array $headers): static;
	public function body(mixed $body, MediaTypeInterface|string $contentType): static;

	public function build(): ResponseInterface;
}
