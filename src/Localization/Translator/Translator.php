<?php

namespace WordPressPluginFramework\Localization\Translator;

use WordPressPluginFramework\Localization\Formatter\FormatterInterface;

readonly class Translator implements TranslatorInterface
{
    public function __construct(
        protected FormatterInterface $formatter,
        protected string $domain,
    ) {
    }

    public function translate(string $message, ?string $context = null): string
    {
        return $context === null ? __($message, $this->domain) : _x($message, $context, $this->domain);
    }

    public function translatePlural(string $message, string $pluralMessage, int $number, ?string $context = null): string
    {
        return sprintf(
            $context === null ? _n($message, $pluralMessage, $number, $this->domain) : _nx($message, $pluralMessage, $number, $context, $this->domain),
            $this->formatter->number($number),
        );
    }
}
