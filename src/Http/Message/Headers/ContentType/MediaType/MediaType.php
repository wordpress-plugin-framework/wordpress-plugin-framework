<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use WordPressPluginFramework\{
	Http\Abnf\Rfc9110,
	Http\Message\Headers\Parameters\Parameters,
	Http\Message\Headers\Parameters\ParametersInterface,
	Preg\Preg,
	Preg\PregInterface,
};

readonly class MediaType implements MediaTypeInterface
{
	protected string $type;
	protected string $subtype;

	public function __construct(
		protected PregInterface $preg,
		string $type,
		string $subtype,
		protected ParametersInterface $parameters,
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
		return new static($this->preg, $type, $this->subtype, $this->parameters);
	}

	public function subtype(): string
	{
		return $this->subtype;
	}

	public function withSubtype(string $subtype): static
	{
		return new static($this->preg, $this->type, $subtype, $this->parameters);
	}

	public function parameters(): ParametersInterface
	{
		return $this->parameters;
	}

	public function withParameters(ParametersInterface $parameters): static
	{
		return new static($this->preg, $this->type, $this->subtype, $parameters);
	}

	public function __toString(): string
	{
		return "{$this->type}/{$this->subtype}{$this->parameters}";
	}

	protected function validateType(string $type): void
	{
		$match = $this->preg->match('@\A' . Rfc9110::TYPE . '\z@', $type);
		if ($match === null) {
			throw new MediaTypeException('invalid type');
		}

		if ($type === '*') {
			throw new MediaTypeException('type must not be a wildcard');
		}
	}

	protected function validateSubtype(string $subtype): void
	{
		$match = $this->preg->match('@\A' . Rfc9110::SUBTYPE . '\z@', $subtype);
		if ($match === null) {
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
