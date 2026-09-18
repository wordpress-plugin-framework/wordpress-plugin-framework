<?php

namespace WordPressPluginFramework\Routes\AdminAjax;

use Closure;
use WordPressPluginFramework\{
	Routes\RouteInterface,
	Routes\RouteException,
	Emitter\EmitterInterface,
	Http\Coders\CodersInterface,
	Http\Negotiator\NegotiatorInterface,
	Http\Message\Headers\Accept\AcceptFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Http\Response\ResponseBuilderInterface,
	Exceptions\Handler\ViewModels\ViewModel,
	Exceptions\Interfaces\HasStatusCodeInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineFactoryInterface,
	View\ViewInterface,
	View\ViewFactoryInterface,
	Renderer\RendererInterface,
};
use Throwable;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected NegotiatorInterface $negotiator,
		protected AcceptFactoryInterface $acceptFactory,
		protected CodersInterface $coders,
		protected ResponseBuilderInterface $responseBuilder,
		protected ViewFactoryInterface $viewFactory,
		protected RendererInterface $renderer,
		protected EmitterInterface $emitter,
		protected PipelineFactoryInterface $pipelineFactory,
		protected array $mediaTypes,
		protected string $action,
		protected Closure $closure,
		protected ?Closure $middlewaresBuilderClosure = null,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			"wp_ajax_{$this->action}",
			$this->callback(...),
			10,
			0,
		);

		add_action(
			"wp_ajax_nopriv_{$this->action}",
			$this->callback(...),
			10,
			0,
		);
	}

	public function up(): void
	{

	}

	public function down(): void
	{

	}

	protected function callback(): void
	{
		$pipeline = $this->pipelineFactory->create($this->request, $this->middlewaresBuilderClosure);

		try {
			$response = $this->response($pipeline(($this->closure)(...)));
		} catch (Throwable $throwable) {
			$response = $this->represent(
				$this->viewFactory->create('exception', ViewModel::createFromThrowable($throwable)),
				$throwable instanceof HasStatusCodeInterface ? $throwable->getStatusCode() : 500,
			);
		}

		$this->emitter->emit($response);

		exit();
	}

	protected function response(mixed $return): ResponseInterface
	{
		if ($return instanceof ResponseInterface) {
			return $return;
		}

		if ($return instanceof ResponseBuilderInterface) {
			return $return->build();
		}

		if ($return instanceof ViewInterface) {
			return $this->represent($return, 200);
		}

		throw new RouteException('controller must return a response or a view');
	}

	protected function represent(ViewInterface $view, int $statusCode): ResponseInterface
	{
		$model = $view->model();

		$mediaType = $this->negotiator->negotiate(
			$this->acceptFactory->tryCreate($this->request->headers()->accept()),
			...$this->available($model),
		);

		if ($mediaType === null) {
			return $this->responseBuilder->withStatusCode(406)->withoutBody()->build();
		}

		$builder = $this->responseBuilder->withStatusCode($statusCode)->withHeaders(['vary' => 'accept']);

		if ($this->html($mediaType)) {
			return $builder->withBody((string) $mediaType, $this->renderer->render($view))->build();
		}

		return $model === null
			? $builder->withoutBody()->build()
			: $builder->withBody((string) $mediaType, $model)->build();
	}

	protected function available(mixed $model): array
	{
		return array_filter(
			$this->mediaTypes,
			fn(MediaTypeInterface $mediaType) =>
			$this->html($mediaType) ||
			$this->coders->encoder($model, $mediaType) !== null,
		);
	}

	protected function html(MediaTypeInterface $mediaType): bool
	{
		return $mediaType->type() === 'text' && $mediaType->subtype() === 'html';
	}
}
