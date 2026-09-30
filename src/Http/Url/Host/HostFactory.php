<?php

namespace WordPressPluginFramework\Http\Url\Host;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};

readonly class HostFactory implements HostFactoryInterface
{
	public function __construct(
		protected PregInterface $preg,
	) {
	}

	public function create(string $host): HostInterface
	{
		$match = $this->preg->match('@\A' . Rfc9110::URI_HOST . '\z@', $host);
		if ($match === null) {
			throw new HostFactoryException('invalid host');
		}

		$host = $this->preg->replaceCallback('@' . Rfc3986::PCT_ENCODED . '@', fn($match) => rawurldecode($match[0]), $match['host']);

		return new Host($this->preg, $host);
	}
}
