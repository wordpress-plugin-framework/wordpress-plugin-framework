<?php

namespace WordPressPluginFramework\Preg;

readonly class Preg implements PregInterface
{
	public function match(string $pattern, string $subject, int $flags = 0): ?array
	{
		$matched = preg_match($pattern, $subject, $match, $flags);
		if ($matched === false) {
			throw new PregException(preg_last_error_msg());
		}

		return $matched === 1 ? $match : null;
	}

	public function matchAll(string $pattern, string $subject, int $flags = 0): array
	{
		$matched = preg_match_all($pattern, $subject, $matches, $flags);
		if ($matched === false) {
			throw new PregException(preg_last_error_msg());
		}

		return $matches;
	}

	public function replaceCallback(string $pattern, callable $callback, string $subject): string
	{
		$replaced = preg_replace_callback($pattern, $callback, $subject);
		if ($replaced === null) {
			throw new PregException(preg_last_error_msg());
		}

		return $replaced;
	}
}
