<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message;

use WordPressPluginFramework\{
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersInterface,
};

interface MessageInterface
{
	public function headers(): HeadersInterface;
	public function withHeaders(HeadersInterface $headers): static;

	public function body(): ?BodyInterface;
	public function withBody(BodyInterface $body): static;
	public function withoutBody(): static;
}
