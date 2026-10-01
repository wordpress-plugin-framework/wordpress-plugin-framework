<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Responder;

use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
};

interface ResponderInterface
{
	public function respond(RequestInterface $request, mixed $result): ResponseInterface;
}
