<?php

namespace WordPressPluginFramework\Http\Request;

use WordPressPluginFramework\{
	Http\Exceptions\BadRequest\Exception as BadRequestException,
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Url\UrlFactoryInterface,
	Uuid\UuidInterface,
};

readonly class RequestFactory implements RequestFactoryInterface
{
	protected const HEADER = '/^HTTP_/';

	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected array $server,
		protected array $post,
		protected string $input,
	) {
	}

	public function create(): RequestInterface
	{
		$method = Method::create($this->method());
		$url = $this->urlFactory->create($this->url());
		$headers = $this->headersFactory->create($this->headers());

		$contentType = $this->contentType();
		$body = $this->body();

		if ($body !== null && $contentType === null) {
			throw new BadRequestException('content without content-type', 'request_factory_error');
		}

		if ($body !== null) {
			$body = $this->parsed($method, $headers->contentType())
				? $this->bodyFactory->create($this->post, $contentType)
				: $this->bodyFactory->createFromEncoded($body, $contentType);
		}

		return new Request($this->uuid, $method, $url, $headers, $body);
	}

	protected function parsed(Method $method, ?MediaTypeInterface $mediaType): bool
	{
		if ($method !== Method::Post) {
			return false;
		}

		return $mediaType?->type() === 'multipart' && $mediaType?->subtype() === 'form-data';
	}

	protected function method(): string
	{
		return $this->server['REQUEST_METHOD'] ?? 'GET';
	}

	protected function url(): string
	{
		$scheme = $this->scheme();
		$host = $this->server['HTTP_HOST'] ?? 'localhost';
		$pathQuery = $this->server['REQUEST_URI'] ?? '';

		return "{$scheme}://{$host}{$pathQuery}";
	}

	protected function scheme(): string
	{
		return ($this->server['HTTPS'] ?? '') === '' ? 'http' : 'https';
	}

	protected function headers(): array
	{
		$headers = [];

		foreach ($this->server as $name => $value) {
			if (!preg_match(self::HEADER, $name)) {
				continue;
			}

			$name = preg_replace([
				self::HEADER,
				'/_/'
			], [
				'',
				'-'
			], $name);

			$headers[strtolower($name)] = $value;
		}

		$contentLength = $this->contentLength();
		if ($contentLength !== null) {
			$headers['content-length'] = $contentLength;
		}

		$contentType = $this->contentType();
		if ($contentType !== null) {
			$headers['content-type'] = $contentType;
		}

		return $headers;
	}

	protected function body(): ?string
	{
		$contentLength = $this->contentLength();
		if ($contentLength === null) {
			return null;
		}

		return $this->input;
	}

	protected function contentLength(): ?string
	{
		$contentLength = $this->server['CONTENT_LENGTH'] ?? '';

		return $contentLength === '' ? null : $contentLength;
	}

	protected function contentType(): ?string
	{
		$contentType = $this->server['CONTENT_TYPE'] ?? '';

		return $contentType === '' ? null : $contentType;
	}
}
