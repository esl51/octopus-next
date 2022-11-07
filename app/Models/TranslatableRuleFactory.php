<?php

namespace App\Models;

use Astrotomic\Translatable\Validation\RuleFactory;

class TranslatableRuleFactory extends RuleFactory
{
    protected function replacePlaceholder(string $locale, string $value): string
    {
        $fallbackLocale = config('translatable.fallback_locale');
        if (!empty($fallbackLocale)) {
            if (preg_match('/^required_if_fallback\:?([^$]+)?$/', $value, $matches) && $locale == $fallbackLocale) {
                $value = 'required';
            } elseif (!empty($matches)) {
                $value = $matches[1];
            }
        }
        return preg_replace($this->getPattern(), $this->getReplacement($locale), $value);
    }
}
