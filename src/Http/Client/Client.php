<?php

namespace WordPressPluginFramework\Http\Client;

use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Http\Request\RequestFactoryInterface,
	Http\Response\ResponseInterface,
	Http\Response\ResponseFactoryInterface,
};
use WP_Error;

readonly class Client implements ClientInterface
{
	public function __construct(
		protected RequestFactoryInterface $requestFactory,
		protected ResponseFactoryInterface $responseFactory,
	) {
	}
	
	public function request(RequestInterface $request): ResponseInterface
	{
		$response = wp_safe_remote_request(
			$request->url(),
			[
				'method' => $request->method()->value,
				'headers' => $request->headers(),
				'body' => $request->body(),
			]
		);

		if ($response instanceof WP_Error) {
			throw new ClientException($response->get_error_message());
		}

		return $this->responseFactory->create(
			wp_remote_retrieve_response_code($response),
			wp_remote_retrieve_headers($response)->getAll(),
			wp_remote_retrieve_body($response)
		);
	}
}
