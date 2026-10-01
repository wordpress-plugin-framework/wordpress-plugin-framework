<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Scheme;

enum Scheme: string
{
	case Http = 'http';
	case Https = 'https';

	public static function create(string $scheme): static
	{
		$normalized = strtolower($scheme);

		$case = static::tryFrom($normalized);
		if ($case === null) {
			throw new SchemeException("invalid scheme");
		}

		return $case;
	}

	public function port(): int
	{
		return match ($this) {
			self::Http => 80,
			self::Https => 443,
		};
	}
}
