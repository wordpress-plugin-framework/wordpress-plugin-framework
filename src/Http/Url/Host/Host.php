<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Host;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3629,
	Http\Abnf\Rfc3986,
	Preg\PregInterface,
};

readonly class Host implements HostInterface
{
	protected const PCT_DECODED = '(?:' . Rfc3629::UTF8_2 . '|' . Rfc3629::UTF8_3 . '|' . Rfc3629::UTF8_4 . ')';

	protected string $host;

	public function __construct(
		protected PregInterface $preg,
		string $host,
	) {
		$this->validate($host);
		$this->host = $this->normalize($host);
	}

	public function __invoke(): string
	{
		return $this->host;
	}

	public function __toString(): string
	{
		return $this->encode($this->host);
	}

	protected function validate(string $host): void
	{
		$match = $this->preg->match('@\A(?:' . Rfc3986::IP_LITERAL . '|' . Rfc3986::IPV4ADDRESS . '|(?:' . Rfc3986::UNRESERVED . '|' . self::PCT_DECODED . '|' . Rfc3986::SUB_DELIMS . ')*)\z@', $host);
		if ($match === null) {
			throw new HostException('invalid host');
		}
	}

	protected function normalize(string $host): string
	{
		return strtolower($host);
	}

	protected function encode(string $host): string
	{
		return $this->preg->replaceCallback('@' . self::PCT_DECODED . '@', fn($match) => rawurlencode($match[0]), $host);
	}
}
