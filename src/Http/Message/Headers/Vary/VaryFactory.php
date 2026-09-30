<?php

namespace WordPressPluginFramework\Http\Message\Headers\Vary;

use WordPressPluginFramework\{
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};

readonly class VaryFactory implements VaryFactoryInterface
{
	public function __construct(
		protected PregInterface $preg,
	) {
	}

	public function create(string $vary): VaryInterface
	{
		$match = $this->preg->match('@\A' . Rfc9110::VARY . '\z@J', $vary);
		if ($match === null) {
			throw new VaryFactoryException('invalid vary');
		}

		$matches = $this->preg->matchAll('@' . Rfc9110::FIELD_NAME . '@', $match['vary']);

		return new Vary($this->preg, $matches['field_name']);
	}
}
