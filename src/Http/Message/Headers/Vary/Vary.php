<?php

namespace WordPressPluginFramework\Http\Message\Headers\Vary;

use ArrayIterator;
use WordPressPluginFramework\Http\Abnf\Rfc9110;
use Traversable;

readonly class Vary implements VaryInterface
{
	protected array $fieldNames;

	public function __construct(
		array $fieldNames,
	) {
		$this->validate($fieldNames);
		$this->fieldNames = $this->normalize($fieldNames);
	}

	public function has(string $fieldName): bool
	{
		return isset($this->fieldNames[strtolower($fieldName)]);
	}

	public function with(string $fieldName): static
	{
		$fieldName = strtolower($fieldName);

		$fieldNames = $this->fieldNames;
		$fieldNames[$fieldName] = $fieldName;

		return new static($fieldNames);
	}

	public function without(string $fieldName): static
	{
		$fieldName = strtolower($fieldName);

		$fieldNames = $this->fieldNames;
		unset($fieldNames[$fieldName]);

		return new static($fieldNames);
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
		return new ArrayIterator(
			array_values($this->fieldNames),
		);
	}

	public function count(): int
	{
		return count($this->fieldNames);
	}

	public function __invoke(): array
	{
		return array_values($this->fieldNames);
	}

	public function __toString(): string
	{
		return implode(', ', $this->fieldNames);
	}

	protected function validate(array $fieldNames): void
	{
		foreach ($fieldNames as $fieldName) {
			if (!is_string($fieldName)) {
				throw new VaryException('field name must be a string');
			}

			if (preg_match('@\A' . Rfc9110::FIELD_NAME . '\z@', $fieldName) !== 1) {
				throw new VaryException("invalid field name \"{$fieldName}\"");
			}
		}
	}

	protected function normalize(array $fieldNames): array
	{
		$fieldNames = array_map(strtolower(...), $fieldNames);

		return array_combine($fieldNames, $fieldNames);
	}
}
