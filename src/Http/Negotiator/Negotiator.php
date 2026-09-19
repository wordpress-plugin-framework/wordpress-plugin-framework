<?php

namespace WordPressPluginFramework\Http\Negotiator;

use WordPressPluginFramework\{
	Http\Exceptions\NotAcceptable\Exception as NotAcceptableException,
	Http\Message\Headers\Vary\Vary,
	Http\Message\Headers\Vary\VaryInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Responses\ResponsesInterface,
};

readonly class Negotiator implements NegotiatorInterface
{
	public function negotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface
	{
		$negotiatedResponses = $this->negotiateResponses($request, $responses);
		if ($negotiatedResponses->count() === 0) {
			throw new NotAcceptableException('no acceptable representation', 'content_negotiator_error');
		}

		$response = $negotiatedResponses->first();

		return $this->withVary($response);
	}

	public function tryNegotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface
	{
		$negotiatedResponses = $this->negotiateResponses($request, $responses);

		$negotiatedResponses = $negotiatedResponses->count() === 0 ? $responses : $negotiatedResponses;
		$response = $negotiatedResponses->first();

		return $this->withVary($response);
	}

	protected function negotiateResponses(RequestInterface $request, ResponsesInterface $responses): ResponsesInterface
	{
		if ($responses->count() === 0) {
			throw new NegotiatorException('no representations available');
		}

		$accept = $request->headers()->accept();
		if ($accept === null) {
			return $responses;
		}

		return $responses
			->filterByAccept($accept)
			->sortByAccept($accept);
	}

	protected function withVary(ResponseInterface $response): ResponseInterface
	{
		return $response->withHeaders(fn($headers) => $headers->withVary(
			fn(?VaryInterface $vary) => ($vary ?? new Vary([]))->with('accept'),
		));
	}
}
