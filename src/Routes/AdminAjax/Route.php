<?php

namespace WordPressPluginFramework\Routes\AdminAjax;

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
		protected string $action,
		protected Closure $closure,
		protected ?Closure $pipelineBuilderClosure = null,
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

		wp_die();
	}

	protected function pipelineBuilder(): PipelineBuilderInterface
	{
		return $this->pipelineBuilderClosure === null ? $this->pipelineBuilder : ($this->pipelineBuilderClosure)($this->pipelineBuilder);
	}
}
