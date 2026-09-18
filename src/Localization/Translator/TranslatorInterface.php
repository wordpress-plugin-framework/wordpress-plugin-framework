<?php

namespace WordPressPluginFramework\Localization\Translator;

interface TranslatorInterface
{
    public function translate(string $message, ?string $context = null): string;
    public function translatePlural(string $message, string $pluralMessage, int $number, ?string $context = null): string;
}
