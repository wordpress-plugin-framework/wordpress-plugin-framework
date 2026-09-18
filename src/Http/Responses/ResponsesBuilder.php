<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Hoo\WordPressPluginFramework\{
	Http\Message\Bodies\BodiesFactoryInterface,
	Http\Message\Bodies\BodiesInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\HeadersInterface,
	Http\Response\Response,
	Uuid\UuidInterface,
};

readonly class ResponsesBuilder implements ResponsesBuilderInterface
{
	protected HeadersInterface $headers;

	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodiesFactoryInterface $bodiesFactory,
		protected UuidInterface $uuid,
		protected ?int $statusCode = null,
		?HeadersInterface $headers = null,
		protected ?BodiesInterface $bodies = null,
	) {
		$this->headers = $headers ?? $this->headersFactory->create();
	}

	public function statusCode(int $statusCode): static
	{
		return new static($this->headersFactory, $this->bodiesFactory, $this->uuid, $statusCode, $this->headers, $this->bodies);
	}

	public function headers(HeadersInterface|array $headers): static
	{
		$headers = $this->createHeaders($headers);
		return new static($this->headersFactory, $this->bodiesFactory, $this->uuid, $this->statusCode, $headers, $this->bodies);
	}

	public function body(mixed $body, MediaTypeInterface|string ...$contentTypes): static
	{
		$bodies = $this->createBodies($body, ...$contentTypes);
		return new static($this->headersFactory, $this->bodiesFactory, $this->uuid, $this->statusCode, $this->headers, $bodies);
	}

	public function build(): ResponsesInterface
	{
		if ($this->statusCode === null) {
			throw new ResponsesBuilderException('status code is mandatory');
		}

		if ($this->bodies === null) {
			throw new ResponsesBuilderException('body is mandatory');
		}

		$responses = [];

		foreach ($this->bodies as $body) {
			$responses[] = new Response($this->uuid, $this->statusCode, $this->headers, $body);
		}

		return new Responses($responses);
	}

	protected function createHeaders(HeadersInterface|array $headers): HeadersInterface
	{
		if ($headers instanceof HeadersInterface) {
			return $headers;
		}

		return $this->headersFactory->create($headers);
	}

	protected function createBodies(mixed $body, MediaTypeInterface|string ...$contentTypes): BodiesInterface
	{
		return $this->bodiesFactory->create($body, ...$contentTypes);
	}
}
