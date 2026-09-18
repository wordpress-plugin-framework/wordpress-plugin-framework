<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

use WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\Precedence\Precedence,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\Parameters\Parameters,
	Http\Message\Headers\Parameters\ParametersInterface,
	Http\Abnf\Rfc9110,
};
use Closure;

readonly class MediaRange implements MediaRangeInterface
{
	protected string $type;
	protected string $subtype;

	public function __construct(
		string $type,
		string $subtype,
		protected ParametersInterface $parameters = new Parameters([]),
		protected ?string $q = null,
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
		return new static($type, $this->subtype, $this->parameters, $this->q);
	}

	public function subtype(): string
	{
		return $this->subtype;
	}

	public function withSubtype(string $subtype): static
	{
		return new static($this->type, $subtype, $this->parameters, $this->q);
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
			throw new MediaRangeException('must provide parameters interface');
		}

		return new static($this->type, $this->subtype, $parameters, $this->q);
	}

	public function q(): ?string
	{
		return $this->q;
	}

	public function withQ(string $q): static
	{
		return new static($this->type, $this->subtype, $this->parameters, $q);
	}

	public function withoutQ(): static
	{
		return new static($this->type, $this->subtype, $this->parameters, null);
	}

	public function mediaType(): ?MediaTypeInterface
	{
		if (
			$this->type === '*' ||
			$this->subtype === '*'
		) {
			return null;
		}

		return new MediaType($this->type, $this->subtype, $this->parameters);
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
		if (!preg_match('@\A' . Rfc9110::TYPE . '\z@', $type)) {
			throw new MediaRangeException('invalid type');
		}
	}

	protected function validateSubtype(string $subtype): void
	{
		if (!preg_match('@\A' . Rfc9110::SUBTYPE . '\z@', $subtype)) {
			throw new MediaRangeException('invalid subtype');
		}
	}

	protected function validateQ(?string $q): void
	{
		if ($q === null) {
			return;
		}

		if (!preg_match('@\A' . Rfc9110::QVALUE . '\z@', $q)) {
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
