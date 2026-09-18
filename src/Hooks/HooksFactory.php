<?php

namespace WordPressPluginFramework\Hooks;

use Closure;

readonly class HooksFactory implements HooksFactoryInterface
{
	public function __construct(
		protected HooksBuilderInterface $hooksBuilder,
	) {
	}

	public function create(Closure $hooksBuilderClosure): HooksInterface
	{
		return new Hooks(
			$this->buildHooks($hooksBuilderClosure),
		);
	}

	protected function buildHooks(Closure $closure): array
	{
		$hooksBuilder = $closure($this->hooksBuilder);
		if (!$hooksBuilder instanceof HooksBuilderInterface) {
			throw new HooksFactoryException('closure must return hooks builder instance');
		}

		return $hooksBuilder->build();
	}
}
