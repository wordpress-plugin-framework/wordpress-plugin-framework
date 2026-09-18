<?php

namespace WordPressPluginFramework\Http\Message\Body;

use WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use Stringable;

interface BodyInterface extends Stringable
{
    public function mediaType(): MediaTypeInterface;
    public function __invoke(): mixed;
}
