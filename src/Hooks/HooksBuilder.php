<?php

namespace WordPressPluginFramework\Hooks;

use Closure;
use WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\PipelineFactoryInterface,
	Renderer\RendererInterface,
};

readonly class HooksBuilder implements HooksBuilderInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected RendererInterface $renderer,
		protected PipelineFactoryInterface $pipelineFactory,
		protected array $hooks = [],
	) {
	}

	public function hooks(): array
	{
		return $this->hooks;
	}

	public function withHooks(HookInterface ...$hooks): static
	{
		return new static($this->request, $this->renderer, $this->pipelineFactory, $hooks);
	}

	public function withoutHooks(): static
	{
		return new static($this->request, $this->renderer, $this->pipelineFactory, []);
	}

	public function withHook(HookInterface $hook): static
	{
		return $this->withHooks(
			...[
				...$this->hooks,
				$hook,
			],
		);
	}

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withHook(
			new Action\Hook($this->request, $this->renderer, $this->pipelineFactory, $name, $closure, $priority, $middlewaresBuilderClosure),
		);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withHook(
			new Filter\Hook($this->request, $this->renderer, $this->pipelineFactory, $name, $closure, $priority, $middlewaresBuilderClosure),
		);
	}

	public function activation(string $file, Closure $closure): static
	{
		return $this->withHook(
			new Activation\Hook($file, $closure),
		);
	}

	public function deactivation(string $file, Closure $closure): static
	{
		return $this->withHook(
			new Deactivation\Hook($file, $closure),
		);
	}

	public function build(): array
	{
		return $this->hooks;
	}
}
