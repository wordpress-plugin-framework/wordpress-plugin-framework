<?php

namespace WordPressPluginFramework\Http\Method;

enum Method: string
{
	case Get = 'GET';
	case Head = 'HEAD';
	case Post = 'POST';
	case Put = 'PUT';
	case Patch = 'PATCH';
	case Delete = 'DELETE';
	case Options = 'OPTIONS';

	public static function create(string $method): static
	{
		$method = static::tryFrom($method);
		if ($method === null) {
			throw new MethodException("invalid method");
		}

		return $method;
	}
}
