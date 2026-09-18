<?php

namespace WordPressPluginFramework\Http\Message\Headers\Parameters;

use WordPressPluginFramework\Http\Abnf\Rfc9110;

readonly class ParametersFactory implements ParametersFactoryInterface
{
	public function create(string $parameters): ParametersInterface
	{
		if (preg_match('@\A' . Rfc9110::PARAMETERS . '\z@', $parameters) !== 1) {
			throw new ParametersFactoryException('invalid parameters');
		}

		preg_match_all('@' . Rfc9110::OWS . ';' . Rfc9110::OWS . Rfc9110::PARAMETER . '@', $parameters, $matches, PREG_SET_ORDER);

		$parameters = [];

		foreach ($matches as $match) {
			$parameters[$match['parameter_name']] = $this->unquote($match['parameter_value']);
		}

		return new Parameters($parameters);
	}

	protected function unquote(string $value): string
	{
		if (preg_match('@\A' . Rfc9110::TOKEN . '\z@', $value)) {
			return $value;
		}

		return preg_replace('@\x5C(.)@s', '$1', substr($value, 1, -1));
	}
}
