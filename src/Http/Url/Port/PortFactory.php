<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Port;

use WordPressPluginFramework\{
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};

readonly class PortFactory implements PortFactoryInterface
{
	public function __construct(
		protected PregInterface $preg,
	) {
	}

	public function create(string $port): PortInterface
	{
		$match = $this->preg->match('@\A' . Rfc9110::PORT . '\z@', $port);
		if ($match === null) {
			throw new PortFactoryException('invalid port');
		}

		$port = (int) $match['port'];

		return new Port($port);
	}
}
