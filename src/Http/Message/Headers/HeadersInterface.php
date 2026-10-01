<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Headers;

use Countable;
use WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\Vary\VaryInterface,
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

	public function vary(): ?VaryInterface;
	public function withVary(VaryInterface $vary): static;
	public function withoutVary(): static;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;
}
