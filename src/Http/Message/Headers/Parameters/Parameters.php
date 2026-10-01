<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Headers\Parameters;

use ArrayIterator;
use WordPressPluginFramework\{
	Http\Abnf\Rfc5234,
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};
use Traversable;

readonly class Parameters implements ParametersInterface
{
	protected const UNQUOTED_PAIR = '(?!' . Rfc9110::QDTEXT . ')' . Rfc5234::OCTET;

	protected array $parameters;

	public function __construct(
		protected PregInterface $preg,
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

		return new static($this->preg, $parameters);
	}

	public function without(string $name): static
	{
		$parameters = $this->parameters;
		unset($parameters[strtolower($name)]);

		return new static($this->preg, $parameters);
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
			$match = $this->preg->match('@\A' . Rfc9110::PARAMETER_NAME . '\z@', (string) $name);
			if ($match === null) {
				throw new ParametersException("invalid parameter name \"{$name}\"");
			}

			if (!is_string($value)) {
				throw new ParametersException("parameter value for \"{$name}\" must be a string");
			}

			$quoted = $this->quote($value);

			$match = $this->preg->match('@\A' . Rfc9110::PARAMETER_VALUE . '\z@', $quoted);
			if ($match === null) {
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
		$match = $this->preg->match('@\A' . Rfc9110::TOKEN . '\z@', $value);
		if ($match !== null) {
			return $value;
		}

		return '"' . $this->preg->replaceCallback('@' . self::UNQUOTED_PAIR . '@',fn($match) => '\\' . $match[0], $value) . '"';
	}
}
