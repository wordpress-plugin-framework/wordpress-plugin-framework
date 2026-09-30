<?php

namespace WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use WordPressPluginFramework\{
	Http\Message\Headers\Parameters\ParametersFactoryInterface,
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
		protected PregInterface $preg,
	) {
	}

	public function create(string $contentType): MediaTypeInterface
	{
		$match = $this->preg->match('@\A' . Rfc9110::CONTENT_TYPE . '\z@', $contentType, PREG_UNMATCHED_AS_NULL);
		if ($match === null) {
			throw new MediaTypeFactoryException('invalid content type');
		}

		$match['parameters'] = $this->parametersFactory->create($match['parameters']);

		return new MediaType($this->preg, $match['type'], $match['subtype'], $match['parameters']);
	}
}
