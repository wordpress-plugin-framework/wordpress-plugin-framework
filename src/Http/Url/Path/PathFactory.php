<?php

namespace WordPressPluginFramework\Http\Url\Path;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc9110,
};

readonly class PathFactory implements PathFactoryInterface
{
	public function create(string $path): PathInterface
	{
		if (preg_match('@\A' . Rfc9110::PATH_ABEMPTY . '\z@', $path) !== 1) {
			throw new PathFactoryException('invalid path');
		}

		if (preg_match_all('@/' . Rfc9110::SEGMENT . '@', $path, $matches, PREG_SET_ORDER) === false) {
			throw new PathFactoryException('path is not splittable');
		}

		$segments = [];

		foreach ($matches as $match) {
			$segments[] = $this->decodeSegment($match['segment']);
		}

		$segments = $this->removeDotSegments($segments);

		return new Path($segments);
	}

	protected function decodeSegment(string $segment): string
	{
		$decoded = preg_replace_callback('@' . Rfc3986::PCT_ENCODED . '@', fn($match) => rawurldecode($match[0]), $segment);
		if ($decoded === null) {
			throw new PathFactoryException('segment is not decodable');
		}

		return $decoded;
	}

	protected function removeDotSegments(array $segments): array
	{
		$output = [];
		$last = array_key_last($segments);

		foreach ($segments as $key => $segment) {
			if ($segment === '..') {
				array_pop($output);
			}

			if ($segment !== '.' && $segment !== '..') {
				$output[] = $segment;
				continue;
			}

			if ($key === $last) {
				$output[] = '';
			}
		}

		return $output;
	}
}
