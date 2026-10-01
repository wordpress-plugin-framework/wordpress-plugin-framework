<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Url\Fragment;

use WordPressPluginFramework\{
	Http\Abnf\Rfc3986,
	Preg\PregInterface,
};

readonly class FragmentFactory implements FragmentFactoryInterface
{
	public function __construct(
		protected PregInterface $preg,
	) {
	}

	public function create(string $fragment): FragmentInterface
	{
		$match = $this->preg->match('@\A' . Rfc3986::FRAGMENT . '\z@', $fragment);
		if ($match === null) {
			throw new FragmentFactoryException('invalid fragment');
		}

		$fragment = $this->preg->replaceCallback('@' . Rfc3986::PCT_ENCODED . '@', fn($match) => rawurldecode($match[0]), $match['fragment']);

		return new Fragment($this->preg, $fragment);
	}
}
