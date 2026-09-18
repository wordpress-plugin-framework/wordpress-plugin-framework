<?php

namespace WordPressPluginFramework\Http\Accessor;

use stdClass;

readonly class Accessor implements AccessorInterface
{
	public function has(array|stdClass $data, string $path): bool
	{
		return $this->read($data, $this->parse($path), '') !== [];
	}

	public function get(array|stdClass $data, string $path): string|int|float|bool|null|array|stdClass
	{
		$accessors = $this->parse($path);

		foreach ($accessors as $accessor) {
			if ($accessor[2]) {
				throw new AccessorException(sprintf('Path "%s" is a wildcard; use values().', $path));
			}
		}

		$values = $this->read($data, $accessors, '');

		if ($values === []) {
			throw new AccessorException(sprintf('No value at path "%s".', $path));
		}

		return reset($values);
	}

	public function values(array|stdClass $data, string $path): array
	{
		return $this->read($data, $this->parse($path), '');
	}

	public function with(array|stdClass $data, string $path, string|int|float|bool|null|array|stdClass $value): array|stdClass
	{
		return $this->write($data, $this->parse($path), $value);
	}

	public function without(array|stdClass $data, string $path): array|stdClass
	{
		return $this->remove($data, $this->parse($path));
	}

	private function read(mixed $current, array $accessors, string $path): array
	{
		if ($accessors === []) {
			return [$path => $current];
		}

		$accessor = $accessors[0];
		$rest = array_slice($accessors, 1);

		if (!$this->matchesType($current, $accessor)) {
			return [];
		}

		if ($accessor[2]) {
			$values = [];
			foreach ($this->entries($current) as $key => $child) {
				$values += $this->read($child, $rest, $this->appendSegment($path, $accessor[0], $key));
			}

			return $values;
		}

		if (!$this->accessorExists($current, $accessor)) {
			return [];
		}

		return $this->read($this->accessorValue($current, $accessor), $rest, $this->appendSegment($path, $accessor[0], $accessor[1]));
	}

	private function write(mixed $current, array $accessors, mixed $value): mixed
	{
		if ($accessors === []) {
			return $value;
		}

		$accessor = $accessors[0];
		$rest = array_slice($accessors, 1);

		if (!$this->matchesType($current, $accessor)) {
			throw new AccessorException(
				sprintf('Path segment expects %s but found %s.', $accessor[0], get_debug_type($current))
			);
		}

		if ($accessor[2]) {
			$result = $current;
			foreach ($this->entries($current) as $key => $child) {
				$result = $this->set($result, $accessor[0], $key, $this->write($child, $rest, $value));
			}

			return $result;
		}

		$child = $this->accessorExists($current, $accessor) ? $this->accessorValue($current, $accessor) : $this->create($rest);

		return $this->set($current, $accessor[0], $accessor[1], $this->write($child, $rest, $value));
	}

	private function create(array $rest): array|stdClass|null
	{
		if ($rest === []) {
			return null;
		}

		return $rest[0][0] === 'object' ? new stdClass() : [];
	}

	private function remove(mixed $current, array $accessors): mixed
	{
		$accessor = $accessors[0];
		$rest = array_slice($accessors, 1);

		if (!$this->matchesType($current, $accessor)) {
			return $current;
		}

		if ($accessor[2]) {
			if ($rest === []) {
				return $this->emptyLike($current);
			}

			$result = $current;
			foreach ($this->entries($current) as $key => $child) {
				$result = $this->set($result, $accessor[0], $key, $this->remove($child, $rest));
			}

			return $result;
		}

		if (!$this->accessorExists($current, $accessor)) {
			return $current;
		}

		if ($rest === []) {
			return $this->drop($current, $accessor[0], $accessor[1]);
		}

		return $this->set($current, $accessor[0], $accessor[1], $this->remove($this->accessorValue($current, $accessor), $rest));
	}

	private function matchesType(mixed $current, array $accessor): bool
	{
		return $accessor[0] === 'object' ? $current instanceof stdClass : is_array($current);
	}

	private function accessorExists(array|stdClass $container, array $accessor): bool
	{
		return $accessor[0] === 'object'
			? property_exists($container, $accessor[1])
			: array_key_exists($accessor[1], $container);
	}

	private function accessorValue(array|stdClass $container, array $accessor): mixed
	{
		return $accessor[0] === 'object'
			? $container->{$accessor[1]}
			: $container[$accessor[1]];
	}

	private function set(array|stdClass $container, string $type, string $key, mixed $value): array|stdClass
	{
		if ($type === 'object') {
			$clone = clone $container;
			$clone->{$key} = $value;

			return $clone;
		}

		$container[$key] = $value;

		return $container;
	}

	private function drop(array|stdClass $container, string $type, string $key): array|stdClass
	{
		if ($type === 'object') {
			$clone = clone $container;
			unset($clone->{$key});

			return $clone;
		}

		$isList = array_is_list($container);
		unset($container[$key]);

		return $isList ? array_values($container) : $container;
	}

	private function emptyLike(array|stdClass $container): array|stdClass
	{
		return $container instanceof stdClass ? new stdClass() : [];
	}

	private function entries(array|stdClass $container): array
	{
		return $container instanceof stdClass ? get_object_vars($container) : $container;
	}

	private function appendSegment(string $path, string $type, string $key): string
	{
		if ($type === 'object') {
			$segment = $this->escapeSegment($key, '\\.[');

			return $path === '' ? $segment : $path . '.' . $segment;
		}

		return $path . '[' . $this->escapeSegment($key, '\\]') . ']';
	}

	private function escapeSegment(string $key, string $reserved): string
	{
		if ($key === '*') {
			return '\*';
		}

		return addcslashes($key, $reserved);
	}

	/**
	 * @return array<int, array{0: string, 1: string, 2: bool}>
	 */
	private function parse(string $path): array
	{
		if ($path === '') {
			throw new AccessorException('Path must not be empty.');
		}

		$accessors = [];
		$index = 0;
		$length = strlen($path);

		while ($index < $length) {
			$char = $path[$index];

			if ($char === '[') {
				[$key, $wildcard, $index] = $this->readIndex($path, $index + 1, $length);
				$accessors[] = ['array', $key, $wildcard];
				continue;
			}

			if ($char === '.') {
				if ($accessors === []) {
					throw new AccessorException(sprintf('Path "%s" must not start with ".".', $path));
				}

				[$key, $wildcard, $index] = $this->readProperty($path, $index + 1, $length);
				$accessors[] = ['object', $key, $wildcard];
				continue;
			}

			if ($accessors !== []) {
				throw new AccessorException(
					sprintf('Unexpected "%s" in path "%s"; a property after the first must be preceded by ".".', $char, $path)
				);
			}

			[$key, $wildcard, $index] = $this->readProperty($path, $index, $length);
			$accessors[] = ['object', $key, $wildcard];
		}

		return $accessors;
	}

	/**
	 * @return array{0: string, 1: bool, 2: int}
	 */
	private function readIndex(string $path, int $index, int $length): array
	{
		$key = '';
		$escaped = false;
		$closed = false;

		while ($index < $length) {
			$char = $path[$index];

			if ($char === '\\') {
				$index++;
				if ($index >= $length) {
					throw new AccessorException('Dangling escape in path.');
				}

				$key .= $path[$index];
				$escaped = true;
				$index++;
				continue;
			}

			if ($char === ']') {
				$closed = true;
				$index++;
				break;
			}

			$key .= $char;
			$index++;
		}

		if (!$closed) {
			throw new AccessorException('Unbalanced "[" in path.');
		}

		if ($key === '') {
			throw new AccessorException('Empty index "[]" in path.');
		}

		return [$key, $key === '*' && !$escaped, $index];
	}

	/**
	 * @return array{0: string, 1: bool, 2: int}
	 */
	private function readProperty(string $path, int $index, int $length): array
	{
		$key = '';
		$escaped = false;

		while ($index < $length) {
			$char = $path[$index];

			if ($char === '\\') {
				$index++;
				if ($index >= $length) {
					throw new AccessorException('Dangling escape in path.');
				}

				$key .= $path[$index];
				$escaped = true;
				$index++;
				continue;
			}

			if ($char === '.' || $char === '[') {
				break;
			}

			$key .= $char;
			$index++;
		}

		if ($key === '') {
			throw new AccessorException('Empty property in path.');
		}

		return [$key, $key === '*' && !$escaped, $index];
	}
}
