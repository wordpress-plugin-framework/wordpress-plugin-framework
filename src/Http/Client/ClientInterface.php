<?php

namespace WordPressPluginFramework\Http\Client;

use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
};

interface ClientInterface
{
	public function request(RequestInterface $request): ResponseInterface;
}
