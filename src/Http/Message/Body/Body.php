<?php

namespace WordPressPluginFramework\Http\Message\Body;

use WordPressPluginFramework\{
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class Body implements BodyInterface
{
	public function __construct(
		protected EncoderInterface $encoder,
		protected mixed $body,
	) {
	}

	public function mediaType(): MediaTypeInterface
	{
		return $this->encoder->contentType();
	}

	public function __invoke(): mixed
	{
		return $this->body;
	}

	public function __toString(): string
	{
		return $this->encoder->encode($this->body);
	}
}
