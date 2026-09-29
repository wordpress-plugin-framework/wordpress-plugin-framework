<?php

namespace WordPressPluginFramework\Http\Url\PercentEncoder;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Http\Abnf\Rfc5234,
};

readonly class PercentEncoder implements PercentEncoderInterface
{
	public function encodePath(string $path): string
	{
		$encoded = preg_replace_callback('@(?!/|' . Rfc3986::PCHAR . ')' . Rfc5234::OCTET . '@', fn($match) => rawurlencode($match[0]), $path);
		if ($encoded === null) {
			throw new PercentEncoderException('path is not encodable');
		}

		return $encoded;
	}

	public function encodeQuery(string $query): string
	{
		$encoded = preg_replace_callback('@(?!' . Rfc3986::PCHAR . '|/|\?)' . Rfc5234::OCTET . '@', fn($match) => rawurlencode($match[0]), $query);
		if ($encoded === null) {
			throw new PercentEncoderException('query is not encodable');
		}

		return $encoded;
	}
}
