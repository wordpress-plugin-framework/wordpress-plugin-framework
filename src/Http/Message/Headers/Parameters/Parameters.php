<?php

namespace WordPressPluginFramework\Http\Message\Headers\Parameters;

use ArrayIterator;
use WordPressPluginFramework\Http\Abnf\Rfc9110;
use Traversable;

readonly class Parameters implements ParametersInterface
{
	protected array $parameters;

	public function __construct(
		array $parameters,
	) {
		$this->validate($parameters);
		$this->parameters = $this->normalize($parameters);
	}

	public function has(string $name): bool
	{
		return isset($this->parameters[strtolower($name)]);
	}

	public function get(string $name): ?string
	{
		return $this->parameters[strtolower($name)] ?? null;
	}

	public function with(string $name, string $value): static
	{
		$parameters = $this->parameters;
		$parameters[strtolower($name)] = $value;

		return new static($parameters);
	}

	public function without(string $name): static
	{
		$parameters = $this->parameters;
		unset($parameters[strtolower($name)]);

		return new static($parameters);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->parameters);
	}

	public function count(): int
	{
		return count($this->parameters);
	}

	public function __invoke(): array
	{
		return $this->parameters;
	}

	public function __toString(): string
	{
		$parameters = '';

		foreach ($this->parameters as $name => $value) {
			$parameters .= "; {$name}={$this->quote($value)}";
		}

		return $parameters;
	}

	protected function validate(array $parameters): void
	{
		foreach ($parameters as $name => $value) {
			if (preg_match('@\A' . Rfc9110::PARAMETER_NAME . '\z@', $name) !== 1) {
				throw new ParametersException("invalid parameter name \"{$name}\"");
			}

			if (!is_string($value)) {
				throw new ParametersException("parameter value for \"{$name}\" must be a string");
			}

			if (preg_match('@\A' . Rfc9110::PARAMETER_VALUE . '\z@', $this->quote($value)) !== 1) {
				throw new ParametersException("invalid parameter value for \"{$name}\"");
			}
		}
	}

	protected function normalize(array $parameters): array
	{
		return array_change_key_case($parameters, CASE_LOWER);
	}

	protected function quote(string $value): string
	{
		if (preg_match('@\A' . Rfc9110::TOKEN . '\z@', $value)) {
			return $value;
		}

		return '"' . preg_replace('@(?!' . Rfc9110::QDTEXT . ')(.)@s', '\\\\$1', $value) . '"';
	}
}
