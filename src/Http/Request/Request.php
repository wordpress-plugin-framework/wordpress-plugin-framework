<?php

namespace WordPressPluginFramework\Http\Request;

use WordPressPluginFramework\{
	Http\Message\Body\BodyInterface,
	Http\Method\Method,
	Http\Message\Headers\HeadersInterface,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};
use Closure;

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

	public function withUrl(UrlInterface|Closure $url): static
	{
		if ($url instanceof Closure) {
			$url = $url($this->url);
		}

		if (!$url instanceof UrlInterface) {
			throw new RequestException('must provide url interface');
		}

		return new static($this->uuid, $this->method, $url, $this->headers, $this->body);
	}

	public function headers(): HeadersInterface
	{
		return $this->headers;
	}

	public function withHeaders(HeadersInterface|Closure $headers): static
	{
		if ($headers instanceof Closure) {
			$headers = $headers($this->headers);
		}

		if (!$headers instanceof HeadersInterface) {
			throw new RequestException('must provide header interface');
		}

		return new static($this->uuid, $this->method, $this->url, $headers, $this->body);
	}

	public function body(): ?BodyInterface
	{
		return $this->body;
	}

	public function withBody(BodyInterface|Closure $body): static
	{
		if ($body instanceof Closure) {
			$body = $body($this->body);
		}

		if (!$body instanceof BodyInterface) {
			throw new RequestException('must provide body interface');
		}

		return new static($this->uuid, $this->method, $this->url, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->uuid, $this->method, $this->url, $this->headers, null);
	}
}
