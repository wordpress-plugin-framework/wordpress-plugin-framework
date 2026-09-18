<?php

namespace WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange\Precedence;

enum Precedence: int
{
    case TypeSubtype = 2;
    case TypeWildcardSubtype = 3;
    case WildcardTypeWildcardSubtype = 4;
}
