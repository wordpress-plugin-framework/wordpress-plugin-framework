<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Responder;

use WordPressPluginFramework\{
	Http\Negotiator\NegotiatorInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Response\ResponseBuilderInterface,
	Http\Responses\ResponsesInterface,
	Http\Responses\ResponsesBuilderInterface,
};

readonly class Responder implements ResponderInterface
{
	public function __construct(
		protected NegotiatorInterface $negotiator,
	) {
	}

	public function respond(RequestInterface $request, mixed $result): ResponseInterface
	{
		if ($result instanceof ResponsesBuilderInterface || $result instanceof ResponseBuilderInterface) {
			return $this->respond($request, $result->build());
		}

		if ($result instanceof ResponsesInterface) {
			return $this->negotiator->negotiate($request, $result);
		}

		if ($result instanceof ResponseInterface) {
			return $result;
		}

		throw new ResponderException(
			sprintf('Unsupported result: %s.', get_debug_type($result)),
		);
	}
}
