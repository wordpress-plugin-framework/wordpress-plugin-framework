<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Multipart;

use Hoo\WordPressPluginFramework\{
	Http\Abnf\Rfc2046,
	Http\Abnf\Rfc5234,
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use stdClass;

readonly class Encoder implements EncoderInterface
{
	protected MediaTypeInterface $contentType;

	public function __construct(
		MediaTypeInterface $contentType = new MediaType('multipart', 'form-data'),
	) {
		if (!$this->encodesContentType($contentType)) {
			throw new EncoderException('does not encode this media type');
		}

		$this->contentType = $contentType->parameters()->has('boundary') ? $contentType : $contentType->withParameters(fn($parameters) => $parameters->with('boundary', bin2hex(random_bytes(16))));
	}

	public function contentType(): MediaTypeInterface
	{
		return $this->contentType;
	}

	public function withContentType(MediaTypeInterface $contentType): static
	{
		return new static($contentType);
	}

	public function encode(mixed $body): string
	{
		if (!$this->encodesBody($body)) {
			throw new EncoderException('does not encode');
		}

		$boundary = $this->contentType->parameters()->get('boundary');

		$encoded = '';

		foreach ($this->fields($body) as [$name, $value]) {
			$quotedName = addcslashes($name, '"\\');

			$encoded .= "--{$boundary}\r\n";
			$encoded .= "Content-Disposition: form-data; name=\"{$quotedName}\"\r\n";
			$encoded .= "\r\n";
			$encoded .= "{$value}\r\n";
		}

		return "{$encoded}--{$boundary}--\r\n";
	}

	public function encodesBody(mixed $body): bool
	{
		if (
			!is_array($body) &&
			!$body instanceof stdClass
		) {
			return false;
		}

		if ((array) $body === []) {
			return false;
		}

		foreach ($body as $name => $value) {
			if (preg_match('/\A(?:(?!' . Rfc5234::SP . '|\.|\[|\x00|' . Rfc5234::CR . '|' . Rfc5234::LF . ').)+\z/s', $name) !== 1) {
				return false;
			}

			if (!$this->encodesValue($value, $name)) {
				return false;
			}
		}

		return true;
	}

	public function encodesContentType(MediaTypeInterface $contentType): bool
	{
		$type = $contentType->type();
		if ($type !== 'multipart') {
			return false;
		}

		$subtype = $contentType->subtype();
		if ($subtype !== 'form-data') {
			return false;
		}

		$parameters = $contentType->parameters();

		$otherParameters = $parameters->without('boundary');
		if ($otherParameters->count() !== 0) {
			return false;
		}

		$boundary = $parameters->get('boundary');
		if (
			$boundary !== null &&
			preg_match('@\A' . Rfc2046::BOUNDARY . '\z@', $boundary) !== 1
		) {
			return false;
		}

		return true;
	}

	protected function encodesValue(mixed $value, string $name): bool
	{
		if (
			is_null($value) ||
			is_scalar($value)
		) {
			$boundary = $this->contentType->parameters()->get('boundary');

			return !str_contains("\n{$value}", "\n--{$boundary}");
		}

		if (
			!is_array($value) &&
			!$value instanceof stdClass
		) {
			return false;
		}

		if ((array) $value === []) {
			return false;
		}

		foreach ($value as $key => $child) {
			if (preg_match('/\A(?:(?!\]|\x00|' . Rfc5234::CR . '|' . Rfc5234::LF . ').)+\z/s', $key) !== 1) {
				return false;
			}

			if (preg_match('/\A(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . '|\x0B|\x0C)\z/', $key) === 1) {
				return false;
			}

			if (
				str_starts_with($key, '__Host-') &&
				!str_starts_with($name, '__Host-')
			) {
				return false;
			}

			if (
				str_starts_with($key, '__Secure-') &&
				!str_starts_with($name, '__Secure-')
			) {
				return false;
			}

			if (!$this->encodesValue($child, $name)) {
				return false;
			}
		}

		return true;
	}

	protected function fields(mixed $body, string $prefix = ''): array
	{
		$fields = [];

		foreach ($body as $name => $value) {
			$name = $prefix === '' ? (string) $name : "{$prefix}[{$name}]";

			if (is_array($value) || $value instanceof stdClass) {
				$fields = [...$fields, ...$this->fields($value, $name)];

				continue;
			}

			$fields[] = [$name, $value];
		}

		return $fields;
	}
}
