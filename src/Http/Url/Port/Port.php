<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Port;

readonly class Port implements PortInterface
{
	public function __construct(
		protected int $port,
	) {
		$this->validate($port);
	}

	public function __invoke(): int
	{
		return $this->port;
	}

	public function __toString(): string
	{
		return (string) $this->port;
	}

	protected function validate(int $port): void
	{
		if ($port < 0 || $port > 65535) {
			throw new PortException('invalid port');
		}
	}
}
