<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Abnf\Rfc9110,
	Http\Message\Headers\Parameters\Parameters,
	Http\Message\Headers\Parameters\ParametersInterface,
};
use Closure;

readonly class MediaType implements MediaTypeInterface
{
	protected string $type;
	protected string $subtype;

	public function __construct(
		string $type,
		string $subtype,
		protected ParametersInterface $parameters = new Parameters([]),
	) {
		$this->validateType($type);
		$this->type = $this->normalizeType($type);

		$this->validateSubtype($subtype);
		$this->subtype = $this->normalizeSubtype($subtype);
	}

	public function type(): string
	{
		return $this->type;
	}

	public function withType(string $type): static
	{
		return new static($type, $this->subtype, $this->parameters);
	}

	public function subtype(): string
	{
		return $this->subtype;
	}

	public function withSubtype(string $subtype): static
	{
		return new static($this->type, $subtype, $this->parameters);
	}

	public function parameters(): ParametersInterface
	{
		return $this->parameters;
	}

	public function withParameters(ParametersInterface|Closure $parameters): static
	{
		if ($parameters instanceof Closure) {
			$parameters = $parameters($this->parameters);
		}

		if (!$parameters instanceof ParametersInterface) {
			throw new MediaTypeException('must provide parameters interface');
		}

		return new static($this->type, $this->subtype, $parameters);
	}

	public function __toString(): string
	{
		return "{$this->type}/{$this->subtype}{$this->parameters}";
	}

	protected function validateType(string $type): void
	{
		if (!preg_match('@\A' . Rfc9110::TYPE . '\z@', $type)) {
			throw new MediaTypeException('invalid type');
		}

		if ($type === '*') {
			throw new MediaTypeException('type must not be a wildcard');
		}
	}

	protected function validateSubtype(string $subtype): void
	{
		if (!preg_match('@\A' . Rfc9110::SUBTYPE . '\z@', $subtype)) {
			throw new MediaTypeException('invalid subtype');
		}

		if ($subtype === '*') {
			throw new MediaTypeException('subtype must not be a wildcard');
		}
	}

	protected function normalizeType(string $type): string
	{
		return strtolower($type);
	}

	protected function normalizeSubtype(string $subtype): string
	{
		return strtolower($subtype);
	}
}
