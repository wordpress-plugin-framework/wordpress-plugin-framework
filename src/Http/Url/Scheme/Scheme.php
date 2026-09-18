<?php

namespace WordPressPluginFramework\Http\Url\Scheme;

enum Scheme: string
{
	case Http = 'http';
	case Https = 'https';

	public static function create(string $scheme): static
	{
		$scheme = static::tryFrom($scheme);
		if ($scheme === null) {
			throw new SchemeException("invalid scheme");
		}

		return $scheme;
	}

	public function port(): int
	{
		return match ($this) {
			self::Http => 80,
			self::Https => 443,
		};
	}
}
