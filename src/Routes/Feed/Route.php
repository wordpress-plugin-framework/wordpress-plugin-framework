<?php

namespace WordPressPluginFramework\Routes\Feed;

use Closure;
use WordPressPluginFramework\{
	Routes\RouteInterface,
	Http\Responder\ResponderInterface,
	Http\Responder\ResponderFactoryInterface,
	Http\Request\RequestInterface,
	Http\Response\ResponseInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineFactoryInterface,
};


readonly class Route implements RouteInterface
{
	protected const string MEDIA_TYPE = 'application/xml';

	protected ResponderInterface $responder;
	protected PipelineInterface $pipeline;

	public function __construct(
		protected RequestInterface $request,
		protected ResponderFactoryInterface $responderFactory,
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $name,
		protected Closure $closure,
		protected ?Closure $middlewaresBuilderClosure = null,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			'init',
			$this->addFeed(...),
			10,
			0,
		);
	}

	public function up(): void
	{
		$this->addFeed();
	}

	public function down(): void
	{

	}

	protected function addFeed(): void
	{
		add_feed(
			$this->name,
			$this->callback(...),
		);
	}

	protected function callback(): void
	{
		$pipeline = $this->pipeline();
		$responder = $this->responder();

		$response = $responder->respond(
			$this->request,
			$pipeline(($this->closure)(...)),
		);

		$this->statusCode($response);
		$this->headers($response);
		$this->body($response);

		exit();
	}

	protected function pipeline(): PipelineInterface
	{
		return $this->pipeline ??= $this->pipelineFactory->create($this->request, $this->middlewaresBuilderClosure);
	}

	protected function responder(): ResponderInterface
	{
		return $this->responder ??= $this->responderFactory->create(self::MEDIA_TYPE);
	}

	protected function statusCode(ResponseInterface $response): void
	{
		http_response_code(
			$response->statusCode(),
		);
	}

	protected function headers(ResponseInterface $response): void
	{
		$headers = $response->headers();
		foreach ($headers as $name => $value) {
			header("{$name}: {$value}");
		}
	}

	protected function body(ResponseInterface $response): void
	{
		echo (string) $response->body();
	}
}
