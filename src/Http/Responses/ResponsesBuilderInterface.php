<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersInterface,
};

interface ResponsesBuilderInterface
{
	public function statusCode(int $statusCode): static;
	public function headers(HeadersInterface|array $headers): static;
	public function body(mixed $body, MediaTypeInterface|string ...$contentTypes): static;

	public function build(): ResponsesInterface;
}
