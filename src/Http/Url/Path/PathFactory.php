<?php

namespace WordPressPluginFramework\Http\Url\Path;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};

readonly class PathFactory implements PathFactoryInterface
{
	public function __construct(
		protected PregInterface $preg,
	) {
	}

	public function create(string $path): PathInterface
	{
		$match = $this->preg->match('@\A' . Rfc9110::PATH_ABEMPTY . '\z@', $path);
		if ($match === null) {
			throw new PathFactoryException('invalid path');
		}

		$segments = [];

		$matches = $this->preg->matchAll('@/' . Rfc9110::SEGMENT . '@', $match['path_abempty'], PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);
		foreach ($matches as $match) {
			$segments[] = $this->preg->replaceCallback('@' . Rfc3986::PCT_ENCODED . '@', fn($match) => rawurldecode($match[0]), $match['segment']);
		}

		$segments = $this->removeDotSegments($segments);

		return new Path($this->preg, $segments);
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
