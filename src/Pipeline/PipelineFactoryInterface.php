<?php

namespace WordPressPluginFramework\Pipeline;

use Closure;
use WordPressPluginFramework\Http\Request\RequestInterface;

interface PipelineFactoryInterface
{
	public function create(RequestInterface $request, ?Closure $middlewaresBuilderClosure = null): PipelineInterface;
}
