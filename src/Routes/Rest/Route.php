<?php

namespace WordPressPluginFramework\Routes\Rest;

use Closure;
use WordPressPluginFramework\{
	Routes\RouteInterface,
	Http\Responder\ResponderInterface,
	Http\Responder\ResponderFactoryInterface,
	Http\Request\RequestInterface,
	Pipeline\PipelineInterface,
	Pipeline\PipelineFactoryInterface,
	Http\Method\Method,
};
use WP_REST_Request;
use WP_REST_Response;

readonly class Route implements RouteInterface
{
	protected const string MEDIA_TYPE = 'application/json';

	protected ResponderInterface $responder;
	protected PipelineInterface $pipeline;

	public function __construct(
		protected RequestInterface $request,
		protected ResponderFactoryInterface $responderFactory,
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $routeNamespace,
		protected string $route,
		protected Closure $closure,
		protected Method $method,
		protected ?Closure $middlewaresBuilderClosure = null,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			'rest_api_init',
			$this->restApiInit(...),
			10,
			0,
		);

		add_filter(
			'rest_pre_serve_request',
			$this->restPreServeRequest(...),
			10,
			3,
		);
	}

	public function up(): void
	{

	}

	public function down(): void
	{

	}

	protected function restApiInit(): void
	{
		register_rest_route(
			$this->routeNamespace,
			$this->route,
			[
				'methods' => [
					$this->method->value,
				],
				'callback' => $this->callback(...),
				'permission_callback' => $this->permissionCallback(...),
			],
		);
	}

	protected function restPreServeRequest(bool $served, WP_REST_Response $response, WP_REST_Request $request): bool
	{
		if ($request->get_route() !== "/{$this->routeNamespace}/{$this->route}") {
			return $served;
		}

		echo $response->get_data();

		return true;
	}

	protected function callback(WP_REST_Request $request): WP_REST_Response
	{
		$pipeline = $this->pipeline();
		$responder = $this->responder();

		$response = $responder->respond(
			$this->request,
			$pipeline(($this->closure)(...)),
		);

		return new WP_REST_Response(
			(string) $response->body(),
			$response->statusCode(),
			$response->headers(),
		);
	}

	protected function pipeline(): PipelineInterface
	{
		return $this->pipeline ??= $this->pipelineFactory->create($this->request, $this->middlewaresBuilderClosure);
	}

	protected function responder(): ResponderInterface
	{
		return $this->responder ??= $this->responderFactory->create(self::MEDIA_TYPE);
	}


	protected function permissionCallback(WP_REST_Request $request): bool
	{
		return true;
	}
}
