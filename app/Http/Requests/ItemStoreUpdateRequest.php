<?php

namespace App\Http\Requests;

use App\Classes\TranslatableRuleFactory;

abstract class ItemStoreUpdateRequest extends ItemRequest
{
    protected $item = null;

    protected ?string $class = null;

    public function __construct($item = null)
    {
        $this->item = $item;
        $this->class = $this->class ?: self::guessClass();
    }

    private static function guessClass(): string
    {
        return preg_replace('/(.+)\\\\Http\\\\Requests\\\\(.+)StoreUpdateRequest$/m', '$1\Models\\\$2', static::class);
    }

    public function authorize(): bool
    {
        if ($this->isMethod('post') && empty($this->item)) {
            return true;
        } elseif (! empty($this->item)) {
            return $this->item->is_editable;
        }

        return false;
    }

    public function isTranslatable(): bool
    {
        return ! empty($this->class) && method_exists($this->class, 'translations');
    }

    public function prepareRules(array $rules): array
    {
        if ($this->isTranslatable()) {
            $rules = TranslatableRuleFactory::make($rules);
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];
        if ($this->isTranslatable()) {
            $translatedAttributes = (new $this->class)->translatedAttributes;
            foreach ($translatedAttributes as $attribute) {
                foreach (config('translatable.locales') as $locale) {
                    $attributes[$attribute.':'.$locale] =
                        trans('validation.attributes.'.$attribute).' ('.$locale.')';
                }
            }
        }

        return $attributes;
    }
}
