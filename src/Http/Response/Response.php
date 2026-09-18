<?php

namespace WordPressPluginFramework\Http\Response;

use WordPressPluginFramework\{
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersInterface,
	Uuid\UuidInterface,
};
use Closure;

readonly class Response implements ResponseInterface
{
	protected HeadersInterface $headers;

	public function __construct(
		protected UuidInterface $uuid,
		protected int $statusCode,
		HeadersInterface $headers,
		protected ?BodyInterface $body = null,
	) {
		$this->validateStatusCode($statusCode);

		$this->headers = $body === null ? $headers : $headers->withContentType($body->mediaType());
	}

	public function uuid(): UuidInterface
	{
		return $this->uuid;
	}

	public function statusCode(): int
	{
		return $this->statusCode;
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->uuid, $statusCode, $this->headers, $this->body);
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
			throw new ResponseException('must provide header interface');
		}

		return new static($this->uuid, $this->statusCode, $headers, $this->body);
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
			throw new ResponseException('must provide body interface');
		}

		return new static($this->uuid, $this->statusCode, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->uuid, $this->statusCode, $this->headers, null);
	}

	protected function validateStatusCode(int $statusCode): void
	{
		if (
			$statusCode < 100 ||
			$statusCode > 599
		) {
			throw new ResponseException("Invalid HTTP status code: {$statusCode}");
		}
	}
}
