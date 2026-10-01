<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Fragment;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc5234,
	Preg\PregInterface,
};

readonly class Fragment implements FragmentInterface
{
	protected const PCT_DECODED = '(?!' . Rfc3986::UNRESERVED . '|' . Rfc3986::SUB_DELIMS . '|:|\@|/|\?)' . Rfc5234::OCTET;

	public function __construct(
		protected PregInterface $preg,
		protected string $fragment,
	) {
	}

	public function __invoke(): string
	{
		return $this->fragment;
	}

	public function __toString(): string
	{
		return $this->preg->replaceCallback('@' . self::PCT_DECODED . '@', fn($match) => rawurlencode($match[0]), $this->fragment);
	}
}
