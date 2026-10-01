<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Hooks\Action;

use Closure;
use WordPressPluginFramework\{
	Hooks\HookInterface,
	Http\Request\RequestInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineBuilderInterface,
	Renderer\RendererInterface,
	View\ViewInterface,
};

readonly class Hook implements HookInterface
{
	protected PipelineInterface $pipeline;

	public function __construct(
		protected RequestInterface $request,
		protected RendererInterface $renderer,
		protected PipelineBuilderInterface $pipelineBuilder,
		protected string $name,
		protected Closure $closure,
		protected int $priority = 10,
		protected ?Closure $pipelineBuilderClosure = null,
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
		$pipeline = $this->pipeline ??= $this->pipelineBuilder()->build();

		$view = $pipeline(fn($request) => ($this->closure)($request, ...$args));
		if ($view instanceof ViewInterface) {
			echo $this->renderer->render($view);
		}
	}

	protected function pipelineBuilder(): PipelineBuilderInterface
	{
		return $this->pipelineBuilderClosure === null ? $this->pipelineBuilder : ($this->pipelineBuilderClosure)($this->pipelineBuilder);
	}
}
