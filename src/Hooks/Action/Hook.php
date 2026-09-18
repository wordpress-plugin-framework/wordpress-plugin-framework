<?php

namespace WordPressPluginFramework\Hooks\Action;

use Closure;
use WordPressPluginFramework\{
	Hooks\HookInterface,
	Http\Request\RequestInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineFactoryInterface,
	Renderer\RendererInterface,
	View\ViewInterface,
};

readonly class Hook implements HookInterface
{
	protected PipelineInterface $pipeline;

	public function __construct(
		protected RequestInterface $request,
		protected RendererInterface $renderer,
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $name,
		protected Closure $closure,
		protected int $priority = 10,
		protected ?Closure $middlewaresBuilderClosure = null,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			$this->name,
			$this->callback(...),
			$this->priority,
			PHP_INT_MAX,
		);
	}

	protected function callback(...$args): void
	{
		$view = $this->pipeline()(fn($request) => ($this->closure)($request, ...$args));
		if ($view instanceof ViewInterface) {
			echo $this->renderer->render($view);
		}
	}

	protected function pipeline(): PipelineInterface
	{
		return $this->pipeline ??= $this->pipelineFactory->create($this->request, $this->middlewaresBuilderClosure);
	}
}
