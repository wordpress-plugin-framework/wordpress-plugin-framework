<?php

namespace WordPressPluginFramework\Renderer\Escaper;

interface EscaperInterface
{
    public function attr(string $value): string;
    public function html(string $value): string;
    public function js(string $value): string;
    public function textarea(string $value): string;
    public function url(string $value): string;
    public function xml(string $value): string;
}