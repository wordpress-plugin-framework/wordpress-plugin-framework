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
		$this->validate($path);

		$normalized = $this->normalize($path);
		$segments = $this->split($normalized);
		$data = $this->decode($segments);

		return new Path($data);
	}

	protected function validate(string $path): void
	{
		$matched = preg_match('@\A' . Rfc9110::PATH_ABEMPTY . '\z@J', $path);
		if ($matched === false) {
			throw new PathFactoryException('path is not checkable');
		}

		if ($matched !== 1) {
			throw new PathFactoryException('invalid path');
		}
	}

	protected function normalize(string $path): string
	{
		$decoded = $this->decodeUnreservedOctets($path);

		return $this->removeDotSegments($decoded);
	}

	protected function decodeUnreservedOctets(string $path): string
	{
		$pattern = '@' . Rfc3986::PCT_ENCODED . '@J';

		$decoded = preg_replace_callback($pattern, $this->unreservedOctet(...), $path);
		if ($decoded === null) {
			throw new PathFactoryException('path is not decodable');
		}

		return $decoded;
	}

	protected function unreservedOctet(array $match): string
	{
		$octet = $this->octet($match);

		$matched = preg_match('@\A' . Rfc3986::UNRESERVED . '\z@', $octet);
		if ($matched !== 1) {
			return $match[0];
		}

		return $octet;
	}

	protected function removeDotSegments(string $path): string
	{
		$input = $path;
		$output = '';

		while ($input !== '') {
			if (str_starts_with($input, '../')) {
				$input = substr($input, 3);
				continue;
			}

			if (str_starts_with($input, './')) {
				$input = substr($input, 2);
				continue;
			}

			if (str_starts_with($input, '/./')) {
				$input = '/' . substr($input, 3);
				continue;
			}

			if ($input === '/.') {
				$input = '/';
				continue;
			}

			if (str_starts_with($input, '/../')) {
				$input = '/' . substr($input, 4);
				$output = $this->removeLastSegment($output);
				continue;
			}

			if ($input === '/..') {
				$input = '/';
				$output = $this->removeLastSegment($output);
				continue;
			}

			if (
				$input === '.' ||
				$input === '..'
			) {
				$input = '';
				continue;
			}

			$next = strpos($input, '/', 1);
			if ($next === false) {
				$output .= $input;
				$input = '';
				continue;
			}

			$output .= substr($input, 0, $next);
			$input = substr($input, $next);
		}

		return $output;
	}

	protected function removeLastSegment(string $output): string
	{
		$last = strrpos($output, '/');
		if ($last === false) {
			return '';
		}

		return substr($output, 0, $last);
	}

	protected function split(string $path): array
	{
		if ($path === '') {
			return [];
		}

		$segments = substr($path, 1);

		return explode('/', $segments);
	}

	protected function decode(array $segments): array
	{
		$data = [];

		foreach ($segments as $segment) {
			$data[] = $this->decodeOctets($segment);
		}

		return $data;
	}

	protected function decodeOctets(string $segment): string
	{
		$pattern = '@' . Rfc3986::PCT_ENCODED . '@J';

		$decoded = preg_replace_callback($pattern, $this->octet(...), $segment);
		if ($decoded === null) {
			throw new PathFactoryException('segment is not decodable');
		}

		return $decoded;
	}

	protected function octet(array $match): string
	{
		$hex = substr($match[0], 1);
		$code = hexdec($hex);

		return chr($code);
	}
}
