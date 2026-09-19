<?php

namespace WordPressPluginFramework\Http\Message\Headers;

use WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeFactoryInterface,
	Http\Message\Headers\Vary\VaryFactoryInterface,
};

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function __construct(
		protected AcceptFactoryInterface $acceptFactory,
		protected MediaTypeFactoryInterface $mediaTypeFactory,
		protected VaryFactoryInterface $varyFactory,
	) {
	}

	public function create(array $headers = []): HeadersInterface
	{
		$headers = array_change_key_case($headers, CASE_LOWER);

		$accept = isset($headers['accept']) ? $this->acceptFactory->create($headers['accept']) : null;
		$contentType = isset($headers['content-type']) ? $this->mediaTypeFactory->create($headers['content-type']) : null;
		$vary = isset($headers['vary']) ? $this->varyFactory->create($headers['vary']) : null;

		return new Headers($headers, $accept, $contentType, $vary);
	}
}
