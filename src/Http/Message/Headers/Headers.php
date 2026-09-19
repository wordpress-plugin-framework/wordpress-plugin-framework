<?php

namespace WordPressPluginFramework\Http\Message\Headers;

use ArrayIterator;
use WordPressPluginFramework\{
	Http\Abnf\Rfc9110,
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\Vary\VaryInterface,
};
use Closure;
use Traversable;

readonly class Headers implements HeadersInterface
{
	protected array $headers;

	public function __construct(
		array $headers = [],
		protected ?AcceptInterface $accept = null,
		protected ?MediaTypeInterface $contentType = null,
		protected ?VaryInterface $vary = null,
	) {
		$this->validate($headers);
		$this->headers = $this->normalize($headers);
	}

	public function has(string $name): bool
	{
		return isset($this->headers[strtolower($name)]);
	}

	public function get(string $name): ?string
	{
		return $this->headers[strtolower($name)] ?? null;
	}

	public function with(string $name, string $value): static
	{
		$name = strtolower($name);

		if ($name === 'accept') {
			throw new HeadersException('accept must be set through withAccept');
		}

		if ($name === 'content-type') {
			throw new HeadersException('content-type must be set through withContentType');
		}

		if ($name === 'vary') {
			throw new HeadersException('vary must be set through withVary');
		}

		$headers = $this->headers;
		$headers[$name] = $value;

		return new static($headers, $this->accept, $this->contentType, $this->vary);
	}

	public function without(string $name): static
	{
		$name = strtolower($name);

		if ($name === 'accept') {
			throw new HeadersException('accept must be removed through withoutAccept');
		}

		if ($name === 'content-type') {
			throw new HeadersException('content-type must be removed through withoutContentType');
		}

		if ($name === 'vary') {
			throw new HeadersException('vary must be removed through withoutVary');
		}

		$headers = $this->headers;
		unset($headers[$name]);

		return new static($headers, $this->accept, $this->contentType, $this->vary);
	}

	public function accept(): ?AcceptInterface
	{
		return $this->accept;
	}

	public function withAccept(AcceptInterface|Closure $accept): static
	{
		if ($accept instanceof Closure) {
			$accept = $accept($this->accept);
		}

		if (!$accept instanceof AcceptInterface) {
			throw new HeadersException('must provide accept interface');
		}

		$headers = $this->headers;
		$headers['accept'] = (string) $accept;

		return new static($headers, $accept, $this->contentType, $this->vary);
	}

	public function withoutAccept(): static
	{
		$headers = $this->headers;
		unset($headers['accept']);

		return new static($headers, null, $this->contentType, $this->vary);
	}

	public function contentType(): ?MediaTypeInterface
	{
		return $this->contentType;
	}

	public function withContentType(MediaTypeInterface|Closure $contentType): static
	{
		if ($contentType instanceof Closure) {
			$contentType = $contentType($this->contentType);
		}

		if (!$contentType instanceof MediaTypeInterface) {
			throw new HeadersException('must provide media type interface');
		}

		$headers = $this->headers;
		$headers['content-type'] = (string) $contentType;

		return new static($headers, $this->accept, $contentType, $this->vary);
	}

	public function withoutContentType(): static
	{
		$headers = $this->headers;
		unset($headers['content-type']);

		return new static($headers, $this->accept, null, $this->vary);
	}

	public function vary(): ?VaryInterface
	{
		return $this->vary;
	}

	public function withVary(VaryInterface|Closure $vary): static
	{
		if ($vary instanceof Closure) {
			$vary = $vary($this->vary);
		}

		if (!$vary instanceof VaryInterface) {
			throw new HeadersException('must provide vary interface');
		}

		$headers = $this->headers;
		$headers['vary'] = (string) $vary;

		return new static($headers, $this->accept, $this->contentType, $vary);
	}

	public function withoutVary(): static
	{
		$headers = $this->headers;
		unset($headers['vary']);

		return new static($headers, $this->accept, $this->contentType, null);
	}

	public function isEmpty(): bool
	{
		return $this->count() === 0;
	}

	public function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->headers);
	}

	public function count(): int
	{
		return count($this->headers);
	}

	public function __invoke(): array
	{
		return $this->headers;
	}

	protected function validate(array $headers): void
	{
		foreach ($headers as $name => $value) {
			if (preg_match('/\A' . Rfc9110::FIELD_NAME . '\z/', $name) !== 1) {
				throw new HeadersException("invalid field name \"{$name}\"");
			}

			if (!is_string($value)) {
				throw new HeadersException("field value for \"{$name}\" must be a string");
			}

			if (preg_match('/\A' . Rfc9110::FIELD_VALUE . '\z/', $value) !== 1) {
				throw new HeadersException("invalid field value for \"{$name}\"");
			}
		}
	}

	protected function normalize(array $headers): array
	{
		return array_change_key_case($headers, CASE_LOWER);
	}
}
