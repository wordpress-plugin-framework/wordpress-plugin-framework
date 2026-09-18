<?php

namespace WordPressPluginFramework\Exceptions\Handler;

use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
};
use Throwable;

interface HandlerInterface
{
	public function handle(RequestInterface $request, Throwable $throwable): ResponseInterface;
}