<?php

declare(strict_types=1);

namespace WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange\Precedence;

enum Precedence: int
{
	case TypeSubtype = 2;
	case TypeWildcardSubtype = 3;
	case WildcardTypeWildcardSubtype = 4;
}
