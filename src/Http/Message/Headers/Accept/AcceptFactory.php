<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept;

use WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\MediaRange,
	Http\Message\Headers\Parameters\ParametersFactoryInterface,
	Http\Abnf\Rfc9110,
};

readonly class AcceptFactory implements AcceptFactoryInterface
{
	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

	public function create(string $accept): AcceptInterface
	{
		if (preg_match('@\A' . Rfc9110::ACCEPT . '\z@J', $accept) !== 1) {
			throw new AcceptFactoryException('invalid accept');
		}

		preg_match_all('@' . Rfc9110::MEDIA_RANGE . Rfc9110::WEIGHT . '|' . Rfc9110::MEDIA_RANGE . '@J', $accept, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$mediaRanges = [];

		foreach ($matches as $match) {
			$parameters = $this->parametersFactory->create($match['parameters'] ?? '');

			$mediaRanges[] = new MediaRange($match['type'], $match['subtype'], $parameters, $match['q'] ?? null);
		}

		return new Accept($mediaRanges);
	}
}
