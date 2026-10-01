<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

use WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\Precedence\Precedence,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\Parameters\ParametersInterface,
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};

readonly class MediaRange implements MediaRangeInterface
{
	protected string $type;
	protected string $subtype;

	public function __construct(
		protected PregInterface $preg,
		string $type,
		string $subtype,
		protected ParametersInterface $parameters,
		protected ?string $q,
	) {
		$this->validateType($type);
		$this->type = $this->normalizeType($type);

		$this->validateSubtype($subtype);
		$this->subtype = $this->normalizeSubtype($subtype);

		$this->validateQ($this->q);
	}

	public function type(): string
	{
		return $this->type;
	}

	public function withType(string $type): static
	{
		return new static($this->preg, $type, $this->subtype, $this->parameters, $this->q);
	}

	public function subtype(): string
	{
		return $this->subtype;
	}

	public function withSubtype(string $subtype): static
	{
		return new static($this->preg, $this->type, $subtype, $this->parameters, $this->q);
	}

	public function parameters(): ParametersInterface
	{
		return $this->parameters;
	}

	public function withParameters(ParametersInterface $parameters): static
	{
		return new static($this->preg, $this->type, $this->subtype, $parameters, $this->q);
	}

	public function q(): ?string
	{
		return $this->q;
	}

	public function withQ(string $q): static
	{
		return new static($this->preg, $this->type, $this->subtype, $this->parameters, $q);
	}

	public function withoutQ(): static
	{
		return new static($this->preg, $this->type, $this->subtype, $this->parameters, null);
	}

	public function __tostring(): string
	{
		$mediaRange = "{$this->type}/{$this->subtype}{$this->parameters}";
		if ($this->q === null) {
			return $mediaRange;
		}

		return "{$mediaRange}; q={$this->q}";
	}

	public function precedence(MediaTypeInterface $mediaType): ?Precedence
	{
		if (
			$this->type === $mediaType->type() &&
			$this->subtype === $mediaType->subtype()
		) {
			return Precedence::TypeSubtype;
		}

		if (
			$this->type === $mediaType->type() &&
			$this->subtype === '*'
		) {
			return Precedence::TypeWildcardSubtype;
		}

		if (
			$this->type === '*'
		) {
			return Precedence::WildcardTypeWildcardSubtype;
		}

		return null;
	}

	protected function validateType(string $type): void
	{
		$match = $this->preg->match('@\A' . Rfc9110::TYPE . '\z@', $type);
		if ($match === null) {
			throw new MediaRangeException('invalid type');
		}
	}

	protected function validateSubtype(string $subtype): void
	{
		$match = $this->preg->match('@\A' . Rfc9110::SUBTYPE . '\z@', $subtype);
		if ($match === null) {
			throw new MediaRangeException('invalid subtype');
		}
	}

	protected function validateQ(?string $q): void
	{
		if ($q === null) {
			return;
		}

		$match = $this->preg->match('@\A' . Rfc9110::QVALUE . '\z@', $q);
		if ($match === null) {
			throw new MediaRangeException('invalid q');
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
