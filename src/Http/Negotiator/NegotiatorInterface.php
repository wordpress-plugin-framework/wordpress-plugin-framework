<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Negotiator;

use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Responses\ResponsesInterface,
};

interface NegotiatorInterface
{
	public function negotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface;
	public function tryNegotiate(RequestInterface $request, ResponsesInterface $responses): ResponseInterface;
}
