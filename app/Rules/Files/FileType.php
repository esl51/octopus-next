<?php

namespace App\Rules\Files;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class FileType implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $filableTypeRule = new FilableType();
        $filableTypeRule->validate('filable_type', $this->data['filable_type'], $fail);
        $className = 'App\\Models\\' . $this->data['filable_type'];
        $item = new $className();
        if (!method_exists($item, 'getFileTypes') || !array_key_exists($value, $item->getFileTypes())) {
            $fail(trans('validation.file_type', [
                'attribute' => $attribute,
            ]));
        }
    }
}
