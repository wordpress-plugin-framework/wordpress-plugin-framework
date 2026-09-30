<?php

namespace WordPressPluginFramework\Exceptions\Handler;

use WordPressPluginFramework\{
	Exceptions\Interfaces\HasStatusCodeInterface,
	Exceptions\Views\View,
	Http\Negotiator\NegotiatorInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Responses\ResponsesBuilderInterface,
};
use Throwable;

readonly class Handler implements HandlerInterface
{
	public function __construct(
		protected NegotiatorInterface $negotiator,
		protected ResponsesBuilderInterface $responsesBuilder,
	) {
	}

	public function handle(RequestInterface $request, Throwable $throwable): ResponseInterface
	{
		$responses = $this->responsesBuilder
			->statusCode($this->statusCode($throwable))
			->body(View::createFromThrowable($throwable))
			->build();

		return $this->negotiator->tryNegotiate($request, $responses);
	}

	protected function statusCode(Throwable $throwable): int
	{
		return $throwable instanceof HasStatusCodeInterface ? $throwable->getStatusCode() : 500;
	}
}
