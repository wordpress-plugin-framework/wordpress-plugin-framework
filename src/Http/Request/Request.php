<?php

namespace WordPressPluginFramework\Http\Request;

use WordPressPluginFramework\{
	Http\Message\Body\BodyInterface,
	Http\Method\Method,
	Http\Message\Headers\HeadersInterface,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};

readonly class Request implements RequestInterface
{
	protected HeadersInterface $headers;

	public function __construct(
		protected UuidInterface $uuid,
		protected Method $method,
		protected UrlInterface $url,
		HeadersInterface $headers,
		protected ?BodyInterface $body = null,
	) {
		$this->headers = $body === null ? $headers : $headers->withContentType($body->contentType());
	}

	public function uuid(): UuidInterface
	{
		return $this->uuid;
	}

	public function method(): Method
	{
		return $this->method;
	}

	public function withMethod(Method $method): static
	{
		return new static($this->uuid, $method, $this->url, $this->headers, $this->body);
	}

	public function url(): UrlInterface
	{
		return $this->url;
	}

	public function withUrl(UrlInterface $url): static
	{
		return new static($this->uuid, $this->method, $url, $this->headers, $this->body);
	}

	public function headers(): HeadersInterface
	{
		return $this->headers;
	}

	public function withHeaders(HeadersInterface $headers): static
	{
		return new static($this->uuid, $this->method, $this->url, $headers, $this->body);
	}

	public function body(): ?BodyInterface
	{
		return $this->body;
	}

	public function withBody(BodyInterface $body): static
	{
		return new static($this->uuid, $this->method, $this->url, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->uuid, $this->method, $this->url, $this->headers, null);
	}
}
