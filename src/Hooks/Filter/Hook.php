<?php

namespace WordPressPluginFramework\Hooks\Filter;

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
		add_filter(
			$this->name,
			$this->callback(...),
			$this->priority,
			PHP_INT_MAX,
		);
	}

	protected function callback(...$args): mixed
	{
		$view = $this->pipeline()(fn($request) => ($this->closure)($request, ...$args));
		if ($view instanceof ViewInterface) {
			return $this->renderer->render($view);
		}

		return $view;
	}

	protected function pipeline(): PipelineInterface
	{
		return $this->pipeline ??= $this->pipelineFactory->create($this->request, $this->middlewaresBuilderClosure);
	}
}
