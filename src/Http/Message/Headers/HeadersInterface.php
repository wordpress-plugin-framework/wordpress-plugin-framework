<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

use Countable;
use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use IteratorAggregate;

interface HeadersInterface extends IteratorAggregate, Countable
{
	public function __invoke(): array;

	public function has(string $name): bool;
	public function get(string $name): ?string;

	public function with(string $name, string $value): static;
	public function without(string $name): static;

	public function accept(): ?AcceptInterface;
	public function withAccept(AcceptInterface $accept): static;
	public function withoutAccept(): static;

	public function contentType(): ?MediaTypeInterface;
	public function withContentType(MediaTypeInterface $contentType): static;
	public function withoutContentType(): static;
}
