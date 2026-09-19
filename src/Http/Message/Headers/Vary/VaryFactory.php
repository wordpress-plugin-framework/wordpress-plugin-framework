<?php

namespace WordPressPluginFramework\Http\Message\Headers\Vary;

use WordPressPluginFramework\Http\Abnf\Rfc9110;

readonly class VaryFactory implements VaryFactoryInterface
{
	public function create(string $vary): VaryInterface
	{
		if (preg_match('@\A' . Rfc9110::VARY . '\z@J', $vary) !== 1) {
			throw new VaryFactoryException('invalid vary');
		}

		preg_match_all('@' . Rfc9110::FIELD_NAME . '@', $vary, $matches);

		return new Vary($matches['field_name']);
	}
}
