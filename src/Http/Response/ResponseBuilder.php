<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\HeadersInterface,
	Uuid\UuidInterface,
};

readonly class ResponseBuilder implements ResponseBuilderInterface
{
	protected HeadersInterface $headers;

	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?int $statusCode = null,
		?HeadersInterface $headers = null,
		protected ?BodyInterface $body = null,
	) {
		$this->headers = $headers ?? $this->headersFactory->create();
	}

	public function statusCode(int $statusCode): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $statusCode, $this->headers, $this->body);
	}

	public function headers(HeadersInterface|array $headers): static
	{
		$headers = $this->createHeaders($headers);
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->body);
	}

	public function body(mixed $body, MediaTypeInterface|string $contentType): static
	{
		$body = $this->createBody($body, $contentType);
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $body);
	}

	public function build(): ResponseInterface
	{
		if ($this->statusCode === null) {
			throw new ResponseBuilderException('status code is mandatory');
		}

		return new Response($this->uuid, $this->statusCode, $this->headers, $this->body);
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
