<?php

namespace WordPressPluginFramework\Http\Url\Host;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc9110,
};

readonly class HostFactory implements HostFactoryInterface
{
	public function create(string $host): HostInterface
	{
		$this->validate($host);

		$decoded = $this->decode($host);

		return new Host($decoded);
	}

	protected function validate(string $host): void
	{
		$matched = preg_match('@\A' . Rfc9110::URI_HOST . '\z@', $host);
		if ($matched === false) {
			throw new HostFactoryException('host is not checkable');
		}

		if ($matched !== 1) {
			throw new HostFactoryException('invalid host');
		}
	}

	protected function decode(string $host): string
	{
		$decoded = preg_replace_callback('@' . Rfc3986::PCT_ENCODED . '@', fn($match) => rawurldecode($match[0]), $host);
		if ($decoded === null) {
			throw new HostFactoryException('host is not decodable');
		}

		return $decoded;
	}
}
