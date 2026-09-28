<?php

namespace WordPressPluginFramework\Http\Url\Host;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3629,
	Http\Abnf\Rfc9110,
};

readonly class HostFactory implements HostFactoryInterface
{
	public function create(string $host): HostInterface
	{
		$encoded = $this->encode($host);
		$match = $this->match($encoded);
		$decoded = rawurldecode($match['host']);

		return new Host($decoded);
	}

	protected function encode(string $host): string
	{
		$encoded = preg_replace_callback('@' . Rfc3629::UTF8_2 . '|' . Rfc3629::UTF8_3 . '|' . Rfc3629::UTF8_4 . '@', fn($match) => rawurlencode($match[0]), $host);
		if ($encoded === null) {
			throw new HostFactoryException('host is not encodable');
		}

		return $encoded;
	}

	protected function match(string $host): array
	{
		$matched = preg_match('@\A' . Rfc9110::URI_HOST . '\z@J', $host, $match, PREG_UNMATCHED_AS_NULL);
		if ($matched === false) {
			throw new HostFactoryException('host is not checkable');
		}

		if ($matched !== 1) {
			throw new HostFactoryException('invalid host');
		}

		return $match;
	}
}
