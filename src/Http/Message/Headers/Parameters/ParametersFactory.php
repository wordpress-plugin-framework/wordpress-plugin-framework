<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Headers\Parameters;

use WordPressPluginFramework\{
	Http\Abnf\Rfc9110,
	Preg\PregInterface,
};

readonly class ParametersFactory implements ParametersFactoryInterface
{
	public function __construct(
		protected PregInterface $preg,
	) {
	}

	public function create(string $parameters): ParametersInterface
	{
		$match = $this->preg->match('@\A' . Rfc9110::PARAMETERS . '\z@', $parameters);
		if ($match === null) {
			throw new ParametersFactoryException('invalid parameters');
		}

		$parameters = [];

		$matches = $this->preg->matchAll('@' . Rfc9110::OWS . ';' . Rfc9110::OWS . Rfc9110::PARAMETER . '@', $match['parameters'], PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);
		foreach ($matches as $match) {
			$parameters[$match['parameter_name']] = $this->unquote($match['parameter_value']);
		}

		return new Parameters($this->preg, $parameters);
	}

	protected function unquote(string $value): string
	{
		$match = $this->preg->match('@\A' . Rfc9110::TOKEN . '\z@', $value);
		if ($match !== null) {
			return $value;
		}

		return $this->preg->replaceCallback('@' . Rfc9110::QUOTED_PAIR . '@', fn($match) => substr($match[0], 1), substr($value, 1, -1));
	}
}
