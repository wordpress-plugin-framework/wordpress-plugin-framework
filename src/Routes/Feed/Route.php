<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Routes\Feed;

use Closure;
use WordPressPluginFramework\{
	Routes\RouteInterface,
	Emitter\EmitterInterface,
	Http\Request\RequestInterface,
	Responder\ResponderInterface,
	Exceptions\Handler\HandlerInterface,
	Pipeline\PipelineBuilderInterface,
};
use Throwable;

readonly class Route implements RouteInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected ResponderInterface $responder,
		protected HandlerInterface $handler,
		protected EmitterInterface $emitter,
		protected PipelineBuilderInterface $pipelineBuilder,
		protected string $name,
		protected Closure $closure,
		protected ?Closure $pipelineBuilderClosure = null,
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
		try {
			$pipeline = $this->pipelineBuilder()->build();

			$response = $this->responder->respond(
				$this->request,
				$pipeline($this->closure),
			);
		} catch (Throwable $throwable) {
			$response = $this->handler->handle($this->request, $throwable);
		}

		$this->emitter->emit($response);

		exit();
	}

	protected function pipelineBuilder(): PipelineBuilderInterface
	{
		return $this->pipelineBuilderClosure === null ? $this->pipelineBuilder : ($this->pipelineBuilderClosure)($this->pipelineBuilder);
	}
}
