<?php

namespace WordPressPluginFramework\Exceptions\Handler;

use WordPressPluginFramework\{
	Exceptions\Handler\ViewModels\ViewModel,
	Exceptions\Interfaces\HasStatusCodeInterface,
	Http\Negotiator\NegotiatorInterface,
	Http\Message\Headers\Accept\AcceptFactoryInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Response\ResponseFactoryInterface,
	View\ViewFactoryInterface,
};
use Throwable;

readonly class Handler implements HandlerInterface
{
	public function __construct(
		protected NegotiatorInterface $negotiator,
		protected AcceptFactoryInterface $acceptFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected ViewFactoryInterface $viewFactory,
	) {
	}

	public function handle(RequestInterface $request, Throwable $throwable): ResponseInterface
	{
		$viewModel = ViewModel::createFromThrowable($throwable);
		$body = $viewModel->toArray();

		$contentType = $this->contentType($request, $body);
		if ($contentType === 'text/html') {
			$view = $this->viewFactory->tryCreate('exception', $viewModel);
			if ($view === null) {
				throw $throwable;
			}

			$body = $view->render();
		}

		return $this->responseFactory->create(
			$this->statusCode($throwable),
			[
				'Content-Type' => $contentType,
			],
			$body,
		);
	}

	protected function statusCode(Throwable $throwable): int
	{
		return $throwable instanceof HasStatusCodeInterface ? $throwable->getStatusCode() : 500;
	}

	protected function contentType(RequestInterface $request, array $body): string
	{
		$accept = $this->acceptFactory->tryCreate($request->headers()?->accept());

		$mediaType = $this->negotiator->tryNegotiate($accept, $body);
		if ($mediaType === null) {
			// RFC 9110 §12.5.1 allows disregarding Accept; an error page defaults to the human-readable representation
			return 'text/html';
		}

		return "{$mediaType->type()}/{$mediaType->subtype()}";
	}
}
