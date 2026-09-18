<?php

namespace WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use WordPressPluginFramework\{
	Http\Message\Headers\Parameters\ParametersFactoryInterface,
	Http\Abnf\Rfc9110,
};

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

	public function create(string $contentType): MediaTypeInterface
	{
		if (preg_match('@\A' . Rfc9110::CONTENT_TYPE . '\z@', $contentType, $match) !== 1) {
			throw new MediaTypeFactoryException('invalid content type');
		}

		$parameters = $this->parametersFactory->create($match['parameters']);

		return new MediaType($match['type'], $match['subtype'], $parameters);
	}
}
