<?php

namespace WordPressPluginFramework\Http\Url\Host;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3629,
	Http\Abnf\Rfc3986,
};

readonly class Host implements HostInterface
{
	protected string $host;

	public function __construct(string $host)
	{
		$this->validate($host);
		$this->host = strtolower($host);
	}

	public function __invoke(): string
	{
		return $this->host;
	}

	public function __toString(): string
	{
		$encoded = preg_replace_callback('@' . Rfc3629::UTF8_2 . '|' . Rfc3629::UTF8_3 . '|' . Rfc3629::UTF8_4 . '@', fn($match) => rawurlencode($match[0]), $this->host);
		if ($encoded === null) {
			throw new HostException('host is not encodable');
		}

		return $encoded;
	}

	protected function validate(string $host): void
	{
		$matched = preg_match('@\A(?:' . Rfc3986::IP_LITERAL . '|' . Rfc3986::IPV4ADDRESS . '|(?:' . Rfc3986::UNRESERVED . '|' . Rfc3986::SUB_DELIMS . '|' . Rfc3629::UTF8_2 . '|' . Rfc3629::UTF8_3 . '|' . Rfc3629::UTF8_4 . ')*)\z@J', $host);
		if ($matched === false) {
			throw new HostException('host is not checkable');
		}

		if ($matched !== 1) {
			throw new HostException('invalid host');
		}
	}
}
