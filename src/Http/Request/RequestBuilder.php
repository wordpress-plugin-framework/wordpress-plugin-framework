<?php

namespace WordPressPluginFramework\Http\Request;

use WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\HeadersInterface,
	Http\Method\Method,
	Http\Url\UrlFactoryInterface,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};

readonly class RequestBuilder implements RequestBuilderInterface
{
	protected HeadersInterface $headers;

	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?Method $method = null,
		protected ?UrlInterface $url = null,
		?HeadersInterface $headers = null,
		protected ?BodyInterface $body = null,
	) {
		$this->headers = $headers ?? $this->headersFactory->create();
	}

	public function method(Method|string $method): static
	{
		$method = $this->createMethod($method);
		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $method, $this->url, $this->headers, $this->body);
	}

	public function url(UrlInterface|string $url): static
	{
		$url = $this->createUrl($url);
		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $url, $this->headers, $this->body);
	}

	public function headers(HeadersInterface|array $headers): static
	{
		$headers = $this->createHeaders($headers);
		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $headers, $this->body);
	}

	public function body(mixed $body, MediaTypeInterface|string $contentType): static
	{
		$body = $this->createBody($body, $contentType);
		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $this->headers, $body);
	}

	public function build(): RequestInterface
	{
		if ($this->method === null) {
			throw new RequestBuilderException('method is mandatory');
		}

		if ($this->url === null) {
			throw new RequestBuilderException('url is mandatory');
		}

		return new Request($this->uuid, $this->method, $this->url, $this->headers, $this->body);
	}

	protected function createMethod(Method|string $method): Method
	{
		if ($method instanceof Method) {
			return $method;
		}

		return Method::create($method);
	}

	protected function createUrl(UrlInterface|string $url): UrlInterface
	{
		if ($url instanceof UrlInterface) {
			return $url;
		}

		return $this->urlFactory->create($url);
	}

	protected function createHeaders(HeadersInterface|array $headers): HeadersInterface
	{
		if ($headers instanceof HeadersInterface) {
			return $headers;
		}

		return $this->headersFactory->create($headers);
	}

	protected function createBody(mixed $body, MediaTypeInterface|string $contentType): BodyInterface
	{
		return $this->bodyFactory->create($body, $contentType);
	}
}
