<?php

namespace App\Http\Requests;

use App\Classes\TranslatableRuleFactory;

abstract class ItemStoreUpdateRequest extends ItemRequest
{
    protected $item = null;

    protected array $fillable = [];

    protected array $fillableTranslations = [];

    public function __construct($item = null)
    {
        $this->item = $item;
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

    public function prepareRules(array $rules): array
    {
        if (count($this->fillableTranslations)) {
            $rules = TranslatableRuleFactory::make($rules);
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];
        if (count($this->fillableTranslations)) {
            foreach ($this->fillableTranslations as $attribute) {
                foreach (config('translatable.locales') as $locale) {
                    $attributes[$attribute.':'.$locale] =
                        trans('validation.attributes.'.$attribute).' ('.$locale.')';
                }
            }
        }

        return $attributes;
    }
}
