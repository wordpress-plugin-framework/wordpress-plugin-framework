<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept;

use WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\MediaRange,
	Http\Message\Headers\Parameters\ParametersFactoryInterface,
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};

readonly class AcceptFactory implements AcceptFactoryInterface
{
	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
		protected PregInterface $preg,
	) {
	}

	public function create(string $accept): AcceptInterface
	{
		$match = $this->preg->match('@\A' . Rfc9110::ACCEPT . '\z@J', $accept);
		if ($match === null) {
			throw new AcceptFactoryException('invalid accept');
		}

		$mediaRanges = [];

		$matches = $this->preg->matchAll('@' . Rfc9110::MEDIA_RANGE . Rfc9110::WEIGHT . '|' . Rfc9110::MEDIA_RANGE . '@J', $match['accept'], PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);
		foreach ($matches as $match) {
			$match['parameters'] = $this->parametersFactory->create($match['parameters']);

			$mediaRanges[] = new MediaRange($this->preg, $match['type'], $match['subtype'], $match['parameters'], $match['q']);
		}

		return new Accept($mediaRanges);
	}
}
